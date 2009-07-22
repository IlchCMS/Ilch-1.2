CREATE TABLE `prefix_allg` (
  `id` int(5) unsigned NOT NULL auto_increment,
  `k` varchar(255) NOT NULL default '',
  `v1` varchar(255) NOT NULL default '',
  `v2` varchar(255) NOT NULL default '',
  `v3` varchar(255) NOT NULL default '',
  `v4` varchar(255) NOT NULL default '',
  `v5` varchar(255) NOT NULL default '',
  `v6` varchar(255) NOT NULL default '',
  `t1` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (1, 'kontakt', '', '1', '1', '', '', '', '#webmaster@test.de|Allgemein');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (2, 'impressum', 'Verantwortlich für diese Seite:', 'Max Mustermann', 'Muster Str. 43', '12345 Musterhausen', '', '', '');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (3, 'kasse_kontodaten', '', '', '', '', '', '', 'Kontoinhaber: Max Mustermann\r\nBankname: Muster Sparkasse\r\nKontonummer: 123\r\nBankleitzahl: 123\r\nBIC: 123\r\nIBAN: 123\r\nVerwendungszweck: Spende für ilch.de ;-)');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (4, 'picofx', 'pic', '0', '', '', '', '', '');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (5, 'picofx', 'directory', '0', '', '', '', '', '');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (6, 'picofx', 'interval', '0', '', '', '', '', '');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (7, 'picofx', 'nextchange', '0', '', '', '', '', '');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (8, 'picofx', 'picwidth', '100', '', '', '', '', '');
INSERT INTO `prefix_allg` (`id`,`k`,`v1`,`v2`,`v3`,`v4`,`v5`,`v6`,`t1`) VALUES (9, 'trainzeiten', '', '', '', '', '', '', 'Kein Train#Kein Train#Kein Train#Kein Train#Kein Train#Kein Train#Kein Train');

CREATE TABLE `prefix_awards` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `time` date NOT NULL default '0000-00-00',
  `platz` varchar(10) NOT NULL default '',
  `team` varchar(100) NOT NULL default '',
  `wofur` text NOT NULL,
  `bild` varchar(100) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_awaycal` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(9) default NULL,
  `pruef` tinyint(2) default '2',
  `von` date default NULL,
  `bis` date default NULL,
  `betreff` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_config` (
  `schl` varchar(50) NOT NULL default '',
  `typ` varchar(10) NOT NULL default '',
  `kat` varchar(50) NOT NULL default '',
  `frage` varchar(255) NOT NULL default '',
  `wert` text NOT NULL,
  `pos` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`schl`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gbook_posts_per_site', 'input', 'G&auml;stebuch Optionen', 'Eintr&auml;ge pro Seite', '20');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gallery_imgs_per_line', 'input', 'Gallery Optionen', 'Bilder pro Zeile', '4');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Aanz', 'input', 'Archiv Optionen', 'Anzahl Banner in der Partner Box', '3');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Nlimit', 'input', 'News Optionen', 'News pro Seite', '5');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Ftanz', 'input', 'Forum Optionen', 'Themen auf einer Seite', '20');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Fpanz', 'input', 'Forum Optionen', 'Posts auf einer Seite', '20');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_avatar_upload', 'r2', 'Forum Optionen', 'Avatar Upload?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gallery_imgs_per_site', 'input', 'Gallery Optionen', 'Bilder pro Seite', '12');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gallery_preview_width', 'input', 'Gallery Optionen', 'Breite der Vorschaubilder', '80');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Fpmf', 'r2', 'Forum Optionen', 'Nachrichten Function', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gfx', 's', 'Allgemeine Optionen', 'Standard Design', 'ilchClan');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('adminMail', 'input', 'Allgemeine Optionen', 'Administrator eMail', 'test');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('smodul', 's', 'Allgemeine Optionen', 'Start Modul der Seite', 'news');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('title', 'input', 'Allgemeine Optionen', 'Titel der Seite', 'Das Clanscript für jeden!');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Fabreite', 'input', 'Forum Optionen', 'max Breite f&uuml;r den Avatar', '80');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Fahohe', 'input', 'Forum Optionen', 'max H&ouml;he f&uuml;r den Avatar', '80');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Fasize', 'input', 'Forum Optionen', 'max Gr&ouml;&szlig;e in Bytes f&uuml;r den Avatar', '16161');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Gsperre', 'input', 'G&auml;stebuch Optionen', 'Ip Sperre in Sekunden', '3600');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Gtxtl', 'input', 'G&auml;stebuch Optionen', 'max Text l&auml;nge im G&auml;stebuch', '600');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Aart', 'r2', 'Archiv Optionen', 'Soll die Partner Box sortiert werden', '0');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Ngkoms', 'r2', 'News Optionen', 'D&uuml;rfen G&auml;ste Kommentare schreiben?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('Nukoms', 'r2', 'News Optionen', 'D&uuml;rfen User Kommentare schreiben?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('allg_menupoint_access', 'r2', 'Allgemeine Optionen', 'Zugriff auf nicht im Men&uuml; verlinkte Module f&uuml;r alle?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_regist', 'r2', 'Forum Optionen', 'D&uuml;rfen sich User registrieren?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_regist_user_pass', 'r2', 'Forum Optionen', 'Passwort vom User beim registrieren selber w&auml;hlbar?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_regist_confirm_link', 'r2', 'Forum Optionen', 'Registrierung per Link im eMail best&auml;tigen?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('archiv_down_userupload', 'r2', 'Archiv Optionen', 'D&uuml;rfen User Dateien hochladen?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('show_session_id', 'r2', 'Allgemeine Optionen', 'SessionID bei G&auml;sten anzeigen?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('wars_last_komms', 's', 'Wars Optionen', 'Kommentare für Lastwars', '0');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('wars_last_limit', 'input', 'Wars Optionen', 'Lastwars pro Seite.', '15');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_usergallery', 'r2', 'Forum Optionen', 'Darf jeder User seine eigene Gallery haben?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gallery_normal_width', 'input', 'Gallery Optionen', 'Breite der normalen Bilder', '500');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gallery_img_koms', 'r2', 'Gallery Optionen', 'Kommentare für Bilder zulassen?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_max_sig', 'input', 'Forum Optionen', 'max. Anzahl Zeichen in der Signatur', '200');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('gbook_koms_for_inserts', 'r2', 'G&auml;stebuch Optionen', 'Kommentare f&uuml;r G&auml;stebucheintr&auml;ge?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('allg_regeln', 'textarea', 'Allgemeine Optionen', 'Die Regeln für die Seite (bbcode erlaubt)', '[list]\r\n[*]Die Registrierung ist völlig Kostenlos\r\n[*]Die Betreiber der Seite übernehmen keine Haftung.\r\n[*]Bitte verhalten Sie sich angemessen und mit Respekt gegenüber den anderen Community Mitgliedern.\r\n[/list]');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('teams_show_list', 'r2', 'Team Optionen', 'Avatar bei den Usern?', '0');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('teams_show_cat', 'r2', 'Team Optionen', 'Sollen die Squads als Kategorie angezeigt werden?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('allg_bbcode_max_img_width', 'input', 'Allgemeine Optionen', 'Wie Breit solle ein Bild maximal sein (in Pixeln)?', '230');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('forum_default_avatar', 'r2', 'Forum Optionen', 'Standard Avatar anzeigen?', '1');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('allg_default_subject', 'input', 'Allgemeine Optionen', 'Standard Absender bei eMails', 'automatische eMail');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_maxwordlength', 'input', 'Shoutbox Optionen', 'Maximale Wortl&auml;nge in der Shoutbox', '10');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_recht', 'grecht', 'Shoutbox Optionen', 'Schreiben in der Shoutbox ab?', '0');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_limit', 'input', 'Shoutbox Optionen', 'Anzahl angezeigter Nachrichten', '5');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam', 'grecht2', 'Allgemeine Optionen', 'Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-2');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('joinus_rules', 'r2', 'Team Optionen', 'Regeln bei Joinus vollst&auml;ndig anzeigen?', '0');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('groups_forall', 'r2', 'Team Optionen', 'Modulrecht <i>Gruppen</i> auf eigene Gruppe beschr&auml;nken?', '1');
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp', 'r2', 'Mail Optionen', 'Soll anstatt der PHP Funktion mail() versucht werden, den angegeben SMTP Server zum Versenden von Mails zu benutzen?', '0', 0);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_login', 'input', 'Mail Optionen', 'SMTP Benutzername', '', 3);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_password', 'password', 'Mail Optionen', 'SMTP Passwort', '', 4);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_host', 'input', 'Mail Optionen', 'Hostadresse des SMTP', '', 1);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_email', 'input', 'Mail Optionen', 'E-Mail-Adresse des Accounts', '', 2);


