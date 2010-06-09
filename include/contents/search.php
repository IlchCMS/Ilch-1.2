<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');

function serach_mark($text, $such) {
    // $text = BBcode($text);
    $serar = explode(' ', $such);
    $text = strip_tags($text);
    $text = stripslashes($text);
    $rte = '';
    $tleng = 30;
    foreach ($serar as $v) {
        $firs = strpos(strtolower($text), strtolower($v));
        $begi = (($firs - $tleng) < 0 ? 0 : $firs - $tleng);
        $leng = strlen($text);
        $ende = (($firs + strlen($v) + $tleng) > $leng ? $leng : $firs + strlen($v) + $tleng);
        $ttxt = substr($text, $begi, ($ende - $begi));
        $rte .= ' ... ' . preg_replace("/" . $v . "/si", '<b>' . $v . '</b>', $ttxt);
    }
    return ($rte);
}

function search_finduser() {
    $design = new design('Finduser', '', 0);
    $design->header();

    $tpl = new tpl('search_finduser');
    $tpl->out(0);
    if (isset($_POST[ 'sub' ]) AND !empty($_POST[ 'name' ])) {
        $name = str_replace('*', "%", $_POST[ 'name' ]);
        $name = escape($name, 'string');
        $q = "SELECT `name`,`name` FROM `prefix_user` WHERE `name` LIKE '" . $name . "'";
        $tpl->set('username', dbliste('', $tpl, 'username', $q));
        $tpl->out(1);
    }
    $tpl->out(2);
    $design->footer();
}

if ($menu->get(1) == 'finduser') {
    search_finduser();
    exit();
}

$such = '';
if ($menu->get(1) != '') {
    $such = $menu->get(1);
} elseif (isset($_REQUEST[ 'search' ])) {
    $such = $_REQUEST[ 'search' ];
}

if ($such == 'aubt' OR $such == 'augt' OR $such == 'aeit') {
    header('Location: index.php?forum-' . $such);
    exit();
}

$such = stripslashes(escape($such, 'string'));

$snac = 'Suche';
if ($such == 'augt' OR $such == 'aeit' OR $such == 'aubt') {
    $ar_s = array(
        'aubt' => 'unbeantworteten Themen',
        'aeit' => 'eigenen Beitr&auml;gen',
        'augt' => 'neue Themen seit dem letzten Besuch'
        );
    $snac = $ar_s[ $such ];
} elseif (isset($_REQUEST[ 'search' ])) {
    $snac = 'nach: ' . $such;
}

$title = $allgAr[ 'title' ] . ' :: Suchen :: ' . htmlentities($snac);
$hmenu = '<a class="smalfont" href="index.php?search">Suchen</a><b> &raquo; </b>' . htmlentities($snac);
$design = new design($title, $hmenu);
$design->header();

$tpl = new tpl('search');
$tpl->set('size', 30);

$gAnz = 0;
$autor = '';
if (isset($_GET[ 'autor' ])) {
    $autor = escape($_GET[ 'autor' ], 'string');
}
$tpl->set('autor', $autor);

if (isset($_GET[ 'in' ])) {
    for ($i = 1; $i <= 3; $i++) {
        if ($_GET[ 'in' ] == $i) {
            $tpl->set('checked' . $i, 'checked="checked"');
        }
    }
} else
    $tpl->set('checked1', 'checked="checked"');

if ($such != 'augt' AND $such != 'aeit' AND $such != 'aubt') {
    $tpl->set('search', escape_for_fields($such), 0);
}

if (isset($_GET[ 'days' ])) {
    $days = ($_GET[ 'days' ] == 0 ? 360 : intval($_GET[ 'days' ]));
} else
    $days = 360;
$days_ar = array(360 => 'alle Beitr&auml;ge (1 Jahr)',
    1 => '1 Tag',
    7 => '7 Tage',
    14 => '2 Wochen',
    30 => '1 Monat',
    90 => '3 Monate',
    180 => '6 Monate'
    );
$tpl->set('days', arlistee($days, $days_ar));
$tpl->out(0);

