<?php
// Copyright Florian Koerner
// Support www.ilch.de

// Vor direkten Zugriff schuetzen
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );

// Menu und Version definieren
$strParam             = $menu->get( 1 );
$strUParam            = $menu->get( 2 );
$menuID               = $menu->get( 3 );
$aktVers              = '1.00.03';
$class                = 'Cnorm';
$standard[ 'prefix' ] = '<b>';
$standard[ 'suffix' ] = ' - Standard</b>';
$rights               = Array(
     'Gast',
    'User',
    'Moderator',
    'Administrator',
    'Bot' 
);

// Funktionen definieren
//> Funktion 'read_ext', falls nicht Ilch 1.2
if ( !function_exists( 'read_ext' ) ) {
    function read_ext( $dir, $ext = '' )
    {
        $buffer = Array( );
        if ( !is_array( $ext ) ) {
            $ext = Array(
                 $ext 
            );
        }
        $open = opendir( $dir );
        while ( $file = readdir( $open ) ) {
            $file_info = pathinfo( $file );
            if ( $file != "." AND $file != ".." AND !is_dir( $dir . '/' . $file ) AND ( in_array( $file_info[ "extension" ], $ext ) OR empty( $ext ) ) ) {
                $buffer[ ] = $file;
            }
        }
        closedir( $open );
        return ( $buffer );
    }
}

//> Template setzen lassen (TRUE|FALSE) bei Radiobutton
function select_radio( $radio, $value )
{
    global $tpl;
    if ( $value != '1' ) {
        $tpl->set( $radio . '_0', ' checked="checked"' );
        $tpl->set( $radio . '_1', '' );
    } else {
        $tpl->set( $radio . '_0', '' );
        $tpl->set( $radio . '_1', ' checked="checked"' );
    }
}

//> Style-Liste erstellen
function get_css_list( $value )
{
    $files  = read_ext( 'include/includes/css/ajax_chat/', 'css' );
    $buffer = '';
    foreach ( $files as $file ) {
        if ( $file == $value ) {
            $buffer .= "<option selected=\"selected\">" . $file . "</option>\n";
        } else {
            $buffer .= "<option>" . $file . "</option>\n";
        }
    }
    return $buffer;
}

//> Neue Konfiguration erstellen
function chat_config_insert( $configAr )
{
    db_query( "INSERT INTO `prefix_ajax_chat_config` (`active`,`name`,`styleDefault`,`defaultChannelID`,`allowPrivateChannels`,
													 `allowPrivateMessages`,`forceAutoLogin`,`showChannelMessages`,
													 `chatClosed`,`allowGuestLogins`,`allowGuestWrite`,`allowGuestUserName`,
													 `allowNickChange`,`allowUserMessageDelete`,`chatBotName`,
													 `requestMessagesPriorChannelEnter`,`requestMessagesTimeDiff`,
													 `requestMessagesLimit`,`maxUsersLoggedIn`,`maxMessageRate`)
											 VALUES (" . escape( $configAr[ 'active' ], 'integer' ) . ",
													 '" . escape( $configAr[ 'name' ], 'string' ) . "',
													 '" . escape( $configAr[ 'styleDefault' ], 'string' ) . "',
													 " . escape( $configAr[ 'defaultChannelID' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowPrivateChannels' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowPrivateMessages' ], 'integer' ) . ",
													 " . escape( $configAr[ 'forceAutoLogin' ], 'integer' ) . ",
													 " . escape( $configAr[ 'showChannelMessages' ], 'integer' ) . ",
													 " . escape( $configAr[ 'chatClosed' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowGuestLogins' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowGuestWrite' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowGuestUserName' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowNickChange' ], 'integer' ) . ",
													 " . escape( $configAr[ 'allowUserMessageDelete' ], 'integer' ) . ",
													 '" . escape( $configAr[ 'chatBotName' ], 'string' ) . "',
													 " . escape( $configAr[ 'requestMessagesPriorChannelEnter' ], 'integer' ) . ",
													 " . escape( $configAr[ 'requestMessagesTimeDiff' ], 'integer' ) . ",
													 " . escape( $configAr[ 'requestMessagesLimit' ], 'integer' ) . ",
													 " . escape( $configAr[ 'maxUsersLoggedIn' ], 'integer' ) . ",
													 " . escape( $configAr[ 'maxMessageRate' ], 'integer' ) . ")" );
    
    // Wenn als Stadard gesetzt, alle anderen Stadards entfernen
    if ( $configAr[ 'active' ] != '0' ) {
        $lastdb = db_last_id();
        db_query( "UPDATE `prefix_ajax_chat_config` SET `active` = 0 WHERE `id` != " . $lastdb );
    }
    
    return 'Konfiguration erfolgreich hinzugef&uuml;gt';
}

