<?php
db_query("INSERT INTO `prefix_loader` (
`pos` ,
`task` ,
`file` ,
`description`
)
VALUES ( '14', 'func', 'statistic_content.php', 'Wer-Ist-Wo und ContentStats')"
);

$rev='203';
$update_messages[$rev][] = 'loader-eintrag zu revision 202';

?>