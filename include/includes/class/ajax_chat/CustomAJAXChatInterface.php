<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Angepasst von Florian K�rner 
defined( 'main' ) or die( 'no direct access' );

class CustomAJAXChatInterface extends CustomAJAXChat
{
    function initialize( )
    {
        // Initialize configuration settings:
        $this->initConfig();
    }
    
}
?>