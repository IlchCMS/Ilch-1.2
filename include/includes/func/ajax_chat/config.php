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

$erg = db_query("SELECT * FROM `prefix_ajax_chat_config` WHERE `active` = '1' LIMIT 1");
$row = db_fetch_assoc($erg);

$row['defaultChannelName'] = db_result(db_query("SELECT `name` FROM `prefix_ajax_chat_channels` WHERE `id` = '".$row['defaultChannelID']."'"),0);

// Funktionen
function get_ToF($value){
	if( $value == '1' ){
		return true;
	}else{
		return false;
	}
}

// AJAX Chat config parameters:
$config = array();

// Available languages:
$config['langAvailable'] = array('ar','bg','bp','ca','cy','cz','de','el','en','es','et','fi','fr','gl','he','hr','hu','in','it','ka','kr','ja','nl','no','pl','ro','ru','sk','sl','sr','sv','tr','uk','zh','zh-tw');
// Default language:
$config['langDefault'] = 'de'; // ggf. anpassbar
// Language names:
$config['langNames'] = array('ar'=>'عربي','bg'=>'Български','bp'=>'Português (Brasil)','ca'=>'Català','cy'=>'Cymraeg','cz'=>'Česky','de'=>'Deutsch','el'=>'Ελληνικα','en'=>'English','es'=>'Español','et'=>'Eesti','fi'=>'Suomi','fr'=>'Français','gl'=>'Galego','he'=>'עברית','hr' => 'Hrvatski','hu' => 'Magyar','in'=>'Bahasa Indonesia','it'=>'Italiano','ja'=>'日本語','ka'=>'ქართული','kr'=>'한글','nl'=>'Nederlands','no'=>'Norsk','pl'=>'Polski','ro'=>'România','ru'=>'Русский','sk'=>'Slovenčina','sl'=>'Slovensko','sr'=>'Srpski','sv'=>'Svenska','tr'=>'Türkçe','uk'=>'Українська','zh'=>'中文 (简体)', 'zh-tw'=>'中文 (繁體)');

// Available styles:
$config['styleAvailable'] = read_ext ('include/includes/css/ajax_chat/', 'css');
// Default style:
$config['styleDefault'] = $row['styleDefault'];

// The encoding used for the XHTML content:
$config['contentEncoding'] = 'UTF-8';
// The encoding of the data source, like userNames and channelNames:
$config['sourceEncoding'] = 'UTF-8';
// The content-type of the XHTML page (e.g. "text/html", will be set dependent on browser capabilities if set to null):
$config['contentType'] = null;

// Session name used to identify the session cookie:
$config['sessionName'] = 'ajax_chat';
// Prefix added to every session key:
$config['sessionKeyPrefix'] = 'ajaxChat';
// The lifetime of the language, style and setting cookies in days:
$config['sessionCookieLifeTime'] = 365;
// The path of the cookies, '/' allows to read the cookies from all directories:
$config['sessionCookiePath'] = '/';
// The domain of the cookies, defaults to the hostname of the server if set to null:
$config['sessionCookieDomain'] = null;
// If enabled, cookies must be sent over secure (SSL/TLS encrypted) connections:
$config['sessionCookieSecure'] = null;

// Default channelName used together with the defaultChannelID if no channel with this ID exists:
$config['defaultChannelName'] = $row['defaultChannelName'];
// ChannelID used when no channel is given:
$config['defaultChannelID'] = $row['defaultChannelID'];
// Defines an array of channelIDs (e.g. array(0, 1)) to limit the number of available channels, will be ignored if set to null:
$config['limitChannelList'] = null;

// UserID plus this value are private channels (this is also the max userID and max channelID):
$config['privateChannelDiff'] = 500000000;
// UserID plus this value are used for private messages:
$config['privateMessageDiff'] = 1000000000;

// Enable/Disable private Channels:
$config['allowPrivateChannels'] = get_ToF($row['allowPrivateChannels']);
// Enable/Disable private Messages:
$config['allowPrivateMessages'] = get_ToF($row['allowPrivateMessages']);

// Private channels should be distinguished by either a prefix or a suffix or both (no whitespace):
$config['privateChannelPrefix'] = '[';
// Private channels should be distinguished by either a prefix or a suffix or both (no whitespace):
$config['privateChannelSuffix'] = ']';

// If enabled, users will be logged in automatically as guest users (if allowed), if not authenticated:
$config['forceAutoLogin'] = get_ToF($row['forceAutoLogin']);

// Defines if login/logout and channel enter/leave are displayed:
$config['showChannelMessages'] = get_ToF($row['showChannelMessages']);

// If enabled, the chat will only be accessible for the admin:
$config['chatClosed'] = get_ToF($row['chatClosed']);

