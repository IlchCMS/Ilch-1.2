<?php
db_query("CREATE TABLE `prefix_opponents` (
`id` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 255 ) NOT NULL ,
`tag` VARCHAR( 100 ) NOT NULL ,
`page` VARCHAR( 255 ) NOT NULL ,
`email` VARCHAR( 255 ) NOT NULL ,
`icq` INT( 11 ) NOT NULL ,
`nation` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM COMMENT = 'Gegner-Datenbank';");

db_query("INSERT INTO `prefix_modules` (`url`, `name`, `gshow`, `ashow`, `fright`, `menu`, `pos`) VALUES ('opponents', 'Gegner', '0', '0', '0', 'ClanBox', '1');");

db_query("UPDATE `prefix_modules` SET `pos` = '8' WHERE `id` =64;");

db_query("UPDATE `prefix_modules` SET `pos` = '7' WHERE `id` =65;");

db_query("UPDATE `prefix_modules` SET `pos` = '1' WHERE `id` =63;");

db_query("UPDATE `prefix_modules` SET `pos` = '2' WHERE `id` =55;");

db_query("UPDATE `prefix_modules` SET `menu` = 'Clanbox', `pos` = '0' WHERE `id` =79;");

db_query("UPDATE `prefix_modules` SET `pos` = '4' WHERE `id` =42;");

db_query("UPDATE `prefix_modules` SET `pos` = '5' WHERE `id` =48;");

db_query("UPDATE `prefix_modules` SET `pos` = '6' WHERE `id` =41;");

db_query("ALTER TABLE `prefix_opponents` ADD `logo` VARCHAR( 255 ) NOT NULL ");

$rev='222';
$update_messages[$rev][] = 'Gegner-Datenbank " prefix_opponents " angelegt und ClanBox Menü neu geordnet';