<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Admins Area', 'Admins Area', 2);
$design->header();

if (!is_admin()) {
    echo 'Dieser Bereich ist nicht fuer dich...';
    $design->footer();
    exit();
}
// hilfsfunktionen
function get_links_array () {
    $ar = array ();
    $handle = opendir('include/contents');
    while ($ver = readdir ($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/' . $ver)) {
            $n = explode('.', $ver);
            $ar[$n[0]] = $ver;
        }
    }
    closedir($handle);
    $handle = opendir('include/contents/selfbp/selfp');
    while ($ver = readdir ($handle)) {
        if ($ver == "." OR $ver == ".." OR is_dir('include/contents/selfbp/selfp/' . $ver)) {
            continue;
        }
        $n = explode('.', $ver);
        if (file_exists ('include/contents/' . $ver) OR file_exists ('include/contents/' . $n[0] . '.php')) {
            $n[0] = 'self-' . $n[0];
        }
        $ar[$n[0]] = 'self_' . $ver;
    }
    closedir($handle);
    asort ($ar);
    return ($ar);
}
// funktionen fuer listen
function admin_allg_gfx ($ak) {
    $gfx = '';
    $o = opendir('include/designs');
    while ($ver = readdir ($o)) {
        if ($ver != "." AND $ver != ".." AND is_dir('include/designs/' . $ver)) {
            if ($ver == $ak) {
                $sel = ' selected';
            } else {
                $sel = '';
            }
            $gfx .= '<option' . $sel . '>' . $ver . '</option>';
        }
    }
    closedir($o);
    return ($gfx);
}
function admin_allg_smodul ($ak) {
    $ordner = array();
    $handle = opendir('include/contents');
    while ($ver = readdir ($handle)) {
        if ($ver == '.' OR $ver == '..' OR is_dir ('include/contents/' . $ver)) {
            continue;
        }
        $lver = explode('.', $ver);
        $ordner[] = $lver[0];
    }
    $smodul = '';
    $ordner = get_links_array ();
    foreach ($ordner as $a => $x) {
        if ($a == $ak) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $smodul .= '<option' . $sel . ' value="' . $a . '">' . ucfirst($a) . '</option>';
    }
    return ($smodul);
}
function admin_allg_wars_last_komms ($ak) {
    $ar = array (0 => 'nein', - 1 => 'ab User', - 3 => 'ab Trial', - 4 => 'ab Member');
    $l = '';
    foreach ($ar as $k => $v) {
        if ($k == $ak) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $l .= '<option' . $sel . ' value="' . $k . '">' . $v . '</option>';
    }
    return ($l);
}
function admin_allg_lang ($ak) {
    $lang = '';
    $o = opendir('include/includes/lang');
    while ($ver = readdir ($o)) {
        if ($ver != "." AND $ver != ".." AND is_dir('include/includes/lang/' . $ver)) {
            if ($ver == $ak) {
                $sel = ' selected';
            } else {
                $sel = '';
            }
            $lang .= '<option' . $sel . '>' . $ver . '</option>';
        }
    }
    closedir($o);
    return ($lang);
}

if (empty ($_POST['submit'])) {
    $gfx = admin_allg_gfx($allgAr['gfx']);
	$lang = admin_allg_lang($allgAr['lang']);
    $smodul = admin_allg_smodul ($allgAr['smodul']);
    $wars_last_komms = admin_allg_wars_last_komms ($allgAr['wars_last_komms']);

    echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/konfiguration.png" /></td><td width="30"></td><td valign="bottom"><h1>Konfiguration</h1></td></tr></table>';

    echo '<form action="admin.php?allg" method="POST">';
    echo '<table cellpadding="3" cellspacing="1" class="border" border="0">';
    // echo '<tr class="Chead"><td colspan="2"><b>Konfiguration</b></td></tr>';
    $ch = '';

    $abf = 'SELECT * FROM `prefix_config` ORDER BY kat,pos,typ ASC';
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        if ($ch != $row['kat']) {
            echo '<tr><td colspan="2" class="Cdark"><b>' . $row['kat'] . '</b></td></tr>';
        }
        echo '<tr><td class="Cmite">' . $row['frage'] . '</td>';
        echo '<td class="Cnorm">';
        if ($row['typ'] == 'input') {
            echo '<input size="50" type="text" name="' . $row['schl'] . '" value="' . $row['wert'] . '">';
        } elseif ($row['typ'] == 'r2') {
            $checkedj = '';
            $checkedn = '';
            if ($allgAr[$row['schl']] == 1) {
                $checkedj = 'checked';
                $checkedn = '';
            } else {
                $checkedn = 'checked';
                $checkedj = '';
            }
            echo '<input type="radio" name="' . $row['schl'] . '" value="1" ' . $checkedj . ' > ja';
            echo '&nbsp;&nbsp;';
            echo '<input type="radio" name="' . $row['schl'] . '" value="0" ' . $checkedn . ' > nein';
        } elseif ($row['typ'] == 's') {
            $vname = $row['schl'];
            echo '<select name="' . $row['schl'] . '">' . $$vname . '</select>';
        } elseif ($row['typ'] == 'textarea') {
            echo '<textarea cols="55" rows="3" name="' . $row['schl'] . '">' . $row['wert'] . '</textarea>';
        } elseif ($row['typ'] == 'grecht') {
            $grl = dblistee($allgAr[$row['schl']], "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC");
            echo '<select name="' . $row['schl'] . '">' . $grl . '</select>';
        } elseif ($row['typ'] == 'grecht2') {
            $grl = dblistee($allgAr[$row['schl']], "SELECT id,name FROM prefix_grundrechte WHERE id >= -2 ORDER BY id ASC");
            echo '<select name="' . $row['schl'] . '">' . $grl . '</select>';
        } elseif ($row['typ'] == 'password') {
            echo '<input size="50" type="password" name="' . $row['schl'] . '" value="***" />';
        }
        echo '</td></tr>' . "\n\n";
        $ch = $row['kat'];
    }

    echo '<tr class="Cdark"><td></td><td><input type="submit" value="Absenden" name="submit"></td></tr>';

    echo '</table>';

    echo '</form>';
} else {
    $abf = 'SELECT * FROM `prefix_config` ORDER BY kat';
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        if ($row['typ'] == 'password' AND $_POST[$row['schl']] == '***') {
            continue;
        } elseif ($row['typ'] == 'password') {
            require_once('include/includes/class/AzDGCrypt.class.inc.php');
            $cr64 = new AzDGCrypt(DBDATE . DBUSER . DBPREF);
            $_POST[$row['schl']] = $cr64->crypt($_POST[$row['schl']]);
        }
        db_query('UPDATE `prefix_config` SET wert = "' . escape($_POST[$row['schl']], 'textarea') . '" WHERE schl = "' . $row['schl'] . '"');
    }
    wd ('admin.php?allg', 'Erfolgreich ge&auml;ndert' , 2);
}
// -----------------------------------------------------------|
$design->footer();

?>