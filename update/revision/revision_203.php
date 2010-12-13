<?php
db_query("REPLACE INTO `prefix_loader` (
`pos` ,
`task` ,
`file` ,
`description`
)
VALUES ( '14', 'func', 'statistic_content.php', 'Wer-Ist-Wo und ContentStats')"
);

$rev='203';
$update_messages[$rev][] = 'loader-eintrag f&uuml;r Content Statistik hinzugef&uuml;gt';