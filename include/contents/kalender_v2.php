<?php
/**
 *
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
Kalender Script © by Nickel
 */
defined('main') or die('no direct access');
// -----------------------------------------------------------|
$title = $allgAr[ 'title' ] . ' :: Kalender V2';
$hmenu = 'Kalender V2';
$design = new design($title, $hmenu);
$design->addheader("\n<link rel='stylesheet' type='text/css' href='include/includes/js/fcal/fullcalendar.css' />");
$design->addheader("\n<link rel='stylesheet' type='text/css' href='include/includes/js/fcal/fullcalendar.print.css' media='print' />");
$design->addheader("\n<link rel='stylesheet' type='text/css' href='include/includes/css/fcal/style.css' media='print' />");
$design->addheader("\n<script type='text/javascript' src='include/includes/js/fcal/fullcalendar.js'></script>");
$design->addheader("\n<script type='text/javascript' src='include/includes/js/jquery/jquery.qtip-1.0.0-rc3.min.js'></script>");
$design->addheader("\n<script type='text/javascript' src='include/includes/js/fcal/datastack.js'></script>");
$design->header();
$tpl = new tpl('kalender_v2.htm');    	     
$tpl->out();
$design->footer();
?>