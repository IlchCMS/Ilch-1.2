<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$um = $menu->get(1);
$siteheader = 'Ilch Admin-Control-Panel :: Wars';
switch ($um){
    default:
        $datei = 'default';
        $contentheader =':: &Uuml;bersicht';
    break;
    // info
    case 'info':
        $datei = 'details';
        $contentheader =':: Details';
    break;
    // last wars
    case 'last':
        $datei = 'last';
        $contentheader =':: Last';
    break;
    // Next wars
    case 'next':
        $datei = 'next';
        $contentheader =':: Next';
    break;
    // Games
    case 'games':
        $datei = 'games';
        $contentheader =':: Games';
    break;
    // Gametype
    case 'gametype':
        $datei = 'gametype';
        $contentheader =':: Spielmodus';
    break;
    // Matchtype
    case 'matchtype':
        $datei = 'matchtype';
        $contentheader =':: Matchtypen';
    break;
    // Locations
    case 'locations':
        $datei = 'locations';
        $contentheader =':: Kartendatenbank';
    break;
    // Gegner
    case 'opponents':
        $datei = 'opponents';
        $contentheader =':: Gegenerdatenbank';
    break;
    // Server
    case 'server':
        $datei = 'server';
        $contentheader =':: Server';
    break;
}

if(($menu->getA(2)=='e' AND $menu->getA(3)=='d') OR ($menu->getA(2)=='e' AND $menu->getA(3)=='a') OR $menu->getA(2) == 'l' OR $menu->getA(2) == 'm'){
	$design = new design ( $siteheader, $contentheader, 0 );
	$design->header();
}elseif($menu->getA(2)=='j'){
}else{
	$design = new design ( $siteheader, $contentheader, 2 );
	$design->header();
}

include_once ('include/admin/contents/wars/'.$datei.'.php');

if(!$menu->getA(2)=='j'){
    $design->footer();
}

?>