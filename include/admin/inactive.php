<?php
#
# www.ilch.de
#
error_reporting(E_ALL & E_NOTICE); ini_set('display_errors','On');

defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );
$design = new design ( 'Admins Area', 'Admins Area', 2 );

// definieren einer time konstante
!defined('CURRENT_TIME') ? define('CURRENT_TIME', time()):'';

/**
 * inactive_modul_count_days: liefert anzahl vergangener tage zurück
 * @author     annemarie`
 * @version    1.0
 * @param      $last_login     	ein Unix Timestamp
 * @return     int				Anzahl vergangener tage
 */
function inactive_modul_count_days($last_login) {
	$rechsec = CURRENT_TIME - $last_login;
	$rechmin = $rechsec / 60;
	$rechstd = $rechmin / 60;
	return round($rechstd / 24);
}

$design->header();
$tpl = new tpl ( 'inactive.htm', 1 );

##################################################
	// Query zum auslesen des konfigurierten wochenwertes
	$sql_wocheninaktiv = 'SELECT wert FROM prefix_config WHERE schl = "inactive"';
	// fetchen der datenbank & des results
	$wocheninaktiv = db_result(db_query($sql_wocheninaktiv));
	
	// Wenn änderung an der inaktivität gespeichert werden soll + Prüfen ob zu speichernder wert eine Zahl ist
	if (isset($_POST['saveinaktiv']) && is_numeric($_POST['inaktivset']) && is_numeric(substr($_POST['inaktivset'],0,1)) && $_POST['inaktivset'] != 0) { // alle daten sind valide
		
		// escapen des zu speichernden wochenwertes
		$setinaktivinput = escape($_POST['inaktivset'], 'integer'); 
		
		// Update query für wochen
		$sql_update_wochen = "UPDATE prefix_config SET wert = ".$setinaktivinput." WHERE schl = 'inactive'";
		// fetchen der datenbank
		db_query($sql_update_wochen);
		
		// prüfen ob wert "woche" oder "wochen" genommen werden soll
		$woche = $setinaktivinput<=1?' woche':' wochen';
		
		// statusmeldung
		wd ('admin.php?inactive', 'Inaktive User werden nun ab '.$setinaktivinput.$woche.' als inaktiv angezeigt',  '5' );
		$design->footer(1);
		
	} else if (isset($_POST['saveinaktiv']) && !is_numeric($_POST['inaktivset'])){ // Es wurde versucht einen nicht numerischen wert zu speichern
		// statusmeldung
		wd ('admin.php?inactive', 'Speichern nicht m&ouml;glich, Wert f&uuml;r Wochen muss eine Zahl sein!',  '5' ); 
		$design->footer(1);
	} else if (isset($_POST['saveinaktiv']) && (!is_numeric(substr($_POST['inaktivset'],0,1)) || $_POST['inaktivset'] == 0)){ // Es wurde versucht einen nicht numerischen wert zu speichern
		// statusmeldung
		wd ('admin.php?inactive', 'Speichern nicht m&ouml;glich, Wert f&uuml;r Wochen muss eine positive Zahl sein!',  '5' ); 
		$design->footer(1);
	} else if (isset($_POST['banid']) && is_numeric($_POST['delete_user_id'])) { // alle daten sind valide
	// Wenn ein user gelöscht werden soll + Prüfen ob zu speichernder wert eine Zahl ist
		// Löschen des users
		user_remove(escape($_POST['delete_user_id'], 'integer'));
		// statusmeldung
		wd ('admin.php?inactive', 'User "'.$_POST['delete_user_name'].'" wird gel&ouml;scht...',  '1' ); 
		$design->footer(1);
	} else if (isset($_POST['banid']) && !is_numeric($_POST['delete_user_id'])) {  // Es wurde versucht einen nicht numerischen wert zu speichern
		// statusmeldung
		wd ('admin.php?inactive', 'Es wurde eine fehlerhafte User ID &uuml;bergeben ...',  '5' ); 
		$design->footer(1);
	}

# ###################################################

// erstellen des querys, entweder query zur anzeige aller user oder query mit der wochenbedingung

$sql_getuser = isset($_POST['show_all']) ? 'SELECT id,name,llogin FROM prefix_user' : 'SELECT id,name,llogin FROM prefix_user WHERE llogin <= '.strtotime('-'.$wocheninaktiv.' weeks', CURRENT_TIME).' ORDER BY llogin ASC';

// fetchen der datenbank
$getuser = @db_query($sql_getuser);

// variable userlist anlegen
$userlist = '';

// iterieren über das datenbank result
while ($listuser = db_fetch_assoc($getuser)) {
	// anzahl tage feststellen, wenn wert nicht numerisch ist Fehlerursache mit ausgeben
	$anzahl_tage = is_numeric($listuser['llogin']) ? inactive_modul_count_days($listuser['llogin']).' Tage' : 'Fehlerhafter Datensatz';
	// erstellen der ausgabe
	$userlist .= '<tr>';
	$userlist .= '	<td>'.$listuser['id'].'</td>';
	$userlist .= '	<td><a href="index.php?user-details-'.$listuser['id'].'">'.$listuser['name'].'</a></td>';
	$userlist .= '	<td><form method="post"><input type="hidden" name="delete_user_name" value="'.$listuser['name'].'"/><input type="hidden" name="delete_user_id" value="'.$listuser['id'].'"/><input type="submit" name="banid" value="l&ouml;schen" /></form></td>';
	$userlist .= '	<td>'.$anzahl_tage.'</td>';
	$userlist .= '</tr>';
}

#########################################################
# Template erstellen                                    #
#########################################################

// entscheidung ob "woche" oder "wochen"
$woche_frontend = $wocheninaktiv<=1?' woche':' wochen';

$ansichtsmodus = isset($_POST['show_all']) ? 'Es werden nun alle User angezeigt' : 'User die l&auml;nger als '.$wocheninaktiv.' '.$woche_frontend.' inaktiv sind';
$modus_switch = isset($_POST['show_all']) ? '<input type="submit" name="show_limit" value="Wochenlimit benutzen" />' : '<input type="submit" name="show_all" value="Alle User anzeigen" />';


$tpl->set('NAME', $listuser['name']);
$tpl->set('ANSICHTSMODUS', $ansichtsmodus);
$tpl->set('MODUS_SWITCH', $modus_switch);
$tpl->set('ANZAHLINAKTIV', $wocheninaktiv);
$tpl->set('USERIST', $userlist);
$tpl->out(0); 

#########################################################
#          Copyright darf nicht entfernt werden

$design->footer();

?>