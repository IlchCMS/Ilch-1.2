<?php
#db_query("INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`, `hide`) VALUES ('extendedlog', 'r2', 'Allgemeine Optionen', 'erweitertes ContentLogging ?', '', '0', '0');");
db_query("ALTER TABLE `prefix_online` ADD `content` VARCHAR( 255 ) NOT NULL ");
db_query("CREATE TABLE `prefix_stats_content` (
		`id` INT NOT NULL AUTO_INCREMENT ,
		`content` VARCHAR( 255 ) NOT NULL ,
		`counter` INT NOT NULL ,
		PRIMARY KEY ( `id` )
		)");
$rev='202';
$update_messages[$rev][] = 'Wer ist Wo und Content-Stats Tabelle/Spalte angelegt';
?>