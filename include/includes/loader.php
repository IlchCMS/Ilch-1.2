<?php 
#   Copyright by Manuel
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );

# load all needed classes
require_once('include/includes/class/tpl.php');
require_once('include/includes/class/design.php');
require_once('include/includes/class/menu.php');
require_once('include/includes/class/bbcode.php');

# fremde classes laden
require_once('include/includes/class/xajax.inc.php');

# load all needed func
require_once('include/includes/func/db/mysql.php');

require_once('include/includes/func/bbcode_config.php');
require_once('include/includes/func/calender.php');
require_once('include/includes/func/user.php');
require_once('include/includes/func/escape.php');
require_once('include/includes/func/allg.php');
require_once('include/includes/func/debug.php');
require_once('include/includes/func/bbcode.php');
require_once('include/includes/func/profilefields.php');
require_once('include/includes/func/statistic.php');
require_once('include/includes/func/listen.php');
require_once('include/includes/func/forum.php');
require_once('include/includes/func/warsys.php'); 
require_once('include/includes/func/ic_mime_type.php');
require_once ('include/includes/func/lang.php');
?>