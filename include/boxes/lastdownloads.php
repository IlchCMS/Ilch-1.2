<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// Einstellungen
$limit = 3; // Wieviele Downloads sollen angezeigt werden?
//

$tpl = new tpl('boxes/lastdownloads');

$abf = "SELECT `a`.`cat`, `a`.`name`, `a`.`id`, DATE_FORMAT(`a`.`time`,'%d.%m.%y') as `date`, `a`.`creater`, `b`.`recht`
		FROM `prefix_downloads` as `a` LEFT JOIN `prefix_downcats` as `b` ON `a`.`cat` = `b`.`id`
		WHERE " . $_SESSION['authright'] . " <= `b`.`recht` 
		ORDER BY `a`.`id` DESC 
		LIMIT 0, " . $limit;
$erg = db_query($abf);

if (db_num_rows($erg) == 0) {
    $tpl->out('nodownloads');
} else {
    while ($row = db_fetch_object($erg)) {
        $tpl->set_ar_out($row, 'downloads');
    }
}
