<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$title  = $allgAr[ 'title' ] . ' :: Impressum';
$hmenu  = 'Impressum';
$design = new design( $title, $hmenu );
$design->header();

$erg = db_query( "SELECT * FROM `prefix_allg` WHERE `k` = 'impressum' LIMIT 1" );
$row = db_fetch_assoc( $erg );

echo $row[ 'v1' ]; // eigentuemer oder sowas
echo '<br /><br />';
echo $row[ 'v2' ]; // voller name
echo '<br />';
echo $row[ 'v3' ]; // strasse nr
echo '<br /><br />';
echo $row[ 'v4' ]; // plz, ort
echo '<br ><br />';
echo 'Kontakt: <a href="index.php?contact">Formular</a><br /><br />';
echo unescape( $row[ 't1' ] );

$design->footer();

?>