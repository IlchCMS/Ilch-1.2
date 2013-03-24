<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/lastnews');

$abf = 'SELECT *
        FROM `prefix_news`
        WHERE `news_recht` >= ' . $_SESSION['authright'] . '
        ORDER BY `news_time` DESC
        LIMIT 0,5';
$erg = db_query($abf);

if (db_num_rows($erg) == 0) {
    $tpl->out('nonews');
} else {
    while ($row = db_fetch_object($erg)) {
        $tpl->set('newsid', $row->news_id);
        $tpl->set('title', $row->news_title);
        $tpl->out('news');
    }
}
