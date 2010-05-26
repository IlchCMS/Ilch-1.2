<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
// hilfsfunktionen
function get_links_array() {
    $ar = array();
    $handle = opendir('include/contents');
    while ($ver = readdir($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/' . $ver)) {
            $n = explode('.', $ver);
            $ar[ $n[ 0 ] ] = $ver;
        }
    }
    closedir($handle);
    $handle = opendir('include/contents/selfbp/selfp');
    while ($ver = readdir($handle)) {
        if ($ver == "." OR $ver == ".." OR is_dir('include/contents/selfbp/selfp/' . $ver)) {
            continue;
        }
        $n = explode('.', $ver);
        if (file_exists('include/contents/' . $ver) OR file_exists('include/contents/' . $n[ 0 ] . '.php')) {
            $n[ 0 ] = 'self-' . $n[ 0 ];
        }
        $ar[ $n[ 0 ] ] = 'self_' . $ver;
    }
    closedir($handle);
    asort($ar);
    return ($ar);
}
// funktionen fuer listen
function admin_allg_gfx($ak) {
    $gfx = '';
    $o = opendir('include/designs');
    while ($ver = readdir($o)) {
        if ($ver != "." AND $ver != ".." AND $ver != '.svn' AND is_dir('include/designs/' . $ver)) {
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
function admin_allg_smodul($ak) {
    $ordner = array();
    $handle = opendir('include/contents');
    while ($ver = readdir($handle)) {
        if ($ver == '.' OR $ver == '..' OR is_dir('include/contents/' . $ver)) {
            continue;
        }
        $lver = explode('.', $ver);
        $ordner[ ] = $lver[ 0 ];
    }
    $smodul = '';
    $ordner = get_links_array();
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
function admin_allg_wars_last_komms($ak) {
    $ar = array(0 => 'nein', - 1 => 'ab User', - 3 => 'ab Trial', - 4 => 'ab Member'
        );
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
function admin_allg_lang($ak) {
    $lang = '';
    $o = opendir('include/includes/lang');
    while ($ver = readdir($o)) {
        if ($ver != "." AND $ver != ".." AND $ver != '.svn' AND is_dir('include/includes/lang/' . $ver)) {
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
// Variablen schreiben
if (empty($_POST[ 'submit' ])) {
    $gfx = admin_allg_gfx($allgAr[ 'gfx' ]);
    $lang = admin_allg_lang($allgAr[ 'lang' ]);
    $smodul = admin_allg_smodul($allgAr[ 'smodul' ]);
    $wars_last_komms = admin_allg_wars_last_komms($allgAr[ 'wars_last_komms' ]);
}

?>