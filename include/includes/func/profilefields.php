<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

function profilefields_functions2 () {
    $ar = array (1 => 'Feld',
        2 => 'Kategorie'
        );
    return ($ar);
}

function profilefields_functions () {
    $ar = array (1 => 'Feld',
        2 => 'Kategorie',
        3 => 'Angezeigt',
        4 => 'Versteckt'
        );
    return ($ar);
}
// Felder zum aendern anzeigen.
function profilefields_change ($uid) {
    $q = db_query("SELECT `id`, `show`, `val` FROM `prefix_profilefields` LEFT JOIN `prefix_userfields` ON `prefix_userfields`.`fid` = `prefix_profilefields`.`id` AND `prefix_userfields`.`uid` = " . $uid . " WHERE `func` = 1 ORDER BY `pos`");
    while ($r = db_fetch_assoc($q)) {
        echo '<label style="float:left; width:35%;">' . $r['show'] . '</label><input type="text" name="profilefields[' . $r['id'] . ']" value="' . $r['val'] . '"><br />';
    }
}
// Felder die uebermittelt wurden speichern.
function profilefields_change_save ($uid) {
    $q = db_query("SELECT `id`, `show`, `val` FROM `prefix_profilefields` LEFT JOIN `prefix_userfields` ON `prefix_userfields`.`fid` = `prefix_profilefields`.`id` AND `prefix_userfields`.`uid` = " . $uid . " WHERE `func` = 1 ORDER BY `pos`");
    while ($r = db_fetch_assoc($q)) {
        if (isset($_REQUEST['profilefields'][$r['id']])) {
            $v = $_REQUEST['profilefields'][$r['id']];
        } else {
            $v = '';
        }
        if ($r['val'] == '' AND $v != '') {
            db_query("INSERT INTO `prefix_userfields` (`fid`,`uid`,`val`) VALUES (" . $r['id'] . "," . $uid . ",'" . $v . "')");
        } elseif ($r['val'] != '' AND $v == '') {
            db_query("DELETE FROM `prefix_userfields` WHERE `fid` = " . $r['id'] . " AND `uid` = " . $uid);
        } elseif ($r['val'] != '' AND $v != '' AND $r['val'] != $v) {
            db_query("UPDATE `prefix_userfields` SET `val` = '" . $v . "' WHERE `fid` = " . $r['id'] . " AND `uid` = " . $uid);
        }
    }
}
// Diese Funktion Zeit ALLE Felder die der Benutzer im Adminbereich unter
// Profilefields sortieren kann an ... is eigentlich total easy ;-)...
function profilefields_show ($uid) {
    $l = '';
    $a = array ();
    $q = db_query("SHOW COLUMNS FROM `prefix_user`");
    while ($r = db_fetch_assoc($q)) {
        $a[$r['Field']] = $r['Field'];
    }

    $q = db_query("SELECT `id`, `show`, `func` FROM `prefix_profilefields` WHERE `func` < 4 ORDER BY `pos`");
    while ($r = db_fetch_assoc($q)) {
        if ($r['func'] == 1) {
            $str = @db_result (db_query ("SELECT `val` FROM `prefix_userfields` WHERE `uid` = " . $uid . " AND `fid` = " . $r['id']) , 0);
            $l .= '<tr><td class="Cmite">' . $r['show'] . '</td><td class="Cnorm">' . $str . '</td></tr>';
        } elseif ($r['func'] == 2) {
            $l .= '<tr><td class="Cdark" colspan="2"><b>' . $r['show'] . '</b></td></tr>';
        } elseif ($r['func'] == 3) {
            $str = '';
            if (isset($a[$r['show']])) {
                $str = @db_result (db_query ("SELECT `" . $r['show'] . "` FROM `prefix_user` WHERE `id` = " . $uid) , 0);
            }
            if (function_exists ('profilefields_show_spez_' . $r['show'])) {
                $l .= call_user_func ('profilefields_show_spez_' . $r['show'], $str, $uid);
            } elseif ($r['show'] != 'opt_pm_popup') {
                $l .= '<tr><td class="Cmite">' . ucfirst($r['show']) . '</td><td class="Cnorm">' . $str . '</td></tr>';
            }
        }
    }
    return ($l);
}
// hier kommen die speziellen funktionen hin...
// #
// ##
function profilefields_show_spez_geschlecht ($value, $uid) {
    global $lang;
    $ar = array (0 => $lang['itdoesntmatter'], 1 => $lang['male'], 2 => $lang['female']);
    return (profilefields_show_echo_standart ($lang['sex'], $ar[$value]));
}
function profilefields_show_spez_status ($value, $uid) {
    global $lang;
    return (profilefields_show_echo_standart ($lang['status'], ($value?'aktiv':'inaktiv')));
}
function profilefields_show_spez_usergallery ($value, $uid) {
    global $allgAr, $lang;
    if ($allgAr['forum_usergallery'] == 1) {
        return (profilefields_show_echo_standart ('Usergallery', '<a href="index.php?user-usergallery-' . $uid . '">ansehen</a>'));
    }
}
function profilefields_show_spez_homepage ($value, $uid) {
    global $lang;
    return (profilefields_show_echo_standart ($lang['homepage'], (empty($value)?'':'<a href="' . $value . '" target="_blank">' . $value . '</a>')));
}
function profilefields_show_spez_opt_mail ($value, $uid) {
    global $lang;
    return (profilefields_show_echo_standart ($lang['mail'], ($value?'<a href="index.php?user-mail-' . $uid . '">' . $lang['send'] . '</a>':'')));
}
function profilefields_show_spez_opt_pm ($value, $uid) {
    global $lang;
    return (profilefields_show_echo_standart ($lang['privatemessages'], ($value?'<a href="index.php?forum-privmsg-new=0&amp;empfid=' . $uid . '">' . $lang['send'] . '</a>':'')));
}
function profilefields_show_spez_sig ($value, $uid) {
    global $lang;
    return (profilefields_show_echo_standart ($lang['signature'], bbcode($value)));
}
function profilefields_show_spez_staat ($value, $uid) {
    global $lang;
    return (profilefields_show_echo_standart ($lang['state'], ((!empty($value) AND file_exists('include/images/flags/' . $value))?'<img src="include/images/flags/' . $value . '" alt="' . $value . '" title="' . $value . '" />':'')));
}
// ##
// #
// help funcs
function get_nationality_array () {
    $ar = array();
    $o = opendir ('include/images/flags');
    while ($f = readdir ($o)) {
        if ($f != '.' AND $f != '..') {
            $ar[$f] = str_replace('.gif', '', $f);
        }
    }
    asort($ar);
    return ($ar);
}

function profilefields_show_echo_standart ($k, $v) {
    return ('<tr><td class="Cmite">' . $k . '</td><td class="Cnorm">' . $v . '</td></tr>');
}

?>