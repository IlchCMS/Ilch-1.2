<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$modul = $menu->get( 0 );
if (empty ($modul) ) {
	$modul = 'admin';
}

// Script version
$out['ilchversion'] = 1.2;
$out['ilchupdate'] = 'A';
$out['phpversion'] = phpversion();
$out['mysqlversion'] = mysql_get_server_info();

// Untermenu
$umenu = '';

// Template laden
$tpl = new tpl ('adminsubmenu', 1);

// PHP-Erweiterung laden
$file = 'include/admin/navigation/'.$modul.'.php';
if (file_exists ($file) ) {
	require_once ($file);
}

// XML Prasen
$file = 'include/admin/navigation/'.$modul.'.xml';
if (file_exists ($file) ) {
	$menus = simplexml_load_file('include/admin/navigation/'.$modul.'.xml');
	
	$umenu .= '<p><b>Weitere Funktionen</b></p>';
	$umenu .= '<ul style="padding-left: 15px;">';

	foreach( $menus->list AS $liste ){
		$umenu .= '<li><b>'.$liste->attributes()->title.'</b><br />';
		foreach( $liste->modul AS $mod ){
			$umenu .= '<a href="'.$mod->url.'" style="color: #FFFFFF">'.utf8_decode($mod->title).'</a><br />';
		}
		$umenu .= '</li><br />';
	}

	$umenu .= '</ul>';
}

$tpl->set( 'umenu', $umenu );
$tpl->set_ar_out( $out, 0 );

?>
