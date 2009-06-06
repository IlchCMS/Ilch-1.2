<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

define ( 'main' , TRUE );
define ( 'admin', TRUE );

@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors','On');

session_name  ('sid');
session_start ();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

db_connect();
$allgAr = getAllgAr ();
user_identification();
$menu = new menu();

if ( user_has_admin_right($menu) ) {
  require_once ('include/admin/'.$menu->get_url('admin'));
}

db_close();
?>