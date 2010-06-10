<?php

$sql1 = "INSERT INTO `prefix_modules` (
			`url` ,
			`name` ,
			`gshow` ,
			`ashow` ,
			`fright` ,
			`menu` ,
			`pos`
			)
			VALUES 
			(
			'modrewrite', 'ModRewrite', '1', '0', '0', 'Admin', '9'
	)";
$sql2 = "INSERT INTO `prefix_config` (
			`schl` ,
			`typ` ,
			`kat` ,
			`frage` ,
			`wert` ,
			`pos`
			)
			VALUES 
			(
			'modrewrite', 'radio', 'Allgemeine Optionen', 'ModReWrite an / aus', '0', '0'
			)
		";
			

$sql3 = "DROP TABLE IF EXISTS `prefix_menu`";
$sql4 = "
		CREATE TABLE IF NOT EXISTS `prefix_menu` (
		  `wo` tinyint(1) NOT NULL DEFAULT '0',
		  `pos` tinyint(4) NOT NULL DEFAULT '0',
		  `was` tinyint(1) NOT NULL DEFAULT '0',
		  `ebene` tinyint(2) NOT NULL DEFAULT '0',
		  `recht` tinyint(2) NOT NULL DEFAULT '0',
		  `name` varchar(100) NOT NULL DEFAULT '',
		  `path` varchar(100) NOT NULL DEFAULT '',
		  PRIMARY KEY (`pos`,`wo`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de'
	";


$sql5 = "INSERT INTO `prefix_menu` (`wo`, `pos`, `was`, `ebene`, `recht`, `name`, `path`) VALUES
		(1, 0, 3, 0, 0, 'Menü', 'allianz.php'),
		(1, 12, 3, 0, 0, 'Clan Menü', 'allianz.php'),
		(1, 23, 1, 0, 0, 'Login', 'login.php'),
		(1, 5, 7, 0, 0, 'Links', 'links'),
		(1, 7, 7, 0, 0, 'Downloads', 'downloads'),
		(1, 8, 7, 0, 0, 'Gallery', 'gallery'),
		(1, 2, 7, 0, 0, 'Forum', 'forum'),
		(1, 16, 7, 0, 0, 'Wars', 'wars'),
		(1, 24, 1, 0, 0, 'Shoutbox', 'shoutbox.php'),
		(1, 20, 7, 0, 0, 'Awards', 'awards'),
		(1, 14, 7, 1, 0, 'Fightus', 'fightus'),
		(1, 15, 7, 1, 0, 'Joinus', 'joinus'),
		(1, 21, 7, 0, 0, 'Regeln', 'rules'),
		(1, 13, 7, 0, 0, 'Squads', 'teams'),
		(1, 3, 7, 1, 0, 'Mitglieder', 'user'),
		(2, 1, 1, 0, 0, 'Umfrage', 'vote.php'),
		(2, 2, 1, 0, 0, 'Allianz', 'allianz.php'),
		(2, 3, 1, 0, 0, 'Statistik', 'statistik.php'),
		(3, 2, 1, 0, 0, 'Lastwars', 'lastwars.php'),
		(3, 3, 1, 0, 0, 'Nextwars', 'nextwars.php'),
		(2, 4, 1, 0, 0, 'Newsletter', 'newsletter.php'),
		(3, 1, 1, 0, 0, 'Last Forum', 'lastforum.php'),
		(3, 4, 1, 0, 0, 'Last News', 'lastnews.php'),
		(2, 5, 1, 0, 0, 'Designs', 'designs.php'),
		(1, 18, 7, 0, -3, 'Training', 'trains'),
		(1, 11, 7, 0, 0, 'Impressum', 'impressum'),
		(1, 22, 7, 0, 0, 'History', 'history'),
		(1, 1, 7, 0, 0, 'News', 'news'),
		(3, 0, 1, 0, 0, 'Kalender', 'calender.php'),
		(1, 10, 7, 0, 0, 'Kontakt', 'contact'),
		(1, 9, 7, 0, 0, 'Kalender', 'kalender'),
		(3, 5, 1, 0, 0, 'PicOfX', 'picofx.php'),
		(1, 17, 7, 0, 0, 'Kasse', 'kasse'),
		(2, 0, 1, 0, 0, 'Suchen', 'search.php'),
		(1, 4, 7, 0, 0, 'Gbook', 'gbook'),
		(3, 6, 1, 0, 0, 'Geburtstag', 'geburtstag.php'),
		(2, 6, 1, 0, 0, 'Online', 'online.php'),
		(1, 19, 7, 0, -3, 'Away', 'awaycal'),
		(1, 6, 7, 0, 0, 'LinkUs', 'linkus')
	";
	
db_query($sql1);
db_query($sql2);
db_query($sql3);
db_query($sql4);
db_query($sql5);
db_query("ALTER TABLE `prefix_config` ADD `hide` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT 'auf 1 setzen um dies NICHT in der konfiguration anzuzeigen'");
db_query("UPDATE `prefix_config` SET `hide` =  '1' WHERE `schl` = 'modrewrite' LIMIT 1 ;");
?>