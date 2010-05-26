<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

$ma = $menu->get_string_ar();
arsort($ma);

foreach ($ma as $v) {
    if (isset($menuAr[ $v ])) {
        $modulname = $menuAr[ $v ][ 'name' ];
        break;
    }
}

if (empty($modulname)) {
    $modulname = 'Herzlich Willkommen im ACP';
}

echo $modulname;

?>
