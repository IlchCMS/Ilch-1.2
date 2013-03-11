<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2013 ilch.de
 */
defined('main') or die('no direct access');

$count_query_xyzXYZ = 0;

function db_connect()
{
    if (Ilch_Registry::get('db') !== null) {
        return;
    }

    $db = new Ilch_Database_Mysql();
    $db->connect(DBHOST, DBUSER, DBPASS);
    $db->setDatabase(DBDATE);
    $db->setPrefix(DBPREF);
    Ilch_Registry::set('db', $db);
    Ilch_Registry::set('dbLink', $db->getLink());
}

function db_close()
{
    mysqli_close(Ilch_Registry::get('dbLink'));
}

if (defined('DEBUG') and DEBUG) {
    $ILCH_DEBUG_DB_QUERIES = array();
    $ILCH_DEBUG_DB_COUNT_QUERIES = 0;

    function db_check_error($r)
    {
        $dbLink = Ilch_Registry::get('dbLink');

        if (!$r AND mysqli_errno($dbLink) != 0) {
            // var_export (debug_backtrace(), true)
            return '<font color="#FF0000">MySQL Error:</font><br/>' . mysqli_errno($dbLink) . ' : ' . mysqli_errno($dbLink);
        }
        return '';
    }

    function db_query($q)
    {
        global $ILCH_DEBUG_DB_COUNT_QUERIES, $ILCH_DEBUG_DB_QUERIES;
        $ILCH_DEBUG_DB_COUNT_QUERIES++;
        $dbLink = Ilch_Registry::get('dbLink');

        if (preg_match("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
            $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i", "UPDATE `" . DBPREF . "\\1`\\2", $q);
        } elseif (preg_match("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
            $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `" . DBPREF . "\\1`\\2", $q);
        } else {
            $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF . "\\1\\2", $q);
        }

        if (!function_exists('debug_bt')) {

            function debug_bt()
            {
                return debug_backtrace();
            }
        }

        $tmp = array();
        $vor = microtime(true);
        $qry = mysqli_query($dbLink, $q);
        $nach = microtime(true);
        $res = is_resource($qry);
        $tmp['is_valid_result_resource'] = $res;
        $tmp['duration'] = $nach - $vor;
        $tmp['time'] = $nach - SCRIPT_START_TIME;
        $tmp['query'] = $q;
        $tmp['affected_rows'] = mysqli_affected_rows($dbLink);
        $tmp['call'] = debug_bt();
        $error = db_check_error($qry);
        if (!empty($error)) {
            $tmp['error'] = $error;
        }

        $ILCH_DEBUG_DB_QUERIES[] = $tmp;
        return ($qry);
    }
} else {

    function db_check_error(&$r, $q)
    {
        $dbLink = Ilch_Registry::get('dbLink');

        if (!$r AND mysqli_errno($dbLink) != 0 AND function_exists('is_coadmin') AND is_coadmin()) {
            // var_export (debug_backtrace(), true)
            echo ('<font color="#FF0000">MySQL Error:</font><br/>' . mysqli_errno($dbLink) . ' : ' . mysqli_error($dbLink) . '<br/>in Query:<br/>' . $q . '<pre>' . debug_bt() . '</pre>');
        }
        return ($r);
    }

    function db_query($q)
    {
        $dbLink = Ilch_Registry::get('dbLink');
        if (preg_match("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
            $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i", "UPDATE `" . DBPREF . "\\1`\\2", $q);
        } elseif (preg_match("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
            $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `" . DBPREF . "\\1`\\2", $q);
        } else {
            $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF . "\\1\\2", $q);
        }

        return (db_check_error(mysqli_query($dbLink, $q), $q));
    }
}

function db_result($erg, $zeile = 0, $spalte = 0)
{
      $erg->data_seek($zeile);
      $ceva = $erg->fetch_row();
      return $ceva[$spalte];
}

function db_fetch_assoc($erg)
{
    return (mysqli_fetch_assoc($erg));
}

function db_fetch_row($erg)
{
    return (mysqli_fetch_row($erg));
}

function db_fetch_object($erg)
{
    return (mysqli_fetch_object($erg));
}

function db_num_rows($erg)
{
    return (mysqli_num_rows($erg));
}

function db_last_id()
{
    $dbLink = Ilch_Registry::get('dbLink');
    return (mysqli_insert_id($dbLink));
}

function db_count_query($query)
{
    return (db_result(db_query($query), 0));
}

function db_list_tables($db)
{
    $sql = 'SHOW tables FROM '.$db;
    $qry = db_query($sql);
    $rows = array();
    while ($row = db_fetch_row($qry)) {
        $rows[] = $row[0];
    }
    return $rows;
}

function db_check_erg($erg)
{
    if ($erg == false OR @db_num_rows($erg) == 0) {
        exit('Es ist ein Fehler aufgetreten');
    }
}

/**
 * Generiert einen HTML Code für eine Paginationauswahl (Multipages)
 * 
 * @param integer $page Seite die derzeit angezeigt wird
 * @param string $where SQL Bedingung (mit WHERE Schlüsselwort) um Einschränkungen zu ermöglichen
 * @param integer $limit Anzahl Einträge pro Seite
 * @param string $link Link der aufgerufen wird und an den -pX angehangen wird, alternativ wird {page} ersetzt, falls -p nicht am Ende des Links stehen soll
 * @param string $table Datenbanktabelle ohne prefix, in der die Einträge gezählt werden
 * @param integer $anzahl Angabe der maximalen Anzahl, wenn angegeben wird keine Datenbankabfrage gemacht
 * @return string
 */
function db_make_sites($page, $where, $limit, $link, $table, $anzahl = null)
{
    $hvmax = 4; // hinten und vorne links nach page
    $maxpage = '';
    $MPL = '';
    if (is_null($anzahl)) {
        $resultID = db_query("SELECT COUNT(*) FROM `prefix_" . $table . "` " . $where);
        $total = (is_resource($resultID)) ? db_result($resultID, 0) : 0;
    } else {
        $total = $anzahl;
    }
    if ($limit < $total) {
        $maxpage = $total / $limit;
        if (is_double($maxpage)) {
            $maxpage = ceil($maxpage);
        }
        $ibegin = $page - $hvmax;
        $iende = $page + $hvmax;

        $vgl1 = $iende + $ibegin;
        $vgl2 = ($hvmax * 2) + 1;
        if ($vgl1 <= $vgl2) {
            $iende = $vgl2;
        }
        $vgl3 = $maxpage - ($vgl2 - 1);
        if ($vgl3 < $ibegin) {
            $ibegin = $vgl3;
        }

        if ($ibegin < 1) {
            $ibegin = 1;
        }
        if ($iende > $maxpage) {
            $iende = $maxpage;
        }
        $vMPL = '';
        if (strpos($link, '{page}') !== false) {
            $link = str_replace('{page}', '-p%u', $link);
        } else {
            $link .= '-p%u';
        }
        $linkTpl = '<a href="' . $link . '">%s</a>';
        if ($ibegin > 1) {
            $vMPL = sprintf($linkTpl, 1, '&laquo;');
        }
        $MPL = $vMPL . '[ ';
        for ($i = $ibegin; $i <= $iende; $i++) {
            if ($i == $page) {
                $MPL .= $i;
            } else {
                $MPL .= sprintf($linkTpl, $i, $i);
            }
            if ($i != $iende) {
                $MPL .= ' | ';
            }
        }
        $MPL .= ' ]';
        if ($iende < $maxpage) {
            $MPL .= sprintf($linkTpl, $maxpage, '&raquo;');
        }
    }
    return $MPL;
}

/**
 * Importiert ein sql File in die Datenbank
 *
 * @param string $filename Dateiname(+pfad) der SQL Datei
 * @param boolean $decodeUtf8 gibt an, ob ggf. utf8 Zeichen decodiert werden sollen
 */
function db_import_sql_file($filename, $decodeUtf8 = false)
{
    if (!file_exists($filename)) {
        throw new Exception($filename . ' wurde nicht gefunden.');
    }
    $sql_file = file_get_contents($filename);
    if ($decodeUtf8 && is_utf8($sql_file)) {
        $sql_file = utf8_decode($sql_file);
    }
    $sql_file = preg_replace("/(\015\012|\015|\012)/", "\n", $sql_file);
    $lines = explode("\n", $sql_file);
    //Kommentare und leere Zeilen entfernen
    foreach ($lines as $no => $line) {
        if (empty($line) || substr($line, 0, 3) == '-- ' || $line == '--') {
            unset($lines[$no]);
        }
    }
    $sql_statements = explode(";\n", implode("\n", $lines));
    foreach ($sql_statements as $sql_statement) {
        if (trim($sql_statement) != '') {
            db_query($sql_statement);
        }
    }
}

/**
 * Schlägt an falls der übergebene string utf8 kodierte zeichen enthält
 *
 * @return bool
 * @author annemarie
 * @link http://w3.org/International/questions/qa-forms-utf-8.html Quelle
 * */
function is_utf8($string)
{
    $var = preg_match('%^(?:
	      [\x09\x0A\x0D\x20-\x7E]            # ASCII
	    | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	    |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	    | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	    |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	    |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	    | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	    |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	)*$%xs', $string);
    if ($var) {
        #$string = preg_replace("/\x93{1}|\x84{1}/i", "\x22", $string);
        #$string = preg_replace("/\x96{1}/i", "-", $string);
        #$string = preg_replace("/\x82|\x94|\x91|\x94/i", "'", $string);
        #$string = preg_replace("/\x9F/i", "Ã", $string);
        #$string = preg_replace("/\x96/i", "¶", $string);
        #$string = preg_replace("/\x96/i", "¶", $string);
        #return iconv("ISO-8859-1", "UTF-8",  $string);
        #return utf8_decode($string);
        return true;
    }
    return false;
}