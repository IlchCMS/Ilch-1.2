<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$such = $menu->get(1);

if ($such == 'aeit') {
    if (isset($_POST[ 'name' ])) {
        $name = escape($_POST[ 'name' ], 'string');
        $uid = @db_result(db_query("SELECT `id` FROM `prefix_user` WHERE `name` = BINARY '" . $name . "'"));
        if ($uid > 0) {
            $menu->set_url(2, $uid);
        }
    }
    if ($menu->get(2) >= 1 AND $menu->get(2) != $_SESSION[ 'authid' ]) {
        $uid = $menu->get(2);
        $name = get_n($uid);
        $mtitle = $lang[ 'posts' ] . ' ' . $lang[ 'from' ] . ' ' . $name;
    } else {
        $uid = $_SESSION[ 'authid' ];
        $mtitle = $lang[ 'ownposts' ];
        $name = '';
    }
} elseif ($such == 'aubt') {
    $mtitle = $lang[ 'topicwithnoreply' ];
} else {
    $mtitle = $lang[ 'newtopicssincelastvisit' ];
}

$title = $allgAr[ 'title' ] . ' :: Forum :: ' . $mtitle;
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b> ' . $mtitle;
$design = new design($title, $hmenu, 1);
$design->header();
// mehrere seiten falls gefordert
$limit = 25; // Limit
$page = ($menu->getE('p') > 0 ? $menu->getE('p') : 1);
$anfang = ($page - 1) * $limit;

$s = "DISTINCT `b`.`id` as `fid`, `a`.`name` as `titel`, `a`.`id` as `id`, `d`.`name` as `author`";
$q = "SELECT {SELECT}
  FROM `prefix_topics` `a`
    LEFT JOIN `prefix_forums` `b` ON `b`.`id` = `a`.`fid`
    LEFT JOIN `prefix_posts` `c` ON `c`.`tid` = `a`.`id`
    LEFT JOIN `prefix_user` `d` ON `c`.`erstid` = `d`.`id`
    LEFT JOIN `prefix_groupusers` `vg` ON `vg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `vg`.`gid` = `b`.`view`
    LEFT JOIN `prefix_groupusers` `rg` ON `rg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `rg`.`gid` = `b`.`reply`
    LEFT JOIN `prefix_groupusers` `sg` ON `sg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `sg`.`gid` = `b`.`start`
  WHERE (((`b`.`view` >= " . $_SESSION[ 'authright' ] . " AND `b`.`view` <= 0) OR
            (`b`.`reply` >= " . $_SESSION[ 'authright' ] . " AND `b`.`reply` <= 0) OR
            (`b`.`start` >= " . $_SESSION[ 'authright' ] . " AND `b`.`start` <= 0)) OR
            (`vg`.`fid` IS NOT NULL OR `rg`.`fid` IS NOT NULL OR `sg`.`fid` IS NOT NULL OR " . $_SESSION[ 'authright' ] . " = -9))
     AND {WHERE}
  ORDER BY `c`.`time` DESC";
$q2 = "SELECT DISTINCT `b`.`id` as `fid`, `a`.`name` as `titel`, `a`.`id` as `id`, MIN(`c`.`id`) AS `firstnew`, `d`.`name` as `author`
    FROM `prefix_topics` `a`
      LEFT JOIN `prefix_forums` `b` ON `b`.`id` = `a`.`fid`
      LEFT JOIN `prefix_posts` `c` ON `c`.`tid` = `a`.`id`
      LEFT JOIN `prefix_user` `d` ON `c`.`erstid` = `d`.`id`
      LEFT JOIN `prefix_groupusers` `vg` ON `vg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `vg`.`gid` = `b`.`view`
      LEFT JOIN `prefix_groupusers` `rg` ON `rg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `rg`.`gid` = `b`.`reply`
      LEFT JOIN `prefix_groupusers` `sg` ON `sg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `sg`.`gid` = `b`.`start`
    WHERE (((`b`.`view` >= " . $_SESSION[ 'authright' ] . " AND `b`.`view` <= 0) OR
            (`b`.`reply` >= " . $_SESSION[ 'authright' ] . " AND `b`.`reply` <= 0) OR
            (`b`.`start` >= " . $_SESSION[ 'authright' ] . " AND `b`.`start` <= 0)) OR
            (`vg`.`fid` IS NOT NULL OR `rg`.`fid` IS NOT NULL OR `sg`.`fid` IS NOT NULL OR " . $_SESSION[ 'authright' ] . " = -9))
      AND `c`.`time` >= " . $_SESSION[ 'lastlogin' ] . "
    GROUP BY `b`.`id`,`a`.`id`, `a`.`name`
    ORDER BY `c`.`time` DESC";
