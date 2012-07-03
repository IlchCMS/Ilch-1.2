<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Users :: Password Reminder';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Password Reminder' . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();

$show = true;

if (isset($_POST[ 'email' ])) {
    $email = get_lower(escape($_POST[ 'email' ], 'string'));
    $erg = db_query("SELECT `name` FROM `prefix_user` WHERE `email` = BINARY '" . $email . "'");
    if (db_num_rows($erg) == 1) {
        $row = db_fetch_assoc($erg);

		$crypt = new PasswdCrypt();
        $new_pass = PasswdCrypt::getRndString(8);
		$crypted_pass = $crypt->cryptPasswd($new_pass);
		
        $id = md5(uniqid(rand()));

        db_query("INSERT INTO `prefix_usercheck` (`check`,`name`,`email`,`pass`, `datime`,`ak`)
		VALUES ('" . $id . "','" . $row[ 'name' ] . "','" . $email . "','" . $crypted_pass . "', NOW(),2)");

        $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];

        $confirmlinktext = "\n" . $lang[ 'registconfirm' ] . "\n\n" . sprintf($lang[ 'registconfirmlink' ], $page, $id);
        $regmail = sprintf($lang[ 'newpasswordmail' ], $row[ 'name' ], $confirmlinktext, $new_pass);

        icmail($email, 'Password Reminder', $regmail); // email an user
        echo $lang[ 'youhavereceivedaemail' ];
        $show = false;
    } else {
        echo $lang[ 'namenotfound' ];
    }
}

if ($show) {
    $tpl = new tpl('user/new_pass');
    $tpl->out(0);
}
$design->footer();
