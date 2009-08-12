<?php

// Angepasst von Florian Körner 
defined ('main') or die ( 'no direct access' );

if( !isset($_GET['iframe']) AND !isset($_POST['submit'])){
	$title = $allgAr['title'].' :: Ajax Chat';
	$hmenu  ='Ajax Chat';
	$design = new design ( $title , $hmenu, 1);
	$design->header();
	$tpl = new tpl ( 'ajax_chat/iframe' );
	$tpl->out(0);
	$design->footer(1);
}

/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Path to the chat directory:
define('AJAX_CHAT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));

// Include Class libraries:
require_once('include/includes/class/ajax_chat/classes.php');

if (!function_exists('db_free')){
	function db_free($erg) {
		@mysql_free_result($erg);
	}
}

if (!function_exists('ds_affected_rows')){
	function db_affected_rows () {
	  return (mysql_affected_rows (CONN));
	}
}


// Userrights DEFINE and RIGHTS

define('AJAX_CHAT_CHATBOT',		4);
define('AJAX_CHAT_ADMIN',		3);
define('AJAX_CHAT_MODERATOR',	2);
define('AJAX_CHAT_USER',		1);
define('AJAX_CHAT_GUEST',		0);

$rights = Array( '0' => AJAX_CHAT_GUEST,
				'-1' => AJAX_CHAT_USER,
				'-2' => AJAX_CHAT_USER,
				'-3' => AJAX_CHAT_USER,
				'-4' => AJAX_CHAT_USER,
				'-5' => AJAX_CHAT_USER,
				'-6' => AJAX_CHAT_USER,
				'-7' => AJAX_CHAT_MODERATOR,
				'-8' => AJAX_CHAT_MODERATOR,
				'-9' => AJAX_CHAT_ADMIN );

				
// Initialize the chat:
$ajaxChat = new CustomAJAXChat();

?>