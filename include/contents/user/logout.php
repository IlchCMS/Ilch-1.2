<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$title  = $allgAr[ 'title' ] . ' :: Logout';
$hmenu  = $extented_forum_menu . 'Logout' . $extented_forum_menu_sufix;
$design = new design( $title, $hmenu, 0 );
// ausloggen
user_logout();

$design->header();
wd( '?' . $allgAr[ 'smodul' ], $lang[ 'logoutsuccessful' ] );
$design->footer();

?>