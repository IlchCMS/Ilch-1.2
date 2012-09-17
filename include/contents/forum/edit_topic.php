<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

if ($forum_rights[ 'mods' ] == false) {
    $forum_failure[ ] = 'Keine Berechtigung dieses Forum zu moderieren';
}

check_forum_failure($forum_failure);

$title = $allgAr[ 'title' ] . ' :: Forum :: ' . aktForumCats($aktForumRow[ 'kat' ], 'title') . ' :: ' . $aktForumRow[ 'name' ] . ' :: ' . $aktTopicRow[ 'name' ] . ' :: Thema &auml;ndern';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats($aktForumRow[ 'kat' ]) . '<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-' . $fid . '">' . $aktForumRow[ 'name' ] . '</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-' . $tid . '">' . $aktTopicRow[ 'name' ] . '</a> <b> &raquo; </b>Thema &auml;ndern' . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();

$uum = $menu->get(3);
$tid = intval($menu->get(2));
switch ($uum) {
    case 1: // change topic title
        db_query("UPDATE `prefix_topics` SET name = '" . escape($_REQUEST[ 'newTopic' ], 'string') . "' WHERE id = '" . $tid . "'");
        wd(array(
                'zur&uuml;ck zum Thema' => 'index.php?forum-showposts-' . $tid,
                'zur Themen &Uuml;bersicht' => 'index.php?forum-showtopics-' . $fid
                ), 'Das Themas wurde umbennant', 3);
        break;
    case 2: // delete topic
        if (empty($_POST[ 'sub' ])) {
            echo '<form action="index.php?forum-edittopic-' . $tid . '-2" method="POST">';
            echo 'Begr&uuml;ndung an den Ersteller (freiwillig)<br /><textarea cols="50" rows="2" name="reason"></textarea>';
            echo '<br /><br/><input type="submit" value="' . $lang[ 'delete' ] . '" name="sub">';
            echo '</form>';
        } else {
            // autor benachrichtigen
            if (!empty($_POST[ 'reason' ])) {
                $uid = db_result(db_query("SELECT `erstid` FROM `prefix_posts` WHERE `tid` = " . $tid . " ORDER BY `id` ASC LIMIT 1"), 0);
                $top = db_result(db_query("SELECT `name` FROM `prefix_topics` WHERE `id` = " . $tid), 0);
                $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
                $txt = "Dein Thema \"" . $top . "\" wurde gelöscht Begründung:\n\n" . $_POST[ 'reason' ];
                sendpm($_SESSION[ 'authid' ], $uid, 'Theme gelöscht', escape($txt, 'textarea'));
            }
            $postsMinus = $aktTopicRow[ 'rep' ] + 1;
            db_query("DELETE FROM `prefix_topics` WHERE `id` = '" . $tid . "' LIMIT 1");
            $erg = db_query("SELECT `erstid` FROM `prefix_posts` WHERE `tid` = " . $tid . " AND `erstid` > 0");
            while ($row = db_fetch_object($erg)) {
                db_query("UPDATE `prefix_user` SET `posts` = `posts` - 1 WHERE `id` = " . $row->erstid);
            }
            db_query("DELETE FROM `prefix_posts` WHERE `tid` = '" . $tid . "'");
            $pid = db_result(db_query("SELECT MAX(`id`) FROM `prefix_posts` WHERE `fid` = " . $fid), 0);
            if (empty($pid)) {
                $pid = 0;
            }
            db_query("UPDATE `prefix_forums` SET `last_post_id` = " . $pid . ", `posts` = `posts` - " . $postsMinus . ", `topics` = `topics` - 1 WHERE `id` = " . $fid);
            wd('index.php?forum-showtopics-' . $fid, 'Das Thema wurde gel&ouml;scht', 2);
        }
        break;
    case 3: // move topic in another forum
        if (empty($_POST[ 'sub' ]) OR $_POST[ 'nfid' ] == 'cat') {
            echo '<form action="index.php?forum-edittopic-' . $tid . '-3" method="POST">';
            echo '<input type="hidden" name="afid" value="' . $fid . '">neues Forum ausw&auml;hlen<br />';
            echo '<select name="nfid">';

            function stufe($anz, $t = 'f') {
                $z = ($t == 'f' ? '&nbsp;&nbsp;' : '&raquo;');
                for ($i = 0; $i < $anz; $i++) {
                    $out .= $z;
                }
                return $out;
            }

            function forum_admin_selectcats($id, $stufe, $sel) {
                $q = "SELECT * FROM `prefix_forumcats` WHERE `cid` = " . $id . " ORDER BY `pos`";
                $erg = db_query($q);
                if (db_num_rows($erg) > 0) {
                    while ($row = db_fetch_object($erg)) {
                        echo '<option style="font-weight:bold;" value="cat">' . stufe($stufe, 'c') . ' ' . $row->name . '</option>';
                        forum_admin_selectcats($row->id, $stufe + 1, $sel);
                        $sql = db_query("SELECT id, name FROM prefix_forums WHERE cid = $row->id");
                        while ($row2 = db_fetch_object($sql)) {
                            if (!forum_user_is_mod($row2->id)) {
                                continue;
                            }
                            echo '<option value="' . $row2->id . '"' . ($sel == $row2->id ? ' selected="selected"' : '') . '>' . stufe($stufe + 1) . ' ' . $row2->name . '</option>';
                        }
                    }
                }
            }

            forum_admin_selectcats(0, 0, $fid);
            echo '</select><br /><input type="checkbox" name="alertautor" value="yes" /> Den Autor &uuml;ber das verschieben informieren?<br /><input type="submit" value="Verschieben" name="sub"></form>';
        } else {
            $_POST['nfid'] = escape($_POST['nfid'], 'integer');
            $_POST['afid'] = escape($_POST['afid'], 'integer');
            $postsMinus = $aktTopicRow[ 'rep' ] + 1;
            db_query("UPDATE `prefix_topics` SET `fid` = " . $_POST[ 'nfid' ] . " WHERE `id` = " . $tid);
            db_query("UPDATE `prefix_posts` SET `fid` = " . $_POST[ 'nfid' ] . " WHERE `tid` = " . $tid);
            $apid = db_result(db_query("SELECT MAX(`id`) FROM `prefix_posts` WHERE `fid` = " . $_POST[ 'afid' ]), 0);
            $npid = db_result(db_query("SELECT MAX(`id`) FROM `prefix_posts` WHERE `fid` = " . $_POST[ 'nfid' ]), 0);
            if (empty($apid)) {
                $apid = 0;
            }
            db_query("UPDATE `prefix_forums` SET `last_post_id` = " . $apid . ", `posts` = `posts` - " . $postsMinus . ", `topics` = `topics` - 1 WHERE `id` = " . $_POST[ 'afid' ]);
            db_query("UPDATE `prefix_forums` SET `last_post_id` = " . $npid . ", `posts` = `posts` + " . $postsMinus . ", `topics` = `topics` + 1 WHERE `id` = " . $_POST[ 'nfid' ]);
            // autor benachrichtigen
            if (isset($_POST[ 'alertautor' ]) AND $_POST[ 'alertautor' ] == 'yes') {
                $uid = db_result(db_query("SELECT `erstid` FROM `prefix_posts` WHERE `tid` = " . $tid . " ORDER BY `id` ASC LIMIT 1"), 0);
                $fal = db_result(db_query("SELECT `name` FROM `prefix_forums` WHERE `id` = " . $_POST[ 'afid' ]), 0);
                $fne = db_result(db_query("SELECT `name` FROM `prefix_forums` WHERE `id` = " . $_POST[ 'nfid' ]), 0);
                $top = db_result(db_query("SELECT `name` FROM `prefix_topics` WHERE `id` = " . $tid), 0);
                $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
                $txt = 'Dein Thema "' . $top . '" wurde von dem Forum "' . $fal . '" in das neue Forum "' . $fne . '" verschoben... ';
                $txt .= "\n\n- [url=http://" . $page . "?forum-showposts-" . $tid . "]Link zum Thema[/url]";
                $txt .= "\n- [url=http://" . $page . "?forum-showtopics-" . $_POST[ 'nfid' ] . "]Link zum neuen Forum[/url]";
                $txt .= "\n- [url=http://" . $page . "?forum-showtopics-" . $_POST[ 'afid' ] . "]Link zum alten Forum[/url]";
                sendpm($_SESSION[ 'authid' ], $uid, 'Thema verschoben', escape($txt, 'textarea'));
            }

            wd(array(
                    'neue Themen Übersicht' => 'index.php?forum-showtopics-' . $_POST[ 'nfid' ],
                    'alte Themen Übersicht' => 'index.php?forum-showtopics-' . $_POST[ 'afid' ],
                    'Zum Thema' => 'index.php?forum-showposts-' . $tid
                    ), 'Thema erfolgreich verschoben', 3);
        }
        break;
    case 4: // change topic status
        $aktion = ($aktTopicRow[ 'stat' ] == 1 ? 0 : 1);
        db_query("UPDATE `prefix_topics` SET `stat` = '" . $aktion . "' WHERE `id` = '" . $tid . "'");
        wd('index.php?forum-showposts-' . $tid, 'ge&auml;ndert', 0);
        break;
    case 5: // change topic art
        $nart = ($aktTopicRow[ 'art' ] == 0 ? 1 : 0);
        db_query("UPDATE `prefix_topics` SET `art` = '" . $nart . "' WHERE `id` = " . $tid);
        wd(array(
                'zur&uuml;ck zum Thema' => 'index.php?forum-showposts-' . $tid,
                'zur Themen &Uuml;bersicht' => 'index.php?forum-showtopics-' . $fid
                ), 'Die Art des Themas wurde ge&auml;ndert', 3);
        break;
}
$design->footer();

?>