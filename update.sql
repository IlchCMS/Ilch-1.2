/* -----> Rev. 37 <----- */
-- Dieser Eintrag muss beim "richtigen Update" noch verschoenert werden. Man kann ja nicht einfach die Tabelle loeschen :D

DROP TABLE `prefix_modules` ;

CREATE TABLE IF NOT EXISTS `prefix_modules` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `gshow` tinyint(1) NOT NULL DEFAULT '0',
  `ashow` tinyint(1) NOT NULL DEFAULT '0',
  `fright` tinyint(1) NOT NULL DEFAULT '0',
  `menu` varchar(200) NOT NULL,
  `pos` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de' AUTO_INCREMENT=39 ;

INSERT INTO `prefix_modules` (`id`, `url`, `name`, `gshow`, `ashow`, `fright`, `menu`, `pos`) VALUES
(1, 'gallery', 'Gallery', 1, 0, 1, 'Content', 4),
(2, 'menu', 'Navigation', 1, 0, 0, 'Admin', 2),
(3, 'groups', 'Teams', 1, 0, 1, 'Clanbox', 2),
(4, 'rules', 'Regeln', 1, 0, 1, 'Clanbox', 5),
(5, 'awards', 'Awards', 1, 0, 1, 'Clanbox', 3),
(6, 'forum', 'Forum', 1, 0, 1, 'Content', 1),
(7, 'archiv-downloads', 'Downloads', 1, 0, 1, 'Content', 2),
(8, 'kalender', 'Kalender', 1, 0, 1, 'Content', 7),
(9, 'wars', 'Wars', 0, 0, 1, '', 0),
(10, 'kasse', 'Kasse', 1, 0, 1, 'Clanbox', 4),
(11, 'gbook', 'Gästebuch', 1, 0, 1, 'Content', 5),
(12, 'awaycal', 'Awaycal', 0, 0, 0, '', 0),
(13, 'news', 'News', 1, 0, 1, 'Content', 0),
(14, 'allg', 'Konfiguration', 1, 0, 0, 'Admin', 0),
(15, 'backup', 'Backup', 1, 0, 0, 'Admin', 3),
(16, 'range', 'Ranks', 1, 0, 0, 'Admin', 4),
(17, 'ajaxchat', 'Ajax Chat', 1, 0, 1, 'Content', 10),
(18, 'wars-last', 'Lastwars', 1, 0, 0, 'Clanbox', 1),
(19, 'smilies', 'Smilies', 1, 0, 0, 'Admin', 5),
(20, 'newsletter', 'Newsletter', 1, 0, 0, 'Admin', 6),
(21, 'checkconf', 'Serverkonfiguration', 1, 0, 0, 'Admin', 7),
(22, 'user', 'User', 1, 0, 0, 'User', 0),
(23, 'grundrechte', 'Grundrechte', 1, 0, 0, 'User', 1),
(24, 'profilefields', 'Profilfelder', 1, 0, 0, 'User', 2),
(25, 'selfbp', 'Eigene Box/Page', 1, 0, 0, 'Eigene Box/Page', 0),
(26, 'wars-next', 'Nextwars', 1, 0, 0, 'Clanbox', 0),
(27, 'history', 'History', 1, 0, 0, 'Clanbox', 6),
(28, 'trains', 'Trainzeiten', 1, 0, 0, 'Clanbox', 7),
(29, 'archiv-links', 'Links', 1, 0, 0, 'Content', 3),
(30, 'vote', 'Umfragen', 1, 0, 0, 'Content', 6),
(31, 'contact', 'Kontakt', 1, 0, 0, 'Content', 8),
(32, 'impressum', 'Impressum', 1, 0, 0, 'Content', 9),
(33, 'archiv-partners', 'Partner', 1, 0, 0, 'Boxen', 0),
(34, 'picofx', 'Pic of X', 1, 0, 0, 'Boxen', 1),
(35, 'modules', 'Modulverwaltung', 1, 0, 0, 'Admin', 8),
(36, 'smtpconf', 'SMTP', 1, 0, 0, 'Admin', 1),
(37, 'puser', 'Nicht bestätigte Registrierungen', 0, 0, 0, '', 0),
(38, 'bbcode', 'BBcode 2.0', 1, 0, 1, 'Content', 11);


/* -----> Rev. 32 <----- */
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('menu_anz', 'input', 'Allgemeine Optionen', 'Wie viele Menüs sollen verwaltet werden?', '5');


