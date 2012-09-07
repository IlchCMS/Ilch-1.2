<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$um = $menu->get(1);

switch ($um)
{
    default:
        $datei = 'default';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: &Uuml;bersicht';
    break;
    // info
    case 'info':
        $datei = 'details';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Details';
    break;
    // last wars
    case 'last':
        $datei = 'last';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Last';
    break;
    // Next wars
    case 'next':
        $datei = 'next';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Next';
    break;
    // Games
    case 'games':
        $datei = 'games';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Games';
    break;
    // Gametype
    case 'gametype':
        $datei = 'gametype';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Spielmodus';
    break;
    // Matchtype
    case 'matchtype':
        $datei = 'matchtype';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Matchtypen';
    break;
    // Locations
    case 'locations':
        $datei = 'locations';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Kartendatenbank';
    break;
    // Gegner
    case 'opponents':
        $datei = 'opponents';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Gegenerdatenbank';
    break;
    // Server
    case 'server':
        $datei = 'server';
        $siteheader = 'Ilch Admin-Control-Panel :: Wars';
        $contentheader =':: Server';
    break;
}

if(($menu->getA(2)=='e' AND $menu->getA(3)=='d') OR ($menu->getA(2)=='e' AND $menu->getA(3)=='a') OR $menu->getA(2) == 'l' OR $menu->getA(2) == 'm'){
	$design = new design ( $siteheader, $contentheader, 0 );
	$design->header();
} elseif($menu->getA(2)=='j'){
debug_out();
}else{
	$design = new design ( $siteheader, $contentheader, 2 );
	$design->header();
}

include_once ('include/admin/contents/wars/'.$datei.'.php');

if($menu->getA(2)!='j'){
    $design->footer();
}
?>