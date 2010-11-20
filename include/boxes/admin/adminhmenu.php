<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
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