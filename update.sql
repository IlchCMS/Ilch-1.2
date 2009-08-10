-- Keine Bedingungen --
ALTER TABLE `prefix_user` ADD `name_clean` VARCHAR( 50 )  `name` varchar(50) NOT NULL default '' AFTER `name`;
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('lang', 's', 'Allgemeine Optionen', 'Standard Sprache', 'de');


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
