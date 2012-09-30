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

$such = $menu->get(1);

if ($such == 'aeit') {
    if (isset($_POST['name'])) {
        $name = escape($_POST['name'],'string');
        $uid = @db_result(db_query("SELECT id FROM prefix_user WHERE name = BINARY '".$name."'"));
        if ($uid > 0) {
            $menu->set_url(2,$uid);
        }
    }
    if ($menu->get(2) >= 1 AND $menu->get(2) != $_SESSION['authid']) {
        $uid = $menu->get(2);
        $name = get_n($uid);
        $mtitle = $lang['posts'].' '.$lang['from'].' '.$name;
    } else {
        $uid = $_SESSION['authid'];
        $mtitle = $lang['ownposts'];
        $name = '';
    }
} elseif ($such == 'aubt') {
    $mtitle = $lang['topicwithnoreply'];
} else {
    $mtitle = $lang['newtopicssincelastvisit'];
}

$title = $allgAr['title'].' :: Forum :: '.$mtitle;
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b> '.$mtitle;
$design = new design ( $title , $hmenu, 1);
$design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
$ilchbb_tpl = new tpl('forum_v12/load_extensions');
$ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

# mehrere seiten falls gefordert
$limit = 25;  // Limit
$page = ($menu->getE('p') > 0 ? $menu->getE('p') : 1 );
$anfang = ($page - 1) * $limit;

$s = "DISTINCT a.art, a.stat, a.rep, b.id as fid, a.name as titel, a.id as id, d.name as author";
$q = "SELECT {SELECT}
  FROM prefix_topics a
    LEFT JOIN prefix_forums b ON b.id = a.fid
    LEFT JOIN prefix_posts c ON c.tid = a.id
    LEFT JOIN prefix_user d ON c.erstid = d.id
    LEFT JOIN prefix_groupusers vg ON vg.uid = ".$_SESSION['authid']." AND vg.gid = b.view
    LEFT JOIN prefix_groupusers rg ON rg.uid = ".$_SESSION['authid']." AND rg.gid = b.reply
    LEFT JOIN prefix_groupusers sg ON sg.uid = ".$_SESSION['authid']." AND sg.gid = b.start
  WHERE (((b.view >= ".$_SESSION['authright']." AND b.view <= 0) OR
            (b.reply >= ".$_SESSION['authright']." AND b.reply <= 0) OR
            (b.start >= ".$_SESSION['authright']." AND b.start <= 0)) OR
            (vg.fid IS NOT NULL OR rg.fid IS NOT NULL OR sg.fid IS NOT NULL OR ".$_SESSION['authright']." = -9))
     AND {WHERE}
  ORDER BY c.time DESC";
$q2 = "SELECT DISTINCT a.art, a.stat, a.rep, b.id as fid, a.name as titel, a.id as id, MIN(c.id) AS firstnew, d.name as author
    FROM prefix_topics a
      LEFT JOIN prefix_forums b ON b.id = a.fid
      LEFT JOIN prefix_posts c ON c.tid = a.id
      LEFT JOIN prefix_user d ON c.erstid = d.id
      LEFT JOIN prefix_groupusers vg ON vg.uid = ".$_SESSION['authid']." AND vg.gid = b.view
      LEFT JOIN prefix_groupusers rg ON rg.uid = ".$_SESSION['authid']." AND rg.gid = b.reply
      LEFT JOIN prefix_groupusers sg ON sg.uid = ".$_SESSION['authid']." AND sg.gid = b.start
    WHERE (((b.view >= ".$_SESSION['authright']." AND b.view <= 0) OR
            (b.reply >= ".$_SESSION['authright']." AND b.reply <= 0) OR
            (b.start >= ".$_SESSION['authright']." AND b.start <= 0)) OR
            (vg.fid IS NOT NULL OR rg.fid IS NOT NULL OR sg.fid IS NOT NULL OR ".$_SESSION['authright']." = -9))
      AND {WHERE}
    GROUP BY b.id,a.id, a.name
    ORDER BY c.time DESC";
