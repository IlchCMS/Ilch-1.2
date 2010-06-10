<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Logout';
$hmenu = $extented_forum_menu . 'Logout' . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 0);
// ausloggen
user_logout();

$design->header();
wd('?' . $allgAr[ 'smodul' ], $lang[ 'logoutsuccessful' ]);
$design->footer();

?>