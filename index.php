<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

#if(file_exists('install.php') || file_exists('install.sql')) die('Installationsdateien noch vorhanden! Bitte erst l&ouml;schen!');

define ( 'main' , TRUE );

//Konfiguration zur Anzeige von Fehlern
//Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet

//Seit php-5.3 ist eine Angabe der TimeZone Pflicht
if (version_compare(phpversion(), '5.3') != -1) {
	@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	date_default_timezone_set('Europe/Berlin');
} else {
	@error_reporting(E_ALL ^ E_NOTICE);
}
@ini_set('display_errors','On');

// Session starten
session_name  ('sid');
session_start ();

// datenbankverbindung aufbauen und Funktionen und Klassen laden
require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

// Allgemeiner Konfig-Array
$allgAr = getAllgAr ();

// Menu, Nutzerverwaltung und Seitenstatistik laden
$menu = new menu();
user_identification();
site_statistic();

// Sprachdateien oeffnen
load_global_lang();
load_modul_lang();

// Modul oeffnen
require_once ('include/contents/'.$menu->get_url());

// Datenbank schließen
db_close();
if (false) { //debugging aktivieren
	debug('anzahl sql querys: '.$count_query_xyzXYZ);
	debug('',1,true);
}


?>