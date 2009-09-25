<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$modul = $menu->get_complete ();
if ( $modul == 'admin' ) {
	$modulname = 'Willkommen';
}else{
	$modulname = @db_result( db_query( 'SELECT `name` FROM `prefix_modules` WHERE `url` = "'.$modul.'" LIMIT 0, 1' ), 0);
}

if( empty( $modulname ) ){
	$modul = explode( '-', $modul );
	$modulname = @db_result( db_query( 'SELECT `name` FROM `prefix_modules` WHERE `url` = "'.$modul[0].'" LIMIT 0, 1' ), 0);
	
	if( empty( $modulname ) ){
		$modulname = 'Ilch CMS 1.2';
	}

}

$exturl = str_replace( '-', '_', $menu->get_complete() );
$expurl = explode( '_', $exturl );
			
if (file_exists('include/images/icons/admin/' . $exturl . '.png')) {
	$bild = 'include/images/icons/admin/' . $exturl . '.png';
}else if (file_exists('include/images/icons/admin/' . $expurl[0] . '.png')) {
	$bild = 'include/images/icons/admin/' . $expurl[0] . '.png';
} else {
	$bild = 'include/images/icons/admin/na.png';
}

echo '<img src="'.$bild.'" alt="'.$modulname.'"> '.$modulname;
?>
