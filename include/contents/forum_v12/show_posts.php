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

// IlchBB Forum 3.1 :: Newest Post :: Start
if ($menu->get(3) == 'firstnew') {
    $check = $ilchBB->checkNewTopics($fid, $tid);

    if ($check == TRUE) {
        list($page, $post) = $ilchBB->newestPostPage($fid, $tid, $allgAr['Fpanz']);
        header('Location: index.php?forum-showposts-'.$tid.'-p'.$page.'#'.$post);
        exit;
    }
}
// IlchBB Forum 3.1 :: Newest Post :: End


// IlchBB Forum 3.1 :: Post Report :: Start
if ($menu->get(2) == 'reportpost' AND isset($_POST['tid']) AND isset($_POST['pid']) AND isset($_POST['page']) AND isset($_POST['grounds'])) {
    
    // Abbruch, wenn nicht eingeloggt
    if (!loggedin()) {
        echo 'Du musst eingeloggt sein, um einen Beitrag zu melden.';
        exit;
    }

    // Alle Daten escapen
    $Ptid = escape($_POST['tid'],'integer');
    $Ppid = escape($_POST['pid'],'integer');
    $Ppage = escape($_POST['page'],'integer');
    $grounds = utf8_decode(($_POST['grounds']));

    // Abbruch, wenn Daten fehlen
    if (!is_numeric($Ptid) OR !is_numeric($Ppid) OR empty($grounds)) {
        echo 'Bitte alle Felder ausfuellen!';
        exit;
    }

    // PM an Admin senden
    $link = $_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"].'?forum-showposts-'.$Ptid.'-p'.$Ppage.'#'.$Ppid;
    $txt = escape("[b]Grund der Meldung[/b]\n".$grounds."\n\n[b]Beitrag[/b]\n[url]".$link."[/url]",'textarea');
    sendpm($_SESSION['authid'], 1, 'Gemeldeter Beitrag', $txt);

    // Return
    echo 'Vielen Dank fuer den Hinweis. Dieser wird umgehend bearbeitet.';
    exit;
}
// IlchBB Forum 3.1 :: Post Report :: End

// IlchBB Forum 3.1 :: Post Rate :: Start
if ($menu->get(2) == 'ratepost' AND isset($_POST['pid']) AND $allgAr['ilchbb_forum_ratepost'] == 1) {

    // Abbruch, wenn nicht eingeloggt
    if (!loggedin()) {
        echo 'Du musst eingeloggt sein, um einen Beitrag gut zu finden.';
        exit;
    }

    // Zeitspanne - Session setzen, wenn nicht vorhanden
    if (!isset($_SESSION['ilchbb_ratetime'])) $_SESSION['ilchbb_ratetime'] = 0;

    // Zeitspanne testen
    if ($_SESSION['ilchbb_ratetime']+$allgAr['ilchbb_forum_ratetime'] >= time()) {
        echo 'Du bewertes die Beitraege zu schnell. Bitte beurteile die Beitraege bedacht.';
        exit;
    }

    // Alle Daten escapen
    $Ppid = escape($_POST['pid'],'integer');

    // Abbruch, wenn Daten fehlen
    if (!is_numeric($Ppid)) {
        echo 'Hoppla. Da lief etwas schief!';
        exit;
    }

    // MySQL Abfrage nach dem Post
    $query = db_query('SELECT `erstid`, `ilchbb_rate` FROM `prefix_posts` WHERE `id` = '.$Ppid);
    $result = db_fetch_assoc($query);

    // Abbruch, wenn Versuch sich selbst zu beurteilen
    if ($result['erstid'] == $_SESSION['authid']) {
        echo 'Du kannst deine eigenen Beitraege leider nicht beurteilen.';
        exit;
    }

    // ilchbb_rate umwandeln
    $ilchbb_rate = unserialize($result['ilchbb_rate']);
    if (!is_array($ilchbb_rate)) $ilchbb_rate = array();

    // Pruefen, ob schonmal beurteilt
    if (isset($ilchbb_rate[$_SESSION['authid']])) {
        $time = $ilchbb_rate[$_SESSION['authid']];
        echo 'Diese Aktion hast du bereits am '.date('d.m.Y',$time).' durchgefuehrt.';
        exit;
    }

    // Beurteilung hinzufuegen
    $ilchbb_rate[$_SESSION['authid']] = time();
    $ilchbb_rate = escape(serialize($ilchbb_rate),'string');
    db_query('UPDATE `prefix_posts` SET `ilchbb_rate` = "'.$ilchbb_rate.'" WHERE `id` = '.$Ppid);

    // Leeres Return
    $_SESSION['ilchbb_ratetime'] = time();
    exit;
}
// IlchBB Forum 3.1 :: Post Rate :: End


