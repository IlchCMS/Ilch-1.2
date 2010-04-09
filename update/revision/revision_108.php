<?php
/* UPDATE FÜR REVISION 108+ */

// pos zu den smilies hinzufügen
db_query("ALTER TABLE `prefix_smilies` ADD `pos` INT( 8 ) NOT NULL DEFAULT '0'");

// initiale werte für pos
db_query("UPDATE `ic1_smilies` SET `pos` = `id`");


?>