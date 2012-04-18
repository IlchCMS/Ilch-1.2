<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
ini_set ('display_errors', 'On');
error_reporting('E_ALL & ~ E_NOTICE');
require_once('./include/backup/backup.class.php');

$design = new design('Ilch Admin-Control-Panel :: Backup', '', 2);
$design->header();
$tpl = new tpl('backup', 1);
define("BACKUPDIR", "./include/backup/");
$filedatename = "sql_".date('Y-m-d_h-m-s').".sql";

if (!is_writeable(BACKUPDIR)) {
    echo 'backupdir != writeable';
} else 
if (isset($_POST['backitup'])){
	$dumper = new MySQLDump(DBDATE, BACKUPDIR.$filedatename, false, false);
    $dumperg=$dumper->doDump();
	if (isset($dumperg) && $dumperg !== false) {
    	echo 'das SQL-Backup wurde erfolgreich unter '.BACKUPDIR.$filedatename.' gespeichert<br />';
	} else {
		echo 'oops, da lief was schief';
	}
}

$tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
$tpl->out(0);