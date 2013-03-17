<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// DEVELOPERS PLEASE NOTE
//
// Some characters you may want to copy&paste:
// ’ » „ “ — …

$lang = array_merge($lang, array(
    //Login
    'login' => 'login',
    'email_name' => 'name/E-mail',
    'password' => 'password',
    'savecookie' => 'stay logged in',
    'registernow' => 'register now',
    'forgottenpassword' => 'Forgot password',
    'yourareloged' => 'You are now logged in',
    'login3failure' => '3 times logged in with false data',
    //Profiledit
    'userpicisnopicture' => 'The userpic isn\'t a picture',
    'userpiccannotupload' => 'Can\'t upload the userpic',
    //Regist
    'adminsaynoregister' => 'The administrator has specified that it is not allowed to register!',
    'noregist' => 'No registration possible',
    'registration' => 'registration',
    'registwithoutconfirm' => "Hello <b>%s</b>,<br /><br />your registration was successful!<br /><br />Please secure your password \"<b>%s</b>\" well since it was only stored encrypted in the database and you don\'t receive it via e-mail. Now you can <a href=\"index.php?user-2\">log in</a>.<br /><br />Your Administrator",
    'registconfirmbyadmin' => "Hello <b>%s</b>,<br /><br />your registration was saved successfully, an administrator will activate your account soon!<br />Please secure your password \"<b>%s</b>\" well, it is only stored encrypted in the database.<br /><br />You will only after activation <a href=\"index.php?user-2\">log in</a>.<br /><br />Your Administrator",
    'registconfirmbylink' => "Hello <b>%s</b>,<br /><br />your registration was successful!<br /><br />A e-mail was sent to you with the access and an activation link.<br />Please secure your password well, it is only stored encrypted in the database.<br /><br />After you have confirmed the activation link in the e-mail, you can <a href=\"index.php?user-2\">log in</a>.<br /><br />Your Administrator",
    'step' => 'step'
));
