-- die Standard Clan-Installation mit allen Menüs für Community und Clans

-- in der Ersten Kommentarzeile wird die Beschreibung der Installationsdatei angegeben, welche als Hilfetext während der Installation ausgegeben wird.

-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
-- Dumped by GeCk0
-- Host: localhost
-- Erstellungszeit: 11. September 2011 um 14:58
-- Server Version: 5.1.50
-- PHP-Version: 5.3.8-ZS5.5.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `ilch_12`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_allg`
--

DROP TABLE IF EXISTS `prefix_allg`;
CREATE TABLE IF NOT EXISTS `prefix_allg` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `k` varchar(255) NOT NULL DEFAULT '',
  `v1` varchar(255) NOT NULL DEFAULT '',
  `v2` varchar(255) NOT NULL DEFAULT '',
  `v3` varchar(255) NOT NULL DEFAULT '',
  `v4` varchar(255) NOT NULL DEFAULT '',
  `v5` varchar(255) NOT NULL DEFAULT '',
  `v6` varchar(255) NOT NULL DEFAULT '',
  `t1` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten für Tabelle `prefix_allg`
--

INSERT INTO `prefix_allg` (`k`, `v1`, `v2`, `v3`, `v4`, `v5`, `v6`, `t1`) VALUES
('kontakt', ' ', '1', '1', ' ', ' ', ' ', 'admin@host.de|Webmaster'),
('impressum', 'Verantwortlich für diese Seite:', 'Max Mustermann', 'Muster Str. 43', '12345 Musterhausen', '', '', ''),
('kasse_kontodaten', '', '', '', '', '', '', 'Kontoinhaber: Max Mustermann\r\nBankname: Muster Sparkasse\r\nKontonummer: 123\r\nBankleitzahl: 123\r\nBIC: 123\r\nIBAN: 123\r\nVerwendungszweck: Spende für ilch.de ;-)'),
('picofx', 'pic', '2.jpg', '', '', '', '', ''),
('picofx', 'directory', '0', '', '', '', '', ''),
('picofx', 'interval', '0', '', '', '', '', ''),
('picofx', 'nextchange', '2011-10-05', '', '', '', '', ''),
('picofx', 'picwidth', '100', '', '', '', '', ''),
('trainzeiten', '', '', '', '', '', '', 'Kein Train#Kein Train#Kein Train#Kein Train#Kein Train#Kein Train#Kein Train'),
('smtpconf', '', '', '', '', '', '', '');


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_awards`
--

DROP TABLE IF EXISTS `prefix_awards`;
CREATE TABLE IF NOT EXISTS `prefix_awards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` date NOT NULL DEFAULT '0000-00-00',
  `platz` varchar(10) NOT NULL DEFAULT '',
  `team` varchar(100) NOT NULL DEFAULT '',
  `wofur` text NOT NULL,
  `bild` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_awards`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_awaycal`
--

DROP TABLE IF EXISTS `prefix_awaycal`;
CREATE TABLE IF NOT EXISTS `prefix_awaycal` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) DEFAULT NULL,
  `pruef` tinyint(2) DEFAULT '2',
  `von` date DEFAULT NULL,
  `bis` date DEFAULT NULL,
  `betreff` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_awaycal`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_bbcode_badword`
--

