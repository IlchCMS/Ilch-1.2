<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
*/

db_query("ALTER TABLE `prefix_koms` ADD COLUMN `userid` int(10) unsigned NOT NULL AFTER `name`");
db_query("ALTER TABLE `prefix_koms` ADD COLUMN `time` int(11) NOT NULL AFTER `cat`");

$rev='244';
$update_messages[$rev][] = 'Fehler beim Eintragen von Kommentaren behoben.';