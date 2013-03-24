<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/lastwars');

$abf = 'SELECT * 
        FROM `prefix_wars` 
		WHERE `status` = "3" 
		ORDER BY `datime` 
		DESC LIMIT 3';
$erg = db_query($abf);

if (db_num_rows($erg) == 0) {
    $tpl->out('nowars');
} else {
    while ($row = db_fetch_object($erg)) {
        $row->tag = (empty($row->tag) ? $row->gegner : $row->tag);
        if ($row->wlp == 1) {
            $bild = 'win.gif';
        } elseif ($row->wlp == 2) {
            $bild = 'los.gif';
        } elseif ($row->wlp == 3) {
            $bild = 'pad.gif';
        }
        $tpl->set('gameimg', get_wargameimg($row->game));
        $tpl->set('warid', $row->id);
        $tpl->set('owp', $row->owp);
        $tpl->set('opp', $row->opp);
        $tpl->set('tag', $row->tag);
        $tpl->set('ergicon', $bild);
        $tpl->out('wars');
    }
}
