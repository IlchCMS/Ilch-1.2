<?php
/* UPDATE FÜR REVISION 94 */

db_query("INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`kat` ,
`frage` ,
`wert` ,
`pos`
)
VALUES (
'show_tooltip', 'r2', 'Kalender Optionen', 'Event-Tooltips', '1', '0'
);
");

$rev='94';
$update_messages[$rev][] = 'Kalender Event-Tooltip in der configtabelle registriert';