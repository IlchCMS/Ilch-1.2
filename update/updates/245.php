<?php

db_query("INSERT INTO `ic1_config` (`schl`, `typ`, `typextra`, `kat`, `frage`, `wert`, `pos`, `hide`, `helptext`) VALUES
    ('sliderShow', 'r2', NULL, 'Contentslider', 'Contentslider aktivieren', '1', 1, 0, NULL),
    ('sliderSmodul', 'r2', NULL, 'Contentslider', 'Nur auf der Startseite anzeigen', '0', 2, 0, 'Der Contentslider wird nur auf der eingestellten Startseite angezeigt.<br />(Allgemein -> Start Modul der Seite) '),
    ('sliderWidth', 'input', NULL, 'Contentslider', 'Breite für den Contentslider', '670', 3, 0, NULL),
    ('sliderHeight', 'input', NULL, 'Contentslider', 'Höhe für den Contentslider', '200', 4, 0, NULL),
    ('sliderSpeed', 'input', NULL, 'Contentslider', 'Dauer zwischen Bannerwechsel', '5000', 5, 0, 'Angabe einer Zahl in Millisekunden<br />(1 Sekunde = 1000 Millisekunden)<br />in welcher Zeit zum nächsten Banner gewechselt wird.'),
    ('sliderDuration', 'input', NULL, 'Contentslider', 'Länge des Effektübergangs', '500', 6, 0, 'Angabe einer Zahl in Millisekunden<br />(1 Sekunde = 1000 Millisekunden)<br /> in welcher Zeit der Effektwechsel stattfindet.'),
    ('sliderAnimation', 'select', '{\"keys\":[1, 2], \"values\":[\"hereingleiten / slide\", \"überblenden / fade\"]}', 'Contentslider', 'Wechseleffekt auswählen', '1', 7, 0, NULL),
    ('sliderTitle', 'r2', NULL, 'Contentslider', 'Titelanzeige aktivieren', '1', 8, 0, NULL),
    ('sliderMarker', 'r2', NULL, 'Contentslider', 'Bildzahlen aktivieren', '1', 9, 0, NULL),
    ('sliderControl', 'r2', NULL, 'Contentslider', 'Vor- und Zurücktasten aktivieren', '1', 10, 0, NULL),
    ('sliderKeyboard', 'r2', NULL, 'Contentslider', 'Tastaturnavigation aktivieren', '1', 11, 0, 'Die Banner können beim Hover über dem Contentslider auch mit den Cursortasten gewechselt werden.'),
    ('sliderAutomic', 'r2', NULL, 'Contentslider', 'Automatische Rotation aktivieren', '1', 12, 0, 'Der Wechsel zwischen den Bannern erfolgt, in der angegebenen Zeit, automatisch.'),
    ('sliderWait', 'r2', NULL, 'Contentslider', 'Hoverpause aktivieren', '1', 13, 0, 'Die Rotation wird beim Hover über dem Contentslider pausiert.'),
    ('sliderResize', 'r2', NULL, 'Contentslider', 'Bildskalierung aktivieren', '1', 14, 0, 'Die Bannergröße wird automatisch an die Maße (Breite und Höhe) des Contenslider angepasst.'),
    ('sliderRandom', 'r2', NULL, 'Contentslider', 'Zufallsanordnung aktivieren', '0', 15, 0, 'Die Banner werden nicht in geordneter Reihenfolge, sondern in zufälliger Reihenfolge angezeigt')");

db_query("CREATE TABLE IF NOT EXISTS `ic1_contentslider` (
    `id` smallint(6) NOT NULL AUTO_INCREMENT,
    `pos` smallint(6) NOT NULL DEFAULT '0',
    `status` smallint(1) NOT NULL DEFAULT '0',
    `name` varchar(100) NOT NULL DEFAULT '',
    `banner` varchar(100) NOT NULL DEFAULT '',
    `link` varchar(100) NOT NULL DEFAULT '',
    `target` varchar(10) NOT NULL DEFAULT '_self',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='powered by ilch.de' AUTO_INCREMENT=6");

db_query("INSERT INTO `ic1_contentslider` (`id`, `pos`, `status`, `name`, `banner`, `link`, `target`) VALUES
    (1, 0, 1, 'Berge im Herbst', 'include/images/contentslider/1_mkygre.jpg', 'http://www.ilch.de/', '_blank'),
    (2, 1, 1, 'Weizenfeld im Sommer', 'include/images/contentslider/2_sigdl7.jpg', 'http://www.ilch.de/', '_blank'),
    (3, 2, 1, 'Straße an der Küste', 'include/images/contentslider/3_yjsdzq.jpg', 'http://www.ilch.de/', '_blank'),
    (4, 3, 1, 'Steine am Meer', 'include/images/contentslider/4_wwnahq.jpg', 'http://www.ilch.de/', '_blank'),
    (5, 4, 1, 'Landschaft mit Waserfall', 'include/images/contentslider/5_4psxyq.jpg', 'http://www.ilch.de/', '_blank')");

db_query("INSERT INTO `ic1_credits` (`id`, `sys`, `name`, `version`, `url`, `lizenzname`, `lizenzurl`) VALUES
    (4, 'ilch', 'Basic jQuery Slider', '1.3', 'http://www.basic-slider.com', 'GPL', 'http://www.opensource.org/licenses/gpl-3.0.html')");

$rev = '245';
$update_messages[$rev][] = 'Einbindung eines konfigurierbaren Contentslider.';