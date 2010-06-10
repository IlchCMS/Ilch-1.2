<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

require_once('include/includes/func/debug.php');
// Datenbankverbindung aufbauen
require_once('include/includes/func/db/mysql.php');
db_connect();
// Eintraege aus `prefix_loader` laden
$sql = 'SELECT `task`,`file` FROM `prefix_loader` ORDER BY `pos` ASC';
$erg = db_query($sql);

while ($row = db_fetch_assoc($erg)) {
    $file = 'include/includes/' . $row[ 'task' ] . '/' . $row[ 'file' ];
    if (file_exists($file)) {
        require_once($file);
    } else {
        echo "<b>ILCH LOADER ERROR:</b> The file <b>" . $file . "</b> does not exists.\n";
    }
}