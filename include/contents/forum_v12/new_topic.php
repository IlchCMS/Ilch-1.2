<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

/**
 * @name    IlchBB Forum
 * @version 3.1
 * @author  Florian Koerner
 * @link    http://www.koerner-ws.de/
 * @license GNU General Public License
 */


defined ('main') or die ( 'no direct access' );

// IlchBB Forum 3.1 :: Loader :: Start
require_once ('include/contents/forum_v12/loader.php');
// IlchBB Forum 3.1 :: Loader :: Ende

if ( $forum_rights['start'] == FALSE ) {
    $forum_failure[] = $lang['nopermission'];
    check_forum_failure($forum_failure);
}

$title = $allgAr['title'].' :: Forum :: '.aktForumCats($aktForumRow['kat'],'title').' :: '.$aktForumRow['name'].' :: neues Thema';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).'<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a>'.$extented_forum_menu_sufix;

$dppk_time = time();
$time = time();
if (!isset($_SESSION['klicktime'])) {
    $_SESSION['klicktime'] = 0;
}

$topic = '';
$txt   = '';
$xnn   = '';

if (isset($_POST['topic'])) {
    $topic = trim(escape($_POST['topic'], 'string'));
}
if (isset($_POST['txt'])) {
    $txt = trim(escape($_POST['txt'], 'textarea'));
}
if (isset($_POST['Gname'])) {
    $xnn = trim(escape_nickname($_POST['Gname']));
}

if (($_SESSION['klicktime'] + 15) > $dppk_time OR empty($topic) OR empty($txt) OR !empty($_POST['priview']) OR (empty($_POST['Gname']) AND !loggedin()) OR !chk_antispam ('newtopic')) {

    $design = new design ( $title , $hmenu, 1);
    $design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
    $ilchbb_tpl = new tpl('forum_v12/load_extensions');
    $ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

    $tpl = new tpl ( 'forum_v12/newtopic' );

    $name = '';
    if ( !loggedin() ) {
        $name = '<label for="Gname">Name:</label><input type="text" value="'.unescape($xnn).'" size="40" maxlength="15" name="Gname"><br />';
    }

    if (isset($_POST['priview'])) {
        $tpl->set_out('txt', bbcode(unescape($txt)), 0);
    }

    // IlchBB Forum 3.1 :: Antispam :: Start
    if (loggedin()) {
        $antiH = '';
    } else {
        $antiH = '<h5>Antispam</h5>';
    }
    // IlchBB Forum 3.1 :: Antispam :: End

    $ar = array (
            'name'    => $name,
            'txt'     => escape_for_fields(unescape($txt)),
            'topic'   => escape_for_fields(unescape($topic)),
            'fid'     => $fid,
            'SMILIES' => getsmilies(),
            'antispam'=> $antiH.get_antispam('newtopic',100)
    );
    $tpl->set_ar_out($ar,1);

} else {

    # save toipc
    $_SESSION['klicktime'] = $dppk_time;

    $design = new design ( $title , $hmenu);
    $design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
    $ilchbb_tpl = new tpl('forum_v12/load_extensions');
    $ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

    if ( loggedin()) {
        $uid = $_SESSION['authid'];
        $erst = escape($_SESSION['authname'],'string');
        db_query("UPDATE `prefix_user` set posts = posts+1 WHERE id = ".$uid);
    } else {
        $erst = $xnn;
        $uid = 0;
    }

    db_query("INSERT INTO `prefix_topics` (fid, name, erst, stat) VALUES ( ".$fid.", '".$topic."', '".$erst."', 1 )");
    $tid = db_last_id();

    # topic alert
    if (!empty($_POST['topic_alert']) AND $_POST['topic_alert'] == 'yes' AND loggedin()) {
        if (0 == db_result(db_query("SELECT COUNT(*) FROM prefix_topic_alerts WHERE uid = ".$_SESSION['authid']." AND tid = ".$tid),0)) {
            db_query("INSERT INTO prefix_topic_alerts (tid,uid) VALUES (".$tid.", ".$_SESSION['authid'].")");
        }
    }

    db_query ("INSERT INTO `prefix_posts` (tid,fid,erst,erstid,time,txt) VALUES ( ".$tid.", ".$fid.", '".$erst."', ".$uid.", ".$time.", '".$txt."')");
    $pid = db_last_id();

    db_query("UPDATE `prefix_topics` SET last_post_id = ".$pid." WHERE id = ".$tid);
    db_query("UPDATE `prefix_forums` SET posts = posts + 1, last_post_id = ".$pid.", topics = topics + 1 WHERE id = ".$fid);

    wd('index.php?forum-showposts-'.$tid,$lang['createtopicsuccessful']);
}

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();
?>