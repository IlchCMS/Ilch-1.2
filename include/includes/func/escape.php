<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

/**
 * Wrapper für stripslashes
 * @param string $var
 * @return string
 */
function unescape($var) {
    return stripslashes($var);
}

/**
 * Entfernt ungültige Zeichen aus einem Nutzernamen und kürzt ihn ggf. auf
 * die maximale Länge
 * @param string $nickname
 * @return string
 */
function escape_nickname($nickname) {
    return escape(
        substr(
            preg_replace("/[^a-zA-Z0-9-\[\]\*\ \+=\._\|]/", '', $nickname),
            0,
            15
        ),
        'string'
    );
}
/**
 * Entfernt ungültige Zeichen aus einer E-Mail-Adresse
 * @param string $email
 * @param boolean $leerzeichen
 * @return string
 */
function escape_for_email($email, $leerzeichen = false) {
    $regex = '/\015\012|\015|\012|\072|\074|\076'
        . ($leerzeichen ? '\040' : '') . '/';
    return preg_replace($regex, '', $email);
}

/**
 * Wrapper für htmlspecialchars, zur Bearbeitung
 * von HTML (oder Texten die Sonderzeichen enthalten dürfen)
 * innerhalb von inputs oder textareas
 * @param string $string
 * @return string
 */
function escape_for_fields($string) {
    return htmlspecialchars($string);
}

/**
 * E-Mail-Adresse zur Ausgabe vorbereiten
 * @param string $$email
 * @return string
 */
function escape_email_to_show($email) {
    $ret = "";
    $arr = unpack("C*", $email);
    foreach ($arr as $char) {
        $ret .= sprintf("%%%X", $char);
    }
    return $ret;
}

/**
 * escape()
 * Filtert Daten aus Usereingaben, um Fehler bei Datenbankabfragen zu verhindern
 *
 * @param mixed $var
 * @param string $type [optional] string|integer|textarea|checkbox|form (oder den ersten Buchstaben), string ist Standard
 * für form die Funktion escapeFormToArray anschauen
 * @return mixed escaped input $var oder false, wenn $var nicht gesetzt
 */
function escape ($var, $type = 's') {
    if (is_array($var)) { //Für Array escape für jeden Eintrag durchführen
        $newar = array();
        foreach ($var as $key => $val){
            $newar[$key] = escape($val, $type);
        }
        return $newar;
    }
    if (!isset($var) AND ($type != 'c' OR $type != 'checkbox')) {
        return false;
    }
    switch ($type) {
        case 'integer' : case 'i' :
            $var = intval ($var);
            break;
        case 'string' : case 's' :
            $var = (get_magic_quotes_gpc() ? stripslashes($var) : $var);
            $var = strip_tags ($var);
            $var = addslashes ($var);
            break;
        case 'textarea' : case 't' :
            $var = (get_magic_quotes_gpc() ? stripslashes($var) : $var);
            $var = addslashes ($var);
            break;
        case 'checkbox' : case 'c' :
            if (isset($var)) {
                $var = 1;
            } else {
                $var = 0;
            }
            break;
        case 'url': case 'u':
            if (!empty($var)) {
                $var = getRealURL($var);
            } else {
                $var = '';
            }
            break;
        case 'form': case 'f':
            return escapeFormToArray($var);
            break;
    }
    return ($var);
}

/**
 * Funktion um Formulardaten zu escapen und für das Eintragen in die Datenbank vorzubereiten
 *
 * @param array $ar Array mit den Formularfeldern und deren vorgesehen Inhalt in Form
 * array('integer_feld' => 'i', 'string_feld' => 's', 'textarea_feld' => 't', 'checkbox_feld' => 'c')
 * falls der Key ein integer ist, wird er nicht berücksichtig, value wird als string escaped
 * @return array Array mit den escapeten Felder aus dem Formular
 */
function escapeFormToArray($ar, $form = null) {
    $out = array();
    if (is_null($form)) {
        $form = &$_POST;
    }
    foreach ($ar as $k => $v) {
        if (is_int($k)) {
            $out[$v] = escape($form[$v]);
        } else {
            $out[$k] = escape($form[$k], $v);
        }
    }
    return $out;
}

/**
 * getClearArray()
 * erzeugt ein "leeres" Array für die Templateausgabe, für ein Eingabearray vom Typ für escapeFormToArray
 *
 * @param array $ar Array mit Schlüsselnamen für das neue "leere" Array
 * @return array
 */
function getClearArray($ar) {
    $out = array();
    foreach($ar as $k => $v) {
        if (is_int($k)) {
            $out[$v] = '';
        } else {
            $out[$k] = '';
        }
    }
    return $out;
}

/**
 * Funktion um ein Array in die Datenbank per INSERT oder UPDATE einzutragen
 *
 * @param string $table Tabellenname in der Datenbank, wohin eingetragen werden soll
 * @param array $ar Array mit Feldnamen und Inhalt, der in die Datenbank eingetragen werden soll
 *                           array('feldname' => 'inhalt',...)
 * @param string $where (optional) Updatebedingung, wenn angegeben, wird ein UPDATE mit dieser Bedingung durchgeführt
 *                           ansonsten wird ein INSERT durchgeführt
 * @param array $ar2 Array mit Feldnamen aus Eingabearray, die nicht mit in die Datenbank eingetragen werden sollen
 * @return bool Eintrag war erfolgreich
 */