$title = $allgAr['title'].' :: Forum :: '.$aktTopicRow['name'].' :: Beitr&auml;ge zeigen';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>'.aktForumCats($aktForumRow['kat']).'<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a><b> &raquo; </b>';
$hmenu .= $aktTopicRow['name'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
$ilchbb_tpl = new tpl('forum_v12/load_extensions');
$ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End

# Topic Hits werden eins hochgesetzt.
db_query('UPDATE `prefix_topics` SET hit = hit + 1 WHERE id = "'.$tid.'"');

# mehrere seiten fals gefordert
$limit = $allgAr['Fpanz'];  // Limit
$page = ($menu->getA(3) == 'p' ? $menu->getE(3) : 1 );
$MPL = db_make_sites ($page , "WHERE tid = ".$tid , $limit , 'index.php?forum-showposts-'.$tid , 'posts' );
$anfang = ($page - 1) * $limit;

$antworten = '';
if (($aktTopicRow['stat'] == 1 AND $forum_rights['reply'] == TRUE) OR ($_SESSION['authright'] <= '-7' OR $forum_rights['mods'] == TRUE)) {
    $antworten = '<div class="button_post_new" style="float: left;"><a href="index.php?forum-newpost-'.$tid.'"></a></div>';
}

$class = 'Cmite';

$tpl = new tpl ( 'forum_v12/showpost' );
$ar = array (
        'SITELINK' => $MPL,
        'tid' => $tid,
        'ANTWORTEN' => $antworten,
        'TOPICNAME' => $aktTopicRow['name'],
        'page' => $page
);
$tpl->set_ar_out($ar,0);
$i = $anfang +1;
$ges_ar = array ('wurstegal', 'maennlich', 'weiblich');

$erg = db_query("SELECT `a`.`id`, `a`.`txt`, `a`.`time`, `a`.`erstid`, `a`.`erst`,
                        `a`.`ilchbb_rate`, `b`.`geschlecht`, `b`.`sig`, `b`.`avatar`,
                        `b`.`posts`, `b`.`icq`, `b`.`homepage`, `b`.`opt_mail`,
                        `b`.`opt_pm`, `b`.`regist`, `b`.`wohnort`, `b`.`msn`, `b`.`yahoo`
                            FROM `prefix_posts` AS `a`
                            LEFT JOIN `prefix_user` AS `b` ON `a`.`erstid` = `b`.`id`
                            WHERE `a`.`tid` = ".$tid." ORDER BY `a`.`time` LIMIT ".$anfang.",".$limit);

while($row = db_fetch_assoc($erg)) {

    $class = ( $class == 'Cnorm' ? 'Cmite' : 'Cnorm' );

    // IlchBB Forum 3.1 :: Postdetails :: Start

    // Beitragstatus
    $new = $ilchBB->checkPostTime($fid, $tid, $row['time']);

    if ($new === TRUE) {
        $row['STATUS_SRC'] = '_unread';
        $row['STATUS_TITLE'] = 'Neuer Beitrag';
    } else {
        $row['STATUS_SRC'] = '';
        $row['STATUS_TITLE'] = 'Beitrag';
    }

    // IlchBB Forum 3.1 :: Postdetails :: End


    // IlchBB Forum 3.1 :: Userdetails :: Start

    // Danke-Funktion erlauben?
    $row['rate_allow'] = $allgAr['ilchbb_forum_ratepost'];

    // Anzahl von Mitgliedern, die den Beitrag gut finden
    if ($row['rate_allow'] == 1) {
        $rate_ar = unserialize($row['ilchbb_rate']);

        if (is_array($rate_ar)) {
            $row['rate'] = count($rate_ar);
        } else {
            $row['rate'] = 0;
        }
    }

    // Online and Offline Button
    if ($row['posts'] != 0) {
        // User Online od. Offline
        $query = "SELECT * FROM prefix_online where uid = " . $row['erstid'];
        $result = db_query($query);
        if (db_num_rows($result) > 0) {
            $row['online'] = '&nbsp;<img src="include/images/forum_v12/icon_online.gif" border="0">';
        }else {
            $row['online'] = '&nbsp;<img src="include/images/forum_v12/icon_offline.gif" border="0">';
        }
    }else {
        $row['online'] = '';
    }

    // Details :: Registriert
    if ($row['posts'] != 0) {
        $row['details'] = 'Registriert: '.date('d.m.Y',$row['regist']);
    } else {
        $row['details'] = '';
    }

    // Details :: Wohnort
    if (!empty($row['wohnort']) AND $row['posts'] != 0) {
        $row['details'] .= '<br />Wohnort: '.$row['wohnort'];
    }

    // Melden Button
    if (loggedin()) {
        $row['report'] = '<a href="javascript:void(0);" onClick="reportPost('.$tid.','.$row['id'].','.$page.');" title="Melden" class="icon post_report" id="report_'.$row['id'].'"></a>';
    } else {
        $row['report'] = '';
    }

    // Quote Button
    if (($aktTopicRow['stat'] == 1 AND $forum_rights['reply'] == TRUE) OR ($_SESSION['authright'] <= '-7' OR $forum_rights['mods'] == TRUE)) {
        $row['quote'] = '<a href="index.php?forum-newpost-'.$tid.'-z'.$row['id'].'" title="Zitieren" class="icon post_quote"></a>';
    } else {
        $row['quote'] = '';
    }

    // MSN Button
    if ($row['msn'] != '' AND $row['posts'] != 0) {
        $row['msn'] = urlencode($row['msn']);
        $row['msn'] = '<a href="http://members.msn.com/?mem='.$row['msn'].'&submit=&lang=de" title="MSN Profil" class="icon contact_msn"></a>&nbsp;';
    } else {
        $row['msn'] = '';
    }

    // MSN Button
    if ($row['yahoo'] != '' AND $row['posts'] != 0) {
        $row['yahoo'] = urlencode($row['yahoo']);
        $row['yahoo'] = '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.$row['yahoo'].'&.src=pg" title="Yahoo Profil" class="icon contact_yahoo"></a>&nbsp;';
    } else {
        $row['yahoo'] = '';
    }

    // ICQ Button
    if ($row['posts'] != 0) {
        $row['icq'] = str_replace("-", "", $row['icq']);
        $row['icq'] = str_replace(".", "", $row['icq']);
        $row['icq'] = str_replace(" ", "", $row['icq']);

        if (is_numeric($row['icq'])) {
            $row['icq'] = '<a href="http://www.icq.com/people/webmsg.php?to='.$row['icq'].'" title="ICQ Profil" class="icon contact_icq"></a>&nbsp;';
        }
    } else {
        $row['icq'] = '';
    }

    // Homepage Button
    if ($row['homepage'] != '' AND $row['posts'] != 0) {
        $row['www'] = '<a href="'.$row['homepage'].'" title="Homepage" target="_blank" class="icon contact_www"></a>&nbsp;';
    }else {
        $row['www'] = '';
    }

    // PM Button
    if ($row['opt_pm'] == '1') {
        $row['pm'] = '<a href="index.php?forum-privmsg-new=0&empfid='.$row['erstid'].'" title="Nachricht schreiben" class="icon contact_pm"></a>&nbsp;';
    }else {
        $row['pm'] = '';
    }
    
    // E-Mail Button
    if ($row['opt_mail'] == '1') {
        $row['email'] = '<a href="?user-mail-'.$row['erstid'].'" title="E-Mail" class="icon contact_email"></a>&nbsp;';
    }else {
        $row['email'] = '';
    }

    // IlchBB Forum 3.1 :: Userdetails :: End

    # define some vars.
    $row['sig'] = ( empty($row['sig']) ? '' : '<br /><hr style="width: 50%;" align="left">'.bbcode($row['sig']) );
    $row['TID'] = $tid;
    $row['class'] = $class;
    $row['date'] = date ('d.m.Y - H:i:s', $row['time'] );
    $row['delete'] = '';
    $row['change'] = '';
    if (!is_numeric($row['geschlecht'])) {
        $row['geschlecht'] = 0;
    }
    if (file_exists($row['avatar'])) {
        $row['avatar'] = '<br /><br /><img src="'.$row['avatar'].'" alt="User Pic" border="0" /><br />';
    }
    elseif ($allgAr['forum_default_avatar']) {
        $row['avatar'] = '<br /><br /><img src="include/images/avatars/'.$ges_ar[$row['geschlecht']].'.jpg" alt="User Pic" border="0" /><br />';
    }
    else {
        $row['avatar'] = '';
    }
    $row['rang']   = userrang ($row['posts'],$row['erstid']);
    $row['txt']    = (isset($_GET['such']) ? markword(bbcode ($row['txt']),$_GET['such']) : bbcode ($row['txt']) );
    $row['i']      = $i;
    $row['page']   = $page;

    if ( $row['posts'] != 0 ) {
        $row['erst'] = '<a href="index.php?user-details-'.$row['erstid'].'"><b>'.$row['erst'].'</b></a>';
    } elseif ( $row['erstid'] != 0 ) {
        $row['rang'] = 'gel&ouml;schter User';
    }

    if ($forum_rights['mods'] == true AND $i > 1) {
        $row['delete'] = '<a href="index.php?forum-delpost-'.$tid.'-'.$row['id'].'" title="L&ouml;schen" class="icon post_delete"></a>';
    }
    if (($forum_rights['mods'] == true OR $row['erstid'] == $_SESSION['authid']) AND loggedin()) {
        $row['change'] = '<a href="index.php?forum-editpost-'.$tid.'-'.$row['id'].'-'.$i.'" title="Bearbeiten" class="icon post_edit"></a>';
    }
    $row['posts']  = ($row['posts']?'Beitr&auml;ge: '.$row['posts']:'').'<br />';
    $tpl->set_ar_out($row,1);

    $i++;
}

$tpl->set_ar_out( array ( 'SITELINK' => $MPL, 'ANTWORTEN' => $antworten ) , 2 );

if (loggedin()) {

    // IlchBB Forum 3.1 :: Quick Post :: Start
    if ((($aktTopicRow['stat'] == 1 AND $forum_rights['reply'] == TRUE) OR ($_SESSION['authright'] <= '-7' OR $forum_rights['mods'] == TRUE))
            AND $allgAr['ilchbb_forum_qpost'] == 1 AND loggedin() AND is_numeric($allgAr['antispam']) AND has_right($allgAr['antispam'])) {
        $tpl->out(4);
    }
    // IlchBB Forum 3.1 :: Quick Post :: End

    if ($menu->get(3) == 'topicalert') {
        if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_topic_alerts WHERE uid = ".$_SESSION['authid']." AND tid = ".$tid),0)) {
            db_query("DELETE FROM prefix_topic_alerts WHERE uid = ".$_SESSION['authid']." AND tid = ".$tid);
        } else {
            db_query("INSERT INTO prefix_topic_alerts (tid,uid) VALUES (".$tid.", ".$_SESSION['authid'].")");
        }
    }

    if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_topic_alerts WHERE uid = " . $_SESSION['authid'] . " AND tid = " . $tid), 0)) {
        echo '<div class="forum ui-corner-all" style="background-color: #DCDDE2; text-align: center;"><a href="index.php?forum-showposts-' . $tid . '-topicalert">' . $lang['nomailonreply'] . '</a></div>';
    } else {
        echo '<div class="forum ui-corner-all" style="background-color: #DCDDE2; text-align: center;"><a href="index.php?forum-showposts-' . $tid . '-topicalert">' . $lang['mailonreply'] . '</a></div>';
    }

    // IlchBB Forum 3.1 :: Post Read :: Start
    $ilchBB->deleteNewTopics($fid, $tid);
    // IlchBB Forum 3.1 :: Post Read :: End
}

if ( $forum_rights['mods'] == TRUE ) {
    $tpl->set ( 'status', ($aktTopicRow['stat'] == 1 ? $lang['close'] : $lang['open'] ) );
    $tpl->set ( 'festnorm', ($aktTopicRow['art'] == 0 ? $lang['fixedtopic'] : $lang['normaltopic'] ) );
    $tpl->set('tid',$tid);
    $tpl->out(3);
}

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();
?>