CREATE TABLE `prefix_counter` (
  `date` date NOT NULL,
  `count` smallint(5) NOT NULL default '0'
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_downcats` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat` mediumint(8) default '0',
  `pos` smallint(6) NOT NULL default '0',
  `recht` tinyint(4) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `desc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_downloads` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat` mediumint(8) default '0',
  `pos` smallint(6) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `creater` varchar(250) default NULL,
  `version` varchar(20) default NULL,
  `hits` int(11) NOT NULL default '0',
  `downs` int(11) NOT NULL default '0',
  `vote_klicks` int(11) NOT NULL default '0',
  `vote_wertung` float NOT NULL default '0',
  `url` varchar(100) NOT NULL default '',
  `surl` varchar(100) NOT NULL default '',
  `ssurl` varchar(255) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `desc` varchar(255) default NULL,
  `descl` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_forumcats` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `cid` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `pos` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_forummods` (
  `uid` mediumint(9) NOT NULL default '0',
  `fid` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`fid`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_forums` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `cid` mediumint(8) NOT NULL default '0',
  `last_post_id` int(11) NOT NULL default '0',
  `view` smallint(6) NOT NULL default '0',
  `reply` smallint(6) NOT NULL default '0',
  `start` smallint(6) NOT NULL default '0',
  `pos` tinyint(3) NOT NULL default '0',
  `posts` mediumint(8) NOT NULL default '0',
  `topics` mediumint(8) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `besch` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_gallery_cats` (
  `id` mediumint(9) NOT NULL auto_increment,
  `cat` mediumint(9) NOT NULL default '0',
  `pos` smallint(6) NOT NULL default '0',
  `recht` smallint(6) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `besch` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_gallery_imgs` (
  `id` int(11) NOT NULL auto_increment,
  `cat` mediumint(9) NOT NULL default '0',
  `klicks` mediumint(9) NOT NULL default '0',
  `vote_wertung` float NOT NULL default '0',
  `vote_klicks` mediumint(9) NOT NULL default '0',
  `datei_name` varchar(50) NOT NULL default '',
  `endung` varchar(5) NOT NULL default '',
  `besch` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_gbook` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `mail` varchar(100) NOT NULL default '',
  `page` varchar(100) NOT NULL default '',
  `time` int(20) NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `txt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_groupfuncs` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `pos` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_groupfuncs` (`id`,`name`,`pos`) VALUES (1, 'Leader', 1);
INSERT INTO `prefix_groupfuncs` (`id`,`name`,`pos`) VALUES (2, 'Co-Leader', 2);
INSERT INTO `prefix_groupfuncs` (`id`,`name`,`pos`) VALUES (3, 'Member', 3);
INSERT INTO `prefix_groupfuncs` (`id`,`name`,`pos`) VALUES (4, 'Trial', 4);

CREATE TABLE `prefix_groups` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `mod1` mediumint(9) NOT NULL default '0',
  `mod2` mediumint(9) NOT NULL default '0',
  `mod3` mediumint(9) NOT NULL default '0',
  `mod4` mediumint(9) NOT NULL default '0',
  `pos` smallint(6) NOT NULL default '0',
  `zeigen` tinyint(1) NOT NULL default '0',
  `show_joinus` tinyint(1) NOT NULL default '0',
  `show_fightus` tinyint(1) NOT NULL default '0',
  `img` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_groupusers` (
  `uid` mediumint(9) NOT NULL default '0',
  `gid` smallint(6) NOT NULL default '0',
  `fid` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`gid`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_grundrechte` (
  `id` smallint(6) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (0, 'Gast');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-1, 'User');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-2, 'Superuser');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-3, 'Trialmember');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-4, 'Member');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-5, 'CoLeader');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-6, 'Leader');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-7, 'SiteAdmin');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-8, 'CoAdmin');
