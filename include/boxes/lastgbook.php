<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/lastgbook');

$abf = "SELECT `id`, `name`, `time` FROM `prefix_gbook` ORDER BY `time` DESC LIMIT 0,5";
$erg = db_query($abf);

if (db_num_rows($erg) == 0) {
    $tpl->out('noinsert');
} else {
    while ($row = db_fetch_object($erg)) {
        $tpl->set('id', $row->id);
        $tpl->set('name', $row->name);
        $tpl->set('date', post_date($row->time));
        $tpl->out('inserts');
    }
}
