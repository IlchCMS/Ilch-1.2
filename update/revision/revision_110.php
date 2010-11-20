<?php
/* UPDATE FILE FÜR REVISION 110 */

// profilefields_special zum laden hinzufügen
db_query("INSERT INTO `prefix_loader` (
`id` ,
`pos` ,
`task` ,
`file` ,
`description`
)
VALUES (
NULL , '15', 'class', 'profilefield_registry.php', 'Verwaltet die Profilfeld-Typen.'
);");

/* zusätzliches feld für die profilfeld-config */
db_query("ALTER TABLE `prefix_profilefields` ADD `config_value` VARCHAR( 2048 ) NOT NULL ;");

$rev='110';
$update_messages[$rev][] = 'profilefields_special zum loader hinzugef&uuml;gt';