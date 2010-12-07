<?php
/* UPDATE FÜR REVISION 196 */

// pos zu den smilies hinzufügen
db_query("ALTER TABLE `prefix_smilies` ADD `pos` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY" );

// initiale werte für pos
db_query("ALTER TABLE `prefix__wars` CHANGE `wo` `wo` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT ''");

$rev='196';
$update_messages[$rev][] = 'Wars - "Wo" spalte in der datenbank auf 255 zeichen verlängert um platz für adressen zu geben';