if (!empty($such) OR !empty($autor)) {
    $page = 1;
    if (isset($_GET[ 'page' ])) {
        $page = str_replace('-p', '', $_GET[ 'page' ]);
    }

    $limit = 25; // Limit
    $anfang = ($page - 1) * $limit;

    $x = time() - (3600 * 24 * $days);

    $such = str_replace('-', '', $such);
    $such = str_replace('=', '', $such);
    $such = str_replace('&', '', $such);

    $serar = explode(' ', $such);
    $str_forum = '';
    $str_forum_a = '';
    $str_news = '';
    $str_news_a = '';
    $str_downs = '';
    $str_downs_ = '';
    $str_downs_a = '';
    foreach ($serar as $v) {
        $str = str_replace('\'', '', $v);
        $str = str_replace('"', '', $str);
        $str = addslashes($str);
        if (!empty($str)) {
            if ($_GET[ 'in' ] == 1) {
                $str_forum .= "`txt` LIKE '%" . $str . "%' AND ";
            } elseif ($_GET[ 'in' ] == 2) {
                $str_news .= "`news_text` LIKE '%" . $str . "%' AND ";
            } elseif ($_GET[ 'in' ] == 3) {
                $str_downs .= "`descl` LIKE '%" . $str . "%' AND ";
                $str_downs_ .= "`name` LIKE '%" . $str . "%' AND ";
            }
        }
    }
    if (isset($_GET[ 'autor' ])) {
        if ($_GET[ 'in' ] == 1) {
            $str_forum_a .= "`c`.`erst` LIKE '%" . $autor . "%' AND ";
        } elseif ($_GET[ 'in' ] == 2) {
            $str_news_a .= "`name` LIKE '%" . $autor . "%' AND ";
        } elseif ($_GET[ 'in' ] == 3) {
            $str_downs_a .= "`creater` LIKE '%" . $autor . "%' AND ";
        }
    }
    // 1 = forum, ist immer standart
    $q = "
	  SELECT DISTINCT
        `a`.`fid` as `fid`,
        `a`.`name` as `titel`,
        'foru' as `typ`,
        `a`.`id` as `id`,
        `time`,
		`c`.`erst` as `autor`
      FROM `prefix_posts` `c`
        LEFT JOIN `prefix_topics` `a` ON `a`.`id` = `c`.`tid`
        LEFT JOIN `prefix_forums` `b` ON `b`.id = `a`.`fid`
        LEFT JOIN `prefix_groupusers` `vg` ON `vg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `vg`.`gid` = `b`.`view`
        LEFT JOIN `prefix_groupusers` `rg` ON `rg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `rg`.`gid` = `b`.`reply`
        LEFT JOIN `prefix_groupusers` `sg` ON `sg`.`uid` = " . $_SESSION[ 'authid' ] . " AND `sg`.`gid` = `b`.`start`
      WHERE (((`b`.`view` >= " . $_SESSION[ 'authright' ] . " AND `b`.`view` <= 0) OR
            (`b`.`reply` >= " . $_SESSION[ 'authright' ] . " AND `b`.`reply` <= 0) OR
            (`b`.`start` >= " . $_SESSION[ 'authright' ] . " AND `b`.`start` <= 0)) OR
            (`vg`.`fid` IS NOT NULL OR `rg`.`fid` IS NOT NULL OR `sg`.`fid` IS NOT NULL OR " . $_SESSION[ 'authright' ] . " = -9))
        AND (" . $str_forum . " 1 = 1)
		AND (" . $str_forum_a . " 1 = 1)
        AND (time >= " . $x . ")
      GROUP BY `a`.`id`
	  ORDER BY `time` DESC";
    if (isset($_GET[ 'in' ])) {
        if ($_GET[ 'in' ] == 2) {
            $q = "
	  SELECT DISTINCT
        '0' as `fid`,
        `news_title` as `titel`,
        'news' as `typ`,
        `news_id` as `id`,
        `news_time` as `time`,
		`prefix_user`.`name` as `autor`
      FROM `prefix_news`
	  	LEFT JOIN `prefix_user` ON `prefix_news`.`user_id` = `prefix_user`.`id`
      WHERE (" . $str_news . " 1 = 1)
	  	AND (" . $str_news_a . " 1 = 1)
        AND (`news_time` >= " . $x . ")
	  ORDER BY `time` DESC";
        } elseif ($_GET[ 'in' ] == 3) {
            $q = "
	  SELECT DISTINCT
        '0' as `fid`,
        CONCAT( `name`, ' ', `version` ) AS `titel`,
        'down' as `typ`,
        `id`,
        UNIX_TIMESTAMP(`time`) as `time`,
		`creater` as `autor`
      FROM `prefix_downloads`
      WHERE ((" . $str_downs . " 1 = 1)
	  	OR (" . $str_downs_ . " 1 = 1))
		AND (" . $str_downs_a . " 1 = 1)
        AND (UNIX_TIMESTAMP(`time`) >= " . $x . ")
	  ORDER BY UNIX_TIMESTAMP(`time`) DESC";
        }
    }

    $gAnz = db_num_rows(db_query($q));

    $q .= " LIMIT " . $anfang . "," . $limit;

    $MPL = db_make_sites($page, "", $limit, "index.php?search=" . urlencode($such) . "&autor=" . urlencode($autor) . "&in=" . $_GET[ 'in' ] . "&days=" . $days . "&page=", "", $gAnz);
    $tpl->set_ar_out(array(
            'MPL' => $MPL,
            'gAnz' => $gAnz
            ), 1);

    $q = db_query($q);
    $class = '';
    while ($r = db_fetch_assoc($q)) {
        $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
        $r[ 'class' ] = $class;
        if ($r[ 'typ' ] == 'foru') {
            $r[ 'ctime' ] = db_result(db_query("SELECT MAX(`time`) FROM `prefix_posts` WHERE `tid` = " . $r[ 'id' ]), 0, 0);
            $r[ 'ord' ] = forum_get_ordner($r[ 'ctime' ], $r[ 'id' ], $r[ 'fid' ]);
            $r[ 'link' ] = 'forum-showposts-' . $r[ 'id' ];
        } elseif ($r[ 'typ' ] == 'news') {
            $r[ 'ord' ] = 'ord';
            $r[ 'link' ] = 'news-' . $r[ 'id' ];
        } elseif ($r[ 'typ' ] == 'down') {
            $r[ 'ord' ] = 'ord';
            $r[ 'link' ] = 'downloads-show-' . $r[ 'id' ];
        }
        $tpl->set_ar_out($r, 2);
    }
    $tpl->out(3);
}

$design->footer();

?>