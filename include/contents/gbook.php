<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr['title'] . ' :: G&auml;stebuch';
$hmenu = 'G&auml;stebuch';
$header = array(
    'jquery/jquery.validate.js',
    'forms/gbook.js'
);
$design = new design($title, $hmenu);
$design->header($header);

// time sperre in sekunden
$timeSperre = (int) $allgAr['gbook_time_ban'];
$now = time();

/**
 * gbook
 * id , name , mail , page , ip , time , txt
 */

/**
 * zeigt die vorschau des text an
 * nur aufrufen, wenn in $_POST['txt'] der text steht
 */
function showPreview() {
    $tpl = new tpl('gbook/insert.htm');
    $tpl->set('TEXT', BBcode(escape($_POST['txt'], 'textarea')));
    $tpl->out('preview');
}

/**
 * Zeigt das Formular an, in dem User ihre Einträge machen können
 *
 * @param string $text Vorbelegung für den text
 * @param string $mail Vorbelegung für die Emailadresse
 * @param string $page Vorbelegung für die Homepage
 * @param string $fehler Fehlermeldung
 * @param integer $now aktueller Unixtimestamp
 * @param integer $timeSperre Anzahl Sekunden, die vergehen muss bevor man erneut posten kann
 */
function showForm($text = "", $mail = "", $page = "", $fehler = "", $now = NULL, $timeSperre = 3600) {
    global $allgAr;
    $tpl = new tpl('gbook/insert.htm');
    $ar = array(
        'uname' => $_SESSION['authname'],
        'SMILIES' => getsmilies(),
        'ANTISPAM' => get_antispam('gbook', 1),
        'TXTL' => $allgAr['gbook_text_length'],
        'TEXT' => $text,
        'PAGE' => get_homepage($page),
        'MAIL' => $mail,
        'FEHLER' => $fehler
    );
    $tpl->set_ar_out($ar, 'formular_insert');
    if (!isset($_SESSION['klicktime_gbook'])) {
        $_SESSION['klicktime_gbook'] = ($now - $timeSperre);
    }
}

// Recht fuer Kommentare pruefen
if (loggedin()) {
    $komsOK = true;
} else {
    $komsOK = false;
}

if ($allgAr['gbook_koms_for_inserts'] == 0) {
    $komsOK = false;
}

