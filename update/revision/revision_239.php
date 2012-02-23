<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
 */

db_query("ALTER TABLE `prefix_selfp` ADD `content` MEDIUMTEXT NOT NULL ");

$rev='239';
$update_messages[$rev][] = 'EigeneSeiten zur Speicherung in der Datenbank';