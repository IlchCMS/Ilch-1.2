<?php
/* UPDATE FÜR REVISION 243 */

//UserID fuer Kommentare
db_query("ALTER TABLE `prefix_koms` ADD `userid` INT( 10 ) UNSIGNED NOT NULL AFTER `name`");

//Kommentarbox anzeigen
$sql = <<<SQL

INSERT INTO `ic1_menu` (`wo`, `pos`, `was`, `ebene`, `recht`, `recht_type`, `name`, `path`) VALUES
(3, 7, 1, 0, 0, 0, 'Kommentare', 'kommentare.php')

SQL;
db_query($sql);


$rev='243';
$update_messages[$rev][] = 'Kommentare um UserID erweitert';