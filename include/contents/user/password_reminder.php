<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: Users :: Password Reminder';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Password Reminder' . $extented_forum_menu_sufix;
$design = new design ($title , $hmenu, 1);
$design->header();

$show = true;

if (isset ($_POST['name'])) {
    $name = escape($_POST['name'], 'string');
    $erg = db_query("SELECT email FROM prefix_user WHERE name = BINARY '" . $name . "'");
    if (db_num_rows($erg) == 1) {
        $row = db_fetch_assoc($erg);

        $new_pass = genkey(8);
        $md5_pass = md5($new_pass);
        $id = md5 (uniqid (rand()));

        db_query("INSERT INTO prefix_usercheck (`check`,name,email,pass,datime,ak)
		VALUES ('" . $id . "','" . $name . "','" . $row['email'] . "','" . $md5_pass . "',NOW(),2)");

        $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];

        $confirmlinktext = "\n" . $lang['registconfirm'] . "\n\n" . sprintf($lang['registconfirmlink'], $page, $id);
        $regmail = sprintf($lang['newpasswordmail'], $name, $confirmlinktext, $new_pass);

        icmail($row['email'], 'Password Reminder', $regmail); # email an user
        echo $lang['youhavereceivedaemail'];
        $show = false;
    } else {
        echo $lang['namenotfound'];
    }
}

if ($show) {
    $tpl = new tpl ('user/new_pass');
    $tpl->out(0);
}
$design->footer();

?>