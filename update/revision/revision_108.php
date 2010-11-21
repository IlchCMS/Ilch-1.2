<?php
/* UPDATE FÜR REVISION 108+ */

// pos zu den smilies hinzufügen
db_query("ALTER TABLE `prefix_smilies` ADD `pos` INT( 8 ) NOT NULL" );

// initiale werte für pos
db_query("UPDATE `prefix_smilies` SET `pos` = `id`");

$rev='108';
$update_messages[$rev][] = 'smiley tabelle einen index gegeben';