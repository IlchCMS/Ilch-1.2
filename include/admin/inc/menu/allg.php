<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

if (empty ($_POST['submit'])) {
	echo '<script type="text/javascript">'
		.'$(function() {'
		.'	$("#tabsnav").tabs();'
		.'	$("#tabsmain").tabs();'
		.'  $(this).removeClass();'
		.'});'
		.'</script>'
		.'<h1>Konfiguration</h1>'
		.'<div id="tabsnav">'
		.'  <ul>';
		
		// Kategorien-ID und -Name
		$katid = 0;
		$katname = '';
		
		// Abfrage für Menü und admin/allg.php starten
		$abf = 'SELECT `kat` FROM `prefix_config` ORDER BY `kat`,`pos`,`typ` ASC';
		$erg = db_query($abf);
		while ($row = db_fetch_assoc($erg)) {
			if ($katname != $row['kat']) {
				$katid++;
				$katname = $row['kat'];
				echo '<li><a href="#tabsmain-'.$katid.'"><span>'.$row['kat'].'</span></a></li>';
			}
		}
		
	echo '  </ul></div>';
}

?>

