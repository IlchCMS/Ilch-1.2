<?php


db_query("INSERT INTO `prefix_modules` (
`id` ,
`url` ,
`name` ,
`gshow` ,
`ashow` ,
`fright` ,
`menu` ,
`pos`
)
VALUES (
NULL , 'inactive', 'inaktive User', '1', '1', '1', 'User', '4'
)");



db_query("INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`kat` ,
`frage` ,
`wert` ,
`pos` ,
`hide`
)
VALUES (
'inactive', 'r2', 'Allgemeine Optionen', 'Ab wie vielen Wochen z&auml;hlt ein User als inaktiv ?', '12', '0', '1'

)");

$rev='213';
$update_messages[$rev][] = 'Zeile zum Speichern der Inaktiv-Zeit f&uuml;r das inavtive-modul hinzugef&uuml;gt und im UserMenu verankert';