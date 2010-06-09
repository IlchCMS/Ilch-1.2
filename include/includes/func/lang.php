<?php
// Copyright by Flomavali
// Support www.ilch.de
defined('main') or die('no direct access');
// Sprache die benutzt wird, wenn die Datei in der Usersprache nicht gefunden wird

// Globale Sprachdateien oeffnen
function load_global_lang() {
    global $lang, $allgAr;
    $file = 'include/includes/lang/' . $_SESSION[ 'authlang' ] . '/global.php';
    if (file_exists($file)) {
        include $file;
    } elseif (file_exists('include/includes/lang/' . $allgAr['lang'] . '/global.php')) {
        include 'include/includes/lang/' . $allgAr['lang'] . '/global.php';
    }
}
// Modulare Sprachdateien oeffnen
function load_modul_lang($content = 1) {
    global $menu, $allgAr, $lang;

    if ($content == 2) {
        $dir = 'admin';
    } else {
        $dir = 'contents';
    }

    $modul = $menu->get(0);
    if (empty($modul) AND $content == 1) {
        $modul = $allgAr[ 'smodul' ];
    } else if (empty($modul)) {
        $modul = 'admin';
    }

    $file = 'include/includes/lang/' . $_SESSION[ 'authlang' ] . '/' . $dir . '/' . $modul . '.php';
    if (file_exists($file)) {
        include $file;
    } elseif (file_exists('include/includes/lang/' . $allgAr['lang'] . '/' . $dir . '/' . $modul . '.php')) {
        include 'include/includes/lang/' . $allgAr['lang'] . '/' . $dir . '/' . $modul . '.php';
    }
}
// Modulare Sprachdatei für eine Box hinzufügen
function load_box_lang($boxname){
    global $lang, $allgAr;
    if (file_exists('include/includes/lang/' . $_SESSION[ 'authlang' ] . '/boxes/' . $boxname)) {
        include 'include/includes/lang/' . $_SESSION[ 'authlang' ] . '/boxes/' . $boxname;
    } elseif ((file_exists('include/includes/lang/' . $allgAr['lang'] . '/boxes/' . $boxname))) {
        include 'include/includes/lang/' . $allgAr['lang'] . '/boxes/' . $boxname;
    }
}
// Variablen setzen
$lang = array();
?>