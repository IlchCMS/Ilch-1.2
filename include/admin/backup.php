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
    wd('admin.php?checkconf', 'Das BackupVerzeichnis ist nicht beschreibbar');
    $design->footer(1);
} else 
if (isset($_POST['backitup'])){
    $nodata   = false;      #!DO NOT DUMP TABLES DATA
    $nostruct = false;      #!DO NOT DUMP TABLES STRUCTURE
    $gzip     = false;      #!DO GZIP OUTPUT

    $dump = new MySQLDump();
    $dbdata =  $dump->dumpDatabase(DBDATE, $nodata, $nostruct);

    if($gzip === false){
        file_put_contents(BACKUPDIR.$filedatename, $dbdata);
        #$dump->sendAttachFile($dbdata,'text/html','sql_dump.sql');
    } else {
        file_put_contents(BACKUPDIR.$filedatename, $dbdata);
        #$dump->sendAttachFileGzip($dbdata,BACKUPDIR.$filedatename'.gz');
    } 
    
    
	if (isset($dbdata) && $dbdata !== false) {
            wd('admin.php?backup', 'das SQL-Backup wurde erfolgreich unter <a href="'.BACKUPDIR.$filedatename.'" target="_blank">'.BACKUPDIR.$filedatename.'</a> gespeichert<br />');
            $design->footer(1);
	} else {
            wd('admin.php?backup', 'ooOOps, da lief was schief...');
            $design->footer(1);
	}
}
$tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
$tpl->out(0);