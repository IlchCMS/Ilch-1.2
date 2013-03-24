<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// Einstellungen
$limit = 3;       // Wieviele Registrierungen sollen angezeigt werden?
$showavatars = 1; // Avatare angezeigen?
//

$tpl = new tpl('boxes/lastregist');

$abf = "SELECT `id`, `name`, `regist`, `geschlecht`, `avatar` FROM `prefix_user` ORDER BY `regist` DESC LIMIT 0, " . $limit;
$erg = db_query($abf);

while ($row = db_fetch_object($erg)) {
    if (!$row->avatar || !file_exists($row->avatar)) {
        $genderArray = array('wurstegal', 'maennlich', 'weiblich');
        $row->avatar = 'include/images/avatars/' . $genderArray[$row->geschlecht] . '.jpg';
    }
    $tpl->set('id', $row->id);
    $tpl->set('name', $row->name);
    $tpl->set('date', date('d.m.y', $row->regist));
    $tpl->set('time', date('H:i', $row->regist));
    $tpl->out('0');
    if ($showavatars) {
        $tpl->set('avatar', $row->avatar);
        $tpl->out('avatarshow');
    }
    $tpl->out('avatarend');
}
