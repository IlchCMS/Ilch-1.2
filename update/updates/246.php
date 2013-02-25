<?php

db_query("DELETE FROM `prefix_config` WHERE `kat` = 'Gästebuch'");

db_query("INSERT INTO `prefix_config` (`schl`, `typ`, `typextra`, `kat`, `frage`, `wert`, `pos`, `hide`, `helptext`) VALUES
('gbook_koms_for_inserts', 'r2', NULL, 'Gästebuch', 'Kommentare für Einträge zulassen?', '1', 5, 0, NULL),
('gbook_posts_per_site', 'input', NULL, 'Gästebuch', 'Einträge pro Seite', '20', 2, 0, NULL),
('gbook_show', 'select', '{\"keys\":[1, 0, 2], \"values\":[\"Die Einträge werden sofort angezeigt\", \"Die Einträge müssen erst freigeschaltet werden\", \"Es werden keine Einträge zugelassen\"]}', 'Gästebuch', 'Status vom Gästebuch', '2', 1, 0, 'Hier kann entschieden werden, ob die Einträge sofort oder erst nach Prüfung sichtbar sind. Die Option \"keine Einträge zulassen\" lässt nur ein Lesen des Gästebuchs zu.'),
('gbook_text_length', 'input', NULL, 'Gästebuch', 'max. Textlänge im Gästebuch', '600', 4, 0, NULL),
('gbook_time_ban', 'input', NULL, 'Gästebuch', 'Ip Sperre in Sekunden', '3600', 3, 0, NULL)");

db_query("ALTER TABLE `prefix_gbook` ADD COLUMN `show` tinyint(1) NOT NULL DEFAULT '0'");

$rev = '246';
$update_messages[$rev][] = 'Gästebuch mit Freischaltfunktion und Information über Loginbox';
