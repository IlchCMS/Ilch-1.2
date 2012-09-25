<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
*/

db_query("UPDATE `prefix_loader` SET `file` = 'pwcrypt.php' fiel='passwdcrypt.php'");

$rev='243';
$update_messages[$rev][] = 'Spalte für die Passwörter vergrößert, so dass crypt bis SHA512 benutzt werden kann';