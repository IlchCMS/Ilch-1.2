<?php
/* UPDATE FÜR REVISION 150 */

$sql1 = "CREATE TABLE IF NOT EXISTS `prefix_linkus` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL,
		  `datei` varchar(255) NOT NULL,
		  `hoch` int(5) NOT NULL,
		  `breit` int(5) NOT NULL,
		  `link` varchar(255) NOT NULL,
		  `views` int(11) NOT NULL,
		  `klicks` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM;
";

$sql2 = "INSERT INTO `prefix_linkus` (`name`, `datei`, `hoch`, `breit`, `link`, `views`, `klicks`) 
			VALUES
			('Ilch.de-Banner 468x60', '468x60ilch.gif', 60, 468, 'http://gecko.ilch.de', 20, 3),
			('Ilch.de-Button 88x31', 'copy_by_ilch.gif', 31, 88, 'http://gecko.ilch.de', 4, 0);
		";


$sql4 = "INSERT INTO `prefix_modules` (
			`url` ,
			`name` ,
			`gshow` ,
			`ashow` ,
			`fright` ,
			`menu` ,
			`pos`
			)
			VALUES (
			'linkus', 'LinkUs', '1', '1', '1', 'Content', '4'
			);
		";
		
$sql5 = "DROP TABLE IF EXISTS `prefix_menu`;
			CREATE TABLE IF NOT EXISTS `prefix_menu` (
			  `wo` tinyint(1) NOT NULL DEFAULT '0',
			  `pos` tinyint(4) NOT NULL DEFAULT '0',
			  `was` tinyint(1) NOT NULL DEFAULT '0',
			  `ebene` tinyint(2) NOT NULL DEFAULT '0',
			  `recht` tinyint(2) NOT NULL DEFAULT '0',
			  `name` varchar(100) NOT NULL DEFAULT '',
			  `path` varchar(100) NOT NULL DEFAULT '',
			  PRIMARY KEY (`pos`,`wo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';
			";
			
$sql6 = "INSERT INTO `prefix_menu` (`wo`, `pos`, `was`, `ebene`, `recht`, `name`, `path`) VALUES
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
			(1, 6, 7, 0, 0, 'LinkUs', 'linkus');
			";
db_query($sql1);
db_query($sql2);

db_query($sql4);
db_query($sql5);
db_query($sql6);
?>