$x = time() - (3600 * 24 * 360);
if ($such == 'aubt') {
    $where = "`c`.`time` >= " . $x . " AND `a`.`rep` = 0";
    $gAnz = @db_result(db_query(str_replace('{WHERE}', $where, str_replace('{SELECT}', ' COUNT(DISTINCT `a`.`id`)', $q))), 0);
    $q = str_replace('{WHERE}', $where, str_replace('{SELECT}', $s, $q));
} elseif ($such == 'augt') {
    $where = "`c`.`time` >= " . $x . " AND `c`.`time` >= " . $_SESSION[ 'lastlogin' ];
    $gAnz = @db_result(db_query(str_replace('{WHERE}', $where, str_replace('{SELECT}', ' COUNT(DISTINCT `a`.`id`)', $q))), 0);
    $q = str_replace('{WHERE}', $where, str_replace('{SELECT}', $s, $q2));
} elseif ($such == 'aeit') {
    $where = "`c`.`time` >= " . $x . " AND `c`.`erstid` = " . $uid;
    $gAnz = @db_result(db_query(str_replace('{WHERE}', $where, str_replace('{SELECT}', ' COUNT(DISTINCT `a`.`id`)', $q))), 0);
    $q = str_replace('{WHERE}', $where, str_replace('{SELECT}', $s, $q));
}
$MPL = db_make_sites($page, "", $limit, 'index.php?forum-' . $such . ($such == 'aeit' ? '-' . $uid : ''), "", $gAnz);

$tpl = new tpl('forum/search');
$q = db_query($q . " LIMIT " . $anfang . "," . $limit);
$class = '';
$tpl->set_out('gAnz', $gAnz, 0);
while ($r = db_fetch_assoc($q)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $r[ 'class' ] = $class;
    $r[ 'ctime' ] = db_result(db_query("SELECT MAX(`time`) FROM `prefix_posts` WHERE `tid` = " . $r[ 'id' ]), 0, 0);
    $r[ 'ord' ] = forum_get_ordner($r[ 'ctime' ], $r[ 'id' ], $r[ 'fid' ]);
    $r[ 'link' ] = 'forum-showposts-' . $r[ 'id' ];
    if ($menu->get(1) == 'aeit') {
        $r[ 'author' ] = '';
    } elseif ($such == 'aubt') {
        $r[ 'author' ] = ' ' . $lang[ 'from' ] . ' ' . $r[ 'author' ];
    } else {
        $r[ 'author' ] = ' ' . $lang[ 'newpost' ] . ' ' . $lang[ 'from' ] . ' ' . $r[ 'author' ];
        $r[ 'postsbefore' ] = db_count_query('SELECT COUNT(`id`) FROM `prefix_posts` WHERE `tid` = ' . $r[ 'id' ] . ' AND `id` < ' . $r[ 'firstnew' ]);
        $r[ 'page' ] = ceil(($r[ 'postsbefore' ] + 1) / $allgAr[ 'Fpanz' ]);
        $r[ 'link' ] .= '-p' . $r[ 'page' ] . '#' . $r[ 'firstnew' ];
    }

    $tpl->set_ar_out($r, 1);
}
$tpl->set_out('MPL', $MPL, 2);
if ($such == 'aeit') {
    $tpl->set_out('name', $name, 3);
}

$design->footer();

?>