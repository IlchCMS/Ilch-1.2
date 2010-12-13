<?php

# Funktionsdatei für WerIstWo Funktionen und AdminStatistiken
# www.ilch.de
# für ilch 1.2

global $allgAr;

# gibt einen String zurück der die aktuelle Seite wiedergibt
# z.b. /index.php?forum
function user_url() {
	if ($_SERVER['SCRIPT_NAME']) {
		return $_SERVER['SCRIPT_NAME'];
	} else { # ist kein Dateiname vorhanden wird die Startseite /index.php ausgegeben
		debug('$_SERVER[] gibt keinen SCRIPT_NAME zurueck - verwende /index.php');
		return '/index.php';
	}
}

# prüft ob ModRewrite an/aus ist
function modrewrite_is_on() {
	if ($allgAr['modrewrite']) {
		return true;
	} else {
		return false;
	}
}

# loggt die aufgerufene Content-Seite
function content_stats($m) {
	$numrows = db_count_query("SELECT COUNT(id) FROM `prefix_stats_content` WHERE content = '".$m."'");
	if ($numrows == 0) {
		if(db_query("INSERT INTO `prefix_stats_content` (content, counter) VALUES ('".$m."', '1')")) {
			debug ('"'.$m.'" zu ContentStats hinzugefuegt');
		} else {
			debug ('"'.$m.'" konnte nicht geloggt werden');
		}
	} else
	if ($numrows >= 1) {
		db_query("UPDATE `prefix_stats_content` SET counter = counter +1 WHERE content = '".$m."'");
		debug ('ContentStats aktualisiert...');
	}
}




