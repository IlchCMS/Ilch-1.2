<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$uid = $menu->get(2);

$abf = 'SELECT * FROM `prefix_user` WHERE id = "' . $uid . '"';
$erg = db_query($abf);
$row = db_fetch_assoc($erg);

$userpic = '';
if (file_exists($row['userpic'])) {
    $userpic = '<img src="' . $row['userpic'] . '" border="0">';
}

$regsek = mktime (0, 0, 0, date('m'), date('d'), date('Y')) - $row['regist'];
$regday = round($regsek / 86400);
$postpday = ($regday == 0 ? 0 : round($row['posts'] / $regday, 2));

$ar = array (
    'NAME' => $row['name'],
    'JOINED' => date('d M Y', $row['regist']),
    'LASTAK' => date('d M Y - H:i', $row['llogin']),
    'POSTS' => $row['posts'],
    'postpday' => $postpday,
    'RANG' => userrang ($row['posts'], $uid),
    'AVATA' => $userpic,
    );

$title = $allgAr['title'] . ' :: Users :: Details von ' . $row['name'];
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Details von ' . $row['name'] . $extented_forum_menu_sufix;
$design = new design ($title , $hmenu, 1);
$design->header();

$tpl = new tpl ('user/userdetails');

$l = profilefields_show ($uid);

$ar['rowspan'] = 4 + substr_count($l, '<tr><td class="');

$ar['profilefields'] = $l;
$tpl->set_ar_out($ar, 0);
$design->footer();

?>