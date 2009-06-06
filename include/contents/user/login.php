<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: Login';
$hmenu = $extented_forum_menu . 'Login' . $extented_forum_menu_sufix;

$tpl = new tpl ('user/login.htm');
if (loggedin()) {
    $design = new design ($title , $hmenu, 0);
    $design->header();
    if (isset($_POST['wdlink'])) {
        $wd = $_POST['wdlink'];
    }else {
        $wd = 'index.php?' . $allgAr['smodul'];
    }
    wd ($wd, $lang['yourareloged']);
    $design->footer();
} else {
    $design = new design ($title , $hmenu);
    $design->header();
    $tpl = new tpl ('user/login.htm');
    $tpl->set_out('WDLINK', 'index.php?' . $allgAr['smodul'], 0);
    $design->footer();
}

?>