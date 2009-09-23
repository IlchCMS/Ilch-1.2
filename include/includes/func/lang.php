<?php
// Copyright by Flomavali
// Support www.ilch.de

defined ('main') or die ('no direct access');

// Globale Sprachdateien oeffnen
function load_global_lang(){
	global $lang;
	$dir = 'include/includes/lang/'.$_SESSION['authlang'].'/global';
    $open = opendir($dir);
    while ($file = readdir ($open)) {
		$file_info = pathinfo($file); 
        if ($file != "." AND $file != ".." AND !is_dir($dir.'/'.$file) AND $file_info["extension"] == 'php' ) {
            require_once ($dir.'/'.$file);
        }
    }
    closedir($open);
	return $lang;
}

// Modulare Sprachdateien oeffnen
function load_modul_lang( $content = 1 ){
	global $menu, $allgAr, $lang;
	
	if( $content == 2 ){
		$dir = 'admin';
	}else{
		$dir = 'contents';
	}
	
	$modul = $menu->get(0);
	if ( empty ($modul) AND $content == 1 ){
		$modul = $allgAr['smodul'];
	}else if( empty ($modul) ){
		$modul = 'admin';
	}
	
	$file = 'include/includes/lang/'.$_SESSION['authlang'].'/'.$dir.'/'.$modul.'.php';
	if (file_exists ($file) ) {
		require_once ($file);
	}
	
	return $lang;
}

// Variablen setzen
$lang = Array();
?>