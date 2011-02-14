<?php

$sql1 = db_query("CREATE TABLE `prefix_credits` (`id` INT NOT NULL AUTO_INCREMENT, `sys` VARCHAR(5) NOT NULL, `name` VARCHAR(250) NOT NULL, `version` VARCHAR(250) NOT NULL, `url` VARCHAR(250) NOT NULL, `lizenzname` VARCHAR(250) NOT NULL, `lizenzurl` VARCHAR(250) NOT NULL, PRIMARY KEY (`id`)) COMMENT = 'Credits-System - bitte doku beachten';");

$sq2 = db_query("INSERT INTO `prefix_credits` (`sys`, `name`, `version`, `url`, `lizenzname`, `lizenzurl`) VALUES ('ilch', 'ilch.de - CMS', 'aktuell verwendete Version', 'http://ilch.de', 'GPL', 'http://www.gnu.de/gpl-ger.html');");

$sql3 = db_query("INSERT INTO `prefix_credits` (`sys`, `name`, `version`, `url`, `lizenzname`, `lizenzurl`) VALUES ('ilch', 'jQuery', '1.5', 'http://jquery.com', 'GPL', 'http://en.wikipedia.org/wiki/GNU_General_Public_License');");

$sql4 = db_query("INSERT INTO `prefix_credits` (`sys`, `name`, `version`, `url`, `lizenzname`, `lizenzurl`) VALUES ('ilch', 'jQuery UI', '1.8.9', 'http://jqueryui.com', 'GPL', 'http://en.wikipedia.org/wiki/GNU_General_Public_License');");

$rev='230';
$update_messages[$rev][] = 'Credits-Seite: Datenbankstruktur';
$update_messages[$rev][] = 'Credits-Seite: ilch-Lizenz hinzugefuegt';
$update_messages[$rev][] = 'Credits-Seite: jQuery hinzugefuegt';
$update_messages[$rev][] = 'Credits-Seite: jQuery UI hinzugefuegt';