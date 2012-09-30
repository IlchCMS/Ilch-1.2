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

$title = $allgAr['title'].' :: Forum';
$hmenu = $extented_forum_menu.'Forum'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
$ilchbb_tpl = new tpl('forum_v12/load_extensions');
$ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

if ($menu->get(1) == 'markallasread') {
    $ilchBB->deleteNewTopics();
}


$tpl = new tpl ( 'forum_v12/showforum' );
$tpl->out (0);

$category_array = array();
$forum_array = array();

$q = "SELECT
  a.id, a.cid, a.name, a.besch,
  a.topics, a.posts, b.name as topic,
  c.id as pid, c.tid, b.rep, c.erst, c.time,
  a.cid, k.name as cname
FROM prefix_forums a
  LEFT JOIN prefix_forumcats k ON k.id = a.cid
  LEFT JOIN prefix_posts c ON a.last_post_id = c.id
  LEFT JOIN prefix_topics b ON c.tid = b.id
	
  LEFT JOIN prefix_groupusers vg ON vg.uid = ".$_SESSION['authid']." AND vg.gid = a.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = ".$_SESSION['authid']." AND rg.gid = a.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = ".$_SESSION['authid']." AND sg.gid = a.start
	
WHERE ((".$_SESSION['authright']." <= a.view AND a.view < 1) 
   OR (".$_SESSION['authright']." <= a.reply AND a.reply < 1)
   OR (".$_SESSION['authright']." <= a.start AND a.start < 1)
	 OR vg.fid IS NOT NULL
	 OR rg.fid IS NOT NULL
	 OR sg.fid IS NOT NULL
	 OR -9 = ".$_SESSION['authright'].")
	 AND k.cid = 0
ORDER BY k.pos, a.pos";
$erg1 = db_query($q);
$xcid = 0;

while ($r = db_fetch_assoc($erg1) ) {

    // IlchBB Forum 3.1 :: Get Forum Status :: Start
    $ord = $ilchBB->checkNewTopics($r['id']);

    if ($ord === TRUE) {
        $r['ORD'] = 'forum_unread';
        $r['TORD'] = 'Neue Beitr&auml;ge';
    } else {
        $r['ORD'] = 'forum_read';
        $r['TORD'] = 'Keine neuen Beitr&auml;ge';
    }
    // IlchBB Forum 3.1 :: Get Forum Status :: Ende
    
    $r['topicl'] = $r['topic'];
    $r['topic']  = html_enc_substr($r['topic'],0,23);
    $r['mods']   = getmods($r['id']);
    $r['datum']  = date('d.m.y - H:i', $r['time']);
    $r['page']   = ceil ( ($r['rep']+1)  / $allgAr['Fpanz'] );
    $tpl->set_ar($r);

    if ($r['cid'] <> $xcid) {
        $tpl->out(1);

        // IlchBB Forum 3.1 :: Set CSS Class :: Start
        $class = 'ilchbb_Cmite';
        // IlchBB Forum 3.1 :: Set CSS Class :: End

        //Unterkategorien
        $sql = db_query("SELECT DISTINCT a.name as cname, a.id as cid FROM `prefix_forumcats` a LEFT JOIN `prefix_forums` b ON a.id = b.cid WHERE a.cid = {$r['cid']} AND a.id = b.cid ORDER BY a.pos, a.name");
        while ($ucat = db_fetch_assoc($sql)) {

            // IlchBB Forum 3.1 :: Change CSS Class :: Start
            $class = ( $class == 'ilchbb_Cmite' ? 'ilchbb_Cnorm' : 'ilchbb_Cmite' );
            $ucat['class'] = $class;
            // IlchBB Forum 3.1 :: Change CSS Class :: Start

            $tpl->set_ar_out($ucat,2);
        }
        //Unterkategorien - Ende
        $xcid = $r['cid'];
    }

    // IlchBB Forum 3.1 :: Change CSS Class :: Start
    $class = ( $class == 'ilchbb_Cmite' ? 'ilchbb_Cnorm' : 'ilchbb_Cmite' );
    $r['class'] = $class;
    // IlchBB Forum 3.1 :: Change CSS Class :: Start

    $tpl->set_ar_out($r,3);
}

// IlchBB Forum 3.1 :: Online Today :: Start
if ($allgAr['ilchbb_forum_dayonline'] == 1) {
    $time = mktime (0,0,0,date("n"),date("j"),date("Y"));

    $query = 'SELECT `id`,`name` FROM `prefix_user` WHERE llogin > '.$time;
    $query = db_query($query);

    $cache = '';

    while ($row = db_fetch_assoc($query)) {
        if (!empty($cache)) $cache .= ', ';
        $cache .= '<a href="index.php?user-details-'.$row['id'].'">'.$row['name'].'</a>';
    }

    $tpl->set('dayonline','<br /><br />Heute waren bereits online:<br />'.$cache);
} else {
    $tpl->set('dayonline','');
}
// IlchBB Forum 3.1 :: Online Today :: End

# statistic #
$ges_online_user = ges_online();
$stats_array = array (
        'privmsgpopup' => check_for_pm_popup (),
        'topics' => db_result(db_query("SELECT COUNT(ID) FROM `prefix_topics`"),0),
        'posts' => db_result(db_query("SELECT COUNT(ID) FROM `prefix_posts`"),0),
        'users' => db_result(db_query("SELECT COUNT(ID) FROM `prefix_user`"),0),
        'istsind' => ( $ges_online_user > 1 ? 'sind' : 'ist' ),
        'gesonline' => $ges_online_user,
        'gastonline' => ges_gast_online(),
        'useronline' => ges_user_online(),
        'userliste' => user_online_liste()
);

$tpl->set_ar_out($stats_array,4);

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();
?>