/* -----> Rev. 31 <----- */
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('userpic_Fabreite', 'input', 'Forum Optionen', 'max Breite f&uuml;r das Userpic', '160');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('userpic_Fahohe', 'input', 'Forum Optionen', 'max H&ouml;he f&uuml;r das Userpic', '160');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('userpic_Fasize', 'input', 'Forum Optionen', 'max Gr&ouml;&szlig;e in Bytes f&uuml;r das Userpic', '32322');
ALTER TABLE `prefix_user` ADD `userpic` VARCHAR( 200 ) NOT NULL AFTER `avatar`;


--------------------------------------
---------> AJAX CHAT START <----------
--------------------------------------

/* -----> Rev. 23 <----- */
CREATE TABLE IF NOT EXISTS `prefix_ajax_chat_bans` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `prefix_ajax_chat_channels` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `right` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `prefix_ajax_chat_channels` (`name`, `right`) VALUES
('Public', 0);

CREATE TABLE IF NOT EXISTS `prefix_ajax_chat_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `active` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'unbekannt',
  `styleDefault` varchar(100) NOT NULL,
  `defaultChannelID` int(10) NOT NULL,
  `allowPrivateChannels` int(10) NOT NULL,
  `allowPrivateMessages` int(10) NOT NULL,
  `forceAutoLogin` int(10) NOT NULL,
  `showChannelMessages` int(10) NOT NULL,
  `chatClosed` int(10) NOT NULL,
  `allowGuestLogins` int(10) NOT NULL,
  `allowGuestWrite` int(10) NOT NULL,
  `allowGuestUserName` int(10) NOT NULL,
  `allowNickChange` int(10) NOT NULL,
  `allowUserMessageDelete` int(10) NOT NULL,
  `chatBotName` varchar(100) NOT NULL,
  `requestMessagesPriorChannelEnter` int(10) NOT NULL,
  `requestMessagesTimeDiff` int(10) NOT NULL,
  `requestMessagesLimit` int(10) NOT NULL,
  `maxUsersLoggedIn` int(10) NOT NULL,
  `maxMessageRate` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

INSERT INTO `prefix_ajax_chat_config` (`active`, `name`, `styleDefault`, `defaultChannelID`, `allowPrivateChannels`, `allowPrivateMessages`, `forceAutoLogin`, `showChannelMessages`, `chatClosed`, `allowGuestLogins`, `allowGuestWrite`, `allowGuestUserName`, `allowNickChange`, `allowUserMessageDelete`, `chatBotName`, `requestMessagesPriorChannelEnter`, `requestMessagesTimeDiff`, `requestMessagesLimit`, `maxUsersLoggedIn`, `maxMessageRate`) VALUES
('1', 'Standardkonfiguration', 'ilchCMS.css', 1, 1, 0, 0, 1, 0, 1, 1, 1, 1, 1, 'ChatBot', 1, 7, 10, 100, 20);

CREATE TABLE IF NOT EXISTS `prefix_ajax_chat_invitations` (
  `userID` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=FIXED;

CREATE TABLE IF NOT EXISTS `prefix_ajax_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `text` text COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `prefix_ajax_chat_online` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

INSERT INTO `prefix_modules` (`url`, `name`, `gshow`, `ashow`, `fright`) VALUES ('ajaxchat', 'Ajax Chat', 0, 1, 1);

--------------------------------------
----------> AJAX CHAT END <-----------
--------------------------------------


-- Keine Bedingungen --

/* -----> Rev. 5 <----- */
ALTER TABLE `prefix_user` ADD `name_clean` VARCHAR( 50 )  `name` varchar(50) NOT NULL default '' AFTER `name`;

/* -----> Rev. 10 <----- */
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('lang', 's', 'Allgemeine Optionen', 'Standard Sprache', 'de');