INSERT INTO `prefix_grundrechte` (`id`,`name`) VALUES (-9, 'Admin');

CREATE TABLE `prefix_history` (
  `id` smallint(6) NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `title` varchar(100) NOT NULL default '',
  `txt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_kalender` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `gid` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `text` text NOT NULL,
  `recht` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_kasse` (
  `id` int(14) NOT NULL auto_increment,
  `datum` date NOT NULL default '0000-00-00',
  `name` varchar(50) NOT NULL default '',
  `verwendung` varchar(50) NOT NULL default '',
  `betrag` float NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_koms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` mediumint(9) NOT NULL default '0',
  `cat` varchar(10) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `text` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_linkcats` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat` mediumint(8) default '0',
  `pos` smallint(6) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `desc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_links` (
  `id` smallint(6) NOT NULL auto_increment,
  `cat` mediumint(8) default '0',
  `pos` smallint(6) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `desc` varchar(255) NOT NULL default '',
  `banner` varchar(100) NOT NULL default '',
  `link` varchar(100) NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_menu` (
  `wo` tinyint(1) NOT NULL default '0',
  `pos` tinyint(4) NOT NULL default '0',
  `was` tinyint(1) NOT NULL default '0',
  `ebene` tinyint(2) NOT NULL default '0',
  `recht` tinyint(2) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `path` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`pos`,`wo`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 0, 3, 0, 0, 'Menü', 'allianz.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 11, 3, 0, 0, 'Clan Menü', 'allianz.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 22, 1, 0, 0, 'Login', 'login.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 5, 7, 0, 0, 'Links', 'links');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 6, 7, 0, 0, 'Downloads', 'downloads');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 7, 7, 0, 0, 'Gallery', 'gallery');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 2, 7, 0, 0, 'Forum', 'forum');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 15, 7, 0, 0, 'Wars', 'wars');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 23, 1, 0, 0, 'Shoutbox', 'shoutbox.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 19, 7, 0, 0, 'Awards', 'awards');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 13, 7, 1, 0, 'Fightus', 'fightus');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 14, 7, 1, 0, 'Joinus', 'joinus');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 20, 7, 0, 0, 'Regeln', 'rules');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 12, 7, 0, 0, 'Squads', 'teams');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 3, 7, 1, 0, 'Mitglieder', 'user');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 1, 1, 0, 0, 'Umfrage', 'vote.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 2, 1, 0, 0, 'Allianz', 'allianz.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 3, 1, 0, 0, 'Statistik', 'statistik.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 2, 1, 0, 0, 'Lastwars', 'lastwars.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 3, 1, 0, 0, 'Nextwars', 'nextwars.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 4, 1, 0, 0, 'Newsletter', 'newsletter.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 1, 1, 0, 0, 'Last Forum', 'lastforum.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 4, 1, 0, 0, 'Last News', 'lastnews.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 5, 1, 0, 0, 'Designs', 'designs.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 17, 7, 0, -3, 'Training', 'trains');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 10, 7, 0, 0, 'Impressum', 'impressum');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 21, 7, 0, 0, 'History', 'history');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 1, 7, 0, 0, 'News', 'news');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 0, 1, 0, 0, 'Kalender', 'calender.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 9, 7, 0, 0, 'Kontakt', 'contact');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 8, 7, 0, 0, 'Kalender', 'kalender');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 5, 1, 0, 0, 'PicOfX', 'picofx.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 16, 7, 0, 0, 'Kasse', 'kasse');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 0, 1, 0, 0, 'Suchen', 'search.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 4, 7, 0, 0, 'Gbook', 'gbook');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (3, 6, 1, 0, 0, 'Geburtstag', 'geburtstag.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (2, 6, 1, 0, 0, 'Online', 'online.php');
INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`) VALUES (1, 18, 7, 0, -3, 'Away', 'awaycal');

CREATE TABLE `prefix_modulerights` (
  `uid` mediumint(9) NOT NULL default '0',
  `mid` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`mid`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_modules` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `url` varchar(20) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `gshow` tinyint(1) NOT NULL default '0',
  `ashow` tinyint(1) NOT NULL default '0',
  `fright` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (1, 'gallery', 'Gallery', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (2, 'news', 'News', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (3, 'groups', 'Gruppen', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (4, 'rules', 'Regeln', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (5, 'awards', 'Awards', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (6, 'forum', 'Forum', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (7, 'archiv-downloads', 'Downloads', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (8, 'kalender', 'Kalender', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (9, 'wars', 'Wars', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (10, 'kasse', 'Kasse', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (11, 'gbook', 'Gbook', 1, 0, 1);
INSERT INTO `prefix_modules` (`id`,`url`,`name`,`gshow`,`ashow`,`fright`) VALUES (12, 'awaycal', 'Awaycal', 0, 0, 1);

CREATE TABLE `prefix_news` (
  `news_id` int(10) unsigned NOT NULL auto_increment,
  `news_title` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `news_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `news_recht` int(11) NOT NULL default '0',
  `news_kat` varchar(100) NOT NULL default '',
  `news_text` text NOT NULL,
  PRIMARY KEY  (`news_id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_newsletter` (
  `email` varchar(100) NOT NULL default ''
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_online` (
  `uptime` datetime default NULL,
  `sid` varchar(32) NOT NULL default '',
  `ipa` varchar(15) NOT NULL default '',
  `uid` mediumint(9) NOT NULL default '0'
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_partners` (
  `id` smallint(6) NOT NULL auto_increment,
  `pos` smallint(6) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `banner` varchar(100) NOT NULL default '',
  `link` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_partners` (`id`,`pos`,`name`,`banner`,`link`) VALUES (1, 0, 'www.ilch.de', 'http://www.ilch.de/images/banner/copy_by_ilch.gif', 'http://www.ilch.de');

CREATE TABLE `prefix_pm` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `sid` mediumint(8) NOT NULL default '0',
  `eid` mediumint(8) NOT NULL default '0',
  `gelesen` tinyint(1) NOT NULL default '0',
  `status` tinyint( 1 ) NOT NULL default '0',
  `time` int(20) NOT NULL default '0',
  `titel` varchar(100) NOT NULL default '',
  `txt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_poll` (
  `poll_id` mediumint(8) unsigned NOT NULL auto_increment,
  `frage` varchar(200) NOT NULL default '',
  `recht` tinyint(1) NOT NULL default '0',
  `stat` tinyint(1) NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY  (`poll_id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_poll_res` (
  `sort` tinyint(2) NOT NULL default '0',
  `poll_id` mediumint(8) NOT NULL default '0',
  `antw` varchar(100) NOT NULL default '',
  `res` smallint(6) NOT NULL default '0'
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_posts` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` mediumint(8) NOT NULL default '0',
  `fid` mediumint(9) NOT NULL default '0',
  `erst` varchar(100) NOT NULL default '',
  `erstid` int(10) NOT NULL default '0',
  `time` bigint(20) NOT NULL default '0',
  `txt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_profilefields` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `show` varchar(20) NOT NULL default '',
  `pos` mediumint(9) NOT NULL default '0',
  `func` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (1, 'msn', 12, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (2, 'opt_pm', 9, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (3, 'opt_mail', 8, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (4, 'yahoo', 13, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (5, 'sig', 6, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (6, 'wohnort', 4, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (7, 'icq', 11, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (8, 'gebdatum', 1, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (9, 'geschlecht', 2, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (10, 'staat', 0, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (11, 'status', 3, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (12, 'Kontakt', 7, 2);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (13, 'aim', 14, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (14, 'homepage', 5, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (15, 'opt_pm_popup', 10, 3);
INSERT INTO `prefix_profilefields` (`id`,`show`,`pos`,`func`) VALUES (16, 'usergallery', 15, 3);

CREATE TABLE `prefix_ranks` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `bez` varchar(100) NOT NULL default '',
  `min` int(10) NOT NULL default '0',
  `spez` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (1, 'Grünschnabel', 1, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (2, 'Jungspund', 25, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (3, 'Mitglied', 50, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (4, 'Eroberer', 75, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (6, 'Doppel-As', 150, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (7, 'Tripel-As', 250, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (8, 'Haudegen', 500, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (9, 'Routinier', 1000, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (15, 'König', 2000, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (11, 'Kaiser', 5000, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (12, 'Legende', 7000, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (13, 'Foren Gott', 10000, 0);
INSERT INTO `prefix_ranks` (`id`,`bez`,`min`,`spez`) VALUES (14, 'Administrator', 0, 1);

CREATE TABLE `prefix_rules` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `zahl` smallint(6) NOT NULL default '0',
  `titel` varchar(200) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_shoutbox` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `nickname` varchar(50) NOT NULL default '',
  `textarea` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_smilies` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `ent` varchar(50) NOT NULL default '',
  `emo` varchar(75) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (1, ':)', 'Smilie', '1.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (2, ':D', 'Lachen', '2.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (3, ':O', 'Opssss', '3.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (4, ':P', 'Auslachen', '4.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (5, ';)', 'Zwinker', '5.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (6, ':(', 'Traurig', '6.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (7, ':S', 'Grummel', '7.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (8, ':|', 'Sauer', '8.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (9, ':\'(', 'Weinen', '9.gif');
INSERT INTO `prefix_smilies` (`id`,`ent`,`emo`,`url`) VALUES (10, ':@', 'Veraergert', '10.gif');


CREATE TABLE `prefix_stats` (
  `wtag` tinyint(2) NOT NULL default '0',
  `stunde` tinyint(2) NOT NULL default '0',
  `day` tinyint(2) NOT NULL default '0',
  `mon` tinyint(2) NOT NULL default '0',
  `yar` int(4) NOT NULL default '0',
  `os` varchar(50) NOT NULL default '',
  `browser` varchar(50) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `ref` varchar(255) NOT NULL default ''
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_topic_alerts` (
  `tid` mediumint(9) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`tid`,`uid`)
) TYPE=MyISAM;

CREATE TABLE `prefix_topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fid` int(10) NOT NULL default '0',
  `last_post_id` mediumint(9) NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `erst` varchar(100) NOT NULL default '',
  `art` int(1) NOT NULL default '0',
  `stat` int(1) NOT NULL default '0',
  `rep` int(10) NOT NULL default '0',
  `hit` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `name_clean` varchar(50) NOT NULL default '',
  `pass` varchar(32) NOT NULL default '',
  `recht` int(1) NOT NULL default '0',
  `posts` int(5) NOT NULL default '0',
  `regist` int(20) default NULL,
  `email` varchar(100) NOT NULL default '',
  `llogin` int(20) default NULL,
  `spezrank` mediumint(9) NOT NULL default '0',
  `opt_pm` tinyint(1) NOT NULL default '0',
  `opt_pm_popup` tinyint(1) NOT NULL default '0',
  `opt_mail` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `geschlecht` tinyint(1) NOT NULL default '0',
  `gebdatum` date NOT NULL default '0000-00-00',
  `wohnort` varchar(50) NOT NULL default '',
  `homepage` varchar(100) NOT NULL default '',
  `staat` varchar(50) NOT NULL default '',
  `avatar` varchar(100) NOT NULL default '',
  `icq` varchar(20) NOT NULL default '',
  `msn` varchar(50) NOT NULL default '',
  `yahoo` varchar(50) NOT NULL default '',
  `aim` varchar(50) NOT NULL default '',
  `sig` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_usercheck` (
  `check` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `pass` varchar(100) NOT NULL default '',
  `datime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ak` tinyint(4) NOT NULL default '0',
  `groupid` tinyint(4) NOT NULL,
  PRIMARY KEY  (`check`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_userfields` (
  `uid` mediumint(8) NOT NULL default '0',
  `fid` mediumint(8) NOT NULL default '0',
  `val` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uid`,`fid`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_usergallery` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `uid` mediumint(9) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `endung` varchar(5) NOT NULL default '',
  `besch` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_warmaps` (
  `wid` smallint(6) NOT NULL default '0',
  `mnr` tinyint(4) NOT NULL default '0',
  `map` varchar(100) NOT NULL default '',
  `opp` MEDIUMINT NOT NULL default '0',
  `owp` MEDIUMINT NOT NULL default '0',
  PRIMARY KEY  (`wid`,`mnr`)
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_warmember` (
  `wid` smallint(6) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `aktion` tinyint(1) NOT NULL default '0',
  `kom` text NOT NULL
) TYPE=MyISAM COMMENT='powered by ilch.de';

CREATE TABLE `prefix_wars` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `datime` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` tinyint(2) NOT NULL default '0',
  `wlp` tinyint(1) NOT NULL default '0',
  `owp` MEDIUMINT NOT NULL default '0',
  `opp` MEDIUMINT NOT NULL default '0',
  `gegner` varchar(100) NOT NULL default '',
  `tag` varchar(100) NOT NULL default '',
  `page` varchar(100) NOT NULL default '',
  `mail` varchar(100) NOT NULL default '',
  `icq` varchar(100) NOT NULL default '',
  `wo` varchar(100) NOT NULL default '',
  `tid` smallint(6) NOT NULL default '0',
  `mod` varchar(100) NOT NULL default '',
  `game` varchar(100) NOT NULL default '',
  `mtyp` varchar(100) NOT NULL default '',
  `land` varchar(100) NOT NULL default '',
  `txt` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='powered by ilch.de';
