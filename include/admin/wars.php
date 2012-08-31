<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

if(($menu->getA(2)=='e' AND $menu->getA(3)=='d') OR ($menu->getA(2)=='e' AND $menu->getA(3)=='a') OR $menu->getA(2) == 'l' OR $menu->getA(2) == 'm'){
	$design = new design ( 'Admins Area', 'Admins Area', 0 );
	$design->header();
} else{
	$design = new design ( 'Admins Area', 'Admins Area', 2 );
	$design->header();
}

$um = $menu->get(1);

switch ($um)
{
    default:
        include ('include/admin/contents/wars/default.php');
    break;
    // last wars
    case 'last':
        include ('include/admin/contents/wars/last.php');
    break;
    // Next wars
    case 'next':
        include ('include/admin/contents/wars/next.php');
    break;
    // Games
    case 'games':
        include ('include/admin/contents/wars/games.php');
    break;
    case 'info':
        include ('include/admin/contents/wars/details.php');
    break;
}
$design->footer();
?>