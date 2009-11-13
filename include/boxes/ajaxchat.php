<?php

// Angepasst von Florian Körner 
defined( 'main' ) or die( 'no direct access' );

// Define some Functions

if ( !function_exists( 'db_free' ) ) {
    function db_free( $erg )
    {
        @mysql_free_result( $erg );
    }
}

if ( !function_exists( 'ds_affected_rows' ) ) {
    function db_affected_rows( )
    {
        return ( mysql_affected_rows( CONN ) );
    }
}

function getShoutBoxContent( )
{
    // URL to the chat directory:
    if ( !defined( 'AJAX_CHAT_URL' ) ) {
        define( 'AJAX_CHAT_URL', '' );
    }
    
    // Path to the chat directory:
    if ( !defined( 'AJAX_CHAT_PATH' ) ) {
        define( 'AJAX_CHAT_PATH', dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ) );
    }
    
    // Validate the path to the chat:
    if ( @is_file( AJAX_CHAT_PATH . '/include/includes/class/ajax_chat/classes.php' ) ) {
        // Include Class libraries:
        require_once( AJAX_CHAT_PATH . '/include/includes/class/ajax_chat/classes.php' );
        
        // Initialize the shoutbox:
        $ajaxChat = new CustomAJAXChatShoutBox();
        
        // Parse and return the shoutbox template content:
        return $ajaxChat->getShoutBoxContent();
    }
    
    return null;
}


// Userrights DEFINE and RIGHTS

define( 'AJAX_CHAT_CHATBOT', 4 );
define( 'AJAX_CHAT_ADMIN', 3 );
define( 'AJAX_CHAT_MODERATOR', 2 );
define( 'AJAX_CHAT_USER', 1 );
define( 'AJAX_CHAT_GUEST', 0 );

$rights = Array(
     '0' => AJAX_CHAT_GUEST,
    '-1' => AJAX_CHAT_USER,
    '-2' => AJAX_CHAT_USER,
    '-3' => AJAX_CHAT_USER,
    '-4' => AJAX_CHAT_USER,
    '-5' => AJAX_CHAT_USER,
    '-6' => AJAX_CHAT_USER,
    '-7' => AJAX_CHAT_MODERATOR,
    '-8' => AJAX_CHAT_MODERATOR,
    '-9' => AJAX_CHAT_ADMIN 
);

// Set AjaxChat SESSION
if ( !isset( $_SESSION[ 'ajaxbox' ] ) ) {
    $_SESSION[ 'ajaxbox' ] = 'true';
}

// Check SESSION option
if ( $_GET[ 'ajaxbox' ] == 'true' ) {
    $_SESSION[ 'ajaxbox' ] = 'true';
} else if ( $_GET[ 'ajaxbox' ] == 'false' ) {
    $_SESSION[ 'ajaxbox' ] = 'false';
}

// Initialize the chat:
if ( $menu->get( 0 ) == 'ajaxchat' OR $_SESSION[ 'ajaxbox' ] == 'false' ) {
    echo '<div style="text-align: center;">Die Shoutbox ist inaktiv!<br /><a href="index.php?' . $menu->get_complete() . '&ajaxbox=true">Jetzt aktivieren</a></div>';
} else {
    echo getShoutBoxContent();
    echo '<div style="text-align: center;"><a href="index.php?' . $menu->get_complete() . '&ajaxbox=false">Jetzt deaktivieren</a>';
}
?>