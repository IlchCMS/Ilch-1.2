<?php
ob_start();
/**
*
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
* @copyright (C) 2000-2010 ilch.de
* @version $Id$
*/
define('main', true);
define('admin', true);
define('DEBUG', true);
define('SCRIPT_START_TIME', microtime(true));
define('AJAXCALL', isset($_GET['ajax']) and $_GET['ajax'] == 'true');
// Konfiguration zur Anzeige von Fehlern
// Auf http://www.php.net/manual/de/function.error-reporting.php sind die verf�gbaren Modi aufgelistet
// Seit php-5.3 ist eine Angabe der TimeZone Pflicht
defined('E_DEPRECATED') or define('E_DEPRECATED', 0);
@error_reporting(E_ALL > E_DEPRECATED ? E_ALL : E_ALL ^ E_DEPRECATED);
date_default_timezone_set('Europe/Berlin');
@ini_set('display_errors', 'On');
// Session starten
session_name('sid');
session_start();
// Datenbankverbindung aufbauen und Funktionen und Klassen laden
require_once('include/includes/config.php');
require_once('include/includes/loader.php');
// Allgemeiner Konfig-Array
$allgAr = getAllgAr();

/* ENTWICKLUNGSVERSION SQL UPDATES */
require_once('update/update.php');
// Wartungsmodus
if ($allgAr['wartung'] == 1) {
    @define('DEBUG', true);
    debug ('Wartungsmodus aktiv !');
}
// Menu, Nutzerverwaltung und Seitenstatistik laden
$menu = new menu();
$m = $menu->get_complete() . ' (Adminbereich)';
user_identification($m);
site_statistic();
// Sprachdateien oeffnen
load_global_lang(2);
load_modul_lang(2);
// Navigation in Variable als Array speichern
$menuAr = $menu->get_menu();
// Modul oeffnen
if (user_has_admin_right($menu)) {
    require_once('include/admin/' . $menu->get_url('admin'));
}
// Datenbank schlie�en
db_close();
debug_out();