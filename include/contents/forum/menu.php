<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
// -----------------------------------------------------------|
/*
$extented_forum_menu = '<center>';
if ( loggedin() ) {
  $pm_name = 'PM Box';
  if ( 1 <= db_result(db_query("SELECT COUNT(*) FROM prefix_pm WHERE gelesen = 0 AND status < 1 AND eid = ".$_SESSION['authid'] ),0) ) {
    $pm_name = '<b>Neue PM</b>';
    check_for_pm_popup ();
  }
  $extented_forum_menu .= '<a href="index.php?user-profil">Profil</a> - <a href="index.php?forum-privmsg">'.$pm_name.'</a> - ';

  if ( user_has_admin_right ($menu,false) ) {
    $extented_forum_menu .= '<a href="admin.php" target="_blank">Admin</a></a> - ';
  }

} else {
  $extented_forum_menu .= '<a href="?user-login">Einloggen</a> - <a href="index.php?user-regist">Registrieren</a> - ';
}


$extented_forum_menu .= '<a href="index.php?search">Suchen</a> - <a href="index.php?user">Mitglieder</a>  <!-- - <a href="index.php?forum-suche">Suche</a> //-->';
$extented_forum_menu .= '</center><br />';
$extented_forum_menu = '<span style="float: left;">';
#$extented_forum_menu .= '';
# margin: 0px; padding: 0px;  width: 20%

$extented_forum_menu_sufix  = '</span><span style="float: right; text-align: right;">';
$extented_forum_menu_sufix .= $lang['hello'].'&nbsp;<b>'. $_SESSION['authname'] . '</b>&nbsp;['. ( loggedin() ? '<a class="smalfont" href="index.php?user-logout">'.$lang['logout'].'</a>' : '<a  class="smalfont" href="index.php?user-login">'.$lang['login'].'</a>' ) . ']</span>';
*/
$extented_forum_menu = '';
$extented_forum_menu_sufix = '';

?>