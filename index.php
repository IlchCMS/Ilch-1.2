<?php
// Copyright by: Manuel
// Support: www.ilch.de
// if(file_exists('install.php') || file_exists('install.sql')) die('Installationsdateien noch vorhanden! Bitte erst l&ouml;schen!');
ob_start();
define('main', true);
define('DEBUG', true);
define('SCRIPT_START_TIME', microtime(true));
define('AJAXCALL', isset($_GET['ajax']) and $_GET['ajax'] == 'true');
// Konfiguration zur Anzeige von Fehlern
// Auf http://www.php.net/manual/de/function.error-reporting.php sind die verf�gbaren Modi aufgelistet
// Seit php-5.3 ist eine Angabe der TimeZone Pflicht
if (version_compare(phpversion(), '5.3') != - 1) {
    if (E_ALL > E_DEPRECATED) {
        @error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    } else {
        @error_reporting(E_ALL ^ E_NOTICE);
    }
    date_default_timezone_set('Europe/Berlin');
} else {
    @error_reporting(E_ALL ^ E_NOTICE);
}

@ini_set('display_errors', 'On');
// Session starten
session_name('sid');
session_start();
// Datenbankverbindung aufbauen und Funktionen und Klassen laden
require_once('include/includes/config.php');
require_once('include/includes/loader.php');
// Allgemeiner Konfig-Array
$allgAr = getAllgAr();
// Menu, Nutzerverwaltung und Seitenstatistik laden
$menu = new menu();
$m = $menu->get_complete();
user_identification($m);
// Sprachdateien oeffnen
load_global_lang();
load_modul_lang();
if (AJAXCALL and isset($_GET['boxreload']) and $_GET['boxreload'] == 'true') {
    ob_start();
    $file = $menu->get_url('box');
    if ($file !== false) {
        require $file;
    }
    $tmp = array('content'=> ob_get_clean());
    echo json_encode($tmp);
    db_close();
    exit;
}
site_statistic();
// Wartungsmodus
if ($allgAr['wartung'] == 1 and is_admin()) {
	@define('DEBUG', true);
	debug ('Wartungsmodus aktiv !');
} else
if ($allgAr['wartung'] == 1 and !is_admin()) {
	die ($allgAr['wartungstext']);
}

/* ENTWICKLUNGSVERSION SQL UPDATES */
require_once('update/update.php');
// Modul oeffnen
require_once('include/contents/' . $menu->get_url());

// Datenbank schließen
db_close();
debug_out();
?>