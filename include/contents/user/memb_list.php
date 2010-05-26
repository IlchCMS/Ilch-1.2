<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: User :: ' . $lang[ 'listofmembers' ];
$hmenu = $extented_forum_menu . 'User <b> &raquo; </b> ' . $lang[ 'listofmembers' ] . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();

$limit = 20; // Limit

$tpl = new tpl('user/memb_list.htm');

if ($menu->exists('filtername')) {
    $page = ($menu->getA(3) == 'p' ? $menu->getE(3) : 1);
    $filtername = escape($menu->get(2), 'string');
} else {
    $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
}

$anfang = ($page - 1) * $limit;

$tpl = new tpl('user/memb_list.htm');

if (isset($_GET[ 'filtername' ]) AND !empty($_GET[ 'filtername' ])) {
    $filtername = escape($_GET[ 'filtername' ], 'string');
}

if (!empty($filtername)) {
    $sql_search = " WHERE `prefix_user`.`name` LIKE '%" . $filtername . "%'";
    $MPL = db_make_sites($page, $sql_search, $limit, '?user-filtername-' . $filtername, 'user');
} else {
    $sql_search = "";
    $MPL = db_make_sites($page, "", $limit, '?user', 'user');
}

$tpl->set_out('SITELINK', $MPL, 0);

$class = '';
$erg = db_query("SELECT
  `posts`,
  `prefix_user`.`id`,
  `prefix_grundrechte`.`name` as `recht_name`,
  `regist`,
  `prefix_user`.`name`
FROM `prefix_user`
 LEFT JOIN `prefix_grundrechte` ON `prefix_user`.`recht` = `prefix_grundrechte`.`id`
 " . $sql_search . "
ORDER BY `recht`,`prefix_user`.`posts` DESC LIMIT " . $anfang . "," . $limit);
while ($row = db_fetch_object($erg)) {
    if ($class == 'Cmite') {
        $class = 'Cnorm';
    } else {
        $class = 'Cmite';
    }
    $ar = array(
        'NAME' => $row->name,
        'RANG' => userrang($row->posts, $row->id),
        'CLASS' => $class,
        'POSTS' => $row->posts,
        'UID' => $row->id,
        'DATE' => date('d.m.Y', $row->regist),
        'GRUPE' => $row->recht_name
        );
    $tpl->set_ar_out($ar, 1);
}
$tpl->set_out('filtername', $filtername ? $filtername : '', 2);

$design->footer();

?>