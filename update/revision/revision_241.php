<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
*/

db_query("INSERT INTO `prefix_config` (`schl`, `typ`, `typextra`, `kat`, `frage`, `wert`, `pos`, `hide`, `helptext`) 
			VALUES 
			('syscheckstatus', 'input', NULL, 'unsichtbar', '', 'ungeprüft', '0', '1', 'Hier speichert das Script den aktuellen Systemstatus'), 
			('syscheckdatum', 'input', NULL, 'unsichtbar', '', '', '0', '1', 'Hier speichert das Script das Datum der letzten Systemüberprüfung');'
		);	
");

$rev='241';
$update_messages[$rev][] = 'SystemCheck Tabelle in die prefix_config "unsichtbar" eingetragen';

