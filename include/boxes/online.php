<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$dif = date('Y-m-d H:i:s', time() - 60);
$abf = "SELECT DISTINCT uid FROM `prefix_online` WHERE uptime > '" . $dif . "'";
$erg = db_query($abf);
$brk = '';
$uid = array();
$guests = 0;

$tpl = new tpl('boxes/online');

// User online
while ($row = db_fetch_object($erg)) {
    if ($row->uid != 0 AND $brk != $row->uid) {
        $name = @db_result(db_query('SELECT `name` FROM `prefix_user` WHERE `id`= ' . $row->uid), 0);
        $tpl->set('uid', $row->uid);
        $tpl->set('name', $name);
        $tpl->out('online');
        $uid[] = $row->uid;
    }
    if ($row->uid == 0) {
        $guests++;
    }
    $brk = $row->uid;
}

// Keine User online
if (empty($uid)) {
    $tpl->out('nouser');
}

// Trennlinie
$tpl->out('line');

// User offline
$where = (count($uid) > 0) ? 'WHERE `id` NOT IN (' . implode(', ', $uid) . ')' : '';
$abf2 = 'SELECT * FROM `prefix_user` ' . $where . ' ORDER BY `llogin` DESC LIMIT 0,5';
$erg2 = db_query($abf2);

while ($row2 = db_fetch_object($erg2)) {
    $tpl->set('datum', date('\a\m d.m.y \u\m H:i \U\h\r', $row2->llogin));
    $tpl->set('uid', $row2->id);
    $tpl->set('name', $row2->name);
    $tpl->out('offline');
}

// Trennlinie
$tpl->out('line');

// Gäste
$guestn = ($guests == 1) ? $lang['guest'] : $lang['guests'];
$tpl->set('anzguests', $guests);
$tpl->set('nameguest', $guestn);
$tpl->out('guests');
