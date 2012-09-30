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

# check ob ein fehler aufgetreten ist.
check_forum_failure($forum_failure);

$title = $allgAr['title'].' :: Forum :: '.aktForumCats($aktForumRow['kat'],'title').' :: '.$aktForumRow['name'];
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).'<b> &raquo; </b>'.$aktForumRow['name'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
$ilchbb_tpl = new tpl('forum_v12/load_extensions');
$ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

$limit = $allgAr['Ftanz'];  // Limit 
$page = ( $menu->getA(3) == 'p' ? $menu->getE(3) : 1 );
$MPL = db_make_sites ($page , "WHERE fid = '$fid'" , $limit , '?forum-showtopics-'.$fid , 'topics' );
$anfang = ($page - 1) * $limit;

$tpl = new tpl ( 'forum_v12/showtopic' );
$tpl->set('CATNAME' ,$aktForumRow['name']);

if ( $forum_rights['start'] == TRUE ) {
    $tpl->set('NEWTOPIC', '<div class="button_topic_new" style="float: left;"><a href="index.php?forum-newtopic-'.$fid.'"></a></div>' );
} else {
    $tpl->set('NEWTOPIC','');
}
$tpl->set('MPL', $MPL);
$tpl->set_out('FID', $fid, 0);

$q = "SELECT a.id, a.name, a.rep, a.erst, a.hit, a.art, a.stat, b.time, b.erst as last, b.id as pid
	FROM prefix_topics a
	LEFT JOIN prefix_posts b ON a.last_post_id = b.id
	WHERE a.fid = {$fid}
	ORDER BY a.art DESC, b.time DESC
	LIMIT ".$anfang.",".$limit;
$erg = db_query($q);
if ( db_num_rows($erg) > 0 ) {

    // IlchBB Forum 3.1 :: Set CSS Class :: Start
    $class = 'ilchbb_Cmite';
    // IlchBB Forum 3.1 :: Set CSS Class :: End

    while($row = db_fetch_assoc($erg) ) {

        // IlchBB Forum 3.1 :: Get Forum Status :: Start
        $ord = $ilchBB->checkNewTopics($fid, $row['id']);

        // Sticky or Topic
        if ($row['art'] == 1) {
            $row['ORD'] = 'sticky';
            $row['TORD'] = 'Ank&uuml;ndigung';
        } else {
            $row['ORD'] = 'topic';
            $row['TORD'] = 'Thema';
        }

        // Unread or Read
        if ($ord === TRUE) {
            $row['ORD'] .= '_unread';
            $row['TORD'] .= ', neue Beitr&auml;ge';
            $row['NPOS'] = '<a href="index.php?forum-showposts-'.$row['id'].'-firstnew"><img src="include/images/ilchbb_forum/icon_topic_newest.gif" title="Neuster Beitrag" border="0" /></a> ';
        } else {
            $row['ORD'] .= '_read';
            $row['TORD'] .= ', keine neuen Beitr&auml;ge';
            $row['NPOS'] = '';
        }

        // Locked or Hot Topic?
        if ($row['stat'] == 0) {
            $row['ORD'] .= '_locked';
            $row['TORD'] .= ', geschlossen';
        } else if ($row['rep'] >= $allgAr['ilchbb_forum_hottopic'] AND $row['art'] == 0) {
            $row['ORD'] .= '_hot';
            $row['TORD'] .= ', viel diskutiert';
        }
        // IlchBB Forum 3.1 :: Get Forum Status :: Ende

        $row['date'] = date('d.m.y - H:i',$row['time']);
        $row['page'] = ceil ( ($row['rep']+1)  / $allgAr['Fpanz'] );

        // IlchBB Forum 3.1 :: Change CSS Class :: Start
        $class = ( $class == 'ilchbb_Cmite' ? 'ilchbb_Cnorm' : 'ilchbb_Cmite' );
        $tpl->set('class',$class);
        // IlchBB Forum 3.1 :: Change CSS Class :: Start

        $tpl->set_ar_out($row,1);

    }
}
else {
    echo '<tr><td colspan="6" class="ilchbb_Cnorm"><b>keine Eintr&auml;ge vorhanden</b></td></tr>';
}

$tpl->out(2);
if ( $forum_rights['mods'] == TRUE ) {
    $tpl->set('id', $fid);
    $tpl->out(3);
}

$tpl->out(4);

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();
?>