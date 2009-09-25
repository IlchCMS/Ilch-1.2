<?php

#$messungStart = strtok(microtime(), " ") + strtok(" ");

#   Copyright by: Manuel
#   Support: www.ilch.de

#if(file_exists('install.php') || file_exists('install.sql')) die('Installationsdateien noch vorhanden! Bitte erst l&ouml;schen!');

define ( 'main' , TRUE );
define ( 'admin', TRUE );

//Konfiguration zur Anzeige von Fehlern
//Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet
@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
date_default_timezone_set('Europe/Berlin');

@ini_set('display_errors','On');

// Session starten
session_name  ('sid');
session_start ();

// Datenbankverbindung aufbauen und Funktionen und Klassen laden
require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

// Allgemeiner Konfig-Array
$allgAr = getAllgAr ();

// Menu, Nutzerverwaltung und Seitenstatistik laden
$menu = new menu();
user_identification();
site_statistic();

// Sprachdateien oeffnen
load_global_lang( 2 );
load_modul_lang( 2 );

// Modul oeffnen
if ( user_has_admin_right($menu) ) {
  require_once ('include/admin/'.$menu->get_url('admin'));
}

// Datenbank schließen
db_close();
if (false) { //debugging aktivieren
	debug('anzahl sql querys: '.$count_query_xyzXYZ);
	debug('', 1, true);
}

 #$messungEnde = strtok(microtime(), " ") + strtok(" ");
 #echo "Dauer: ".number_format($messungEnde - $messungStart, 6)." Sekunden";

?>