<?php
$sql = array();
$sql[] = 'ALTER TABLE `prefix_config` ADD `typextra` text AFTER `typ`, ADD `helptext` text';
$sql[] = <<<SQL
INSERT INTO `ic1_config` (`schl`, `typ`, `typextra`, `kat`, `frage`, `wert`, `pos`, `hide`, `helptext`) VALUES ('kalender_standard_list', 'select', '{"keys":[1, 0], "values":["Listenansicht", "Monatsansicht"]}', 'Kalender Optionen', 'Standardansicht', '1', '0', '0', 'Gibt an, ob die Listenansicht oder die Monatsansicht verwendet wird, wenn man den Kalender aufruft.');
SQL;
$sql[] = <<<SQL
UPDATE `prefix_config` SET `helptext` = 'Wenn aktiviert werden bei Kalendereintr&auml;gen in einem Tooltip schon Details zu dem Eintrag angezeigt.' WHERE `schl` = 'show_tooltip';
SQL;

foreach ($sql as $q){
    db_query($q);
}

$rev='223';
$update_messages[$rev][] = 'Config Tabelle um 2 Spalten erweitert f&uuml;r Hilfetexte und erweiterte Selects, Kalender Optionen erweitert';