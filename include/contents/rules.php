<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$title  = $allgAr[ 'title' ] . ' :: Regeln';
$hmenu  = 'Regeln';
$design = new design( $title, $hmenu );
$design->header();
// -----------------------------------------------------------|
$erg = db_query( 'SELECT `zahl`,`titel`,`text` FROM `prefix_rules` ORDER BY `zahl`' );
while ( $row = db_fetch_row( $erg ) ) {
    echo '<table width="100%" border="0" cellpadding="5" cellspacing="1" class="border">';
    echo '<tr class="Cmite"><td><b>&sect;' . $row[ 0 ] . '. &nbsp; ' . $row[ 1 ] . '</b></td></tr>';
    echo '<tr class="Cnorm"><td>' . bbcode( $row[ 2 ] ) . '</td></tr>';
    echo '</table><br /><br />';
}

$design->footer();

?>
