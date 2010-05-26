<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

function getmods($fid) {
    $erg = db_query("SELECT `b`.`id`,`b`.`name` FROM `prefix_forummods` `a` LEFT JOIN `prefix_user` `b` ON `b`.`id` = `a`.`uid` WHERE `a`.`fid` = " . $fid);
    if (db_num_rows($erg) > 0) {
        $mods = '<br /><u>Moderators:</u> ';
        while ($row = db_fetch_assoc($erg)) {
            $mods .= '<a class="smalfont" href="index.php?user-details-' . $row[ 'id' ] . '">' . $row[ 'name' ] . '</a>, ';
        }
        $mods = substr($mods, 0, - 2);
        return ($mods);
    } else {
        return ('');
    }
}

/**
 * Gibt die ids aller Forummoderatoren des entsprechenden Forums zurück
 *
 * @param  $fid die Forum Id des Forums, dessen Moderatoren gebraucht werden
 */
function getmod_ids($fid) {
    $erg = db_query(sprintf("SELECT `uid` FROM `prefix_forummods` WHERE fid=%d", $fid));
    $mods = array();
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_assoc($erg)) {
            $mods[] = $row["uid"];
        }
    }
    return $mods;
}
// forum oder topic las update zeit
// id ( forum oder topic id )
// fid ( 0 is forum, > 0 is forum_id_vom_topic )
function forum_get_ordner($ftime, $id, $fid = 0) {
    // $_SESSION['forumSEE'] enthält ein zweidimensionales array
    // $_SESSION['forumSEE'][$FORUMID][$TOPICID]
    if ($ftime >= $_SESSION[ 'lastlogin' ]) {
        if ($fid == 0) {
            $anzUnreadTopics = db_result(db_query("SELECT COUNT(*) FROM `prefix_topics` LEFT JOIN `prefix_posts` ON `prefix_posts`.`id` = `prefix_topics`.`last_post_id` WHERE `prefix_topics`.`fid` = " . $id . " AND `prefix_posts`.`time` >= " . $_SESSION[ 'lastlogin' ]), 0);
            if ((($anzUnreadTopics > 0) AND
                        !isset($_SESSION[ 'forumSEE' ][ $id ])) OR
                    $anzUnreadTopics > count($_SESSION[ 'forumSEE' ][ $id ])
                    OR max($_SESSION[ 'forumSEE' ][ $id ]) <= ($ftime - 4)) {
                return ('nord');
            } else {
                return ('ord');
            }
        } else {
            if (isset($_SESSION[ 'forumSEE' ][ $fid ][ $id ]) AND $ftime <= $_SESSION[ 'forumSEE' ][ $fid ][ $id ]) {
                return ('ord');
            } else {
                return ('nord');
            }
        }
    } else {
        return ('ord');
    }
}

/**
 * Checkt, ob ein Post neu ist
 *
 * @param  $ftime Die letzte Updatezeit des Topics
 * @param  $id Die Id des Topics
 * @param  $postid die Id des Posts
 */
function post_is_new($ftime, $topicId, $forumId) {
    // wir rufen ganz frech forum_get_ordner auf und überprüfen den rückgabewert
    $result = forum_get_ordner($ftime, $topicId, $forumId) == 'nord' ? true : false;
    return $result;
}

/**
 * Alle topics, in denen sich seit dem letzten login was getan hat.
 */
function get_topics_since_last_login() {
    return get_topics_since($_SESSION["lastlogin"]);
}
/**
 * Gibt alle Topics mit neuen Posts seit dem letzten login zurück
 * sortiert nach der Zeit, neueste zuerst
 * Rechte werden dabei beachtet!
 * TODO: limit variabel machen
 *
 * @param  $since timestamp, ab wann nach topics gesucht werden soll
 */
