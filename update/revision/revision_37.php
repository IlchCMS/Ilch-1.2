<?php
// revision 37

// erst mal die revisionsnummer hinzufügen
db_query("INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`kat` ,
`frage` ,
`wert` ,
`pos`
)
VALUES (
'revision', 'input', 'Allgemeine Optionen', 'Revisionsnummer', '0', '0'
);");

db_query("DROP TABLE `prefix_modules` ;");

db_query("CREATE TABLE IF NOT EXISTS `prefix_modules` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `gshow` tinyint(1) NOT NULL DEFAULT '0',
  `ashow` tinyint(1) NOT NULL DEFAULT '0',
  `fright` tinyint(1) NOT NULL DEFAULT '0',
  `menu` varchar(200) NOT NULL,
  `pos` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='powered by ilch.de' AUTO_INCREMENT=39 ;");

db_query("INSERT INTO `prefix_modules` (`url`, `name`, `gshow`, `ashow`, `fright`, `menu`, `pos`) VALUES
('gallery', 'Gallery', 1, 0, 1, 'Content', 4),
('menu', 'Navigation', 1, 0, 0, 'Admin', 2),
('groups', 'Teams', 1, 0, 1, 'Clanbox', 2),
('rules', 'Regeln', 1, 0, 1, 'Clanbox', 5),
('awards', 'Awards', 1, 0, 1, 'Clanbox', 3),
('forum', 'Forum', 1, 0, 1, 'Content', 1),
('archiv-downloads', 'Downloads', 1, 0, 1, 'Content', 2),
('kalender', 'Kalender', 1, 0, 1, 'Content', 7),
('wars', 'Wars', 0, 0, 1, '', 0),
('kasse', 'Kasse', 1, 0, 1, 'Clanbox', 4),
('gbook', 'Gästebuch', 1, 0, 1, 'Content', 5),
('awaycal', 'Awaycal', 0, 0, 0, '', 0),
('news', 'News', 1, 0, 1, 'Content', 0),
('allg', 'Konfiguration', 1, 0, 0, 'Admin', 0),
('backup', 'Backup', 1, 0, 0, 'Admin', 3),
('range', 'Ranks', 1, 0, 0, 'Admin', 4),
('wars-last', 'Lastwars', 1, 0, 0, 'Clanbox', 1),
('smilies', 'Smilies', 1, 0, 0, 'Admin', 5),
('newsletter', 'Newsletter', 1, 0, 0, 'Admin', 6),
('checkconf', 'Serverkonfiguration', 1, 0, 0, 'Admin', 7),
('user', 'User', 1, 0, 0, 'User', 0),
('grundrechte', 'Grundrechte', 1, 0, 0, 'User', 1),
('profilefields', 'Profilfelder', 1, 0, 0, 'User', 2),
('selfbp', 'Eigene Box/Page', 1, 0, 0, 'Eigene Box/Page', 0),
('wars-next', 'Nextwars', 1, 0, 0, 'Clanbox', 0),
('history', 'History', 1, 0, 0, 'Clanbox', 6),
('trains', 'Trainzeiten', 1, 0, 0, 'Clanbox', 7),
('archiv-links', 'Links', 1, 0, 0, 'Content', 3),
('vote', 'Umfragen', 1, 0, 0, 'Content', 6),
('contact', 'Kontakt', 1, 0, 0, 'Content', 8),
('impressum', 'Impressum', 1, 0, 0, 'Content', 9),
('archiv-partners', 'Partner', 1, 0, 0, 'Boxen', 0),
('picofx', 'Pic of X', 1, 0, 0, 'Boxen', 1),
('modules', 'Modulverwaltung', 1, 0, 0, 'Admin', 8),
('smtpconf', 'SMTP', 1, 0, 0, 'Admin', 1),
('puser', 'Nicht bestätigte Registrierungen', 0, 0, 0, '', 0),
('bbcode', 'BBcode 2.0', 1, 0, 1, 'Content', 11);");
