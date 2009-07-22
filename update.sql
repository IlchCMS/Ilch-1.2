ALTER TABLE `prefix_user` ADD `name_clean` VARCHAR( 50 )  `name` varchar(50) NOT NULL default '' AFTER `name`;




-- Beim Update darauf achten --
-- Alle Nutzernamen und Email-Adressen mit get_lower als Array durchlaufen
-- Nutzernamen als name_clean speichern und Email ersetzen
-- Doppelte Email-Adressen löschen