/* -----> Rev. 19 <----- */
CREATE TABLE IF NOT EXISTS `prefix_loader` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pos` int(10) NOT NULL,
  `task` varchar(200) NOT NULL,
  `file` varchar(200) NOT NULL,
  `description` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

INSERT INTO `prefix_loader` (`pos`, `task`, `file`, `description`) VALUES
(1, 'class', 'tpl.php', 'Template-Class'),
(2, 'class', 'design.php', 'Design-Class'),
(3, 'class', 'menu.php', 'Menu-Class'),
(4, 'class', 'bbcode.php', 'BB-Code 2.0 Class'),
(5, 'class', 'xajax.inc.php', 'Die xAjax-Class'),
(1, 'func', 'bbcode_config.php', 'BB-Code Einstellungen'),
(2, 'func', 'calender.php', 'Funktionen fuer den Kalender'),
(3, 'func', 'user.php', 'Userverwaltung Login/Logout und Rechte'),
(4, 'func', 'escape.php', 'Sicherheitsvorkehrungen vom Ilch CMS'),
(5, 'func', 'allg.php', 'Allgemeine Funktionen und Einstellungen'),
(6, 'func', 'debug.php', 'Ilch-Debugger'),
(7, 'func', 'bbcode.php', 'BB-Code Buttons und Funktionen'),
(8, 'func', 'profilefields.php', 'Profilfelder Funktionen'),
(9, 'func', 'statistic.php', 'Statistiken über den Besucher speichern'),
(10, 'func', 'listen.php', 'Funktionen, zum Listen erstellen'),
(11, 'func', 'forum.php', 'Alle Funktionen für das Forum'),
(12, 'func', 'warsys.php', 'Funktionen für das War-System'),
(13, 'func', 'ic_mime_type.php', 'Funktionen für den Umgang mit Dateiuploads'),
(14, 'func', 'lang.php', 'Funktionen zum Aufrufen der Sprachdateien');

-- Nur, wenn BB-Code 2.0 nicht installiert ist --
CREATE TABLE `prefix_bbcode_badword` (
	`fnBadwordNr` int(10) unsigned NOT NULL auto_increment,
	`fcBadPatter` varchar(70) NOT NULL default '',
	`fcBadReplace` varchar(70) NOT NULL default '',
	PRIMARY KEY  (`fnBadwordNr`) );
	INSERT INTO `prefix_bbcode_badword` (`fcBadPatter`,`fcBadReplace`) VALUES ('Idiot', '*peep*'), ('Arschloch', '*peep*');

CREATE TABLE `prefix_bbcode_buttons` (
	`fnButtonNr` int(10) unsigned NOT NULL auto_increment,
	`fnFormatB` tinyint(1) unsigned NOT NULL default '1',
	`fnFormatI` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatU` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatS` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatEmph` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatColor` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatSize` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatUrl` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatUrlAuto` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatEmail` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatLeft` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatCenter` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatRight` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatSmilies` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatList` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatKtext` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatImg` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatScreen` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatVideo` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatPhp` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatCss` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatHtml` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatCode` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatQuote` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatCountdown` tinyint(1) unsigned NOT NULL default '0',
	`fnFormatFlash` tinyint(1) unsigned NOT NULL default '0',
	 PRIMARY KEY  (`fnButtonNr`) );
	INSERT INTO `prefix_bbcode_buttons` (`fnButtonNr`, `fnFormatB`, `fnFormatI`, `fnFormatU`, `fnFormatS`, `fnFormatEmph`, `fnFormatColor`, `fnFormatSize`, `fnFormatUrl`, `fnFormatUrlAuto`, `fnFormatEmail`, `fnFormatLeft`, `fnFormatCenter`, `fnFormatRight`, `fnFormatSmilies`, `fnFormatList`, `fnFormatKtext`, `fnFormatImg`, `fnFormatScreen`, `fnFormatVideo`, `fnFormatPhp`, `fnFormatCss`, `fnFormatHtml`, `fnFormatCode`, `fnFormatQuote`, `fnFormatCountdown`) VALUES (1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

CREATE TABLE `prefix_bbcode_config` (
	`fnConfigNr` int(10) unsigned NOT NULL auto_increment,
	`fnYoutubeBreite` smallint(3) unsigned NOT NULL default '0',
	`fnYoutubeHoehe` smallint(3) unsigned NOT NULL default '0',
	`fcYoutubeHintergrundfarbe` varchar(7) NOT NULL default '',
	`fnGoogleBreite` smallint(3) unsigned NOT NULL default '0',
	`fnGoogleHoehe` smallint(3) unsigned NOT NULL default '0',
	`fcGoogleHintergrundfarbe` varchar(7) NOT NULL default '',
	`fnMyvideoBreite` smallint(3) unsigned NOT NULL default '0',
	`fnMyvideoHoehe` smallint(3) unsigned NOT NULL default '0',
	`fcMyvideoHintergrundfarbe` varchar(7) NOT NULL default '',
	`fnSizeMax` tinyint(2) unsigned NOT NULL default '0',
	`fnImgMaxBreite` smallint(3) unsigned NOT NULL default '0',
	`fnImgMaxHoehe` smallint(3) unsigned NOT NULL default '0',
	`fnScreenMaxBreite` smallint(3) unsigned NOT NULL default '0',
	`fnScreenMaxHoehe` smallint(3) unsigned NOT NULL default '0',
	`fnUrlMaxLaenge` smallint(3) unsigned NOT NULL default '0',
	`fnWortMaxLaenge` smallint(3) unsigned NOT NULL default '0',
	`fnFlashBreite` smallint(3) unsigned NOT NULL default '0',
	`fnFlashHoehe` smallint(3) unsigned NOT NULL default '0',
	`fcFlashHintergrundfarbe` varchar(7) NOT NULL default '',
	PRIMARY KEY  (`fnConfigNr`) );
	INSERT INTO `prefix_bbcode_config` (`fnConfigNr`, `fnYoutubeBreite`, `fnYoutubeHoehe`, `fcYoutubeHintergrundfarbe`, `fnGoogleBreite`, `fnGoogleHoehe`, `fcGoogleHintergrundfarbe`, `fnMyvideoBreite`, `fnMyvideoHoehe`, `fcMyvideoHintergrundfarbe`, `fnSizeMax`, `fnImgMaxBreite`, `fnImgMaxHoehe`, `fnScreenMaxBreite`, `fnScreenMaxHoehe`, `fnUrlMaxLaenge`, `fnWortMaxLaenge`, `fnFlashBreite`, `fnFlashHoehe`, `fcFlashHintergrundfarbe`) VALUES (1, 425, 350, '#000000', 400, 326, '#ffffff', 470, 406, '#ffffff', 20, 500, 500, 150, 150, 60, 70, 400, 300, '#ffffff');

CREATE TABLE `prefix_bbcode_design` (
	`fnDesignNr` int(10) unsigned NOT NULL auto_increment,
	`fcQuoteRandFarbe` varchar(7) NOT NULL default '',
	`fcQuoteTabelleBreite` varchar(7) NOT NULL default '',
	`fcQuoteSchriftfarbe` varchar(7) NOT NULL default '',
	`fcQuoteHintergrundfarbe` varchar(7) NOT NULL default '',
	`fcQuoteHintergrundfarbeIT` varchar(7) NOT NULL default '',
	`fcQuoteSchriftformatIT` varchar(6) NOT NULL default '',
	`fcQuoteSchriftfarbeIT` varchar(7) NOT NULL default '',
	`fcBlockRandFarbe` varchar(7) NOT NULL default '',
	`fcBlockTabelleBreite` varchar(7) NOT NULL default '',
	`fcBlockSchriftfarbe` varchar(7) NOT NULL default '',
	`fcBlockHintergrundfarbe` varchar(7) NOT NULL default '',
	`fcBlockHintergrundfarbeIT` varchar(7) NOT NULL default '',
	`fcBlockSchriftfarbeIT` varchar(7) NOT NULL default '',
	`fcKtextRandFarbe` varchar(7) NOT NULL default '',
	`fcKtextTabelleBreite` varchar(7) NOT NULL default '',
	`fcKtextRandFormat` varchar(6) NOT NULL default '',
	`fcEmphHintergrundfarbe` varchar(7) NOT NULL default '',
	`fcEmphSchriftfarbe` varchar(7) NOT NULL default '',
	`fcCountdownRandFarbe` varchar(7) NOT NULL default '',
	`fcCountdownTabelleBreite` varchar(7) NOT NULL default '',
	`fcCountdownSchriftfarbe` varchar(7) NOT NULL default '',
	`fcCountdownSchriftformat` varchar(7) NOT NULL default '',
	`fnCountdownSchriftsize` smallint(2) unsigned NOT NULL default '0',
	PRIMARY KEY  (`fnDesignNr`) );
	INSERT INTO `prefix_bbcode_design` (`fnDesignNr`, `fcQuoteRandFarbe`, `fcQuoteTabelleBreite`, `fcQuoteSchriftfarbe`, `fcQuoteHintergrundfarbe`, `fcQuoteHintergrundfarbeIT`, `fcQuoteSchriftformatIT`, `fcQuoteSchriftfarbeIT`, `fcBlockRandFarbe`, `fcBlockTabelleBreite`, `fcBlockSchriftfarbe`, `fcBlockHintergrundfarbe`, `fcBlockHintergrundfarbeIT`, `fcBlockSchriftfarbeIT`, `fcKtextRandFarbe`, `fcKtextTabelleBreite`, `fcKtextRandFormat`, `fcEmphHintergrundfarbe`, `fcEmphSchriftfarbe`, `fcCountdownRandFarbe`, `fcCountdownTabelleBreite`, `fcCountdownSchriftfarbe`, `fcCountdownSchriftformat`, `fnCountdownSchriftsize`) VALUES (1, '#f6e79d', '320', '#666666', '#f6e79d', '#faf7e8', 'italic', '#666666', '#f6e79d', '350', '#666666', '#f6e79d', '#faf7e8', '#FF0000', '#000000', '90%', 'dotted', '#ffd500', '#000000', '#FF0000', '90%', '#FF0000', 'bold', 10);

	

-- Beim Update darauf achten --
-- Alle Nutzernamen und Email-Adressen mit get_lower als Array durchlaufen
-- Nutzernamen als name_clean speichern und Email ersetzen
-- Doppelte Email-Adressen löschen
