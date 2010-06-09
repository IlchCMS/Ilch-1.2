<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
$design = new design('Ilch Admin-Control-Panel :: ModRewrite', '', 2);
$tpl = new tpl('modrewrite', 1);
$design->header();

# Ein paar Variablen definieren

$filestr				= '';
$mr['parse_htaccess'] 	= '';
$mr['check_0']			= '';
$mr['check_1']			= '';
$mr['infotxt'] 			= '';
$mr['infotype']			= 'info';
$default_on_file 		= "RewriteEngine On\nRewriteRule ^(.*).html$ ./index.php?$1";
$default_off_file 		= "RewriteEngine Off\nRewriteRule ^(.*).html$ ./index.php?$1";

# Benötigte Funktionen überprüfen
if (!function_exists('fopen') or !function_exists('fwrite')) {
	$mr['infotype']		= 'warning';
	$mr['infotxt'] 		.= 'Der Server unsterst&uuml;tzt nicht die ben&ouml;tigte Funktion zum Anlegen der .htaccess <br />';
}
if (!function_exists('file_put_contents') or !function_exists('file_get_contents')) {
	$mr['infotype']		= 'warning';
	$mr['infotxt'] 		.= 'Der Server unsterst&uuml;tzt die ben&ouml;tigte Funktion zum editieren der .htaccess <br />';
}

if (file_exists('.htaccess') and !is_writeable('.htaccess')) {
	$mr['infotype']		= 'warning';
	$mr['infotxt'] 		.= 'Die Datei .htaccess ist nicht beschreibbar (chmod 0777)';
}
# Vorauswahl festlegen
if (!file_exists('.htaccess')) {
	
	$mr['check_0'] 					= 'checked="checked"';
	$_POST['aktivieremodrewrite'] 	= 0;
	$mr['infotxt'] 					.= 'Es ist noch keine .htaccess-Datei angelegt <br />';
} else {
	
	$file = file('.htaccess');
	$filekey = -1;
	
	if (function_exists('parse_ini_string')) {
		$mr['parse_htaccess'] = file_get_contents('.htaccess');
	} else {
		$mr['parse_htaccess'] = '';
	}
	
	foreach ($file as $line) {
		
		$filekey++;
		if (preg_grep('#^\s*RewriteEngine\s+(?i:On)\s*$#', $file)) { # is it on ?

			$mr['check_1'] 			= 'checked="checked"';
			$mr['infotxt']			= 'ReWrite On ( defined on line '.$filekey.' )<br />';
			break;
		}
		if (preg_grep('#^\s*RewriteEngine\s+(?i:Off)\s*$#', $file)) { # is it off ?

			$mr['check_0'] 			= 'checked="checked"';
			$mr['infotxt']			= 'ReWrite Off ( defined on line '.$filekey.' )<br />';
			break;
		}
	}
}

# kk
if (isset($_POST['submitmodrewrite'])) {
	
	if ($_POST['aktivieremodrewrite'] == 'on') {
		# aktiviere
		if (!file_exists('.htaccess') && is_writable('.')) {
			
			$onhandle = @fopen('.htaccess',"w+b");
			if(@fwrite($onhandle, $default_on_file)){ 
				$mr['infotxt'] = 'ModRewrite erfolgreich aktiviert <br />'; 
			} else { 
				$mr['infotype']= 'warning';
				$mr['infotxt'] = 'ModRewrite anlegen fehlgeschlagen<br />'; 
			}
			
			
		} else {
			$file[$filekey] = 'RewriteEngine On';
			$onhandle = @fopen('.htaccess',"w+b");
			
			$filestr = implode("\n", $file);
			
			if(fwrite($onhandle, $filestr)){ 
				$mr['infotxt'] = 'ModRewrite erfolgreich aktiviert <br />'; 
			} else { 
				$mr['infotype']= 'warning';
				$mr['infotxt'] = 'ModRewrite anlegen fehlgeschlagen<br />'; 
			}
		}
		@fclose($onhandle);
		
		db_query("UPDATE `prefix_config` SET `wert` =  '1' WHERE `schl` = 'modrewrite'");
	
	} else
	if ($_POST['aktivieremodrewrite'] == 'off') {
		# deaktiviere
		if (!file_exists('.htaccess') && is_writable('.')) {
			
			$offhandle = @fopen('.htaccess',"w+b");
			
			if(@fwrite($offhandle, $default_off_file)){ 
				$mr['infotxt'] = '.htaccess-Datei erfolgreich angelegt <br />'; 
			} else { 
				$mr['infotype']= 'warning';
				$mr['infotxt'] = 'ModRewrite anlegen fehlgeschlagen<br />'; 
			}
		} else {
			
			$file[$filekey] = 'RewriteEngine Off';
			$offhandle = @fopen('.htaccess',"w+b");
			
			$filestr = implode("\n", $file);	
		
			if(@fwrite($offhandle, $filestr)){ 
				$mr['infotxt'] = 'ModRewrite erfolgreich deaktiviert <br />'; 
			} else { 
				$mr['infotype']= 'warning';
				$mr['infotxt'] = 'ModRewrite anlegen fehlgeschlagen<br />'; 
			}
		}
		fclose($offhandle);
		
		db_query("UPDATE `prefix_config` SET `wert` =  '0' WHERE `schl` = 'modrewrite'");
		
	}
	wd('admin.php?modrewrite', $mr['infotxt'], 2);
	$design->footer(1);
}
# contentbox "putten"
if (isset($_POST['submithtaccesscontentbox'])) {
	$newcontent = escape($_POST['htaccesscontentbox'], 'textarea');
	if (file_put_contents('.htaccess', $newcontent)) {
		
		$mr['infotxt'] = '.htaccess erfolgreich gespeichert';
		wd('admin.php?modrewrite', $mr['infotxt'], 2);
		$design->footer(1);
	} else {
		$mr['infotype'] = 'warning';
		$mr['infotxt'] = 'oOops... da lief was schief';
		wd('admin.php?modrewrite', $mr['infotxt'], 2);
		$design->footer(1);
	}
}
$tpl->set_ar($mr, 0);
$tpl->out(0);
$design->footer();
?>

