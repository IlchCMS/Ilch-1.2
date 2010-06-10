<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

if ($forum_rights[ 'start' ] == false) {
    $forum_failure[ ] = $lang[ 'nopermission' ];
    check_forum_failure($forum_failure);
}

$title = $allgAr[ 'title' ] . ' :: Forum :: ' . aktForumCats($aktForumRow[ 'kat' ], 'title') . ' :: ' . $aktForumRow[ 'name' ] . ' :: neues Thema';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats($aktForumRow[ 'kat' ]) . '<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-' . $fid . '">' . $aktForumRow[ 'name' ] . '</a>' . $extented_forum_menu_sufix;

$dppk_time = time();
$time = time();
if (!isset($_SESSION[ 'klicktime' ])) {
    $_SESSION[ 'klicktime' ] = 0;
}

$topic = '';
$txt = '';
$xnn = '';

if (isset($_POST[ 'topic' ])) {
    $topic = trim(escape($_POST[ 'topic' ], 'string'));
}
if (isset($_POST[ 'txt' ])) {
    $txt = trim(escape($_POST[ 'txt' ], 'textarea'));
}
if (isset($_POST[ 'Gname' ])) {
    $xnn = trim(escape_nickname($_POST[ 'Gname' ]));
}

if (($_SESSION[ 'klicktime' ] + 15) > $dppk_time OR empty($topic) OR empty($txt) OR !empty($_POST[ 'priview' ]) OR (empty($_POST[ 'Gname' ]) AND !loggedin()) OR !chk_antispam('newtopic')) {
    $design = new design($title, $hmenu, 1);
    $design->header();

    $tpl = new tpl('forum/newtopic');

    $name = '';
    if (!loggedin()) {
        $name = '<tr><td class="Cmite"0><b>' . $lang[ 'name' ] . '</b></td>';
        $name .= '<td class="Cnorm"><input type="text" value="' . unescape($xnn) . '" maxlength="15" name="Gname"></td></tr>';
    }

    if (isset($_POST[ 'priview' ])) {
        $tpl->set_out('txt', bbcode(unescape($txt)), 0);
    }

    $ar = array(
        'name' => $name,
        'txt' => escape_for_fields(unescape($txt)),
        'topic' => escape_for_fields(unescape($topic)),
        'fid' => $fid,
        'SMILIES' => getsmilies(),
        'antispam' => get_antispam('newtopic', 1)
        );
    $tpl->set_ar_out($ar, 1);
} else {
    // save toipc
    $_SESSION[ 'klicktime' ] = $dppk_time;

    $design = new design($title, $hmenu, 0);
    $design->header();

    if (loggedin()) {
        $uid = $_SESSION[ 'authid' ];
        $erst = escape($_SESSION[ 'authname' ], 'string');
        db_query("UPDATE `prefix_user` SET `posts` = `posts`+1 WHERE `id` = " . $uid);
    } else {
        $erst = $xnn;
        $uid = 0;
    }

    db_query("INSERT INTO `prefix_topics` (`fid`, `name`, `erst`, `stat`) VALUES ( " . $fid . ", '" . $topic . "', '" . $erst . "', 1 )");
    $tid = db_last_id();
    // topic alert
    if (!empty($_POST[ 'topic_alert' ]) AND $_POST[ 'topic_alert' ] == 'yes' AND loggedin()) {
        if (0 == db_result(db_query("SELECT COUNT(*) FROM `prefix_topic_alerts` WHERE `uid` = " . $_SESSION[ 'authid' ] . " AND `tid` = " . $tid), 0)) {
            db_query("INSERT INTO `prefix_topic_alerts` (`tid`,`uid`) VALUES (" . $tid . ", " . $_SESSION[ 'authid' ] . ")");
        }
    }

    db_query("INSERT INTO `prefix_posts` (`tid`,`fid`,`erst`,`erstid`,`time`,`txt`) VALUES ( " . $tid . ", " . $fid . ", '" . $erst . "', " . $uid . ", " . $time . ", '" . $txt . "')");
    $pid = db_last_id();

    db_query("UPDATE `prefix_topics` SET `last_post_id` = " . $pid . " WHERE `id` = " . $tid);
    db_query("UPDATE `prefix_forums` SET `posts` = `posts` + 1, `last_post_id` = " . $pid . ", `topics` = `topics` + 1 WHERE `id` = " . $fid);
    // toipc als gelesen markieren
    $_SESSION[ 'forumSEE' ][ $fid ][ $tid ] = time();

    wd('index.php?forum-showposts-' . $tid, $lang[ 'createtopicsuccessful' ]);
}

$design->footer();

?>