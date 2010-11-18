<?php
/* UPDATE FILE FÜR REVISION 89 */

db_query("INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`kat` ,
`frage` ,
`wert` ,
`pos`
)
VALUES (
'threadersteller_in_uebersicht', 'r2', 'Forum Optionen', 'Threadersteller - Anzeige in Übersicht?', '', '0'
);");

$rev='89';
$update_messages[$rev][] = 'Forum Option "threadersteller in uebersicht" in der configtabelle registriert';