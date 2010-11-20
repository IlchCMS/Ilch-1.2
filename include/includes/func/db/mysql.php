<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

/**
 *
 * @todo bei UPDATE/INSERT/DELETE true oder false zurückgeben um einen fehler im script sachgemäß dem user präsentieren zu können
 */

$count_query_xyzXYZ = 0;

function db_connect() {
    if (defined('CONN')) {
        return;
    }
    define('CONN', @mysql_pconnect(DBHOST, DBUSER, DBPASS));
    $db = @mysql_select_db(DBDATE, CONN);

    if (!CONN) {
        die('Verbindung nicht m&ouml;glich, bitte pr&uuml;fen Sie ihre mySQL Daten wie Passwort, Username und Host<br />');
    }
    if (!$db) {
        die('Kann Datenbank "' . DBDATE . '" nicht benutzen : ' . mysql_error(CONN));
    }
}

function db_close() {
    mysql_close(CONN);
}

if (defined('DEBUG') and DEBUG) {
    $ILCH_DEBUG_DB_QUERIES = array();
    $ILCH_DEBUG_DB_COUNT_QUERIES = 0;

    function db_check_error($r) {
        if (!$r AND mysql_errno(CONN) != 0) {
            // var_export (debug_backtrace(), true)
            return '<font color="#FF0000">MySQL Error:</font><br/>' . mysql_errno(CONN) . ' : ' . mysql_error(CONN);
        }
        return '';
    }

    /*
      TODO funktion flexibel machen und nicht nur in die decode falle laufen lassen, für den umstieg auf eine utf8 datenbank oder um schreiben in utf8 zu erzwingen
    */
    function is_utf8($string) {
        
        // From http://w3.org/International/questions/qa-forms-utf-8.html
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
                return utf8_decode($string);
            }
            return $string;
    } 

    function db_query ($q) {
        global $ILCH_DEBUG_DB_COUNT_QUERIES, $ILCH_DEBUG_DB_QUERIES;
        $ILCH_DEBUG_DB_COUNT_QUERIES++;

        // Hilfsmodus zum einspielen von utf8 Installationsdatensätzen in die veraltete datenbank *yawn*
        if (defined('INSTALL_COMPLIANCE_MODE')) {
          $q = is_utf8($q);
        }
        
        if (preg_match ("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
            $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i","UPDATE `".DBPREF."\\1`\\2", $q);
        } elseif (preg_match ("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
            $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `".DBPREF."\\1`\\2", $q);
        } else {
            $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF."\\1\\2", $q);
            $q = is_utf8($q);
        }
        
        if (!function_exists('debug_bt')) {
          function debug_bt(){
            return debug_backtrace();
          }
        }
        
        $tmp = array();
        $vor = microtime(true);
        $qry = @mysql_query($q, CONN);
        $nach = microtime(true);
        $res = is_resource($qry);
        $tmp['is_valid_result_resource'] = $res;
        $tmp['duration'] = $nach - $vor;
        $tmp['time'] = $nach - SCRIPT_START_TIME;
        $tmp['query'] = $q;
        $tmp['affected_rows'] = mysql_affected_rows(CONN);
        $tmp['result_index'] = (int)$qry;
        $tmp['call'] = debug_bt();
        $error = db_check_error($qry);
        if (!empty($error)) {
            $tmp['error'] = $error;
        }
        
        $ILCH_DEBUG_DB_QUERIES[] = $tmp;
        return ($qry);
    }
} else {
    function db_check_error(&$r, $q) {
        if (!$r AND mysql_errno(CONN) != 0 AND function_exists('is_coadmin') AND is_coadmin()) {
            // var_export (debug_backtrace(), true)
            echo ('<font color="#FF0000">MySQL Error:</font><br/>' . mysql_errno(CONN) . ' : ' . mysql_error(CONN) . '<br/>in Query:<br/>' . $q . '<pre>' . debug_bt() . '</pre>');
        }
        return ($r);
    }

    function db_query($q) {
        if (preg_match("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
            $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i", "UPDATE `" . DBPREF . "\\1`\\2", $q);
        } elseif (preg_match("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
            $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `" . DBPREF . "\\1`\\2", $q);
        } else {
            $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF . "\\1\\2", $q);
        }

        return (db_check_error(@mysql_query($q, CONN), $q));
    }
}

function db_result($erg, $zeile = 0, $spalte = 0) {
    return (mysql_result($erg, $zeile, $spalte));
}

function db_fetch_assoc($erg) {
    return (mysql_fetch_assoc($erg));
}

function db_fetch_row($erg) {
    return (mysql_fetch_row($erg));
}

function db_fetch_object($erg) {
    return (mysql_fetch_object($erg));
}

function db_num_rows($erg) {
    return (mysql_num_rows($erg));
}

function db_last_id() {
    return (mysql_insert_id(CONN));
}

function db_count_query($query) {
    return (db_result(db_query($query), 0));
}

function db_list_tables($db) {
    return (mysql_list_tables($db, CONN));
}

function db_tablename($db, $i) {
    return (mysql_tablename($db, $i));
}

function db_check_erg($erg) {
    if ($erg == false OR @db_num_rows($erg) == 0) {
        exit('Es ist ein Fehler aufgetreten');
    }
}

function db_make_sites($page, $where, $limit, $link, $table, $anzahl = null) {
    $hvmax = 4; // hinten und vorne links nach page
    $maxpage = '';
    if (empty($MPL)) {
        $MPL = '';
    }
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
        if ($ibegin > 1) {
            $vMPL = '<a href="' . $link . '-p1">&laquo;</a> ';
        }
        $MPL = $vMPL . '[ ';
        for ($i = $ibegin; $i <= $iende; $i++) {
            if ($i == $page) {
                $MPL .= $i;
            } else {
                $MPL .= '<a href="' . $link . '-p' . $i . '">' . $i . '</a>';
            }
            if ($i != $iende) {
                $MPL .= ' | ';
            }
        }
        $MPL .= ' ]';
        if ($iende < $maxpage) {
            $MPL .= ' <a href="' . $link . '-p' . $maxpage . '">&raquo;</a>';
        }
    }
    return $MPL;
}

?>