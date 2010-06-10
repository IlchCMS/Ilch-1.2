<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('adminmenu', 1);
$ma = $menu->get_string_ar();
arsort($ma);

if (is_coadmin()) {
    $kat = '';
    $class = '';
    $i = 1;
    $mg = 0;
    foreach ($menuAr as $key => $tab) {
        if ($kat != $tab[ 'menu' ]) {
            if (!empty($kat)) {
                $tpl->set_ar_out(Array(
                        'class' => $class,
                        'url' => $url,
                        'id' => $i++,
                        'title' => $kat
                        ), 0);
                $class = '';
            }
            $kat = $tab[ 'menu' ];
            $url = $key;
        }
        if ($mg == 0) {
            if (in_array($key, $ma)) {
                $class = ' id="activetab"';
                $mg = 1;
            }
        }
    }
    if (!empty($kat)) {
        $tpl->set_ar_out(Array(
                'class' => $class,
                'url' => $url,
                'id' => $i++,
                'title' => $kat
                ), 0);
    }
} elseif (count($_SESSION[ 'authmod' ]) > 0) {
    $tpl->set_ar_out(Array(
            'class' => ' id="activetab"',
            'url' => 'admin',
            'id' => 1,
            'title' => 'Module'
            ), 0);
}

?>