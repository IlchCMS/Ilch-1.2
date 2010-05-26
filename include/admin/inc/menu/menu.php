<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

$tpl = new tpl('adminsubmenu', 1);

$tpl->set_out('headline', 'Men&uuml; ausw&auml;hlen', 0);
$tpl->out(1);

for ($i = 1; $i <= $allgAr[ 'menu_anz' ]; $i++) {
    $tpl->set_ar_out(Array(
            'url' => 'admin.php?menu-' . $i,
            'title' => 'Menü ' . $i
            ), 2);
}

$tpl->out(3);

?>