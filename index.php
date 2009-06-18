<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

if(file_exists('install.php') || file_exists('install.sql')) die('Installationsdateien noch vorhanden! Bitte erst l&ouml;schen!');

define ( 'main' , TRUE );

//Konfiguration zur Anzeige von Fehlern
//Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet
@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors','On');

session_name  ('sid');
session_start ();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

db_connect();
$allgAr = getAllgAr ();
$menu = new menu();
user_identification();
site_statistic();

require_once ('include/contents/'.$menu->get_url());

db_close();
debug('anzahl sql querys: '.$count_query_xyzXYZ);
debug('',1,false);


?>