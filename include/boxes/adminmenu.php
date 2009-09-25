<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

// Funktionen
// Menue im Template ausgeben
function make_menu ($erg, $tpl, $katname = '') {
	
	while ($row = db_fetch_assoc($erg)) {
		if( $katname != $row['menu'] ){
			if( $katname != '' ){
				$tpl->out( 3 );
			}
			$tpl->set_ar_out( Array( 'kat' => $row['menu'], 'url' => $row['url'] ), 1 );
			$katname = $row['menu'];
		}
		
		$exturl = str_replace( '-', '_', $row['url'] );
		$expurl = explode( '_', $exturl );
			
		if (file_exists('include/images/icons/admin/' . $exturl . '.png')) {
			$bild = 'include/images/icons/admin/' . $exturl . '.png';
		}else if (file_exists('include/images/icons/admin/' . $expurl[0] . '.png')) {
			$bild = 'include/images/icons/admin/' . $expurl[0] . '.png';
		} else {
			$bild = 'include/images/icons/admin/na.png';
		}
			
		$tpl->set_ar_out( Array( 'url' => $row['url'], 'pic' => $bild, 'name' => $row['name'] ), 2 );
	}
	
	if( $katname != '' ){
		$tpl->out( 3 );
	}
}

// Template laden
$tpl = new tpl ('adminmenu', 1);
		
// Template-Header
$tpl->out( 0 );

if (is_coadmin()) { 

		// Module abfragen und Ausgeben
		$first_erg = db_query("SELECT * FROM `prefix_modules` WHERE `menu` = 'admin' ORDER BY  `pos` ASC");
		$second_erg = db_query("SELECT * FROM `prefix_modules` WHERE `menu` != '' AND `menu` != 'admin' ORDER BY `menu`, `pos` ASC");
		
		// Admin gesondert ausgeben
		$tpl->set_ar_out( Array( 'kat' => 'Admin', 'url' => 'admin' ), 1 );
		make_menu ($first_erg, $tpl, 'Admin');
		
		// Restliche Module
		make_menu ($second_erg, $tpl);
		
} elseif (count($_SESSION['authmod']) > 0) {
    echo "[null, 'Module', null, null, null,";
    $q = "SELECT DISTINCT `url`, `name`
	FROM `prefix_modulerights` `a`
	LEFT JOIN `prefix_modules` `b` ON `b`.`id` = `a`.`mid`
	WHERE `b`.`gshow` = 1 AND `uid` = " . $_SESSION['authid'];
    $erg = db_query($q);
    while ($row = db_fetch_assoc($erg)) {
        echo '[null, \'' . $row['name'] . '\', \'admin.php?' . $row['url'] . '\', null, null],' . "\n";
    }
    echo "],";
}

// Template-Footer
$tpl->out( 4 );

?>