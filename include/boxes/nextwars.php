<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/nextwars');

$akttime = date('Y-m-d');
$abf = "SELECT DATE_FORMAT(`datime`,'%d.%m.%y - %H:%i') as `time`,`tag`,`gegner`, `id`, `game` 
        FROM `prefix_wars` 
		WHERE `status` = 2 AND `datime` > '" . $akttime . "' 
		ORDER BY `datime`";
$erg = @db_query($abf);

if (@db_num_rows($erg) == 0) {
    $tpl->out('nowars');
} else {
    while ($row = @db_fetch_object($erg)) {
        $row->tag = (empty($row->tag) ? $row->gegner : $row->tag);
        $tpl->set('gameimg', get_wargameimg($row->game));
        $tpl->set('warid', $row->id);
        $tpl->set('date', $row->time);
        $tpl->set('tag', $row->tag);
        $tpl->out('wars');
    }
}