$x = time() - (3600 * 24 * 360);
if ($such == 'aubt') {
    $where = "c.time >= ". $x ." AND a.rep = 0";
    $gAnz  = @db_result(db_query(str_replace('{WHERE}',$where,str_replace('{SELECT}',' COUNT(DISTINCT a.id)',$q))),0);
    $q     = str_replace('{WHERE}',$where,str_replace('{SELECT}',$s,$q));
} elseif ($such == 'augt') {
    $where = "`a`.`id` IN (".$ilchBB->showNewTopics().")";
    $gAnz  = @db_result(db_query(str_replace('{WHERE}',$where,str_replace('{SELECT}',' COUNT(DISTINCT a.id)',$q))),0);
    $q     = str_replace('{WHERE}',$where,str_replace('{SELECT}',$s,$q2));
} elseif ($such == 'aeit') {
    $where = "c.time >= ". $x ." AND c.erstid = ".$uid;
    $gAnz  = @db_result(db_query(str_replace('{WHERE}',$where,str_replace('{SELECT}',' COUNT(DISTINCT a.id)',$q))),0);
    $q     = str_replace('{WHERE}',$where,str_replace('{SELECT}',$s,$q));
}
$MPL = db_make_sites ($page , "", $limit , 'index.php?forum-'.$such.($such == 'aeit' ? '-'.$uid : '') , "" , $gAnz);

$tpl = new tpl('forum_v12/search');
$q = db_query($q." LIMIT $anfang,$limit");
$class = '';
$tpl->set_out('gAnz',$gAnz,0);
while($r = db_fetch_assoc($q) ) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
    $r['class'] = $class;
    $r['ctime'] = db_result(db_query("SELECT MAX(time) FROM prefix_posts WHERE tid = ".$r['id']),0,0);
    $r['link'] = 'forum-showposts-'.$r['id'];

    // IlchBB Forum 3.1 :: Get Forum Status :: Start
    $ord = $ilchBB->checkNewTopics($r['fid'], $r['id']);

    // Sticky or Topic
    if ($r['art'] == 1) {
        $r['ORD'] = 'sticky';
        $r['TORD'] = 'Ank&uuml;ndigung';
    } else {
        $r['ORD'] = 'topic';
        $r['TORD'] = 'Thema';
    }

    // Unread or Read
    if ($ord === TRUE) {
        $r['ORD'] .= '_unread';
        $r['TORD'] .= ', neue Beitr&auml;ge';
        $r['NPOS'] = '<a href="index.php?forum-showposts-'.$r['id'].'-firstnew"><img src="include/images/ilchbb_forum/icon_topic_newest.gif" title="Neuster Beitrag" border="0" /></a> ';
    } else {
        $r['ORD'] .= '_read';
        $r['TORD'] .= ', keine neuen Beitr&auml;ge';
        $r['NPOS'] = '';
    }

    // Locked or Hot Topic?
    if ($r['stat'] == 0) {
        $r['ORD'] .= '_locked';
        $r['TORD'] .= ', geschlossen';
    } else if ($r['rep'] >= $allgAr['ilchbb_forum_hottopic'] AND $r['art'] == 0) {
        $r['ORD'] .= '_hot';
        $r['TORD'] .= ', viel diskutiert';
    }
    // IlchBB Forum 3.1 :: Get Forum Status :: Ende

    if ($menu->get(1) == 'aeit') {
        $r['author'] = '';
    } elseif ($such == 'aubt') {
        $r['author'] = ' '.$lang['from'].' '.$r['author'];
    } else {
        $r['author'] = ' '.$lang['newpost'].' '.$lang['from'].' '.$r['author'];
        $r['postsbefore'] = db_count_query('SELECT COUNT(id) FROM prefix_posts WHERE tid = '.$r['id'].' AND id < '.$r['firstnew']);
        $r['page'] = ceil(($r['postsbefore']+1)/$allgAr['Fpanz']);
        $r['link'] .= '-p'.$r['page'].'#'.$r['firstnew'];
    }

    $tpl->set_ar_out($r,1);
}
$tpl->set_out('MPL',$MPL,2);
if ($such == 'aeit') {
    $tpl->set_out('name',$name,3);
}

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();
?>