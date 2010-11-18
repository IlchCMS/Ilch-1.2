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
		

db_query($sql1);
db_query($sql2);

db_query($sql4);

$rev='150';
$update_messages[$rev][] = '"link us" tabelle angelegt sofern sie noch nicht existierte';
$update_messages[$rev][] = '2 ilch banner zur linkus tabelle als standard hinzugef&uuml;gt';
$update_messages[$rev][] = '"link us" in der modultabelle registriert';
