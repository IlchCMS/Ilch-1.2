<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Angepasst von Florian Körner 
defined ('main') or die ( 'no direct access' );

class CustomAJAXChatShoutBox extends CustomAJAXChat {

	function initialize() {
		// Initialize configuration settings:
		$this->initConfig();
	}

	function getShoutBoxContent() {
		$template = new AJAXChatTemplate($this, AJAX_CHAT_PATH.'/include/templates/ajax_chat/shoutbox.html');
		
		// Return parsed template content:
		return $template->getParsedContent();
	}

}
?>