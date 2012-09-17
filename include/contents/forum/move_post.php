<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

if ($forum_rights['mods'] == false) {
    $forum_failure[] = 'Keine Berechtigung dieses Forum zu moderieren';
    check_forum_failure($forum_failure);
}

$title = $allgAr['title'] . ' :: Forum :: ' . $aktForumRow['kat'] . ' :: ' . $aktForumRow['name'] . ' :: ' . $aktTopicRow['name'] . ' :: Beitrag verschieben';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showcat-' . $aktForumRow['cid'] . '">' . $aktForumRow['kat'] . '</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-' . $fid . '">' . $aktForumRow['name'] . '</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-' . $tid . '">' . $aktTopicRow['name'] . '</a> <b> &raquo; </b>Beitrag verschieben' . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();
$oldtid = escape($menu->get(2), 'integer');
$pid = escape($menu->get(3), 'integer');
if (empty($_POST['newtid'])) {

    $erg = db_query('SELECT * from prefix_topics');
    while ($row = db_fetch_assoc($erg)) {
        $newtid .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }
    $tpl = new tpl('forum/move_post');
    $tpl_ar = array(
        'tid' => $tid,
        'pid' => $pid,
        'newtid' => '',
        'selectnewtid' => '<select name="newtid">' . $newtid . '</select>'
    );

    $tpl->set_ar_out($tpl_ar, 1);
} else {

    $postnewtid = escape($_POST['newtid'], 'integer');

    //Neue FID ermitteln
    $newfid = db_fetch_assoc(db_query('SELECT `fid` FROM `prefix_topics` WHERE `id` = ' . $postnewtid));
    $newfid = $newfid['fid'];

    //alte FID ermitteln
    $oldfid = db_fetch_assoc(db_query('SELECT `fid` FROM `prefix_topics` WHERE `id` = ' . $oldtid));
    $oldfid = $oldfid['fid'];

    //Post mit neuer TID und FID versehen
    db_query('UPDATE `prefix_posts` SET `tid` = ' . $postnewtid . ' WHERE `id` = ' . $pid);
    db_query('UPDATE `prefix_posts` SET `fid` = ' . $newfid['newfid'] . ' WHERE `id` = ' . $pid);

    //aus alter FID die Anzahl Posts extrahieren und dekrementieren
    $newanzposts = db_fetch_assoc(db_query('SELECT `posts` FROM `prefix_forums` WHERE `id` = ' . $oldfid));
    $newanzposts = $newanzposts['posts'] - 1;
    db_query('UPDATE `prefix_forums` SET `posts` = ' . $newanzposts . ' WHERE `id` = ' . $oldfid);  //dekrementierten Wert ind alte FID schreiben
    //aus neuer FID die ANzahl Posts ermitteln  und inkrementieren
    $newanzposts = db_fetch_assoc(db_query('SELECT `posts` FROM `prefix_forums` WHERE `id` = ' . $newfid));
    $newanzposts = $newanzposts['posts'] + 1;
    db_query('UPDATE `prefix_forums` SET `posts` = ' . $newanzposts . ' WHERE `id` = ' . $newfid);  //inkrementierten Wert ind neue FID schreiben
    //aus alter TID die Anzahl Antworten extrahieren und dekrementieren
    $newanzreplys = db_fetch_assoc(db_query('SELECT `rep` FROM `prefix_topics` WHERE `id` = ' . $oldtid));
    $newanzreplys = $newanzreplys['rep'] - 1;
    db_query('UPDATE `prefix_topics` SET `rep` = ' . $newanzreplys . ' WHERE `id` = ' . $oldtid);  //dekrementierten Wert ind alte TID schreiben
    //aus neuer TID die ANzahl Antworten ermitteln  und inkrementieren
    $newanzreplys = db_fetch_assoc(db_query('SELECT `rep` FROM `prefix_topics` WHERE `id` = ' . $postnewtid));
    $newanzreplys = $newanzreplys['rep'] + 1;
    db_query('UPDATE `prefix_topics` SET `rep` = ' . $newanzreplys . ' WHERE `id` = ' . $postnewtid);  //inkrementierten Wert ind neue TID schreiben
    $page = ceil(($newanzreplys + 1) / $allgAr['Fpanz']);

    //Nun muss ermittelt werden was nun der letzte Post im neuen Topic ist
    $erg = db_query('SELECT * from `prefix_posts` WHERE `tid` = ' . $postnewtid); //erstmal alle Posts der neuen Topic einlesen
    $newest_post_in_tid = ''; //temporäre Variable zur speicherung des neuesten Posts in dieser Topic
    while ($row = db_fetch_assoc($erg)) {
        // Wenn eingelesener Post neuer als vorher ausgewerteter Post
        if ($row['time'] > $newest_post_in_tid['time']) {
            $newest_post_in_tid = $row; // dann überschreibe die temporäre Variable
        }
    }
    $newest_post_in_tid = $newest_post_in_tid['id'];
    db_query('UPDATE `prefix_topics` SET `last_post_id` = ' . $newest_post_in_tid . ' WHERE `id` = ' . $postnewtid); //nun der Topic und dem Forum mitteilen was der letzte Post ist
    // Nun muss ermittelt werden was nun der letzte Post im alten Topic ist
    $erg = db_query('SELECT * from `prefix_posts` WHERE `tid` = ' . $oldtid); //erstmal alle Posts der neuen Topic einlesen
    $newest_post_in_tid = ''; //temporäre Variable zur speicherung des neuesten Posts in dieser Topic
    while ($row = db_fetch_assoc($erg)) {
        // Wenn eingelesener Post neuer als vorher ausgewerteter Post
        if ($row['time'] > $newest_post_in_tid['time']) {
            $newest_post_in_tid = $row; // dann überschreibe die temporäre Variable
        }
    }
    $newest_post_in_tid = $newest_post_in_tid['id'];
    db_query('UPDATE `prefix_topics` SET `last_post_id` = ' . $newest_post_in_tid . ' WHERE `id` = ' . $oldtid); //nun der Topic und dem Forum mitteilen was der letzte Post ist
    // Jetzt noch schnell zur neuen Topic springen und fertig
    wd('index.php?forum-showposts-' . $postnewtid . '-p' . $page . '#' . $newest_post_in_tid, 'Post erfolgreich verschoben', 3);
}
$design->footer();

