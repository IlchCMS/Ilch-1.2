<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
// liest die <!--@..=..@--> in den ersten 1024 Zeichen in ein Array aus
function get_properties($t) {
    preg_match_all("|(?:<!--@(?P<name>[^=]*)=(?P<value>.*)@-->)|U", $t, $out, PREG_SET_ORDER);

    $properties = array();
    foreach ($out as $x) {
        $properties[ $x[ 'name' ] ] = htmlspecialchars($x[ 'value' ]);
    }
    unset($out);
    return $properties;
}
// setzt die Eigenschaften zu einem String zusammen
function set_properties($ar) {
    $l = '';
    foreach ($ar as $k => $v) {
        $l .= '<!--@' . $k . '=' . $v . '@-->';
    }
    return ($l);
}

function rteSafe($strText) {
    // returns safe code for preloading in the RTE
    $tmpString = $strText;
    // convert all types of single quotes
    $tmpString = str_replace(chr(145), chr(39), $tmpString);
    $tmpString = str_replace(chr(146), chr(39), $tmpString);
    $tmpString = str_replace("'", "&#39;", $tmpString);
    // convert all types of double quotes
    $tmpString = str_replace(chr(147), chr(34), $tmpString);
    $tmpString = str_replace(chr(148), chr(34), $tmpString);
    $tmpString = str_replace("\\\"", "\"", $tmpString);
    // replace carriage returns & line feeds
    $tmpString = str_replace(chr(10), " ", $tmpString);
    $tmpString = str_replace(chr(13), " ", $tmpString);

    return $tmpString;
}
// gibt die  options fuer die Dateiauswahl zurueck
function get_akl($ak) {
    $ar_l = array();

    if (is_writeable('include/contents/selfbp/selfp')) {
        $ar_l[ 'pneu.php' ] = 'Neue Seite';
        $o = opendir('include/contents/selfbp/selfp');
        while ($v = readdir($o)) {
            if (substr($v, - 4) != '.php') {
                continue;
            }
            $ar_l[ 'p' . $v ] = $v;
        }
        closedir($o);
    }
    if (is_writeable('include/contents/selfbp/selfb')) {
        $ar_l[ 'bneu.php' ] = 'Neue Box';
        $o = opendir('include/contents/selfbp/selfb');
        while ($v = readdir($o)) {
            if (substr($v, - 4) != '.php') {
                continue;
            }
            $ar_l[ 'b' . $v ] = $v;
        }
        closedir($o);
    }

    $l = '';
    foreach ($ar_l as $k => $v) {
        if ($k == $ak) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $l .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
    }
    return ($l);
}

function get_view($select = "normal") {
    $ar = array(
        'normal' => 'Normal',
        'fullscreen' => 'Vollbild',
        'popup' => 'Neues Fenster'
        );
    $l = '';
    foreach ($ar as $k => $v) {
        if ($k == $select) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $l .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
    }
    return ($l);
}

function get_filename($akl) {
    $n = basename(substr($akl, 1));
    if ($n == 'neu.php' or !file_exists('include/contents/selfbp/self' . substr($akl, 0, 1) . '/' . $n)) {
        return '';
    }
    return (basename($n));
}
// loescht Sonderzeichen aus dem Dateinamen
function get_nametosave($n) {
    $n = preg_replace("/[^a-zA-Z0-9\.]/", "", $n);
    if (substr($n, - 4) != ".php") {
        $n .= '.php';
    }
    return ($n);
}
// gibt den inhalt der ausgewaehlten Datei als String zurueck
function get_text($akl) {
    $f = substr($akl, 0, 1);
    $n = basename(substr($akl, 1));
    if (($f == 'b' OR $f == 'p') AND file_exists('include/contents/selfbp/self' . $f . '/' . $n)) {
        $t = implode("", file('include/contents/selfbp/self' . $f . '/' . $n));
        return ($t);
    }

    return ('');
}
// fuegt defined('main') hinzu, oder entfernt es
function edit_text($t, $add) {
    $erg = preg_match("/^\s*<\?php defined \('main'\) or die \('no direct access'\); \?>/s", $t);
    if (!$erg AND $add) {
        $t = trim($t);
        $t = '<?php defined (\'main\') or die (\'no direct access\'); ?>' . $t;
        // $t = preg_replace("/\/([^>]*)>/","/\\1>\n",$t);
    } elseif ($erg AND !$add) {
        $t = preg_replace("/^\s*<\?php defined \('main'\) or die \('no direct access'\); \?>(.*)$/s", "\\1", $t);
        $t = preg_replace("/<!--@(.*)@-->/", "", $t);
        // $t = preg_replace ("/(\015\012|\015|\012)/", "", $t);
    }
    return ($t);
}
// speichert die datei
function save_file_to($filename, $data, $flags = 0, $f = false) {
    if (($f === false) && (($flags % 2) == 1))
        $f = fopen($filename, 'a');
    else if ($f === false)
        $f = fopen($filename, 'w');
    if (round($flags / 2) == 1)
        while (!flock($f, LOCK_EX)) {
        /* lock */
    }
    if (is_array($data))
        $data = implode('', $data);
    fwrite($f, $data);
    if (round($flags / 2) == 1)
        flock($f, LOCK_UN);
    fclose($f);
}

function gallery_admin_showcats($id, $stufe) {
    $q = "SELECT * FROM prefix_gallery_cats WHERE cat = " . $id . " ORDER BY pos";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            echo '<tr class="Cmite"><td>' . $stufe . '- <a href="admin.php?selfbp-imagebrowser-' . $row->id . '">' . $row->name . '</a></td></tr>';
            gallery_admin_showcats($row->id, $stufe . ' &nbsp;');
        }
    }
}

?>