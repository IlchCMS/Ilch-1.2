<?php
// Copyright Florian Körner 
defined ('main') or die ( 'no direct access' );

// Template laden
$tpl = new tpl('ajax_chat/ajaxchatstats');

// Mitglieder anzeigen lassen
$sql = "SELECT `userID`,`userNAME` FROM `prefix_ajax_chat_online` WHERE `userID` < '400000000' AND `userID` != '2147483647'";
$erg = db_query($sql);

$tpl->out(0);
$tpl->out(1);
if( db_num_rows($erg) > 0 ){
	while( $row = db_fetch_assoc($erg) ){
		$tpl->set_ar_out($row, 3);
	}
}else{
	$tpl->out(5);
}
$tpl->out(6);

// Gäste anzeigen lassen
$sql = "SELECT `userID`,`userNAME` FROM `prefix_ajax_chat_online` WHERE `userID` >= '400000000' AND `userID` != '2147483647'";
$erg = db_query($sql);

$tpl->out(0);
$tpl->out(2);
if( db_num_rows($erg) > 0 ){
	while( $row = db_fetch_assoc($erg) ){
		$tpl->set_ar_out($row, 4);
	}
}else{
	$tpl->out(5);
}
$tpl->out(6);

// Alle Nutzer im Chat zählen und popup ausgeben
$num = db_result(db_query("SELECT COUNT(*) FROM `prefix_ajax_chat_online` WHERE `userID` != '2147483647'"),0);
$tpl->set('completemenu', $menu->get_complete() );
$tpl->set_out('num', $num, 7);

unset($tpl);
?>