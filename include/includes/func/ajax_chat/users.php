<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// List containing the unregistered chat users:
$users = array( );

// Default guest user (don't delete this one):
$users[ 0 ]               = array( );
$users[ 0 ][ 'userRole' ] = $rights[ $_SESSION[ 'authright' ] ];
$users[ 0 ][ 'userName' ] = null;
$users[ 0 ][ 'password' ] = null;
$users[ 0 ][ 'channels' ] = array(
     0 
);
// Sample admin user:
//$users[0] = array();
//$users[0]['userRole'] = $right[$_SESSION['authright']];
//$users[0]['userName'] = $_SESSION['authname'];
//$users[0]['password'] = null;
//$users[0]['channels'] = array(0,1);
//// Sample moderator user:
//$users[2] = array();
//$users[2]['userRole'] = $right[-7];
//$users[2]['userName'] = 'moderator';
//$users[2]['password'] = 'moderator';
//$users[2]['channels'] = array(0,1);

//// Sample registered user:
//$users[3] = array();
//$users[3]['userRole'] = $right[-1];
//$users[3]['userName'] = 'user';
//$users[3]['password'] = 'user';
//$users[3]['channels'] = array(0,1);

/* Ausgeklammert, da in der Ilch-Version nicht nötig */
?>