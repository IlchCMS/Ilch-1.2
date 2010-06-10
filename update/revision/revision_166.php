<?php
//mairu
db_query('UPDATE `prefix_loader` SET `task` =  \'libs\', `file` = \'xajax//xajax.inc.php\' WHERE `file` = \'xajax.inc.php\'');
db_query('DELETE FROM `prefix_loader` WHERE `file` = "bbcode_config.php"');
db_query('DELETE FROM `prefix_loader` WHERE `file` = "debug.php"');
db_query('UPDATE ic1_smilies SET `url` = REPLACE(`url`, ".gif", ".png")');

//Gecko
$sql1 = "INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`kat` ,
`frage` ,
`wert` ,
`pos` ,
`hide`
)
VALUES (
'wartung', 'r2', 'Allgemeine Optionen', 'Wartungsmodus ?', '0', '0', '0'
)
";
$sql2 = "INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`kat` ,
`frage` ,
`wert` ,
`pos` ,
`hide`
)
VALUES (
'wartungstext', 'input', 'Allgemeine Optionen', 'Wartungstext', 'Die Seite befindet sich zur Zeit im Wartungsmodus und ist bald wieder für dich erreichbar', '0', '0'
)
";
db_query($sql1);
db_query($sql2);

$modrewrite 		= db_query("UPDATE `prefix_config` SET `pos` =  '0' WHERE `prefix_config`.`schl` = 'modrewrite' LIMIT 1");
$antispam 			= db_query("UPDATE `prefix_config` SET `pos` =  '1' WHERE `prefix_config`.`schl` = 'antispam' LIMIT 1");
$adminMail	 		= db_query("UPDATE `prefix_config` SET `pos` =  '2' WHERE `prefix_config`.`schl` = 'adminMail' LIMIT 1");
$title 				= db_query("UPDATE `prefix_config` SET `pos` =  '3' WHERE `prefix_config`.`schl` = 'title' LIMIT 1");
$allg_bbcode_max_img_width 	= db_query("UPDATE `prefix_config` SET `pos` =  '4' WHERE `prefix_config`.`schl` = 'allg_bbcode_max_img_width' LIMIT 1");
$allg_default_subject 		= db_query("UPDATE `prefix_config` SET `pos` =  '5' WHERE `prefix_config`.`schl` = 'allg_default_subject' LIMIT 1");
$menu_anz 			= db_query("UPDATE `prefix_config` SET `pos` =  '6' WHERE `prefix_config`.`schl` = 'menu_anz' LIMIT 1");
$revision 			= db_query("UPDATE `prefix_config` SET `pos` =  '7' WHERE `prefix_config`.`schl` = 'revision' LIMIT 1");
$wartungstext 		= db_query("UPDATE `prefix_config` SET `pos` =  '8' WHERE `prefix_config`.`schl` = 'wartungstext' LIMIT 1");
$wartung 			= db_query("UPDATE `prefix_config` SET `pos` =  '9' WHERE `prefix_config`.`schl` = 'wartung' LIMIT 1");
$allg_menupoint_access 		= db_query("UPDATE `prefix_config` SET `pos` =  '10' WHERE `prefix_config`.`schl` = 'allg_menupoint_access' LIMIT 1");
$show_session_id 	= db_query("UPDATE `prefix_config` SET `pos` =  '11' WHERE `prefix_config`.`schl` = 'show_session_id' LIMIT 1");
$mail_smtp 			= db_query("UPDATE `prefix_config` SET `pos` =  '12' WHERE `prefix_config`.`schl` = 'mail_smtp' LIMIT 1");
$smodul 			= db_query("UPDATE `prefix_config` SET `pos` =  '13' WHERE `prefix_config`.`schl` = 'smodul' LIMIT 1 ;");
$gfx 				= db_query("UPDATE `prefix_config` SET `pos` =  '14' WHERE `prefix_config`.`schl` = 'gfx' LIMIT 1");
$language 			= db_query("UPDATE `prefix_config` SET `pos` =  '15' WHERE `prefix_config`.`schl` = 'lang' LIMIT 1 ;");
$allg_regeln 			= db_query("UPDATE `prefix_config` SET `pos` =  '16' WHERE `prefix_config`.`schl` = 'allg_regeln' LIMIT 1 ;");