DROP TABLE IF EXISTS `prefix_bbcode_badword`;
CREATE TABLE IF NOT EXISTS `prefix_bbcode_badword` (
  `fnBadwordNr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fcBadPatter` varchar(70) NOT NULL DEFAULT '',
  `fcBadReplace` varchar(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`fnBadwordNr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_bbcode_badword`
--

INSERT INTO `prefix_bbcode_badword` (`fnBadwordNr`, `fcBadPatter`, `fcBadReplace`) VALUES
(1, 'Idiot', '*peep*'),
(2, 'Arschloch', '*peep*');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_bbcode_buttons`
--

DROP TABLE IF EXISTS `prefix_bbcode_buttons`;
CREATE TABLE IF NOT EXISTS `prefix_bbcode_buttons` (
  `fnButtonNr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fnFormatB` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fnFormatI` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatU` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatS` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatEmph` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatColor` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatSize` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatUrl` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatUrlAuto` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatEmail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatLeft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatCenter` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatRight` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatSmilies` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatList` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatKtext` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatImg` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatScreen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatVideo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatPhp` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatCss` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatHtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatCode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatQuote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatCountdown` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fnFormatFlash` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fnButtonNr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_bbcode_buttons`
--

INSERT INTO `prefix_bbcode_buttons` (`fnButtonNr`, `fnFormatB`, `fnFormatI`, `fnFormatU`, `fnFormatS`, `fnFormatEmph`, `fnFormatColor`, `fnFormatSize`, `fnFormatUrl`, `fnFormatUrlAuto`, `fnFormatEmail`, `fnFormatLeft`, `fnFormatCenter`, `fnFormatRight`, `fnFormatSmilies`, `fnFormatList`, `fnFormatKtext`, `fnFormatImg`, `fnFormatScreen`, `fnFormatVideo`, `fnFormatPhp`, `fnFormatCss`, `fnFormatHtml`, `fnFormatCode`, `fnFormatQuote`, `fnFormatCountdown`, `fnFormatFlash`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_bbcode_config`
--

DROP TABLE IF EXISTS `prefix_bbcode_config`;
CREATE TABLE IF NOT EXISTS `prefix_bbcode_config` (
  `fnConfigNr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fnYoutubeBreite` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnYoutubeHoehe` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fcYoutubeHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  `fnGoogleBreite` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnGoogleHoehe` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fcGoogleHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  `fnMyvideoBreite` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnMyvideoHoehe` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fcMyvideoHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  `fnSizeMax` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fnImgMaxBreite` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnImgMaxHoehe` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnScreenMaxBreite` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnScreenMaxHoehe` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnUrlMaxLaenge` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnWortMaxLaenge` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnFlashBreite` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fnFlashHoehe` smallint(3) unsigned NOT NULL DEFAULT '0',
  `fcFlashHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  PRIMARY KEY (`fnConfigNr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


--
-- Daten f&uuml;r Tabelle `prefix_bbcode_config`
--

INSERT INTO `prefix_bbcode_config` (`fnConfigNr`, `fnYoutubeBreite`, `fnYoutubeHoehe`, `fcYoutubeHintergrundfarbe`, `fnGoogleBreite`, `fnGoogleHoehe`, `fcGoogleHintergrundfarbe`, `fnMyvideoBreite`, `fnMyvideoHoehe`, `fcMyvideoHintergrundfarbe`, `fnSizeMax`, `fnImgMaxBreite`, `fnImgMaxHoehe`, `fnScreenMaxBreite`, `fnScreenMaxHoehe`, `fnUrlMaxLaenge`, `fnWortMaxLaenge`, `fnFlashBreite`, `fnFlashHoehe`, `fcFlashHintergrundfarbe`) VALUES
(1, 425, 350, '#000000', 400, 326, '#ffffff', 470, 406, '#ffffff', 20, 500, 500, 150, 150, 60, 70, 400, 300, '#ffffff');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_bbcode_design`
--

DROP TABLE IF EXISTS `prefix_bbcode_design`;
CREATE TABLE IF NOT EXISTS `prefix_bbcode_design` (
  `fnDesignNr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fcQuoteRandFarbe` varchar(7) NOT NULL DEFAULT '',
  `fcQuoteTabelleBreite` varchar(7) NOT NULL DEFAULT '',
  `fcQuoteSchriftfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcQuoteHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcQuoteHintergrundfarbeIT` varchar(7) NOT NULL DEFAULT '',
  `fcQuoteSchriftformatIT` varchar(6) NOT NULL DEFAULT '',
  `fcQuoteSchriftfarbeIT` varchar(7) NOT NULL DEFAULT '',
  `fcBlockRandFarbe` varchar(7) NOT NULL DEFAULT '',
  `fcBlockTabelleBreite` varchar(7) NOT NULL DEFAULT '',
  `fcBlockSchriftfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcBlockHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcBlockHintergrundfarbeIT` varchar(7) NOT NULL DEFAULT '',
  `fcBlockSchriftfarbeIT` varchar(7) NOT NULL DEFAULT '',
  `fcKtextRandFarbe` varchar(7) NOT NULL DEFAULT '',
  `fcKtextTabelleBreite` varchar(7) NOT NULL DEFAULT '',
  `fcKtextRandFormat` varchar(6) NOT NULL DEFAULT '',
  `fcEmphHintergrundfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcEmphSchriftfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcCountdownRandFarbe` varchar(7) NOT NULL DEFAULT '',
  `fcCountdownTabelleBreite` varchar(7) NOT NULL DEFAULT '',
  `fcCountdownSchriftfarbe` varchar(7) NOT NULL DEFAULT '',
  `fcCountdownSchriftformat` varchar(7) NOT NULL DEFAULT '',
  `fnCountdownSchriftsize` smallint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fnDesignNr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_bbcode_design`
--

INSERT INTO `prefix_bbcode_design` (`fnDesignNr`, `fcQuoteRandFarbe`, `fcQuoteTabelleBreite`, `fcQuoteSchriftfarbe`, `fcQuoteHintergrundfarbe`, `fcQuoteHintergrundfarbeIT`, `fcQuoteSchriftformatIT`, `fcQuoteSchriftfarbeIT`, `fcBlockRandFarbe`, `fcBlockTabelleBreite`, `fcBlockSchriftfarbe`, `fcBlockHintergrundfarbe`, `fcBlockHintergrundfarbeIT`, `fcBlockSchriftfarbeIT`, `fcKtextRandFarbe`, `fcKtextTabelleBreite`, `fcKtextRandFormat`, `fcEmphHintergrundfarbe`, `fcEmphSchriftfarbe`, `fcCountdownRandFarbe`, `fcCountdownTabelleBreite`, `fcCountdownSchriftfarbe`, `fcCountdownSchriftformat`, `fnCountdownSchriftsize`) VALUES
(1, '#f6e79d', '320', '#666666', '#f6e79d', '#faf7e8', 'italic', '#666666', '#f6e79d', '350', '#666666', '#f6e79d', '#faf7e8', '#FF0000', '#000000', '90%', 'dotted', '#ffd500', '#000000', '#FF0000', '90%', '#FF0000', 'bold', 10);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_config`
--

DROP TABLE IF EXISTS `prefix_config`;
CREATE TABLE IF NOT EXISTS `prefix_config` (
  `schl` varchar(50) NOT NULL DEFAULT '',
  `typ` varchar(10) NOT NULL DEFAULT '',
  `typextra` text,
  `kat` varchar(50) NOT NULL DEFAULT '',
  `frage` varchar(255) NOT NULL DEFAULT '',
  `wert` text NOT NULL,
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'auf 1 setzen um dies NICHT in der konfiguration anzuzeigen',
  `helptext` text,
  PRIMARY KEY (`schl`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_config`
--

INSERT INTO `prefix_config` (`schl`, `typ`, `typextra`, `kat`, `frage`, `wert`, `pos`, `hide`, `helptext`) VALUES
('gbook_posts_per_site', 'input', NULL, 'G&auml;stebuch Optionen', 'Eintr&auml;ge pro Seite', '20', 0, 0, NULL),
('gallery_imgs_per_line', 'input', NULL, 'Gallery Optionen', 'Bilder pro Zeile', '4', 0, 0, NULL),
('Aanz', 'input', NULL, 'Archiv Optionen', 'Anzahl Banner in der Partner Box', '3', 0, 0, NULL),
('Nlimit', 'input', NULL, 'News Optionen', 'News pro Seite', '5', 0, 0, NULL),
('Ftanz', 'input', NULL, 'Forum Optionen', 'Themen auf einer Seite', '20', 0, 0, NULL),
('Fpanz', 'input', NULL, 'Forum Optionen', 'Posts auf einer Seite', '20', 0, 0, NULL),
('forum_avatar_upload', 'r2', NULL, 'Forum Optionen', 'Avatar und Userpic Upload?', '1', 0, 0, NULL),
('gallery_imgs_per_site', 'input', NULL, 'Gallery Optionen', 'Bilder pro Seite', '12', 0, 0, NULL),
('gallery_preview_width', 'input', NULL, 'Gallery Optionen', 'Breite der Vorschaubilder', '80', 0, 0, NULL),
('Fpmf', 'r2', NULL, 'Forum Optionen', 'Nachrichten Function', '1', 0, 0, NULL),
('gfx', 's', NULL, 'Allgemeine Optionen', 'Standard Design', 'ilchClan', 0, 0, NULL),
('smodul', 's', NULL, 'Allgemeine Optionen', 'Start Modul der Seite', 'news', 0, 0, NULL),
('title', 'input', NULL, 'Allgemeine Optionen', 'Titel der Seite', 'Das Clanscript f&uuml;r jeden!', 0, 0, NULL),
('Fabreite', 'input', NULL, 'Forum Optionen', 'max Breite f&uuml;r den Avatar', '80', 0, 0, NULL),
('Fahohe', 'input', NULL, 'Forum Optionen', 'max H&ouml;he f&uuml;r den Avatar', '80', 0, 0, NULL),
('Gsperre', 'input', NULL, 'G&auml;stebuch Optionen', 'Ip Sperre in Sekunden', '3600', 0, 0, NULL),
('Gtxtl', 'input', NULL, 'G&auml;stebuch Optionen', 'max Text l&auml;nge im G&auml;stebuch', '600', 0, 0, NULL),
('Aart', 'r2', NULL, 'Archiv Optionen', 'Soll die Partner Box sortiert werden', '0', 0, 0, NULL),
('Ngkoms', 'r2', NULL, 'News Optionen', 'D&uuml;rfen G&auml;ste Kommentare schreiben?', '1', 0, 0, NULL),
('Nukoms', 'r2', NULL, 'News Optionen', 'D&uuml;rfen User Kommentare schreiben?', '1', 0, 0, NULL),
('allg_menupoint_access', 'r2', NULL, 'Allgemeine Optionen', 'Zugriff auf nicht im Men&uuml; verlinkte Module f&uuml;r alle?', '0', 0, 0, NULL),
('forum_regist', 'r2', NULL, 'Forum Optionen', 'D&uuml;rfen sich User registrieren?', '1', 0, 0, NULL),
('forum_regist_user_pass', 'r2', NULL, 'Forum Optionen', 'Passwort vom User beim registrieren selber w&auml;hlbar?', '1', 0, 0, NULL),
('forum_regist_confirm_link', 'r2', NULL, 'Forum Optionen', 'Registrierung per Link im eMail best&auml;tigen?', '1', 0, 0, NULL),
('archiv_down_userupload', 'r2', NULL, 'Archiv Optionen', 'D&uuml;rfen User Dateien hochladen?', '1', 0, 0, NULL),
('show_session_id', 'r2', NULL, 'Allgemeine Optionen', 'SessionID bei G&auml;sten anzeigen?', '1', 0, 0, NULL),
('wars_last_komms', 's', NULL, 'Wars Optionen', 'Kommentare f&uuml;r Lastwars', '0', 0, 0, NULL),
('wars_last_limit', 'input', NULL, 'Wars Optionen', 'Lastwars pro Seite.', '15', 0, 0, NULL),
('forum_usergallery', 'r2', NULL, 'Forum Optionen', 'Darf jeder User seine eigene Gallery haben?', '1', 0, 0, NULL),
('gallery_normal_width', 'input', NULL, 'Gallery Optionen', 'Breite der normalen Bilder', '500', 0, 0, NULL),
('gallery_img_koms', 'r2', NULL, 'Gallery Optionen', 'Kommentare f&uuml;r Bilder zulassen?', '1', 0, 0, NULL),
('forum_max_sig', 'input', NULL, 'Forum Optionen', 'max. Anzahl Zeichen in der Signatur', '200', 0, 0, NULL),
('gbook_koms_for_inserts', 'r2', NULL, 'G&auml;stebuch Optionen', 'Kommentare f&uuml;r G&auml;stebucheintr&auml;ge?', '1', 0, 0, NULL),
('allg_regeln', 'textarea', NULL, 'Allgemeine Optionen', 'Die Regeln f&uuml;r die Seite (bbcode erlaubt)', '[list]\r\n[*]Die Registrierung ist v&ouml;llig Kostenlos\r\n[*]Die Betreiber der Seite &uuml;bernehmen keine Haftung.\r\n[*]Bitte verhalten Sie sich angemessen und mit Respekt gegen&uuml;ber den anderen Community Mitgliedern.\r\n[/list]', 0, 0, NULL),
('teams_show_list', 'r2', NULL, 'Team Optionen', 'Avatar bei den Usern?', '0', 0, 0, NULL),
('teams_show_cat', 'r2', NULL, 'Team Optionen', 'Sollen die Squads als Kategorie angezeigt werden?', '1', 0, 0, NULL),
('allg_bbcode_max_img_width', 'input', NULL, 'Allgemeine Optionen', 'Wie Breit solle ein Bild maximal sein (in Pixeln)?', '230', 0, 0, NULL),
('forum_default_avatar', 'r2', NULL, 'Forum Optionen', 'Standard Avatar anzeigen?', '1', 0, 0, NULL),
('allg_default_subject', 'input', NULL, 'Allgemeine Optionen', 'Standard Absender bei eMails', 'automatische eMail', 0, 0, NULL),
('sb_maxwordlength', 'input', NULL, 'Shoutbox Optionen', 'Maximale Wortl&auml;nge in der Shoutbox', '10', 0, 0, NULL),
('sb_recht', 'grecht', NULL, 'Shoutbox Optionen', 'Schreiben in der Shoutbox ab?', '0', 0, 0, NULL),
('sb_limit', 'input', NULL, 'Shoutbox Optionen', 'Anzahl angezeigter Nachrichten', '5', 0, 0, NULL),
('antispam', 'grecht2', NULL, 'Allgemeine Optionen', 'Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-2', 0, 0, NULL),
('joinus_rules', 'r2', NULL, 'Team Optionen', 'Regeln bei Joinus vollst&auml;ndig anzeigen?', '0', 0, 0, NULL),
('groups_forall', 'r2', NULL, 'Team Optionen', 'Modulrecht <i>Gruppen</i> auf eigene Gruppe beschr&auml;nken?', '1', 0, 0, NULL),
('lang', 's', NULL, 'Allgemeine Optionen', 'Standard Sprache', 'de', 0, 0, NULL),
('mail_smtp', 'r2', NULL, 'Allgemeine Optionen', 'SMTP f&uuml;r den Mailversand verwenden? <a href="admin.php?smtpconf" class="smalfont">weitere Einstellungen</a>', '0', 0, 0, NULL),
('userpic_Fabreite', 'input', NULL, 'Forum Optionen', 'max Breite f&uuml;r das Userpic', '160', 0, 0, NULL),
('userpic_Fahohe', 'input', NULL, 'Forum Optionen', 'max H&ouml;he f&uuml;r das Userpic', '160', 0, 0, NULL),
('news_social', 'r2', NULL, 'News Optionen', 'Bei News Social Network Buttons anzeigen?', '0', 0, 0, 'Zeigt bei News dann Buttons von Social Networks wie Facebook an.'),
('menu_anz', 'input', NULL, 'Allgemeine Optionen', 'Wie viele Men&uuml;s sollen verwaltet werden?', '5', 0, 0, NULL),
('revision', 'input', NULL, 'Allgemeine Optionen', 'Revisionsnummer', '238', 0, 0, NULL),
('threadersteller_in_uebersicht', 'r2', NULL, 'Forum Optionen', 'Threadersteller - Anzeige in &uuml;bersicht?', '0', 0, 0, NULL),
('show_tooltip', 'r2', NULL, 'Kalender Optionen', 'Event-Tooltips', '1', 0, 0, 'Wenn aktiviert werden bei Kalendereintr&auml;gen in einem Tooltip schon Details zu dem Eintrag angezeigt.'),
('modrewrite', 'radio', NULL, 'Allgemeine Optionen', 'ModReWrite an / aus', '0', 0, 1, NULL),
('wartung', 'r2', NULL, 'Allgemeine Optionen', 'Wartungsmodus ?', '0', 0, 0, NULL),
('wartungstext', 'input', NULL, 'Allgemeine Optionen', 'Wartungstext', '0', 0, 0, NULL),
('inactive', 'r2', NULL, 'Allgemeine Optionen', 'Ab wie vielen Wochen z&auml;hlt ein User als inaktiv ?', '12', 0, 1, NULL),
('kalender_standard_list', 'select', '{"keys":[1, 0], "values":["Listenansicht", "Monatsansicht"]}', 'Kalender Optionen', 'Standardansicht', '0', 0, 0, 'Gibt ob Die Listenansicht oder die Monatsansicht verwendet wird, wenn man den Kalender aufruft.'),
('sb_archive_limit', 'input', NULL, 'Shoutbox Optionen', 'Anzahl angezeigter Nachrichten pro Seite im Archiv', '5', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_counter`
--

DROP TABLE IF EXISTS `prefix_counter`;
CREATE TABLE IF NOT EXISTS `prefix_counter` (
  `date` date NOT NULL,
  `count` smallint(5) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_counter`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_credits`
--

DROP TABLE IF EXISTS `prefix_credits`;
CREATE TABLE IF NOT EXISTS `prefix_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys` varchar(5) COLLATE utf8_bin NOT NULL,
  `name` varchar(250) COLLATE utf8_bin NOT NULL,
  `version` varchar(250) COLLATE utf8_bin NOT NULL,
  `url` varchar(250) COLLATE utf8_bin NOT NULL,
  `lizenzname` varchar(250) COLLATE utf8_bin NOT NULL,
  `lizenzurl` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Credits-System - bitte doku beachten';

--
-- Daten f&uuml;r Tabelle `prefix_credits`
--

INSERT INTO `prefix_credits` (`id`, `sys`, `name`, `version`, `url`, `lizenzname`, `lizenzurl`) VALUES
(1, 'ilch', 'ilch.de - CMS', '1.2', 'http://ilch.de', 'GPL', 'http://www.gnu.de/gpl-ger.html'),
(2, 'ilch', 'jQuery', '1.5', 'http://jquery.com', 'GPL', 'http://en.wikipedia.org/wiki/GNU_General_Public_License'),
(3, 'ilch', 'jQuery UI', '1.8.9', 'http://jqueryui.com', 'GPL', 'http://en.wikipedia.org/wiki/GNU_General_Public_License');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_downcats`
--

DROP TABLE IF EXISTS `prefix_downcats`;
CREATE TABLE IF NOT EXISTS `prefix_downcats` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat` mediumint(8) DEFAULT '0',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `recht` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_downcats`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_downloads`
--

DROP TABLE IF EXISTS `prefix_downloads`;
CREATE TABLE IF NOT EXISTS `prefix_downloads` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat` mediumint(8) DEFAULT '0',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creater` varchar(250) DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `downs` int(11) NOT NULL DEFAULT '0',
  `vote_klicks` int(11) NOT NULL DEFAULT '0',
  `vote_wertung` float NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL DEFAULT '',
  `surl` varchar(100) NOT NULL DEFAULT '',
  `ssurl` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `desc` varchar(255) DEFAULT NULL,
  `descl` text,
  `drecht` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_downloads`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_forumcats`
--

DROP TABLE IF EXISTS `prefix_forumcats`;
CREATE TABLE IF NOT EXISTS `prefix_forumcats` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `cid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `pos` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_forumcats`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_forummods`
--

DROP TABLE IF EXISTS `prefix_forummods`;
CREATE TABLE IF NOT EXISTS `prefix_forummods` (
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `fid` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_forummods`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_forums`
--

DROP TABLE IF EXISTS `prefix_forums`;
CREATE TABLE IF NOT EXISTS `prefix_forums` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cid` mediumint(8) NOT NULL DEFAULT '0',
  `last_post_id` int(11) NOT NULL DEFAULT '0',
  `view` smallint(6) NOT NULL DEFAULT '0',
  `reply` smallint(6) NOT NULL DEFAULT '0',
  `start` smallint(6) NOT NULL DEFAULT '0',
  `pos` tinyint(3) NOT NULL DEFAULT '0',
  `posts` mediumint(8) NOT NULL DEFAULT '0',
  `topics` mediumint(8) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `besch` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_forums`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_gallery_cats`
--

DROP TABLE IF EXISTS `prefix_gallery_cats`;
CREATE TABLE IF NOT EXISTS `prefix_gallery_cats` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `cat` mediumint(9) NOT NULL DEFAULT '0',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `recht` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `besch` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_gallery_cats`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_gallery_imgs`
--

DROP TABLE IF EXISTS `prefix_gallery_imgs`;
CREATE TABLE IF NOT EXISTS `prefix_gallery_imgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat` mediumint(9) NOT NULL DEFAULT '0',
  `klicks` mediumint(9) NOT NULL DEFAULT '0',
  `vote_wertung` float NOT NULL DEFAULT '0',
  `vote_klicks` mediumint(9) NOT NULL DEFAULT '0',
  `datei_name` varchar(50) NOT NULL DEFAULT '',
  `endung` varchar(5) NOT NULL DEFAULT '',
  `besch` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_gallery_imgs`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_gbook`
--

DROP TABLE IF EXISTS `prefix_gbook`;
CREATE TABLE IF NOT EXISTS `prefix_gbook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `mail` varchar(100) NOT NULL DEFAULT '',
  `page` varchar(100) NOT NULL DEFAULT '',
  `time` int(20) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_gbook`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_groupfuncs`
--

DROP TABLE IF EXISTS `prefix_groupfuncs`;
CREATE TABLE IF NOT EXISTS `prefix_groupfuncs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_groupfuncs`
--

INSERT INTO `prefix_groupfuncs` (`id`, `name`, `pos`) VALUES
(1, 'Leader', 1),
(2, 'Co-Leader', 2),
(3, 'Member', 3),
(4, 'Trial', 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_groups`
--

DROP TABLE IF EXISTS `prefix_groups`;
CREATE TABLE IF NOT EXISTS `prefix_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `mod1` mediumint(9) NOT NULL DEFAULT '0',
  `mod2` mediumint(9) NOT NULL DEFAULT '0',
  `mod3` mediumint(9) NOT NULL DEFAULT '0',
  `mod4` mediumint(9) NOT NULL DEFAULT '0',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `zeigen` tinyint(1) NOT NULL DEFAULT '0',
  `show_joinus` tinyint(1) NOT NULL DEFAULT '0',
  `show_fightus` tinyint(1) NOT NULL DEFAULT '0',
  `img` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_groups`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_groupusers`
--

DROP TABLE IF EXISTS `prefix_groupusers`;
CREATE TABLE IF NOT EXISTS `prefix_groupusers` (
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `gid` smallint(6) NOT NULL DEFAULT '0',
  `fid` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_groupusers`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_grundrechte`
--

DROP TABLE IF EXISTS `prefix_grundrechte`;
CREATE TABLE IF NOT EXISTS `prefix_grundrechte` (
  `id` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_grundrechte`
--

INSERT INTO `prefix_grundrechte` (`id`, `name`) VALUES
(0, 'Gast'),
(-1, 'User'),
(-2, 'Superuser'),
(-3, 'Trialmember'),
(-4, 'Member'),
(-5, 'CoLeader'),
(-6, 'Leader'),
(-7, 'SiteAdmin'),
(-8, 'CoAdmin'),
(-9, 'Admin');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_history`
--

DROP TABLE IF EXISTS `prefix_history`;
CREATE TABLE IF NOT EXISTS `prefix_history` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `title` varchar(100) NOT NULL DEFAULT '',
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_history`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_kalender`
--

DROP TABLE IF EXISTS `prefix_kalender`;
CREATE TABLE IF NOT EXISTS `prefix_kalender` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `recht` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_kalender`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_kasse`
--

DROP TABLE IF EXISTS `prefix_kasse`;
CREATE TABLE IF NOT EXISTS `prefix_kasse` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL DEFAULT '0000-00-00',
  `name` varchar(50) NOT NULL DEFAULT '',
  `verwendung` varchar(50) NOT NULL DEFAULT '',
  `betrag` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_kasse`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_koms`
--

DROP TABLE IF EXISTS `prefix_koms`;
CREATE TABLE IF NOT EXISTS `prefix_koms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `cat` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `text` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_koms`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_linkcats`
--

DROP TABLE IF EXISTS `prefix_linkcats`;
CREATE TABLE IF NOT EXISTS `prefix_linkcats` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat` mediumint(8) DEFAULT '0',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_linkcats`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_links`
--

DROP TABLE IF EXISTS `prefix_links`;
CREATE TABLE IF NOT EXISTS `prefix_links` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `cat` mediumint(8) DEFAULT '0',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL DEFAULT '',
  `banner` varchar(100) NOT NULL DEFAULT '',
  `link` varchar(100) NOT NULL DEFAULT '',
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_links`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_linkus`
--

DROP TABLE IF EXISTS `prefix_linkus`;
CREATE TABLE IF NOT EXISTS `prefix_linkus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `datei` varchar(255) NOT NULL,
  `hoch` int(5) NOT NULL,
  `breit` int(5) NOT NULL,
  `link` varchar(255) NOT NULL,
  `views` int(11) NOT NULL,
  `klicks` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_linkus`
--

INSERT INTO `prefix_linkus` (`id`, `name`, `datei`, `hoch`, `breit`, `link`, `views`, `klicks`) VALUES
(1, 'Ilch.de-Banner 468x60', '468x60ilch.gif', 60, 468, 'http://gecko.ilch.de', 22, 3),
(2, 'Ilch.de-Button 88x31', 'copy_by_ilch.gif', 31, 88, 'http://gecko.ilch.de', 6, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_loader`
--

DROP TABLE IF EXISTS `prefix_loader`;
CREATE TABLE IF NOT EXISTS `prefix_loader` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pos` int(10) NOT NULL,
  `task` varchar(200) NOT NULL,
  `file` varchar(200) NOT NULL,
  `description` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_loader`
--

INSERT INTO `prefix_loader` (`id`, `pos`, `task`, `file`, `description`) VALUES
(20, 1, 'class', 'tpl.php', 'Template-Class'),
(21, 2, 'class', 'design.php', 'Design-Class'),
(22, 3, 'class', 'menu.php', 'Menu-Class'),
(23, 4, 'class', 'bbcode.php', 'BB-Code 2.0 Class'),
(24, 5, 'libs', 'xajax/xajax.inc.php', 'Die xAjax-Class'),
(26, 2, 'func', 'calender.php', 'Funktionen fuer den Kalender'),
(27, 3, 'func', 'user.php', 'Userverwaltung Login/Logout und Rechte'),
(28, 4, 'func', 'escape.php', 'Sicherheitsvorkehrungen vom Ilch CMS'),
(29, 5, 'func', 'allg.php', 'Allgemeine Funktionen und Einstellungen'),
(40, 14, 'func', 'statistic_content.php', 'Wer-Ist-Wo und ContentStats'),
(31, 7, 'func', 'bbcode.php', 'BB-Code Buttons und Funktionen'),
(32, 8, 'func', 'profilefields.php', 'Profilfelder Funktionen'),
(33, 9, 'func', 'statistic.php', 'Statistiken &uuml;ber den Besucher speichern'),
(34, 10, 'func', 'listen.php', 'Funktionen, zum Listen erstellen'),
(35, 11, 'func', 'forum.php', 'Alle Funktionen f&uuml;r das Forum'),
(36, 12, 'func', 'warsys.php', 'Funktionen f&uuml;r das War-System'),
(37, 13, 'func', 'ic_mime_type.php', 'Funktionen f&uuml;r den Umgang mit Dateiuploads'),
(38, 14, 'func', 'lang.php', 'Funktionen zum Aufrufen der Sprachdateien'),
(39, 15, 'class', 'profilefield_registry.php', 'Verwaltet die Profilfeld-Typen.');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_menu`
--

DROP TABLE IF EXISTS `prefix_menu`;
CREATE TABLE IF NOT EXISTS `prefix_menu` (
  `wo` tinyint(1) NOT NULL DEFAULT '0',
  `pos` tinyint(4) NOT NULL DEFAULT '0',
  `was` tinyint(1) NOT NULL DEFAULT '0',
  `ebene` tinyint(2) NOT NULL DEFAULT '0',
  `recht` tinyint(2) NOT NULL DEFAULT '0',
  `recht_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 -> ab (>) ; 1 -> genau dieses; 2 -> bis (<)',
  `name` varchar(100) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`pos`,`wo`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_menu`
--

INSERT INTO `prefix_menu` (`wo`, `pos`, `was`, `ebene`, `recht`, `recht_type`, `name`, `path`) VALUES
(1, 0, 3, 0, 0, 0, 'Men&uuml;', 'allianz.php'),
(1, 1, 7, 0, 0, 0, 'News', 'news'),
(1, 2, 7, 0, 0, 0, 'Forum', 'forum'),
(1, 3, 7, 1, 0, 0, 'Mitglieder', 'user'),
(1, 4, 7, 0, 0, 0, 'Gbook', 'gbook'),
(1, 5, 7, 0, 0, 0, 'Links', 'links'),
(1, 6, 7, 0, 0, 0, 'LinkUs', 'linkus'),
(1, 7, 7, 0, 0, 0, 'Downloads', 'downloads'),
(1, 8, 7, 0, 0, 0, 'Gallery', 'gallery'),
(1, 9, 7, 0, 0, 0, 'Kalender', 'kalender'),
(1, 10, 7, 0, 2, 3, 'Kontakt', 'contact'),
(1, 11, 7, 0, 0, 0, 'Impressum', 'impressum'),
(1, 12, 3, 0, 0, 0, 'Clan Men&uuml;', 'allianz.php'),
(1, 13, 7, 0, 0, 0, 'Squads', 'teams'),
(1, 14, 7, 1, 0, 0, 'Fightus', 'fightus'),
(1, 15, 7, 1, 0, 0, 'Joinus', 'joinus'),
(1, 16, 7, 0, 0, 0, 'Wars', 'wars'),
(1, 17, 7, 0, 0, 0, 'Kasse', 'kasse'),
(1, 18, 7, 0, -3, 0, 'Training', 'trains'),
(1, 19, 7, 0, -3, 0, 'Away', 'awaycal'),
(1, 20, 7, 0, 0, 0, 'Awards', 'awards'),
(1, 21, 7, 0, 0, 0, 'Regeln', 'rules'),
(1, 22, 7, 0, 0, 0, 'History', 'history'),
(1, 23, 1, 0, 0, 0, 'Login', 'login.php'),
(1, 24, 1, 0, 0, 0, 'Shoutbox', 'shoutbox.php'),
(2, 0, 1, 0, 0, 0, 'Suchen', 'search.php'),
(2, 1, 1, 0, 0, 0, 'Umfrage', 'vote.php'),
(2, 2, 1, 0, 0, 0, 'Allianz', 'allianz.php'),
(2, 3, 1, 0, 0, 0, 'Statistik', 'statistik.php'),
(2, 4, 1, 0, 0, 0, 'Newsletter', 'newsletter.php'),
(2, 5, 1, 0, 0, 0, 'Designs', 'designs.php'),
(2, 6, 1, 0, 0, 0, 'Online', 'online.php'),
(3, 0, 1, 0, 0, 0, 'Kalender', 'calender.php'),
(3, 1, 1, 0, 0, 0, 'Last Forum', 'lastforum.php'),
(3, 2, 1, 0, 0, 0, 'Lastwars', 'lastwars.php'),
(3, 3, 1, 0, 0, 0, 'Nextwars', 'nextwars.php'),
(3, 4, 1, 0, 0, 0, 'Last News', 'lastnews.php'),
(3, 5, 1, 0, 0, 0, 'PicOfX', 'picofx.php'),
(3, 6, 1, 0, 0, 0, 'Geburtstag', 'geburtstag.php'),
(5, 0, 7, 0, 0, 0, 'Shoutbox (Archiv)', 'shoutbox');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_modulerights`
--

DROP TABLE IF EXISTS `prefix_modulerights`;
CREATE TABLE IF NOT EXISTS `prefix_modulerights` (
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `mid` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_modulerights`
--

INSERT INTO `prefix_modulerights` (`uid`, `mid`) VALUES
(1, 41),
(1, 43),
(1, 46),
(1, 47),
(1, 49),
(1, 50),
(1, 75),
(2, 51),
(3, 45);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_modules`
--

DROP TABLE IF EXISTS `prefix_modules`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_modules`
--

INSERT INTO `prefix_modules` (`id`, `url`, `name`, `gshow`, `ashow`, `fright`, `menu`, `pos`) VALUES
(39, 'gallery', 'Gallery', 1, 0, 1, 'Content', 4),
(40, 'menu', 'Navigation', 1, 0, 0, 'Admin', 2),
(41, 'groups', 'Teams', 1, 0, 1, 'Clanbox', 6),
(42, 'rules', 'Regeln', 1, 0, 1, 'Clanbox', 4),
(43, 'awards', 'Awards', 1, 0, 1, 'Clanbox', 3),
(44, 'forum', 'Forum', 1, 0, 1, 'Content', 1),
(45, 'archiv-downloads', 'Downloads', 1, 0, 1, 'Content', 2),
(46, 'kalender', 'Kalender', 1, 0, 1, 'Content', 7),
(47, 'wars', 'Wars', 0, 0, 1, '', 0),
(48, 'kasse', 'Kasse', 1, 0, 1, 'Clanbox', 5),
(49, 'gbook', 'G&auml;stebuch', 1, 0, 1, 'Content', 5),
(50, 'awaycal', 'Awaycal', 0, 0, 0, '', 0),
(51, 'news', 'News', 1, 0, 1, 'Content', 0),
(52, 'allg', 'Konfiguration', 1, 0, 0, 'Admin', 0),
(53, 'backup', 'Backup', 1, 0, 0, 'Admin', 3),
(54, 'range', 'Ranks', 1, 0, 0, 'Admin', 4),
(55, 'wars-last', 'Lastwars', 1, 0, 0, 'Clanbox', 2),
(56, 'smilies', 'Smilies', 1, 0, 0, 'Admin', 5),
(57, 'newsletter', 'Newsletter', 1, 0, 0, 'Admin', 6),
(58, 'checkconf', 'Serverkonfiguration', 1, 0, 0, 'Admin', 7),
(59, 'user', 'User', 1, 0, 0, 'User', 0),
(60, 'grundrechte', 'Grundrechte', 1, 0, 0, 'User', 1),
(61, 'profilefields', 'Profilfelder', 1, 0, 0, 'User', 2),
(62, 'selfbp', 'Eigene Box/Page', 1, 0, 0, 'Eigene Box/Page', 0),
(63, 'wars-next', 'Nextwars', 1, 0, 0, 'Clanbox', 1),
(64, 'history', 'History', 1, 0, 0, 'Clanbox', 8),
(65, 'trains', 'Trainzeiten', 1, 0, 0, 'Clanbox', 7),
(66, 'archiv-links', 'Links', 1, 0, 0, 'Content', 3),
(67, 'vote', 'Umfragen', 1, 0, 0, 'Content', 6),
(68, 'contact', 'Kontakt', 1, 0, 0, 'Content', 8),
(69, 'impressum', 'Impressum', 1, 0, 0, 'Content', 9),
(70, 'archiv-partners', 'Partner', 1, 0, 0, 'Boxen', 0),
(71, 'picofx', 'Pic of X', 1, 0, 0, 'Boxen', 1),

(72, 'smtpconf', 'SMTP', 1, 0, 0, 'Admin', 1),
(73, 'puser', 'Nicht best&auml;tigte Registrierungen', 0, 0, 0, '', 0),
(74, 'bbcode', 'BBcode 2.0', 1, 0, 1, 'Content', 11),
(75, 'linkus', 'LinkUs', 1, 1, 1, 'Content', 4),
(76, 'modrewrite', 'ModRewrite', 1, 0, 0, 'Admin', 9),
(77, 'inactive', 'inaktive User', 1, 1, 1, 'Clanbox', 0),
(78, 'opponents', 'Gegner', 0, 0, 0, 'Clanbox', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_news`
--

DROP TABLE IF EXISTS `prefix_news`;
CREATE TABLE IF NOT EXISTS `prefix_news` (
  `news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_title` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `news_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `news_recht` int(11) NOT NULL DEFAULT '0',
  `news_kat` varchar(100) NOT NULL DEFAULT '',
  `news_text` text NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_news`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_newsletter`
--

DROP TABLE IF EXISTS `prefix_newsletter`;
CREATE TABLE IF NOT EXISTS `prefix_newsletter` (
  `email` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_newsletter`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_online`
--

DROP TABLE IF EXISTS `prefix_online`;
CREATE TABLE IF NOT EXISTS `prefix_online` (
  `uptime` datetime DEFAULT NULL,
  `sid` varchar(32) NOT NULL DEFAULT '',
  `ipa` varchar(15) NOT NULL DEFAULT '',
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `content` varchar(255) NOT NULL DEFAULT '(Startseite)'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_online`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_opponents`
--

DROP TABLE IF EXISTS `prefix_opponents`;
CREATE TABLE IF NOT EXISTS `prefix_opponents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `page` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `icq` int(11) NOT NULL,
  `nation` varchar(100) NOT NULL,
  `logo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Gegner-Datenbank';

--
-- Daten f&uuml;r Tabelle `prefix_opponents`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_partners`
--

DROP TABLE IF EXISTS `prefix_partners`;
CREATE TABLE IF NOT EXISTS `prefix_partners` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `banner` varchar(100) NOT NULL DEFAULT '',
  `link` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_partners`
--

INSERT INTO `prefix_partners` (`id`, `pos`, `name`, `banner`, `link`) VALUES
(1, 0, 'www.ilch.de', 'http://www.ilch.de/images/banner/copy_by_ilch.gif', 'http://www.ilch.de');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_pm`
--

DROP TABLE IF EXISTS `prefix_pm`;
CREATE TABLE IF NOT EXISTS `prefix_pm` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `sid` mediumint(8) NOT NULL DEFAULT '0',
  `eid` mediumint(8) NOT NULL DEFAULT '0',
  `gelesen` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `time` int(20) NOT NULL DEFAULT '0',
  `titel` varchar(100) NOT NULL DEFAULT '',
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_pm`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_poll`
--

DROP TABLE IF EXISTS `prefix_poll`;
CREATE TABLE IF NOT EXISTS `prefix_poll` (
  `poll_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `frage` varchar(200) NOT NULL DEFAULT '',
  `recht` tinyint(1) NOT NULL DEFAULT '0',
  `stat` tinyint(1) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  PRIMARY KEY (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_poll`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_poll_res`
--

DROP TABLE IF EXISTS `prefix_poll_res`;
CREATE TABLE IF NOT EXISTS `prefix_poll_res` (
  `sort` tinyint(2) NOT NULL DEFAULT '0',
  `poll_id` mediumint(8) NOT NULL DEFAULT '0',
  `antw` varchar(100) NOT NULL DEFAULT '',
  `res` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_poll_res`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_posts`
--

DROP TABLE IF EXISTS `prefix_posts`;
CREATE TABLE IF NOT EXISTS `prefix_posts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` mediumint(8) NOT NULL DEFAULT '0',
  `fid` mediumint(9) NOT NULL DEFAULT '0',
  `erst` varchar(100) NOT NULL DEFAULT '',
  `erstid` int(10) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL DEFAULT '0',
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_posts`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_profilefields`
--

DROP TABLE IF EXISTS `prefix_profilefields`;
CREATE TABLE IF NOT EXISTS `prefix_profilefields` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `show` varchar(20) NOT NULL DEFAULT '',
  `pos` mediumint(9) NOT NULL DEFAULT '0',
  `func` tinyint(1) NOT NULL DEFAULT '0',
  `config_value` varchar(2048) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_profilefields`
--

INSERT INTO `prefix_profilefields` (`id`, `show`, `pos`, `func`, `config_value`) VALUES
(1, 'msn', 12, 3, ''),
(2, 'opt_pm', 9, 3, ''),
(3, 'opt_mail', 8, 3, ''),
(4, 'yahoo', 13, 3, ''),
(5, 'sig', 6, 3, ''),
(6, 'wohnort', 4, 3, ''),
(7, 'icq', 11, 3, ''),
(8, 'gebdatum', 1, 3, ''),
(9, 'geschlecht', 2, 3, ''),
(10, 'staat', 0, 3, ''),
(11, 'status', 3, 3, ''),
(12, 'Kontakt', 7, 2, ''),
(13, 'aim', 14, 3, ''),
(14, 'homepage', 5, 3, ''),
(15, 'opt_pm_popup', 10, 3, ''),
(16, 'usergallery', 15, 3, ''),
(19, 'Testfeld', 16, 1, 'a:4:{s:3:"sid";i:0;s:4:"show";s:8:"Testfeld";s:4:"func";i:1;s:3:"sub";s:9:"Eintragen";}'),
(20, 'Ein Slider', 17, 4, 'a:6:{s:3:"sid";i:0;s:4:"show";s:10:"Ein Slider";s:4:"func";i:4;s:9:"textlinks";s:11:"linker Text";s:10:"textrechts";s:12:"rechter Text";s:3:"sub";s:9:"Eintragen";}'),
(21, 'Eine Seite', 18, 5, 'a:4:{s:3:"sid";i:0;s:4:"show";s:10:"Eine Seite";s:4:"func";i:5;s:3:"sub";s:9:"Eintragen";}');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_ranks`
--

DROP TABLE IF EXISTS `prefix_ranks`;
CREATE TABLE IF NOT EXISTS `prefix_ranks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bez` varchar(100) NOT NULL DEFAULT '',
  `min` int(10) NOT NULL DEFAULT '0',
  `spez` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_ranks`
--

INSERT INTO `prefix_ranks` (`id`, `bez`, `min`, `spez`) VALUES
(1, 'Gr&uuml;nschnabel', 1, 0),
(2, 'Jungspund', 25, 0),
(3, 'Mitglied', 50, 0),
(4, 'Eroberer', 75, 0),
(6, 'Doppel-As', 150, 0),
(7, 'Tripel-As', 250, 0),
(8, 'Haudegen', 500, 0),
(9, 'Routinier', 1000, 0),
(15, 'K&ouml;nig', 2000, 0),
(11, 'Kaiser', 5000, 0),
(12, 'Legende', 7000, 0),
(13, 'Foren Gott', 10000, 0),
(14, 'Administrator', 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_rules`
--

DROP TABLE IF EXISTS `prefix_rules`;
CREATE TABLE IF NOT EXISTS `prefix_rules` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `zahl` smallint(6) NOT NULL DEFAULT '0',
  `titel` varchar(200) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_rules`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_selfp`
--

DROP TABLE IF EXISTS `prefix_selfp`;
CREATE TABLE IF NOT EXISTS `prefix_selfp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `path` varchar(30) NOT NULL,
  `cpath` varchar(255) NOT NULL COMMENT 'complete path',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT 'category id',
  `wysiwyg` tinyint(1) NOT NULL DEFAULT '1',
  `php` tinyint(1) NOT NULL DEFAULT '0',
  `view` smallint(6) NOT NULL DEFAULT '0',
  `pos` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_selfp`
--

INSERT INTO `prefix_selfp` (`id`, `name`, `path`, `cpath`, `cid`, `wysiwyg`, `php`, `view`, `pos`) VALUES
(1, 'S&auml;ubern', 'saeubern', 'kueche-saeubern', 1, 1, 0, 0, 0),
(2, 'Testseite', 'testseite', 'testseite', 0, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_selfp_cat`
--

DROP TABLE IF EXISTS `prefix_selfp_cat`;
CREATE TABLE IF NOT EXISTS `prefix_selfp_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `path` varchar(30) NOT NULL,
  `cpath` varchar(255) NOT NULL COMMENT 'complete path',
  `cid` int(11) NOT NULL COMMENT 'category id',
  `startpage` int(11) NOT NULL,
  `pos` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_selfp_cat`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_shoutbox`
--

DROP TABLE IF EXISTS `prefix_shoutbox`;
CREATE TABLE IF NOT EXISTS `prefix_shoutbox` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `nickname` varchar(50) NOT NULL DEFAULT '',
  `textarea` text,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_shoutbox`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_smilies`
--

DROP TABLE IF EXISTS `prefix_smilies`;
CREATE TABLE IF NOT EXISTS `prefix_smilies` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ent` varchar(50) NOT NULL DEFAULT '',
  `emo` varchar(75) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `pos` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_smilies`
--

INSERT INTO `prefix_smilies` (`id`, `ent`, `emo`, `url`, `pos`) VALUES
(1, ':)', 'Smilie', '1.png', 1),
(2, ':D', 'Lachen', '2.png', 2),
(3, ':O', 'Opssss', '3.png', 3),
(4, ':P', 'Auslachen', '4.png', 4),
(5, ';)', 'Zwinker', '5.png', 5),
(6, ':(', 'Traurig', '6.png', 6),
(7, ':S', 'Grummel', '7.png', 7),
(8, ':|', 'Sauer', '8.png', 8),
(9, ':''(', 'Weinen', '9.png', 9),
(10, ':@', 'Veraergert', '10.png', 10);

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_stats`
--

DROP TABLE IF EXISTS `prefix_stats`;
CREATE TABLE IF NOT EXISTS `prefix_stats` (
  `wtag` tinyint(2) NOT NULL DEFAULT '0',
  `stunde` tinyint(2) NOT NULL DEFAULT '0',
  `day` tinyint(2) NOT NULL DEFAULT '0',
  `mon` tinyint(2) NOT NULL DEFAULT '0',
  `yar` int(4) NOT NULL DEFAULT '0',
  `os` varchar(50) NOT NULL DEFAULT '',
  `browser` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `ref` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_stats`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_stats_content`
--

DROP TABLE IF EXISTS `prefix_stats_content`;
CREATE TABLE IF NOT EXISTS `prefix_stats_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_stats_content`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_topics`
--

DROP TABLE IF EXISTS `prefix_topics`;
CREATE TABLE IF NOT EXISTS `prefix_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL DEFAULT '0',
  `last_post_id` mediumint(9) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT '',
  `erst` varchar(100) NOT NULL DEFAULT '',
  `art` int(1) NOT NULL DEFAULT '0',
  `stat` int(1) NOT NULL DEFAULT '0',
  `rep` int(10) NOT NULL DEFAULT '0',
  `hit` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_topics`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_topic_alerts`
--

DROP TABLE IF EXISTS `prefix_topic_alerts`;
CREATE TABLE IF NOT EXISTS `prefix_topic_alerts` (
  `tid` mediumint(9) NOT NULL DEFAULT '0',
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten f&uuml;r Tabelle `prefix_topic_alerts`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_user`
--

DROP TABLE IF EXISTS `prefix_user`;
CREATE TABLE IF NOT EXISTS `prefix_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `name_clean` varchar(50) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `recht` int(1) NOT NULL DEFAULT '0',
  `posts` int(5) NOT NULL DEFAULT '0',
  `regist` int(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `llogin` int(20) DEFAULT NULL,
  `spezrank` mediumint(9) NOT NULL DEFAULT '0',
  `opt_pm` tinyint(1) NOT NULL DEFAULT '0',
  `opt_pm_popup` tinyint(1) NOT NULL DEFAULT '0',
  `opt_mail` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sperre` tinyint(1) NOT NULL,
  `geschlecht` tinyint(1) NOT NULL DEFAULT '0',
  `gebdatum` date NOT NULL DEFAULT '0000-00-00',
  `wohnort` varchar(50) NOT NULL DEFAULT '',
  `homepage` varchar(100) NOT NULL DEFAULT '',
  `staat` varchar(50) NOT NULL DEFAULT '',
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `userpic` varchar(100) NOT NULL DEFAULT '',
  `icq` varchar(20) NOT NULL DEFAULT '',
  `msn` varchar(50) NOT NULL DEFAULT '',
  `yahoo` varchar(50) NOT NULL DEFAULT '',
  `aim` varchar(50) NOT NULL DEFAULT '',
  `sig` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_user`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_usercheck`
--

DROP TABLE IF EXISTS `prefix_usercheck`;
CREATE TABLE IF NOT EXISTS `prefix_usercheck` (
  `check` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(100) NOT NULL DEFAULT '',
  `datime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ak` tinyint(4) NOT NULL DEFAULT '0',
  `groupid` tinyint(4) NOT NULL,
  PRIMARY KEY (`check`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_usercheck`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_userfields`
--

DROP TABLE IF EXISTS `prefix_userfields`;
CREATE TABLE IF NOT EXISTS `prefix_userfields` (
  `uid` mediumint(8) NOT NULL DEFAULT '0',
  `fid` mediumint(8) NOT NULL DEFAULT '0',
  `val` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_userfields`
--

INSERT INTO `prefix_userfields` (`uid`, `fid`, `val`) VALUES
(2, 2, '1'),
(2, 3, '1'),
(3, 2, '1'),
(3, 3, '1'),
(4, 2, '1'),
(4, 3, '1'),
(1, 20, '52');

-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_usergallery`
--

DROP TABLE IF EXISTS `prefix_usergallery`;
CREATE TABLE IF NOT EXISTS `prefix_usergallery` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `endung` varchar(5) NOT NULL DEFAULT '',
  `besch` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_usergallery`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_warmaps`
--

DROP TABLE IF EXISTS `prefix_warmaps`;
CREATE TABLE IF NOT EXISTS `prefix_warmaps` (
  `wid` smallint(6) NOT NULL DEFAULT '0',
  `mnr` tinyint(4) NOT NULL DEFAULT '0',
  `map` varchar(100) NOT NULL DEFAULT '',
  `opp` mediumint(9) NOT NULL DEFAULT '0',
  `owp` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`wid`,`mnr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_warmaps`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_warmember`
--

DROP TABLE IF EXISTS `prefix_warmember`;
CREATE TABLE IF NOT EXISTS `prefix_warmember` (
  `wid` smallint(6) NOT NULL DEFAULT '0',
  `uid` mediumint(9) NOT NULL DEFAULT '0',
  `aktion` tinyint(1) NOT NULL DEFAULT '0',
  `kom` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_warmember`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur f&uuml;r Tabelle `prefix_wars`
--

DROP TABLE IF EXISTS `prefix_wars`;
CREATE TABLE IF NOT EXISTS `prefix_wars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `wlp` tinyint(1) NOT NULL DEFAULT '0',
  `owp` mediumint(9) NOT NULL DEFAULT '0',
  `opp` mediumint(9) NOT NULL DEFAULT '0',
  `gegner` varchar(100) NOT NULL DEFAULT '',
  `tag` varchar(100) NOT NULL DEFAULT '',
  `page` varchar(100) NOT NULL DEFAULT '',
  `mail` varchar(100) NOT NULL DEFAULT '',
  `icq` varchar(100) NOT NULL DEFAULT '',
  `wo` varchar(100) NOT NULL DEFAULT '',
  `tid` smallint(6) NOT NULL DEFAULT '0',
  `mod` varchar(100) NOT NULL DEFAULT '',
  `game` varchar(100) NOT NULL DEFAULT '',
  `mtyp` varchar(100) NOT NULL DEFAULT '',
  `land` varchar(100) NOT NULL DEFAULT '',
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de';

--
-- Daten f&uuml;r Tabelle `prefix_wars`
--

