<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

if (!isset($_GET['step'])) {
    $_GET['step'] = 1;
}

if ($allgAr['forum_regist'] == 0) {
    // user duerfen sich nicht registrieren.
    $title = $allgAr['title'] . ' :: Users :: ' . $lang['noregist'];
    $hmenu = $extented_forum_menu
            . '<a class="smalfont" href="?user">User</a><b> &raquo; </b>'
            . $lang['noregist']
            . $extented_forum_menu_sufix;
    $design = new design($title, $hmenu, 1);
    $design->header();
    $tpl = new tpl('user/login');
    echo '<b> ' . $lang['adminsaynoregister'] . ' </b>';
    $tpl->set_out('WDLINK', '?' . $allgAr['smodul'], 0);
    $design->footer();
    exit();
}

switch ($menu->get(2)) {
    default:
        $title = $allgAr['title'] . ' :: Users :: ' . $lang['registration'] . ' :: ' . $lang['step'] . ' 1 ' . $lang['from'] . ' 3';
        $hmenu = $extented_forum_menu
                . '<a class="smalfont" href="?user">User</a><b> &raquo; </b>'
                . '<a class="smalfont" href="?user-regist">' . $lang['registration'] . '</a><b> &raquo; </b>'
                . $lang['step'] . ' 1 ' . $lang['from'] . ' 3'
                . $extented_forum_menu_sufix;
        $design = new design($title, $hmenu, 1);
        $design->header();
        $tpl = new tpl('user/regist');
        $getrulez = bbcode($allgAr['allg_regeln']);
        $tpl->set_out('regeln', $getrulez, 0);
        $design->footer();
        break;

    case 2:
        $name = '';
        $email = '';
        if (!empty($_POST['nutz'])) {
            $name = escape($_POST['nutz'], 'string');
        }
        if (!empty($_POST['email'])) {
            $email = escape($_POST['email'], 'string');
        }

        $ch_name = false;
        $xname = escape_nickname($name);
        if (!empty($name) AND $xname == $name AND 0 == db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `name_clean` = BINARY '" . get_lower($name) . "'"), 0)) {
            $ch_name = true;
        }

        $ch_email = false;
        $xemail = escape_for_email($email);
        if (!empty($email) AND $xemail == $email AND 0 == db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `email` = BINARY '" . get_lower($email) . "'"), 0)) {
            $ch_email = true;
        }

        if (empty($name) OR empty($email) OR $name != $xname OR $ch_name == false OR $email != $xemail OR $ch_email == false) {
            $title = $allgAr['title'] . ' :: Users :: ' . $lang['registration'] . ' :: ' . $lang['step'] . ' 2 ' . $lang['from'] . ' 3';
            $hmenu = $extented_forum_menu
                    . '<a class="smalfont" href="?user">User</a><b> &raquo; </b>'
                    . '<a class="smalfont" href="?user-regist">' . $lang['registration'] . '</a><b> &raquo; </b>'
                    . $lang['step'] . ' 2 ' . $lang['from'] . ' 3'
                    . $extented_forum_menu_sufix;
            $header = Array(
                'jquery/pstrength-min.1.2.js',
                'jquery/pstrength.css',
                'jquery/jquery.validate.js',
                'forms/regist.js'
            );
            $design = new design($title, $hmenu, 1);
            $design->header($header);
            if (empty($name) OR empty($email)) {
                $fehler = $lang['yourdata'];
            } elseif ($name != $xname) {
                $fehler = $lang['wrongnickname'];
            } elseif ($ch_name == false) {
                $fehler = $lang['namealreadyinuse'];
            } elseif ($email != $xemail) {
                $fehler = $lang['wrongemail'];
            } elseif ($ch_email == false) {
                $fehler = $lang['emailalreadyinuse'];
            }
            $tpl = new tpl('user/regist');
            $tpl->set('name', $name);
            $tpl->set('email', $email);
            $tpl->set_out('FEHLER', $fehler, 1);
            if ($allgAr['forum_regist_user_pass'] == 1) {
                $tpl->out(2);
            }
            $tpl->out(3);
        } else {
            $pass = PwCrypt::getRndString(8);
            if (!empty($_POST['pass'])) {
                $pass = escape($_POST['pass'], 'string');
            }
            user_regist($name, $email, $pass);

            $tpl = new tpl('user/regist');
            $title = $allgAr['title'] . ' :: Users :: ' . $lang['registration'] . ' :: ' . $lang['step'] . ' 3 ' . $lang['from'] . ' 3';
            $hmenu = $extented_forum_menu
                    . '<a class="smalfont" href="?user">User</a><b> &raquo; </b>'
                    . '<a class="smalfont" href="?user-regist">' . $lang['registration'] . '</a><b> &raquo; </b>'
                    . $lang['step'] . ' 3 ' . $lang['from'] . ' 3'
                    . $extented_forum_menu_sufix;
            $design = new design($title, $hmenu, 1);
            $design->header();
            $tpl->set_out('INFO', $registinfo, 4);
        }
        $design->footer();
        break;
}
