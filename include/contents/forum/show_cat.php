<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Forum :: ' . aktForumCats($aktForumRow[ 'kat' ], 'title');
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats($aktForumRow[ 'kat' ]) . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();

$tpl = new tpl('forum/show_cat');
$tpl->out(0);

$cid = escape($menu->get(2), 'integer');

$q = "SELECT
  `a`.`id`, `a`.`cid`, `a`.`name`, `a`.`besch`,
  `a`.`topics`, `a`.`posts`, `b`.`name` as `topic`,
  `c`.`id` as `pid`, `c`.`tid`, `b`.`rep`, `c`.`erst`, `c`.`time`,
  `a`.`cid`, `k`.`name` as `cname`
FROM `prefix_forums` `a`
  LEFT JOIN `prefix_forumcats` `k` ON `k`.`id` = `a`.`cid`
  LEFT JOIN `prefix_posts` `c` ON `a`.`last_post_id` = `c`.`id`
  LEFT JOIN `prefix_topics` `b` ON `c`.`tid` = `b`.`id`

  LEFT JOIN `prefix_groupusers` `vg` ON `vg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `vg`.`gid` = `a`.`view`
  LEFT JOIN `prefix_groupusers` `rg` ON `rg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `rg`.`gid` = `a`.`reply`
  LEFT JOIN `prefix_groupusers` `sg` ON `sg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `sg`.`gid` = `a`.`start`

WHERE ((" . $_SESSION[ 'authright' ] . " <= `a`.`view` AND `a`.`view` < 1)
   OR (" . $_SESSION[ 'authright' ] . " <= `a`.`reply` AND `a`.`reply` < 1)
   OR (" . $_SESSION[ 'authright' ] . " <= `a`.`start` AND `a`.`start` < 1)
	 OR `vg`.`fid` IS NOT NULL
	 OR `rg`.`fid` IS NOT NULL
	 OR `sg`.`fid` IS NOT NULL
	 OR -9 >= " . $_SESSION[ 'authright' ] . ")
  AND `k`.`id`     = " . $cid . "
ORDER BY `k`.`pos`, `a`.`pos`";
$erg1 = db_query($q);
$xcid = 0;
while ($r = db_fetch_assoc($erg1)) {
    $r[ 'topicl' ] = $r[ 'topic' ];
    $r[ 'topic' ] = html_enc_substr($r[ 'topic' ], 0, 23);
    $r[ 'ORD' ] = forum_get_ordner($r[ 'time' ], $r[ 'id' ]);
    $r[ 'mods' ] = getmods($r[ 'id' ]);
    $r[ 'datum' ] = date('d.m.y - H:i', $r[ 'time' ]);
    $r[ 'page' ] = ceil(($r[ 'rep' ] + 1) / $allgAr[ 'Fpanz' ]);
    $tpl->set_ar($r);

    if ($r[ 'cid' ] != $xcid) {
        $tpl->out(1);
        // Unterkategorien
        $sql = db_query("SELECT DISTINCT `a`.`name` as `cname`, `a`.`id` as `cid` FROM `prefix_forumcats` `a` LEFT JOIN `prefix_forums` `b` ON `a`.`id` = `b`.`cid` WHERE `a`.`cid` = {$r['cid']} AND `a`.`id` = `b`.`cid` ORDER BY `a`.`pos`, `a`.`name`");
        while ($ucat = db_fetch_assoc($sql)) {
            $tpl->set_ar_out($ucat, 2);
        }
        // Unterkategorien - Ende
        $xcid = $r[ 'cid' ];
    }
    $tpl->out(3);
}
$tpl->out(4);
$design->footer();

?>