function get_topics_since($since) {
    $erg = db_query("SELECT  DISTINCT `a`.`id` as `id`,
									 `b`.`id` as `fid`,
									 `a`.`name` as `title`,
									 `c`.`id` as `pid`,
									 `d`.`name` as `author`,
									 `a`.`rep` as `replies`
						FROM `prefix_topics` `a`
						LEFT JOIN `prefix_forums` `b` ON `b`.`id` = `a`.`fid`
						LEFT JOIN `prefix_posts` `c` ON `c`.`tid` = `a`.`id`
						LEFT JOIN `prefix_user` `d` ON `c`.`erstid` = `d`.`id`
						LEFT JOIN `prefix_groupusers` `vg` ON `vg`.`uid` = " . $_SESSION["authid"] . "
							AND `vg`.`gid` = `b`.`view`
						LEFT JOIN `prefix_groupusers` `rg` ON `rg`.`uid` = " . $_SESSION["authid"] . "
							AND `rg`.`gid` = `b`.`reply`
						LEFT JOIN `prefix_groupusers` `sg` ON `sg`.`uid` = " . $_SESSION["authid"] . "
							AND `sg`.`gid` = `b`.`start`
								WHERE (((`b`.`view` >= " . $_SESSION["authright"] . "
											AND `b`.`view` <= 0)
										OR (`b`.`reply` >= " . $_SESSION["authright"] . "
											AND `b`.`reply` <= 0)
										OR (`b`.`start` >= " . $_SESSION["authright"] . "
											AND `b`.`start` <= 0))
										OR (`vg`.`fid` IS NOT NULL
											OR `rg`.`fid` IS NOT NULL
											OR `sg`.`fid` IS NOT NULL
											OR " . $_SESSION["authright"] . " = -9)
											)
										AND `c`.`time` >= " . $since . "
										AND `c`.`id` = `a`.`last_post_id`
						ORDER BY `c`.`time` DESC
						LIMIT 0,5");

    $posts = array();
    while ($row = db_fetch_assoc($erg)) {
        $posts[] = $row;
    }
    return $posts;
}

/**
 * Checkt, ob ein gegebener Post existiert oder nicht
 *
 * @param int $postId
 */
function post_exists($postId) {
    return db_num_rows(db_query(sprintf("SELECT `id` FROM `prefix_posts` WHERE id=%d", $postId)));
}

/**
 * Gibt den Titel des Topics zurück
 *
 * @param  $topicId
 */
function get_topic_title($topicId) {
    $result = db_fetch_assoc(db_query(sprintf("SELECT `name` FROM `prefix_topics` WHERE id = %d", $topicId)));
    return $result["name"];
}

/**
 * Gibt die Forum id des gegebenen Topics zurück
 *
 * @param int $topicId
 */
function get_forum_id($topicId) {
    $row = db_fetch_assoc(db_query(sprintf("SELECT `fid` FROM `prefix_topics` WHERE id=%d", $topicId)));
    return $row["fid"];
}

function check_for_pm_popup() {
    // opt_pm_popup
    if (1 == db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `id` = " . $_SESSION[ 'authid' ] . " AND `opt_pm_popup` = 1"), 0, 0) AND 1 <= db_result(db_query("SELECT COUNT(*) FROM `prefix_pm` WHERE `gelesen` = 0 AND `status` < 1 AND `eid` = " . $_SESSION[ 'authid' ]), 0)) {
        $x = <<< html
    <script language="JavaScript" type="text/javascript"><!--
    function closeNewPMdivID () { document.getElementById("newPMdivID").style.display = "none"; }
    //--></script>
    <div id="newPMdivID" style="position:absolute; top:200px; left:300px; display:inline; width:200px;">
    <table width="100%" class="border" border="0" cellspacing="1" cellpadding="4">
      <tr>
        <td class="Cdark" align="left">
        <a href="javascript:closeNewPMdivID()"><img style="float:right; border: 0" src="include/images/icons/del.gif" alt="schliessen" title="schliessen"></a>
        <b>neue private Nachricht</b>
        bitte deinen <a href="?forum-privmsg">Posteingang</a> kontrolieren.
        Damit dieses Fenster dauerhaft verschwindet musst du alle neuen Nachrichten
        lesen, oder die Option in deinem <a href="?user-profil">Profil</a> abschalten.
        </td>
      </tr>
    </table>
    </div>
html;
        return ($x);
    }
}

function forum_user_is_mod($fid) {
    if (is_siteadmin()) {
        return (true);
    }

    if (1 == db_result(db_query("SELECT COUNT(*) FROM `prefix_forummods` WHERE `uid` = " . $_SESSION[ 'authid' ] . " AND `fid` = " . $fid), 0)) {
        return (true);
    }
    return (false);
}

function check_forum_failure($ar) {
    if (array_key_exists(0, $ar)) {
        $hmenu = '<a class="smalfont" href="?forum">Forum</a><b> &raquo; </b> Fehler aufgetreten';
        $title = 'Forum : Fehler aufgetreten';
        $design = new design($title, $hmenu);
        $design->header();
        echo '<b>Es ist/sind folgende(r) Fehler aufgetreten</b><br />';
        foreach ($ar as $v) {
            echo $v . '<br />';
        }
        echo '<br /><a href="javascript:history.back(-1)">zur&uuml;ck</a>';
        $design->footer();
        exit();
    }

    return (true);
}

?>