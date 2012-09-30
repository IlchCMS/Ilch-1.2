<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
*/

/* Installiere ilchBB-Datenbank-Struktur und Administration*/
db_query("INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`) VALUES
('ilchbb_forum_active', 'r2', 'IlchBB Forum', 'IlchBB Forum aktivieren?', '1'),
('ilchbb_forum_qpost', 'r2', 'IlchBB Forum', 'Schnellantwort aktivieren?', '1'),
('ilchbb_forum_dayonline', 'r2', 'IlchBB Forum', 'Anzeigen, welche Mitglieder heute online waren?', '1'),
('ilchbb_forum_hottopic', 'input', 'IlchBB Forum', 'Ab wie vielen Beitr&auml;gen gilt ein Thema als \"Hot Topic\"?', '20'),
('ilchbb_forum_ratepost', 'r2', 'IlchBB Forum', 'Sollen Mitglieder sich f&uuml;r Beitr&auml;ge bedanken k&ouml;nnen?', '1'),
('ilchbb_forum_ratetime', 'input', 'IlchBB Forum', 'Min. Abstand zwischen Bedankungen f&uuml;r einen Beitrag in Sek.', '30')");
db_query("INSERT INTO `prefix_allg` (`id`, `k`, `v1`) VALUES (NULL, 'ilchbb', '3.1')");
db_query("ALTER TABLE `prefix_user` ADD `ilchbb_lastquery` INT( 11 ) NOT NULL");
db_query("ALTER TABLE `prefix_user` ADD `ilchbb_newtopics` TEXT NOT NULL");
db_query("ALTER TABLE `prefix_posts` ADD `ilchbb_rate` TEXT NOT NULL");

/* Update Datenbank zu ilchBB->Forum V1.2*/
db_query("UPDATE `prefix_config` SET  `kat` =  'Forum' WHERE  `ic1_config`.`kat` =  'IlchBB Forum'");
db_query("UPDATE `prefix_config` SET  `frage` =  'Forum V1.2 aktivieren?' WHERE  `ic1_config`.`schl` =  'ilchbb_forum_active'");
db_query("UPDATE `prefix_config` SET  `frage` =  'Forum V1.2: Schnellantwort aktivieren?' WHERE  `ic1_config`.`schl` =  'ilchbb_forum_qpost'");
db_query("UPDATE `prefix_config` SET  `frage` =  'Forum V1.2: Anzeigen, welche Mitglieder heute online waren?' WHERE  `ic1_config`.`schl` =  'ilchbb_forum_dayonline'");
db_query("UPDATE `prefix_config` SET  `frage` =  'Forum V1.2: Ab wie vielen Beitr&auml;gen gilt ein Thema als \"Hot Topic\"?' WHERE  `ic1_config`.`schl` =  'ilchbb_forum_hottopic'");
db_query("UPDATE `prefix_config` SET  `frage` =  'Forum V1.2: Sollen Mitglieder sich f&uuml;r Beitr&auml;ge bedanken k&ouml;nnen?' WHERE  `ic1_config`.`schl` =  'ilchbb_forum_ratepost'");
db_query("UPDATE `prefix_config` SET  `frage` =  'Forum V1.2: Min. Abstand zwischen Bedankungen f&uuml;r einen Beitrag in Sek.' WHERE  `ic1_config`.`schl` =  'ilchbb_forum_ratetime'");

db_query("UPDATE `prefix_user` SET `ilchbb_lastquery` = `llogin`");

/* Insert the Copyright-Credits for Grafics from phpBB-Forum-Style*/
db_query("INSERT INTO `prefix_credits` (`id`, `sys`, `name`, `version`, `url`, `lizenzname`, `lizenzurl`) VALUES ( NULL , 'gfx', 'ilchBB-Forum', '3.1', 'http://ilch.de', 'phpBB © Group', 'http://www.phpbb.com/');");

$rev='245';
$update_messages[$rev][] = 'IlchBB Installation';