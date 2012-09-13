<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
function get_gamepic_ar () {
	$ar = array();
	$o = opendir('include/images/wargames');
	while ($f = readdir($o) ) {
		if ( $f != '.' AND $f != '..' ) {
			$ar[$f] = $f;
		}
	}
	closedir($o);
	return ($ar);
}
function get_locations($gid){
	$l[0]='----Bitte Ausw&auml;hlen---';
	if($gid!=0){
		$e = db_query('SELECT id,name FROM `prefix_wars_locations` WHERE inaktive = 0 AND gid='.$gid);
         	while($r = db_fetch_row($e) ) {
			$l[$r[0]].=$r[1];
		}
	}
	return $l;
}
function get_groups(){
	$l[0]='----Bitte Ausw&auml;hlen---';
	$e = db_query('SELECT id,name FROM `prefix_groups` WHERE show_fightus = 1');
         while($r = db_fetch_row($e) ) {
		$l[$r[0]].=$r[1];
	}
	return $l;
}
function get_locationpic_ar ($s) {
	if($s=='db_error'){
		$ar['error']='Fehler:Game ist inaktiv';
	} elseif($s=='choose'){
		$ar['choose']='Bitte erst Game ausw&auml;hlen';
	} else {
		$ar = array();
		$ar['nopic']='Kein Bild';
		$ar['neu']='Neues Bild';
        $dir='include/images/locations/';
        if(is_dir($dir) && is_writeable($dir)) {
            $s=strtolower($s);
            $s=str_replace (' ', '', $s);
            $o = @opendir($dir.$s);
            while ($f = @readdir($o) ) {
                if ( $f != '.' AND $f != '..' AND $f != 'preview' ) {
                    $ar[$s.$f] = $f;
                }
            }
            @closedir($o);
        }
	}
	return ($ar);
}
function get_opponents(){
	$l[0]='----Bitte Ausw&auml;hlen---';
	$e = db_query('SELECT id,name FROM `prefix_wars_opponents` WHERE inaktive = 0');
         while($r = db_fetch_row($e) ) {
		$l[$r[0]].=$r[1];
	}
	return $l;
}
function get_server(){
	$l[0]='----Bitte Ausw&auml;hlen---';
	$e = db_query('SELECT id,name FROM `prefix_wars_server` WHERE inaktive = 0');
         while($r = db_fetch_row($e) ) {
		$l[$r[0]].=$r[1];
	}
	return $l;
}
function checkValue ($arg){
	if (is_string($arg)){
		$arg = rehtmlspecialchars(htmlspecialchars($arg));
	}else{
		foreach ($arg AS $key => $value){
			$arg[$key] = checkValue($value);
		}
	}
	return $arg;
}
function regGlobals ($array){
	reset($array);
	// get the vars out of the get-, post- or cookie-arrays
	foreach ($array AS $key => $value){
		global ${$key};
		// we don't register arrays with more than one dimension,
		// we only add slashes if required and use rehtmlspecialchars()
		$value              = checkValue($value);
		${$key}             = $value;
	}
	return true;
}
function rehtmlspecialchars($arg){
	$arg = str_replace ("&lt;", "<", $arg);
	$arg = str_replace ("&gt;", ">", $arg);
	$arg = str_replace ("&quot;", "\"", $arg);
	$arg = str_replace ("&amp;", "&", $arg);
	return $arg;
}

function get_game_icon($gid){
	$icon=db_result(db_query('SELECT icon FROm prefix_wars_games WHERE id='.$gid),0);
         return('<img src="include/images/wargames/'.$icon.'" alt="'.$icon.'" border="0">');
}
##get games
function get_games(){
	$l[0]='----Bitte Ausw&auml;hlen---';
	$e = db_query('SELECT id,name FROM `prefix_wars_games` WHERE inaktive = 0');
         while($r = db_fetch_row($e) ) {
		$l[$r[0]].=$r[1];
	}
	return $l;
}
function get_matchtypes(){
	$l[0]='----Bitte Ausw&auml;hlen---';
	$e = db_query('SELECT id,name FROM `prefix_wars_matchtype` WHERE inaktive = 0');
         while($r = db_fetch_row($e) ) {
		$l[$r[0]].=$r[1];
	}
	return $l;
}
function get_gametypes(){
	$l[0]='----Bitte Ausw&auml;hlen---';
	$e = db_query('SELECT id,name FROM `prefix_wars_gametype` WHERE inaktive = 0');
         while($r = db_fetch_row($e) ) {
		$l[$r[0]].=$r[1];
	}
	return $l;
}
?>