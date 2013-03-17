    <?php

    /**
     * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
     * @copyright (C) 2000-2010 ilch.de
     * @version $Id$
     */
    defined('main') or die('no direct access');

    $tpl = new tpl('user/boxen_login.htm');

    if (loggedin()) {
        $admin = '';
        if (user_has_admin_right($menu, false)) {
            $admin = '<a class="box" href="admin.php?admin">' . $lang['adminarea'] . '</a>';
        }
        if (has_right(-8, 'gbook') && ($checkGbook = @db_result(db_query("SELECT COUNT(*) FROM `prefix_gbook` WHERE `show` = 0"), 0)) > 0) {
            $admin .= ' | <a class="box" href="admin.php?gbook" title="' . $lang['uncheckedgbook'] . '">' . $lang['gbook'] . '</a>&nbsp;(' . $checkGbook . ')';
        }
        if (has_right(-8, 'user') && $allgAr['forum_regist_confirm'] == 2 && ($checkPuser = @db_result(db_query("SELECT COUNT(*) FROM `prefix_usercheck` WHERE `ak` = 1"), 0)) > 0) {
            $admin .= ' | <a class="box" href="admin.php?puser" title="' . $lang['uncheckeduser'] . '">' . $lang['confirmuser'] . '</a>&nbsp;(' . $checkPuser . ')';
        }
        $tpl->set('ADMIN', $admin);

        if ($allgAr['Fpmf'] == 1) {
            $erg = db_query("SELECT COUNT(id) FROM `prefix_pm` WHERE `gelesen` = 0 AND `status` < 1 AND `eid` = " . $_SESSION['authid']);
            $check_pm = db_result($erg, 0);
            $nachrichten_link = '<a class="box" href="index.php?forum-privmsg">' . $lang['messages'] . '</a>&nbsp;(' . $check_pm . ')<br />';
        } else {
            $nachrichten_link = '';
        }

        $tpl->set('SID', session_id());
        $tpl->set('NACHRICHTEN', $nachrichten_link);
        $tpl->set('NAME', $_SESSION['authname']);
        $tpl->out(0);
    } else {
        if (empty($_POST['login_email'])) {
            $_POST['login_email'] = $lang['email_name'];
        }
        if (empty($_POST['login_pw'])) {
            $_POST['login_pw'] = 'ääääääää';
        }
        $regist = '';
        if ($allgAr['forum_regist'] == 1) {
            $regist = ' &nbsp; &nbsp; <a href="index.php?user-regist">' . $lang['regist'] . '</a>';
        }
        $tpl->set_ar_out(array(
            'regist' => $regist,
            'wdlink' => '?' . $allgAr['smodul'],
            'PASS' => $_POST['login_pw'],
            'EMAIL' => $_POST['login_email']
                ), 1);
    }
    unset($tpl);