// Defines the timezone offset in seconds (-12*60*60 to 12*60*60) - if null, the server timezone is used:
$config['timeZoneOffset'] = null;
// Defines the hour of the day the chat is opened (0 - closingHour):
$config['openingHour'] = 0;
// Defines the hour of the day the chat is closed (openingHour - 24):
$config['closingHour'] = 24;
// Defines the weekdays the chat is opened (0=Sunday to 6=Saturday):
$config['openingWeekDays'] = array(0,1,2,3,4,5,6);

// Enable/Disable guest logins:
$config['allowGuestLogins'] = get_ToF($row['allowGuestLogins']);
// Enable/Disable write access for guest users - if disabled, guest users may not write messages:
$config['allowGuestWrite'] = get_ToF($row['allowGuestWrite']);
// Allow/Disallow guest users to choose their own userName:
$config['allowGuestUserName'] = get_ToF($row['allowGuestUserName']);
// Guest users should be distinguished by either a prefix or a suffix or both (no whitespace):
$config['guestUserPrefix'] = '(';
// Guest users should be distinguished by either a prefix or a suffix or both (no whitespace):
$config['guestUserSuffix'] = ')';
// Guest userIDs may not be lower than this value (and not higher than privateChannelDiff):
$config['minGuestUserID'] = 400000000; // NICHT AENDERN

// Allow/Disallow users to change their userName (Nickname):
$config['allowNickChange'] = get_ToF($row['allowNickChange']);
// Changed userNames should be distinguished by either a prefix or a suffix or both (no whitespace):
$config['changedNickPrefix'] = '(';
// Changed userNames should be distinguished by either a prefix or a suffix or both (no whitespace):
$config['changedNickSuffix'] = ')';

// Allow/Disallow registered users to delete their own messages:
$config['allowUserMessageDelete'] = get_ToF($row['allowUserMessageDelete']);

// The userID used for ChatBot messages:
$config['chatBotID'] = 2147483647;		// NICHT AENDERN!
// The userName used for ChatBot messages
$config['chatBotName'] = $row['chatBotName'];

// Minutes until a user is declared inactive (last status update) - the minimum is 2 minutes:
$config['inactiveTimeout'] = 2;
// Interval in minutes to check for inactive users:
$config['inactiveCheckInterval'] = 5;

// Defines if messages are shown which have been sent before the user entered the channel:
$config['requestMessagesPriorChannelEnter'] = get_ToF($row['requestMessagesPriorChannelEnter']);
// Defines an array of channelIDs (e.g. array(0, 1)) for which the previous setting is always true (will be ignored if set to null):
$config['requestMessagesPriorChannelEnterList'] = null;
// Max time difference in hours for messages to display on each request:
$config['requestMessagesTimeDiff'] = $row['requestMessagesTimeDiff'];
// Max number of messages to display on each request:
$config['requestMessagesLimit'] = $row['requestMessagesLimit'];

// Max users in chat (does not affect moderators or admins):
$config['maxUsersLoggedIn'] = $row['maxUsersLoggedIn'];
// Max userName length:
$config['userNameMaxLength'] = 16;
// Max messageText length:
$config['messageTextMaxLength'] = 1040;
// Defines the max number of messages a user may send per minute:
$config['maxMessageRate'] = $row['maxMessageRate'];

// Defines the default time in minutes a user gets banned if kicked from a moderator without ban minutes parameter:
$config['defaultBanTime'] = 5;

// Argument that is given to the handleLogout JavaScript method:
$config['logoutData'] = 'index.php?ajaxchat&iframe=false&logout=true';

// If true, checks if the user IP is the same when logged in:
$config['ipCheck'] = true;

// Defines the max time difference in hours for logs when no period or search condition is given:
$config['logsRequestMessagesTimeDiff'] = 1;
// Defines how many logs are returned on each logs request:
$config['logsRequestMessagesLimit'] = 10;

// Defines the earliest year used for the logs selection:
$config['logsFirstYear'] = 2009;

// Defines if old messages are purged from the database:
$config['logsPurgeLogs'] = false;
// Max time difference in days for old messages before they are purged from the database:
$config['logsPurgeTimeDiff'] = 365;

// Defines if registered users (including moderators) have access to the logs (admins are always granted access):
$config['logsUserAccess'] = false;
// Defines a list of channels (e.g. array(0, 1)) to limit the logs access for registered users, includes all channels the user has access to if set to null:
$config['logsUserAccessChannelList'] = null;

// Defines if the socket server is enabled:
$config['socketServerEnabled'] = false;
// Defines the hostname of the socket server used to connect from client side (the server hostname is used if set to null):
$config['socketServerHost'] = null;
// Defines the IP of the socket server used to connect from server side to broadcast update messages:
$config['socketServerIP'] = '127.0.0.1';
// Defines the port of the socket server:
$config['socketServerPort'] = 1935;
// This ID can be used to distinguish between different chat installations using the same socket server:
$config['socketServerChatID'] = 0;
?>