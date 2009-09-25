<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Admins Area', 'Admins Area', 2);
$design->header();

// Funktionen
//// Template setzen lassen (TRUE|FALSE) bei Radiobutton
function select_radio( $radio, $value ){
	global $tpl;
	if( $value == 1 ){
		$tpl->set( $radio.'_0', '' );
		$tpl->set( $radio.'_1', ' checked="checked"' );
	}else{
		$tpl->set( $radio.'_0', ' checked="checked"' );
		$tpl->set( $radio.'_1', '' );
	}
}

$aid = $menu->get(2);
switch ($aid) {
    default : 
		
		// Modul aendern oder hinzufuegen
		if (isset ($_POST['submit'])) {
			// POST-daten escapen
			$mid = $menu->get (3);
			$name = escape( $_POST['name'], 'string' );
			$url = escape( $_POST['url'], 'string' );
			$gshow = escape( $_POST['gshow'], 'integer' );
			$ashow = escape( $_POST['ashow'], 'integer' );
			$fright = escape( $_POST['fright'], 'integer' );
			
			// Query schreiben
			if (empty ($mid)) {
				$sql = 'INSERT INTO `prefix_modules` (`name`, `url`, `gshow`, `ashow`, `fright`) VALUES ("'.$name.'", "'.$url.'", "'.$gshow.'", "'.$ashow.'", "'.$fright.'")';
			}else{
				$sql = 'UPDATE `prefix_modules` SET `name` = "'.$name.'", `url` = "'.$url.'", `gshow` = '.$gshow.', `ashow` = '.$ashow.', `fright` = '.$fright.' WHERE `id` = '.$mid;
			}
			
			// Aenderungen speichern
			db_query ($sql);
			
			// Weiterleitung und Footer
			wd ('admin.php?modules-database', 'Eintrag gespeichert' );
			$design->footer (1);
		}

		// Class
		$class = 'Cmite';
		
		// Template laden
		$tpl = new tpl ('modules/database', 1);
		
		// Template-Header
		$tpl->out (0) ;
		
		// Module abfragen und Ausgeben
		$erg = db_query("SELECT `id`, `name`, `url`, `gshow`, `ashow`, `fright` FROM `prefix_modules` ORDER BY `name` ASC");
		
		if( db_num_rows ($erg) > 0 ){
			$tpl->out (3);
			while ($row = db_fetch_assoc($erg)) {
				$class =  ($class == 'Cmite' ? 'Cnorm' : 'Cmite') ;
				$row['class'] = $class;
				$tpl->set_ar_out ($row , 4) ;
			}
		}
		
		// Tabellenuebergang
		$tpl->out (1) ;
		
		// Aendern oder Einfuegen
		if ($aid == 'edit') {
			$mid = $menu->get (3);
			$erg = db_query('SELECT `name`, `url`, `gshow`, `ashow`, `fright` FROM `prefix_modules` WHERE `id` = '.$mid);
			$row = db_fetch_assoc($erg);
			
			select_radio( 'gshow', $row['gshow'] );
			select_radio( 'ashow', $row['ashow'] );
			select_radio( 'fright', $row['fright'] );
			
			$tpl->set_ar_out (Array ( 'aname' => 'Eintrag hinzuf&uuml;gen', 'name' => $row['name'], 'url' => $row['url'] ), 5) ;
		}else{
			select_radio( 'gshow', 0 );
			select_radio( 'ashow', 0 );
			select_radio( 'fright', 0 );

			$tpl->set_ar_out (Array ( 'aname' => 'Eintrag hinzuf&uuml;gen', 'name' => '', 'url' => '' ), 5) ;
		}
		
		// Template-Footer
		$tpl->out (2) ;
		
        break;
        

    case 'del' : 
		// Betroffene Modul-ID
		$mid = $menu->get (3);
		
		// Wert entfernen
		db_query ('DELETE FROM `prefix_modules` WHERE `id` = '.$mid) ;
		
		wd ('admin.php?modules-database', 'Eintrag gel&ouml;scht' );
		$design->footer (1);
	break;
}

$design->footer();

?>