//> Konfiguration bearbeiten
function chat_config_update( $configAr )
{
    global $menuID;
    $countConf = db_result( db_query( "SELECT COUNT(*) FROM `prefix_ajax_chat_config` WHERE `id` = " . $menuID ), 0 );
    $Actv      = db_result( db_query( "SELECT `active` FROM `prefix_ajax_chat_config` WHERE `id` = " . $menuID ), 0 );
    $retcache  = '';
    
    if ( ( $countConf == 0 ) OR ( $Actv == 1 AND $configAr[ 'active' ] == '0' ) ) {
        $configAr[ 'active' ] = 1;
        $retcache             = "<b>FATAL ERROR: Can't remove Standard!</b>\n";
    }
    
    db_query( "UPDATE `prefix_ajax_chat_config` SET `active` = " . escape( $configAr[ 'active' ], 'integer' ) . ",
												   `name` = '" . escape( $configAr[ 'name' ], 'string' ) . "',
												   `styleDefault` = '" . escape( $configAr[ 'styleDefault' ], 'string' ) . "',
												   `defaultChannelID` = " . escape( $configAr[ 'defaultChannelID' ], 'integer' ) . ",
												   `allowPrivateChannels` = " . escape( $configAr[ 'allowPrivateChannels' ], 'integer' ) . ",
												   `allowPrivateMessages` = " . escape( $configAr[ 'allowPrivateMessages' ], 'integer' ) . ",
												   `forceAutoLogin` = " . escape( $configAr[ 'forceAutoLogin' ], 'integer' ) . ",
												   `showChannelMessages` = " . escape( $configAr[ 'showChannelMessages' ], 'integer' ) . ",
												   `chatClosed` = " . escape( $configAr[ 'chatClosed' ], 'integer' ) . ",
												   `allowGuestLogins` = " . escape( $configAr[ 'allowGuestLogins' ], 'integer' ) . ",
												   `allowGuestWrite` = " . escape( $configAr[ 'allowGuestWrite' ], 'integer' ) . ",
												   `allowGuestUserName` = " . escape( $configAr[ 'allowGuestUserName' ], 'integer' ) . ",
												   `allowNickChange` = " . escape( $configAr[ 'allowNickChange' ], 'integer' ) . ",
												   `allowUserMessageDelete` = " . escape( $configAr[ 'allowUserMessageDelete' ], 'integer' ) . ",
												   `chatBotName` = '" . escape( $configAr[ 'chatBotName' ], 'string' ) . "',
												   `requestMessagesPriorChannelEnter` = " . escape( $configAr[ 'requestMessagesPriorChannelEnter' ], 'integer' ) . ",
												   `requestMessagesTimeDiff` = " . escape( $configAr[ 'requestMessagesTimeDiff' ], 'integer' ) . ",
												   `requestMessagesLimit` = " . escape( $configAr[ 'requestMessagesLimit' ], 'integer' ) . ",
												   `maxUsersLoggedIn` = " . escape( $configAr[ 'maxUsersLoggedIn' ], 'integer' ) . ",
												   `maxMessageRate` = " . escape( $configAr[ 'maxMessageRate' ], 'integer' ) . " WHERE `id` = " . $menuID );
    
    // Wenn als Stadard gesetzt, alle anderen Stadards entfernen
    if ( $configAr[ 'active' ] != '0' ) {
        $lastdb = $menuID;
        db_query( "UPDATE `prefix_ajax_chat_config` SET `active` = 0 WHERE `id` != " . $lastdb );
    }
    
    return $retcache . 'Konfiguration erfolgreich ge&auml;ndert';
}

//> Konfiguration loeschen
function chat_config_delete( )
{
    global $menuID;
    db_query( "DELETE FROM `prefix_ajax_chat_config` WHERE id = " . $menuID );
    return 'Konfiguration erfolgreich gel&ouml;scht';
}

//> Channel erstellen
function chat_channel_insert( $configAr )
{
    db_query( "INSERT INTO `prefix_ajax_chat_channels` (`name`,`right`) VALUES ('" . escape( $configAr[ 'name' ], 'string' ) . "',
																			   " . escape( $configAr[ 'right' ], 'integer' ) . ")" );
    return 'Channel erfolgreich hinzugef&uuml;gt';
}


//> Channel bearbeiten
function chat_channel_update( $configAr )
{
    global $strUParam;
    db_query( "UPDATE `prefix_ajax_chat_channels` SET `name` = '" . escape( $configAr[ 'name' ], 'string' ) . "',
												   `right` = " . escape( $configAr[ 'right' ], 'integer' ) . " WHERE `id` = " . $strUParam );
    return 'Channel erfolgreich ge&auml;ndert';
}

//> Channel loeschen
function chat_channel_delete( )
{
    global $menuID;
    db_query( "DELETE FROM `prefix_ajax_chat_channels` WHERE id = " . $menuID );
    return 'Channel erfolgreich gel&ouml;scht';
}

// Neue Designklasse und Header ausgeben
$design = new design( 'Ilch Admin-Control-Panel :: Ajaxchat', '', 2 );
$design->header();

// switch-Menuefuehrung
switch ( $strParam ) {
    
    // Menuefuehrung
    default:
        echo "Folgende Auswahlm&ouml;glichkeiten:\n" . "<ul>\n" . "<li><a href=\"admin.php?ajaxchat-config\">AjaxChat Einstellungen</a></li>\n" . "<li><a href=\"admin.php?ajaxchat-channels\">Channel Verwaltung</a></li>\n" . "</ul>";
        break;
    
    // AjaxChat Einstellungen
    case 'config':
        
        // switch-Untermenuefuehrung
        switch ( $strUParam ) {
            
            // Alle gespeicherten Einstellungen anzeigen
            default:
                
                $tpl = new tpl( 'ajax_chat/config_default', 1 );
                $tpl->out( 0 );
                
                $sql = db_query( "SELECT `id`,`active`,`name` FROM `prefix_ajax_chat_config` ORDER BY `active` DESC" );
                while ( $row = db_fetch_assoc( $sql ) ) {
                    $class                = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
                    $row[ 'prefix' ]      = $standard[ 'prefix' ];
                    $row[ 'suffix' ]      = $standard[ 'suffix' ];
                    $row[ 'class' ]       = $class;
                    $standard[ 'prefix' ] = '';
                    $standard[ 'suffix' ] = '';
                    $tpl->set_ar_out( $row, 1 );
                }
                
                $tpl->out( 2 );
                
                break;
            
            // Einstellung bearbeiten, hinzufügen
            case 'show':
                
                // Pruefen, ob Aenderungen empfangen wurden
                if ( isset( $_POST[ 'submit' ] ) AND is_numeric( $menuID ) ) {
                    wd( 'admin.php?ajaxchat-config', chat_config_update( $_POST ) );
                    $design->footer( 1 );
                } else if ( isset( $_POST[ 'submit' ] ) ) {
                    wd( 'admin.php?ajaxchat-config', chat_config_insert( $_POST ) );
                    $design->footer( 1 );
                }
                
                $tpl = new tpl( 'ajax_chat/config_show', 1 );
                
                if ( is_numeric( $menuID ) ) {
                    $sql = "SELECT * FROM `prefix_ajax_chat_config` WHERE `id` = " . $menuID;
                } else {
                    $sql = "SELECT * FROM `prefix_ajax_chat_config` WHERE `active` = 1";
                }
                
                $erg = db_query( $sql );
                $row = db_fetch_assoc( $erg );
                
                $row[ 'styleDefault' ]     = get_css_list( $row[ 'styleDefault' ] );
                $row[ 'defaultChannelID' ] = dblistee( $row[ 'defaultChannelID' ], "SELECT `id`, `name` FROM `prefix_ajax_chat_channels` ORDER BY `name` ASC" );
                
                $donot = Array(
                     'id',
                    'name',
                    'styleDefault',
                    'chatBotName',
                    'defaultChannelID',
                    'requestMessagesTimeDiff',
                    'requestMessagesLimit',
                    'maxUsersLoggedIn',
                    'maxMessageRate' 
                );
                foreach ( $row as $key => $value ) {
                    if ( !in_array( $key, $donot ) ) {
                        select_radio( $key, $value );
                    } else if ( $key != 'id' ) {
                        $tpl->set( $key, $value );
                    }
                }
                
                $tpl->out( 0 );
                
                break;
            
            // Einstellung loeschen
            case 'del';
                
                $row = db_result( db_query( "SELECT `active` FROM `prefix_ajax_chat_config` WHERE `id` = " . $menuID ), 0 );
                
                if ( $row != 1 ) {
                    wd( 'admin.php?ajaxchat-config', chat_config_delete() );
                } else {
                    wd( 'admin.php?ajaxchat-config', '<b>Fehler</b><br />Kann Standardkonfiguration nicht l&ouml;schen!' );
                }
                
                break;
        }
        
        break;
    
    // Channel Verwaltung
    case 'channels':
        
        // switch-Untermenuefuehrung
        switch ( $strUParam ) {
            
            // Alle gespeicherten Channels anzeigen
            default:
                
                if ( isset( $_POST[ 'submit' ] ) AND is_numeric( $strUParam ) ) {
                    wd( 'admin.php?ajaxchat-channels', chat_channel_update( $_POST ) );
                    $design->footer( 1 );
                } else if ( isset( $_POST[ 'submit' ] ) ) {
                    wd( 'admin.php?ajaxchat-channels', chat_channel_insert( $_POST ) );
                    $design->footer( 1 );
                }
                
                $tpl = new tpl( 'ajax_chat/channel_default', 1 );
                $tpl->out( 0 );
                
                $sql = db_query( "SELECT * FROM `prefix_ajax_chat_channels` ORDER BY `right` ASC" );
                while ( $row = db_fetch_assoc( $sql ) ) {
                    $class          = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
                    $row[ 'class' ] = $class;
                    $row[ 'right' ] = $rights[ $row[ 'right' ] ];
                    $tpl->set_ar_out( $row, 1 );
                }
                
                if ( is_numeric( $strUParam ) ) {
                    $sql = db_query( "SELECT * FROM `prefix_ajax_chat_channels` WHERE `id` = " . $strUParam );
                    $row = db_fetch_assoc( $sql );
                    
                    $row[ 's0' ] = '';
                    $row[ 's1' ] = '';
                    $row[ 's2' ] = '';
                    $row[ 's3' ] = '';
                    
                    if ( $row[ 'right' ] == 0 ) {
                        $row[ 's0' ] = 'selected="selected" ';
                    } else if ( $row[ 'right' ] == 1 ) {
                        $row[ 's1' ] = 'selected="selected" ';
                    } else if ( $row[ 'right' ] == 2 ) {
                        $row[ 's2' ] = 'selected="selected" ';
                    } else if ( $row[ 'right' ] == 3 ) {
                        $row[ 's3' ] = 'selected="selected" ';
                    }
                } else {
                    $row = Array(
                         'name' => '',
                        's0' => '',
                        's1' => '',
                        's2' => '',
                        's3' => '' 
                    );
                }
                $tpl->set_ar_out( $row, 2 );
                
                break;
            
            // Channel loeschen
            case 'del';
                
                $numConf = db_result( db_query( "SELECT COUNT(*) FROM `prefix_ajax_chat_config` WHERE `defaultChannelID` = " . $menuID ), 0 );
                
                if ( $numConf != 0 ) {
                    wd( 'admin.php?ajaxchat-channels', '<b>Fehler</b><br />Dieser Channel wird von einer Konfiguration benutzt!' );
                } else {
                    wd( 'admin.php?ajaxchat-channels', chat_channel_delete() );
                }
                
                $design->footer( 1 );
                
                break;
        }
        
        break;
}

$design->footer();
?>