function arrayToDb($table, $ar, $where = '', $ar2 = array()) {
    $mode = empty($where) ? 'INSERT INTO' : 'UPDATE';
    $fields = '';
    foreach ($ar as $k => $v) {
        if (in_array($k, $ar2)) {
            continue;
        }
        $fields .= ", `$k` = " . (is_null($v) ? 'NULL' : "'$v'");
    }
    $fields = substr($fields, 2);
    $query = "$mode $table SET $fields $where";
    return db_query($query);
}

/**
 * allRowsFromQuery()
 * Gibt ein Array mit allen Datensätzen einer Abfrage
 *
 * @param string $qry MySQL Query
 * @paran string $key [optional] Gibt die für die Arraykeys eine Spalte der Abfrage anzugeben
 * @return array Array mit allen Datensätzen der Query
 */
function allRowsFromQuery($qry, $key = false) {
    $sql = db_query($qry);
    $out = array();
    if ($sql) {
        if ($key === false) {
            while ($r = db_fetch_assoc($sql)) {
                $out[] = $r;
            }
        } else {
            while ($r = db_fetch_assoc($sql)) {
                $out[$r[$key]] = $r;
            }
        }

    }
    return $out;
}

/**
 * simpleArrayFromQuery()
 * Gibt Array mit den beiden ersten beiden Feldern des Ergebnisses als index und wert zurück
 * Wenn nur ein Feld angegeben ist, wird es als Wert benutzt
 *
 * @param string $qry SQL-Query
 * @return array array(feld1 => feld2, ...);
 */
function simpleArrayFromQuery($qry){
    $sql = db_query($qry);
    $out = array();
    if ($sql) {
        if (db_num_fields($sql) > 1) {
            while ($r = db_fetch_row($sql)) {
                $out[$r[0]] = $r[1];
            }
        } else {
            while ($r = db_fetch_row($sql)) {
                $out[] = $r[0];
            }
        }
    }
    return $out;
}

/**
 * validInt() prüft ob die Eingabe eine ganze Zahl ist und gibt diese als int zurück,
 * oder false falls es keine war
 *
 * @param string $string Eingabe
 * @return int |boolean
 */
function validInt($string) {
    if (is_int($string) || ctype_digit($string)) {
        return (int) $string;
    } else {
        return false;
    }
}

/**
 * validGermanDate()
 * prüft ein String auf ein deutsches Format und gibt das englische Datumsformat zurück oder false
 *
 * @param string $string deutsches Datum
 * @return string |boolean englisches Datum oder false
 */
function validGermanDate($string) {
    $string = trim($string);
    if (preg_match('%^\d{1,2}.\d{1,2}.(\d{2}|\d{4})$%', $string) == 1) {
        $d = explode('.', $string);
        if (strlen($d[2]) == 2) {
            if ($d[2] < 20) {
                $d[2] = '20' . $d[2];
            } else {
                $d[2] = '19' . $d[2];
            }
        }
        if (checkdate($d[1], $d[0], $d[2])) {
            if (strlen($d[0]) == 1) {
                $d[0] = '0' . $d[0];
            }
            if (strlen($d[1]) == 1) {
                $d[1] = '0' . $d[1];
            }
            return $d[2] . '-' . $d[1] . '-' . $d[0];
        } else {
            return false;
        }
    }
    return false;
}

/**
 * getGermanDate()
 * Wandelt englisches Datum in deutsches Datum um
 *
 * @param string $string englisches Datum
 * @return string |boolean deutsches Datum oder false
 */
function getGermanDate($string) {
    $string = trim($string);
    if (preg_match('%^\d{4}-\d{2}-\d{2}$%', $string) == 1) {
        $d = explode('-', $string);
        if (checkdate($d[1], $d[2], $d[0])) {
            return $d[2] . '.' . $d[1] . '.' . $d[0];
        }
    }
    return false;
}

/**
 * validGermanTime()
 *
 * @param string $string Deutsche Zeit im Format HH:MM oder H:MM
 * @return string|boolen Deutsche Zeit im Format HH:MM oder false
 */
function validGermanTime($string){
    $string = trim($string);
    if (preg_match('%^\d{1,2}:\d{2}$%', $string) == 1) {
        $d = explode(':', $string);
        if (strlen($d[0]) == 1) {
            $d[0] = '0'.$d[0];
        } elseif ($d[0] > 23) {
            return false;
        }
        if ($d[1] > 59) {
            return false;
        }
        return $d[0].':'.$d[1];
    }
    return false;
}

/**
 * getRealURL()
 * Schreibt ggf, noch http:// vor die Adresse
 *
 * @param string $url Internetadresse
 * @return string Internetadresse mit http:// am Anfang
 */
function getRealURL($url) {
    if (preg_match('%^(http|ftp|https)://%', $url) == 0) {
        return 'http://' . $url;
    } else {
        return $url;
    }
}
