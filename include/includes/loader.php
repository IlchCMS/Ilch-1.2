<?php 
#   Copyright by Manuel
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );


// Datenbankverbindung aufbauen
require_once('include/includes/func/db/mysql.php');
db_connect();

// Klassen laden
$sql = 'SELECT `file` FROM `prefix_loader` WHERE `task` = "class" ORDER BY `pos` ASC';
$erg = db_query ($sql);

while( $row = db_fetch_assoc ($erg) ){
	require_once('include/includes/class/'.$row['file']);
}

// Funktionen laden
$sql = 'SELECT `file` FROM `prefix_loader` WHERE `task` = "func" ORDER BY `pos` ASC';
$erg = db_query ($sql);

while( $row = db_fetch_assoc ($erg) ){
	require_once('include/includes/func/'.$row['file']);
}
?>