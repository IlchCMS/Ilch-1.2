<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// Einstellungen
$limit = 3;       // Wieviele Geburtstag sollen angezeigt werden?
$recht = -1;      // Anzeige Modus 0 = Alle / -1 Alle die mehr als Memberrechte haben usw.
$showavatars = 1; // Avatare angezeigen?
//

$tpl = new tpl('boxes/geburtstag');
$timestamp = time();
$akttime = date('Y-m-d', $timestamp);

function get_gebtage($datum) {
    list($y, $m, $d) = explode('-', $datum);
    return ($d . '.' . $m . '.' . $y);
}

$abf = "SELECT `name`, `id`, `avatar`, `geschlecht`,
       CASE WHEN ( MONTH(`gebdatum`) < MONTH(NOW()) ) OR ( MONTH(`gebdatum`) <= MONTH(NOW()) 
	   AND DAYOFMONTH(`gebdatum`) < DAYOFMONTH(NOW()) ) THEN
       gebdatum + INTERVAL (YEAR(NOW()) - YEAR(`gebdatum`) + 1) YEAR
       ELSE `gebdatum` + INTERVAL (YEAR(NOW()) - YEAR(`gebdatum`)) YEAR
	   END AS `gebtage`
       FROM `prefix_user` 
	   WHERE `gebdatum` > 0000-00-00 AND `recht` <= " . $recht . " 
	   ORDER BY `gebtage` LIMIT " . $limit;
$erg = db_query($abf);

while ($row = db_fetch_object($erg)) {
    if (!$row->avatar || !file_exists($row->avatar)) {
        $genderArray = array('wurstegal', 'maennlich', 'weiblich');
        $row->avatar = 'include/images/avatars/' . $genderArray[$row->geschlecht] . '.jpg';
    }
    $tpl->out('0');
    if ($akttime == $row->gebtage) {
        $tpl->set_ar_out($row, 'birthday_today');
    } else {
        $row->gebTag = get_gebtage($row->gebtage);
        $tpl->set_ar_out($row, 'birthday_future');
        if ($showavatars) {
            $tpl->set('avatar', $row->avatar);
            $tpl->out('avatarshow');
        }
        $tpl->out('avatarend');
    }
}
