<?php

// $messungStart = strtok(microtime(), " ") + strtok(" ");

//   Copyright by: Manuel
//   Support: www.ilch.de

//if(file_exists('install.php') || file_exists('install.sql')) die('Installationsdateien noch vorhanden! Bitte erst l&ouml;schen!');

define( 'main', TRUE );

//Konfiguration zur Anzeige von Fehlern
//Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet
@error_reporting( E_ALL ^ E_NOTICE ^ E_DEPRECATED );
date_default_timezone_set( 'Europe/Berlin' );

@ini_set( 'display_errors', 'On' );

// prüfen ob die config.php existiert und lesbar ist
if (is_readable('include/includes/config.php')) {
	
	// Session starten
	session_name( 'sid' );
	session_start();
	
	// Datenbankverbindung aufbauen und Funktionen und Klassen laden
	require_once( 'include/includes/config.php' );
	require_once( 'include/includes/loader.php' );
	
	// Allgemeiner Konfig-Array
	$allgAr = getAllgAr();
	
	// Menu, Nutzerverwaltung und Seitenstatistik laden
	$menu = new menu();
	user_identification();
	site_statistic();
	
	// Sprachdateien oeffnen
	load_global_lang();
	load_modul_lang();
	
	/* ENTWICKLUNGSVERSION SQL UPDATES */
	require_once('update/update.php');
	
	// Modul oeffnen
	require_once( 'include/contents/' . $menu->get_url() );
	
	// Datenbank schließen
	db_close();
	if ( false ) { //debugging aktivieren
		debug( 'anzahl sql querys: ' . $count_query_xyzXYZ );
		debug( '', 1, true );
	}

} else { // wenn config.php nicht existiert, abbrechen
	die ('Bitte erst die install.php aufrufen und das IlchScript installieren');
}
// $messungEnde = strtok(microtime(), " ") + strtok(" ");
// echo "Dauer: ".number_format($messungEnde - $messungStart, 6)." Sekunden";

?>