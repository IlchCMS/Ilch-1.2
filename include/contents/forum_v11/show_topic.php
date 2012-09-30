<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
// check ob ein fehler aufgetreten ist.
check_forum_failure($forum_failure);

$title = $allgAr[ 'title' ] . ' :: Forum :: ' . aktForumCats($aktForumRow[ 'kat' ], 'title') . ' :: ' . $aktForumRow[ 'name' ];
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats($aktForumRow[ 'kat' ]) . '<b> &raquo; </b>' . $aktForumRow[ 'name' ] . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();

$limit = $allgAr[ 'Ftanz' ]; // Limit
$page = ($menu->getA(3) == 'p' ? $menu->getE(3) : 1);
$MPL = db_make_sites($page, "WHERE fid = '" . $fid . "'", $limit, '?forum-showtopics-' . $fid, 'topics');
$anfang = ($page - 1) * $limit;

$tpl = new tpl('forum_v11/showtopic');

if ($forum_rights[ 'start' ] == true) {
    $tpl->set('NEWTOPIC', '<b>[ <a href="index.php?forum-newtopic-' . $fid . '">' . $lang[ 'newtopic' ] . '</a> ]</b>');
} else {
    $tpl->set('NEWTOPIC', '');
}
$tpl->set('MPL', $MPL);
$tpl->set_out('FID', $fid, 0);

$q = "SELECT `a`.`id`, `a`.`name`, `a`.`rep`, `a`.`erst`, `a`.`hit`, `a`.`art`, `a`.`stat`, `b`.`time`, `b`.`erst` as `last`, `b`.`id` as `pid`
	FROM `prefix_topics` `a`
	LEFT JOIN `prefix_posts` `b` ON `a`.`last_post_id` = `b`.`id`
	WHERE `a`.`fid` = {$fid}
	ORDER BY `a`.`art` DESC, `b`.`time` DESC
	LIMIT " . $anfang . "," . $limit;
$erg = db_query($q);
if (db_num_rows($erg) > 0) {
    while ($row = db_fetch_assoc($erg)) {
        if ($row[ 'stat' ] == 0) {
            $row[ 'ORD' ] = 'cord';
        } else {
            // $row['ORD'] = get_ordner($row['time']);
            $row[ 'ORD' ] = forum_get_ordner($row[ 'time' ], $row[ 'id' ], $fid);
        }
		$row[ 'datum' ] =  post_date($row[ 'time' ]);
        $row[ 'page' ] = ceil(($row[ 'rep' ] + 1) / $allgAr[ 'Fpanz' ]);
        $row[ 'VORT' ] = ($row[ 'art' ] == 1 ? 'Fest: ' : '');
        $tpl->set_ar_out($row, 1);
    }
} else {
    echo '<tr><td colspan="6" class="Cnorm"><b>keine Eintr&auml;ge vorhanden</b></td></tr>';
}

$tpl->out(2);
if ($forum_rights[ 'mods' ] == true) {
    $tpl->set('id', $fid);
    $tpl->out(3);
}

$design->footer();

?>