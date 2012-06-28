<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
*/

db_query("ALTER TABLE `prefix_user` 
	MODIFY COLUMN `pass` varchar(64) NOT NULL DEFAULT '',
	ADD COLUMN `salt` VARCHAR(37) NOT NULL DEFAULT '' AFTER `pass`;
	ALTER TABLE ``prefix_usercheck` 
	MODIFY COLUMN `pass` varchar(64) NOT NULL DEFAULT '',
	ADD COLUMN `salt` VARCHAR(37) NOT NULL DEFAULT '' AFTER `pass`");

$rev='242';
$update_messages[$rev][] = 'SystemCheck Tabelle in die prefix_config "unsichtbar" eingetragen';

