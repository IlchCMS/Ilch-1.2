<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
// menu
require_once('include/contents/forum/menu.php');

switch ($menu->get(1)) {
    default:
        $userDatei = 'memb_list';
        break;
    case 'regist':
    case 1:
        $userDatei = 'regist';
        break;
    case 'confirm':
        $userDatei = 'confirm';
        break;
    case 'login':
    case 2:
        $userDatei = 'login';
        break;
    case 'logout':
    case 3:
        $userDatei = 'logout';
        break;
    case 'mail':
    case 4:
        $userDatei = 'mail';
        break;
    case 'usergallery':
        $userDatei = 'usergallery';
        break;
    case 'details':
    case 6:
        $userDatei = 'user_details';
        break;
    case 'profil':
    case 8:
        $userDatei = 'profil_edit';
        break;
    case 'remind':
    case 13:
        $userDatei = 'password_reminder';
        break;
    case 'search':
        $userDatei = 'search';
        break;
}

require_once('include/contents/user/' . $userDatei . '.php');

?>