switch ($menu->get(1)) {

    // Gaestebuch eintragen
    case 'insert':

        if (isset($_POST['preview'])) {
            showPreview();
            showForm($_POST['txt'], $_POST['mail'], get_homepage($_POST['page']), false, $now, $timeSperre);
        } elseif (isset($_POST['submit'])) {
            $fehler = '';
            // Fehlerabfrage
            if (($_SESSION['klicktime_gbook'] + $timeSperre) > $now) {
                $fehler .= '&middot;&nbsp;' . $lang['donotpostsofast'] . '<br />';
            }
            if (trim($_POST['name']) == '') {
                $fehler .= '&middot;&nbsp;' . $lang['emptyname'] . '<br />';
            }
            if (strlen($_POST['txt']) > $allgAr['gbook_text_length']) {
                $fehler .= '&middot;&nbsp;' . sprintf($lang['gbooktexttolong'], $allgAr['gbook_text_length']) . '<br />';
            }
            if (trim($_POST['txt']) == '') {
                $fehler .= '&middot;&nbsp;' . $lang['emptymessage'] . '<br />';
            }
            if (chk_antispam('gbook') != true) {
                $fehler .= '&middot;&nbsp;' . $lang['incorrectspam'] . '<br />';
            }
            //
            if ($fehler === '') {
                $txt = escape($_POST['txt'], 'textarea');
                if ($_SESSION['authid'] == 0) {
                    $name = escape_nickname($_POST['name'], 'string') . ' (Gast)';
                } else {
                    $name = escape_nickname($_POST['name'], 'string');
                }
                $mail = escape($_POST['mail'], 'string');
                $page = get_homepage(escape($_POST['page'], 'string'));

                db_query("INSERT INTO `prefix_gbook` (`name`,`mail`,`page`,`time`,`ip`,`txt`,`show`) 
				VALUES ('" . $name . "', '" . $mail . "', '" . $page . "', '" . time() . "', '" . getip() . "', '" . $txt . "', '" . $allgAr['gbook_show'] . "')");

                $_SESSION['klicktime_gbook'] = $now;
                wd('index.php?gbook', (($allgAr['gbook_show'] == 1) ? $lang['insertsuccessful'] : $lang['insertcheck']), 5);
            } else {
                showForm($_POST['txt'], $_POST['mail'], get_homepage($_POST['page']), '<div id="formfehler">' . $fehler . '</div>', $now, $timeSperre);
            }
        } else {
            showForm('', '', '', '', $now);
            break;
        }
        break;

    // einzelnen Eintrag anzeigen
    case 'show':

        $id = escape($menu->get(2), 'integer');
        if (isset($_POST['name']) && isset($_POST['text']) && chk_antispam('gbookkom')) {
            if (loggedin()) {
                $name = $_SESSION['authname'];
                $userid = $_SESSION['authid'];
            } else {
                $name = escape($_POST['name'], 'string') . ' (Gast)';
                $userid = 0;
            }
            $text = escape($_POST['text'], 'string');
            db_query("INSERT INTO `prefix_koms` (`name`,`userid`,`text`,`time`,`uid`,`cat`) 
			VALUES ('" . $name . "', " . $userid . ", '" . $text . "','" . time() . "', " . $id . ", 'GBOOK')");
        }
        if ($menu->getA(3) == 'd' AND is_numeric($menu->getE(3)) AND has_right(- 7, 'gbook')) {
            $did = escape($menu->getE(3), 'integer');
            db_query("DELETE FROM `prefix_koms` WHERE `uid` = " . $id . " AND `cat` = 'GBOOK' AND `id` = " . $did);
        }
        $r = db_fetch_assoc(db_query("SELECT `time`, `name`, `mail`, `page`, `txt` as `text`, `id` FROM `prefix_gbook` WHERE `id` = " . $id));
        $r['datum'] = post_date($r['time']);
        if ($r['page'] != '') {
            $r['page'] = get_homepage($r['page']);
            $r['page'] = ' &nbsp; <a href="' . $r['page'] . '" target="_blank"><img src="include/images/icons/page.gif" border="0" alt="Homepage ' . $lang['from'] . ' ' . $r['name'] . '"></a>';
        }
        if ($r['mail'] != '') {
            $r['mail'] = ' &nbsp; <a href="mailto:' . escape_email_to_show($r['mail']) . '"><img src="include/images/icons/mail.gif" border="0" alt="E-Mail ' . $lang['from'] . ' ' . $r['name'] . '"></a>';
        }
        $tpl = new tpl('gbook/show.htm');
        $r['ANTISPAM'] = get_antispam('gbookkom', 0);
        if (loggedin()) {
            $r['uname'] = $_SESSION['authname'];
            $r['readonly'] = 'readonly';
        } else {
            $r['uname'] = '';
            $r['readonly'] = '';
        }
        $r['text'] = bbcode($r['text']);
        $tpl->set_ar_out($r, "showsingle");
        // Kommentare
        if ($komsOK) {
            $tpl->set_ar_out($r, 'koms_on');
            $erg = db_query("SELECT `id`, `name`, `userid`, `text`, `time` FROM `prefix_koms` WHERE `uid` = " . $id . " AND `cat` = 'GBOOK' ORDER BY `id` DESC");
            $anz = db_num_rows($erg);
            if ($anz == 0) {
                echo $lang['nocomments'];
            } else {
                while ($r1 = db_fetch_assoc($erg)) {
                    if (has_right(- 7, 'gbook')) {
                        $del = ' <a href="index.php?gbook-show-' . $id . '-d' . $r1['id'] . '"><img src="include/images/icons/del.gif" alt="' . $lang['delete'] . '" border="0" title="' . $lang['delete'] . '" /></a>';
                    }
                    $r1['zahl'] = $anz;
                    $r1['avatar'] = get_avatar($r1['userid']);
                    $r1['time'] = post_date($r1['time'], 1) . $del;
                    $r1['text'] = bbcode($r1['text']);
                    $tpl->set_ar_out($r1, 'koms_self');
                    $anz--;
                }
            }
            $tpl->out('koms_off');
        }
        break;

    // Gaestebuch anzeigen
    default:

        $limit = $allgAr['gbook_posts_per_site']; // Limit
        $page = ($menu->getA(1) == 'p' ? escape($menu->getE(1), 'integer') : 1);
        $countOfGbookInserts = @db_result(db_query("SELECT COUNT(ID) FROM `prefix_gbook` WHERE `show` = 1"), 0);
        $MPL = db_make_sites($page, '', $limit, "?gbook", '', $countOfGbookInserts);
        $anfang = ($page - 1) * $limit;
        $tpl = new tpl('gbook/show.htm');
        // Gaestebuch gesperrt
        if ($allgAr['gbook_show'] != 2) {
            $insertButton = '<a href="index.php?gbook-insert">' . $lang['insert'] . '</a>';
        } else {
            $insertButton = '';
        }
        $ar = array(
            'INSERTS' => $countOfGbookInserts . ' ' . (($countOfGbookInserts == 1) ? $lang['entry'] : $lang['entries']),
            'BUTTON' => $insertButton
        );
        $tpl->set_ar_out($ar, 0);
        $erg = db_query("SELECT * FROM `prefix_gbook` WHERE `show` = 1 ORDER BY `time` DESC LIMIT " . $anfang . "," . $limit) or die(db_error());
        while ($row = db_fetch_object($erg)) {
            $page = '';
            $mail = '';
            if ($row->page) {
                $row->page = get_homepage($row->page);
                $page = ' &nbsp; <a href="' . $row->page . '" target="_blank"><img src="include/images/icons/page.gif" border="0" alt="Homepage ' . $lang['from'] . ' ' . $row->name . '"></a>';
            }
            if ($row->mail) {
                $mail = ' &nbsp; <a href="mailto:' . escape_email_to_show($row->mail) . '"><img src="include/images/icons/mail.gif" border="0" alt="E-Mail ' . $lang['from'] . ' ' . $row->name . '"></a>';
            }
            $koms = '';
            if ($komsOK) {
                $koms = db_result(db_query("SELECT COUNT(*) FROM `prefix_koms` WHERE `uid` = " . $row->id . " AND `cat` = 'GBOOK'"), 0, 0);
                $koms = '<a href="index.php?gbook-show-' . $row->id . '">' . $koms . ' ' . $lang['comments'] . '</a>';
            }
            $ar = array(
                'NAME' => $row->name,
                'DATE' => post_date($row->time),
                'koms' => $koms,
                'MAIL' => $mail,
                'ID' => $row->id,
                'PAGE' => $page,
                'TEXT' => BBCode($row->txt)
            );
            $tpl->set_ar_out($ar, "showlist_start");
        }
        $tpl->set_out('SITELINK', $MPL, "showlist_end");
        break;
}

$design->footer();
