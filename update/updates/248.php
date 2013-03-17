<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
 */
// Issue38_regist_admin
$sql = 'DELETE FROM `prefix_config` 
        WHERE `schl` = "forum_regist_confirm"';
db_query($sql);

$sql = 'UPDATE `prefix_config` 
        SET `pos` = "1" 
        WHERE `schl` = "forum_regist"';
db_query($sql);

$sql = 'UPDATE `prefix_config` 
        SET `pos` = "2" 
        WHERE `schl` = "forum_regist_user_pass"';
db_query($sql);

$sql = 'INSERT INTO `prefix_config` 
        (
	        `schl`, 
			`typ`, 
			`typextra`, 
			`kat`, 
			`frage`, 
			`wert`, 
			`pos`, 
			`hide`, 
			`helptext`
		) 
		VALUES 
		(
			"forum_regist_confirm", 
			"select", 
			"{\"keys\":[1, 2, 0],  \"values\":[\"per E-Mail-Link freischalten\", \"durch Admin freischalten\", \"ohne Prüfung freischalten\"]}", 
			"Forum", 
			"Freischaltung der Registrierung", 
			"1", 
			3, 
			0, 
			"Es kann zwischen 3 Registrierungsformen gewählt werden.<br /><br />Die Option \"per E-Mail\" sendet dem neuen User eine E-Mail zu, welche einen Aktivierungslink enthält. Es wird somit auch die E-Mail-Adresse geprüft.<br /><br />Die Option \"durch Admin\" erfordert die Aktivierung der Registrierung durch einen Admin. Es wird keine E-Mail versendet.<br /><br />Bei der Option \"ohne Prüfung\" wird der neue User direkt nach der Registrierung und ohne weitere Prüfung freigeschaltet."
		)';
db_query($sql);

// Änderung! - Standardwert auf Gbook-Einträge werden sofort angezeigt
$sql = 'UPDATE `prefix_config` 
        SET `wert` = "1" 
	WHERE `schl` = "gbook_show"';
db_query($sql);

// Änderung! - Rechtschreibfehler Waserfall -> Wasserfall
$sql = 'UPDATE `prefix_contentslider`
        SET `name` = "Landschaft mit Wasserfall" 
	WHERE `id` = 5';
db_query($sql);

$rev = '248';
$update_messages[$rev][] = 'Registrierung durch Adminprüfung / E-Mail-Link / keine Prüfung';
