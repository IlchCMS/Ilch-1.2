<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Forum :: Beitrag melden';
$hmenu = $extented_forum_menu . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);

$design->header();

$topicId = escape($menu->get(2), "integer");
$postId = escape($menu->get(3), "integer");

$tpl = new tpl('forum/report_post');
// wenn einer der beiden parameter leer ist
if (empty($topicId) || empty($postId)) {
    $tpl->out("error_no_ids");
    // wenn dieser beitrag nicht existiert
} else if (!post_exists($postId)) {
    $tpl->out("no_such_post");
} else {
    // PM Versenden
    $getmodids = getmod_ids(get_forum_id($topicId));
    if (empty($getmodids)) {
        // An den Admin schicken
        if (isset($_SESSION["authid"])) {
            $fromUser = $_SESSION["authid"];
        } else {
            $fromUser = 0;
        }
        $tpl->set("NAME", get_n($fromUser));
        $tpl->set("BEITRAG", get_topic_title($topicId));
        $tpl->set("PID", $postId);
        $tpl->set("TID", $topicId);
        sendpm($fromUser, 1, $tpl->get("pm_betreff"), $tpl->get("pm_content"), 0);
        // weiterleitung
        wd("index.php?forum-showposts-" . $topicId, $tpl->get("weiterleitung"));
    } else {
        // An die Mods schicken
        foreach($getmodids as $userid) {
            if (isset($_SESSION["authid"])) {
                $fromUser = $_SESSION["authid"];
            } else {
                $fromUser = 0;
            }
            $tpl->set("NAME", get_n($fromUser));
            $tpl->set("BEITRAG", get_topic_title($topicId));
            $tpl->set("PID", $postId);
            $tpl->set("TID", $topicId);
            sendpm($fromUser, $userid, $tpl->get("pm_betreff"), $tpl->get("pm_content"), 0);
            // weiterleitung
            wd("index.php?forum-showposts-" . $topicId, $tpl->get("weiterleitung"));
        }
    }
}

$design->footer();

?>