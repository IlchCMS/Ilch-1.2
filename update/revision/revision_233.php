<?php
/* UPDATE FÜR REVISION 233 */

// drecht (Downloadrecht) zu den downloads hinzufügen
db_query("ALTER TABLE `prefix_downloads` ADD `drecht` tinyint( 4 ) NOT NULL DEFAULT 0");

$rev='233';
$update_messages[$rev][] = 'Downloads - "drecht" für Downloadrecht hinzugefügt';