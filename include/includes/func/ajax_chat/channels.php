<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// List containing the custom channels:
$channels = array();

$erg = db_query("SELECT * FROM `prefix_ajax_chat_channels` WHERE `right` <= '".$rights[$_SESSION['authright']]."'");
while( $row = db_fetch_assoc($erg) ){
	$channels[$row['id']] = $row['name'];
}
?>