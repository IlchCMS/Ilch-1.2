<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
//new feature, enhances Debug mode!
define('DEVELOPER_MODE',false);
require_once('include/includes/func/debug.php');
require_once('include/includes/func/db/mysql.php');

spl_autoload_register(function($className) {
    $className = strtolower(str_replace('_', '/', $className));
    $filePath = 'include/includes/class/'.$className.'.php';

    if(file_exists($filePath)) {
        require_once $filePath;
    } else {
        throw new InvalidArgumentException('the file "'.$filePath.'" does not exist');
    }
});

db_connect();


$ilchDb = Ilch_Registry::get('db');
// Eintraege aus `prefix_loader` laden
$sql = 'SELECT `task`,`file` FROM `prefix_loader` ORDER BY `pos` ASC';
$erg = db_query($sql);

while ($row = db_fetch_assoc($erg)) {
    $file_path = 'include/includes/' . $row[ 'task' ] . '/' . $row[ 'file' ];
    if (file_exists($file_path)) {
        require_once($file_path);
    } else {
        echo '<b>ILCH LOADER ERROR:</b> The file <b>"' . $file_path . '"</b> does not exists.' . "\n";
    }
}