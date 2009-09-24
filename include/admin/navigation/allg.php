<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

if (!is_admin()) {
    echo 'Dieser Bereich ist nicht fuer dich...';
    $design->footer();
    exit();
}

if (empty ($_POST['submit'])) {
	echo '<script type="text/javascript">'
		.'$(function() {'
		.'	$("#tabsnav").tabs();'
		.'	$("#tabsmain").tabs();'
		.'});'
		.'</script>'
		.'<p><b>Konfiguration</b></p>'
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
				echo '<li><a href="#tabsmain-'.$katid.'">'.$row['kat'].'</a></li>';
			}
		}
		
	echo '  </ul></div>';
}

?>

