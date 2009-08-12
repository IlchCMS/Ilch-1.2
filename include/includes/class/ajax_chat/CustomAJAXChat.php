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

class CustomAJAXChat extends AJAXChat {
	// Initialize custom request variables:
	function initCustomRequestVars() {

		// Auto-login ilchCMS users:
		if(!$this->getRequestVar('logout') && ($_SESSION['authid'] != 0)) {
			$this->setRequestVar('login', true);
		}
	}

	// Replace custom template tags:
	function replaceCustomTemplateTags($tag, $tagContent) {
		switch($tag) {

			case 'FORUM_LOGIN_URL':
				if(loggedin()) {
					return ($this->getRequestVar('view') == 'logs') ? './?view=logs' : './';
				} else {
					return $this->htmlEncode(generate_board_url().'/index.php?user-login');
				}
				
			case 'REDIRECT_URL':
				if(loggedin()) {
					return '';
				} else {
					return $this->htmlEncode($this->getRequestVar('view') == 'logs' ? $this->getChatURL().'?view=logs' : $this->getChatURL());
				}
			
			default:
				return null;
		}
	}

	// Returns true if the userID of the logged in user is identical to the userID of the authentication system
	// or the user is authenticated as guest in the chat and the authentication system
	function revalidateUserID() {
		if($this->getUserRole() === AJAX_CHAT_GUEST && $_SESSION['authid'] == 0 || ($this->getUserID() === $_SESSION['authid'])) {
			return true;
		}
		return false;
	}

	// Returns an associative array containing userName, userID and userRole
	// Returns null if login is invalid
	function getValidLoginUserData() {
		global $rights;
		// Check if we have a valid registered user:
		if(loggedin()) {
				
			$userData = array();
			$userData['userID'] = $_SESSION['authid'];

			$userData['userName'] = $this->trimUserName($_SESSION['authname']);
			
			$userData['userRole'] = $rights[$_SESSION['authright']];

			return $userData;
		} else {
			// Guest users:
			return $this->getGuestUser();
		}
	}

	// Store the channels the current user has access to
	// Make sure channel names don't contain any whitespace
	function &getChannels() {
		global $rights;
		if($this->_channels === null) {
			$this->_channels = $this->getAllChannels();
		}
		return $this->_channels;
	}

	// Store all existing channels
	// Make sure channel names don't contain any whitespace
	function &getAllChannels() {
		if($this->_allChannels === null) {
			// Get all existing channels:
			$customChannels = $this->getCustomChannels();
			
			$defaultChannelFound = false;
			
			foreach($customChannels as $key=>$value) {
				$forumName = $this->trimChannelName($value);
				
				$this->_allChannels[$forumName] = $key;
				
				if($key == $this->getConfig('defaultChannelID')) {
					$defaultChannelFound = true;
				}
			}
			
			if(!$defaultChannelFound) {
				// Add the default channel as first array element to the channel list:
				$this->_allChannels = array_merge(
					array(
						$this->trimChannelName($this->getConfig('defaultChannelName'))=>$this->getConfig('defaultChannelID')
					),
					$this->_allChannels
				);
			}
		}
		return $this->_allChannels;
	}

	function &getCustomUsers() {
		global $rights;
		// List containing the registered chat users:
		$users = null;
		require_once(AJAX_CHAT_PATH.'/include/includes/func/ajax_chat/users.php');
		return $users;
	}
	
	function &getCustomChannels() {
		global $rights;
		// List containing the custom channels:
		$channels = null;
		require_once(AJAX_CHAT_PATH.'/include/includes/func/ajax_chat/channels.php');
		return $channels;
	}

}
?>