<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

if ($aktTopicRow['stat'] == 0 OR $forum_rights['reply'] == false) {
    if ($aktTopicRow['stat'] == 0 AND $_SESSION['authright'] > '-7') {
        if ($forum_rights['mods'] == false)
            $forum_failure[] = $lang['topicclosed'];
    } elseif ($aktTopicRow['stat'] != 0 AND $_SESSION['authright'] > '-7') {
        if ($forum_rights['mods'] == false)
            $forum_failure[] = $lang['nopermission'];
    }
    check_forum_failure($forum_failure);
}

$title = $allgAr['title'] . ' :: Forum :: ' . aktForumCats($aktForumRow['kat'], 'title') . ' :: ' . $aktForumRow['name'] . ' :: neuer Beitrag';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats($aktForumRow['kat']) . '<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-' . $fid . '">' . $aktForumRow['name'] . '</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-' . $tid . '">' . $aktTopicRow['name'] . '</a>' . $extented_forum_menu_sufix;

$dppk_time = time();
$time = time();
if (!isset($_SESSION['klicktime'])) {
    $_SESSION['klicktime'] = 0;
}

$topic = '';
$txt = '';
$xnn = '';

if (isset($_POST['txt'])) {
    $txt = trim(escape($_POST['txt'], 'textarea'));
}
if (isset($_POST['Gname'])) {
    $xnn = trim(escape_nickname($_POST['Gname']));
}

if (($_SESSION['klicktime'] + 15) > $dppk_time OR empty($txt) OR !empty($_POST['priview']) OR (empty($_POST['Gname']) AND !loggedin()) OR !chk_antispam ('newpost')) {
    $design = new design ($title , $hmenu, 1);
    $design->header();

    $name = '';
    if (!loggedin()) {
        $name = '<tr><td class="Cmite"0><b>' . $lang['name'] . '</b></td>';
        $name .= '<td class="Cnorm"><input type="text" value="' . unescape($xnn) . '" maxlength="15" name="Gname"></td></tr>';
    }

    $tpl = new tpl ('forum/newpost');

    $xtext = '';
    if ($menu->getA(3) == 'z') {
        $row = db_fetch_object(db_query("SELECT txt,erst FROM prefix_posts WHERE id = " . $menu->getE(3)));
        $xtext = '[quote=' . escape_nickname($row->erst) . ']' . "\n" . $row->txt . "\n[/quote]";
    }

    if ($menu->getA(3) == 'f') {
        $r = db_fetch_assoc(db_query("SELECT id,text,title FROM prefix_faqs WHERE id = " . $menu->getE(3)));
        $xtext = 'FAQ Artikel: [url=index.php?faqs-s' . $r['id'] . '#FAQ' . $r['id'] . ']' . $r['title'] . '[/url]' . "\n" . unescape($r['text']);
    }

    if (isset($_POST['priview'])) {
        $tpl->set_out('txt', bbcode(unescape($txt)), 0);
    }
    if (empty($txt)) {
        $txt = $xtext;
    }

    $tpl = new tpl ('forum/newpost');

    $ar = array (
        'txt' => escape_for_fields(unescape($txt)),
        'tid' => $tid,
        'name' => $name,
        'SMILIES' => getsmilies(),
        'antispam' => get_antispam('newpost', 1)
        );

    $tpl->set_ar_out($ar, 1);

    $erg = db_query('SELECT erst, txt FROM `prefix_posts` WHERE tid = "' . $tid . '" ORDER BY time DESC LIMIT 0,5');
    while ($row = db_fetch_assoc($erg)) {
        $row['txt'] = bbcode($row['txt']);
        $tpl->set_ar_out($row, 2);
    }
    $tpl->out(3);
} else {
    // save post
    $_SESSION['klicktime'] = $dppk_time;

    $design = new design ($title , $hmenu, 1);
    $design->header();

    if (loggedin()) {
        $uid = $_SESSION['authid'];
        $erst = escape($_SESSION['authname'], 'string');
        db_query("UPDATE `prefix_user` set posts = posts+1 WHERE id = " . $uid);
    } else {
        $erst = $xnn;
        $uid = 0;
    }
    // topic alert ausfuehren.
    $topic_alerts_abf = "SELECT
      prefix_topics.name as topic,
      prefix_user.email as email,
      prefix_user.name as user,
      prefix_user.id as uid
    FROM prefix_topic_alerts
      LEFT JOIN prefix_topics ON prefix_topics.id = prefix_topic_alerts.tid
      LEFT JOIN prefix_user   ON prefix_user.id   = prefix_topic_alerts.uid
    WHERE prefix_topic_alerts.tid = " . $tid;

    $topic_alerts_erg = db_query($topic_alerts_abf);
    while ($topic_alerts_row = db_fetch_assoc($topic_alerts_erg)) {
        if ($uid == $topic_alerts_row['uid']) continue;
        $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
        $text = sprintf ($lang['topicalertmessage'], $topic_alerts_row['user'], $topic_alerts_row['topic'], $page, $tid);
        icmail ($topic_alerts_row['email'], 'neue Antwort im Thema: "' . $topic_alerts_row['topic'] . '"', $text);
        debug ($topic_alerts_row['email']);
    }
    db_query("DELETE FROM prefix_topic_alerts WHERE tid = " . $tid);
    // topic alert insert wenn gewaehlt.
    if (!empty($_POST['topic_alert']) AND $_POST['topic_alert'] == 'yes' AND loggedin()) {
        if (0 == db_result(db_query("SELECT COUNT(*) FROM prefix_topic_alerts WHERE uid = " . $_SESSION['authid'] . " AND tid = " . $tid), 0)) {
            db_query("INSERT INTO prefix_topic_alerts (tid,uid) VALUES (" . $tid . ", " . $_SESSION['authid'] . ")");
        }
    }
    // topic alert ende
    db_query ("INSERT INTO `prefix_posts` (tid,fid,erst,erstid,time,txt) VALUES ( " . $tid . ", " . $fid . ", '" . $erst . "', " . $uid . ", " . $time . ", '" . $txt . "')");
    $pid = db_last_id();

    db_query("UPDATE `prefix_topics` SET last_post_id = " . $pid . ", rep = rep + 1 WHERE id = " . $tid);
    db_query("UPDATE `prefix_forums` SET posts = posts + 1, last_post_id = " . $pid . " WHERE id = " . $fid);

    $page = ceil (($aktTopicRow['rep'] + 1) / $allgAr['Fpanz']);
    // toipc als gelesen markieren
    $_SESSION['forumSEE'][$fid][$tid] = time();

    wd (array ($lang['backtotopic'] => 'index.php?forum-showposts-' . $tid . '-p' . $page . '#' . $pid,
            $lang['backtotopicoverview'] => 'index.php?forum-showtopics-' . $fid
            ) , $lang['createpostsuccessful'] , 3);
}

$design->footer();

?>