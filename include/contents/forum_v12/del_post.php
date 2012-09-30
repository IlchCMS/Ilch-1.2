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

if ( $forum_rights['mods'] == FALSE ) {
    $forum_failure[] = 'Keine Berechtigung dieses Forum zu moderiren';
    check_forum_failure($forum_failure);
}


$title = $allgAr['title'].' :: Forum :: '.$aktForumRow['kat'].' :: '.$aktForumRow['name'].' :: '.$aktTopicRow['name'].' :: Beitrag l&ouml;schen';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showcat-'.$aktForumRow['cid'].'">'.$aktForumRow['kat'].'</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-'.$tid.'">'.$aktTopicRow['name'].'</a> <b> &raquo; </b>Beitrag l&ouml;schen'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
$ilchbb_tpl = new tpl('forum_v12/load_extensions');
$ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

$postid = escape($menu->get(3), 'integer');
if ( empty($_POST['delete']) ) {
    $tpl = new tpl ( 'forum_v12/del_post' );
    $tpl->set_ar(array('tid'=>$tid,'get3'=>$postid));
    $tpl->out(0);
} else {
    $erstid = @db_result(db_query("SELECT erstid FROM `prefix_posts` WHERE id = ".$postid." LIMIT 1"),0);
    if ($erstid > 0) db_query("UPDATE `prefix_user` SET posts = posts - 1 WHERE id = $erstid");

    db_query("DELETE FROM `prefix_posts` WHERE id = ".$postid." LIMIT 1");
    $erg = db_query("SELECT MAX(id) FROM prefix_posts WHERE tid = ".$tid );
    $max = db_result($erg,0);
    db_query("UPDATE `prefix_topics` SET last_post_id = ".$max.", `rep` = `rep` - 1 WHERE id = ".$tid );
    db_query("UPDATE `prefix_forums` SET last_post_id = ".$max.", posts = posts - 1 WHERE id = ".$fid );

    $tpl = new tpl ( 'forum_v12/del_post' );
    $tpl->set_out('tid',$tid,1);
}

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();

?>