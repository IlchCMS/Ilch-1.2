<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$count_query_xyzXYZ = 0;

function db_connect () {
    if (defined('CONN')) {
        return;
    }
    define ('CONN', @mysql_pconnect(DBHOST, DBUSER, DBPASS));
    $db = @mysql_select_db(DBDATE, CONN);

    if (!CONN) {
        die('Verbindung nicht m&ouml;glich, bitte pr&uuml;fen Sie ihre mySQL Daten wie Passwort, Username und Host<br />');
    }
    if (!$db) {
        die ('Kann Datenbank "' . DBDATE . '" nicht benutzen : ' . mysql_error(CONN));
    }
}

function db_close () {
    mysql_close (CONN);
}

function db_check_error (&$r, $q) {
    if (!$r AND mysql_errno(CONN) != 0 AND function_exists('is_coadmin') AND is_coadmin()) {
        // var_export (debug_backtrace(), true)
        echo('<font color="#FF0000">MySQL Error:</font><br>' . mysql_errno(CONN) . ' : ' . mysql_error(CONN) . '<br>in Query:<br>' . $q . '<pre>' . debug_bt() . '</pre>');
    }
    return ($r);
}

function db_query ($q) {
    global $count_query_xyzXYZ;
    $count_query_xyzXYZ++;

    if (preg_match ("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
        $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i", "UPDATE `" . DBPREF . "\\1`\\2", $q);
    } elseif (preg_match ("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
        $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `" . DBPREF . "\\1`\\2", $q);
    } else {
        $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF . "\\1\\2", $q);
    }

    return (db_check_error(@mysql_query($q, CONN), $q));
}

function db_result ($erg, $zeile = 0, $spalte = 0) {
    return (mysql_result ($erg, $zeile, $spalte));
}

function db_fetch_assoc ($erg) {
    return (mysql_fetch_assoc($erg));
}

function db_fetch_row ($erg) {
    return (mysql_fetch_row($erg));
}

function db_fetch_object ($erg) {
    return (mysql_fetch_object($erg));
}

function db_num_rows ($erg) {
    return (mysql_num_rows ($erg));
}

function db_last_id () {
    return (mysql_insert_id (CONN));
}

function db_count_query ($query) {
    return (db_result(db_query($query), 0));
}

function db_list_tables ($db) {
    return (mysql_list_tables ($db, CONN));
}

function db_tablename ($db, $i) {
    return (mysql_tablename ($db, $i));
}

function db_check_erg ($erg) {
    if ($erg == false OR @db_num_rows($erg) == 0) {
        exit ('Es ist ein Fehler aufgetreten');
    }
}

function db_make_sites ($page , $where , $limit , $link , $table, $anzahl = null) {
    $hvmax = 4; // hinten und vorne links nach page
    $maxpage = '';
    if (empty ($MPL)) {
        $MPL = '';
    }
    if (is_null ($anzahl)) {
        $resultID = db_query ("SELECT COUNT(*) FROM prefix_" . $table . " " . $where);
        $total = db_result($resultID, 0);
    } else {
        $total = $anzahl;
    }
    if ($limit < $total) {
        $maxpage = $total / $limit;
        if (is_double($maxpage)) {
            $maxpage = ceil($maxpage);
        }
        $ibegin = $page - $hvmax;
        $iende = $page + $hvmax ;

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
        for($i = $ibegin; $i <= $iende; $i++) {
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