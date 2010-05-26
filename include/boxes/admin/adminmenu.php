<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

$tpl = new tpl('adminmenu', 1);

if (is_coadmin()) {
    $kat = '';
    $i = 1;
    foreach ($menuAr as $key => $tab) {
        if ($kat != $tab[ 'menu' ]) {
            if (!empty($kat)) {
                $tpl->out(3);
            }
            $tpl->set_out('id', $i++, 1);
            $kat = $tab[ 'menu' ];
        }
        $tpl->set_ar_out(Array(
                'url' => $key,
                'title' => $tab[ 'name' ]
                ), 2);
    }
} elseif (count($_SESSION[ 'authmod' ]) > 0) {
    $kat = 'modulerights';

    $q = "SELECT DISTINCT `url`, `name`
	FROM `prefix_modulerights` `a`
	LEFT JOIN `prefix_modules` `b` ON `b`.`id` = `a`.`mid`
	WHERE `b`.`gshow` = 1 AND `uid` = " . $_SESSION[ 'authid' ];

    $tpl->set_out('id', 1, 1);
    $erg = db_query($q);

    while ($row = db_fetch_assoc($erg)) {
        $tpl->set_ar_out(Array(
                'url' => $row[ 'url' ],
                'title' => $row[ 'name' ]
                ), 2);
    }
}

if (!empty($kat)) {
    $tpl->out(3);
}

?>