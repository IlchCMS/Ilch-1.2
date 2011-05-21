<?php
/* UPDATE FÜR REVISION 239 */

//Allg Option ALT entfernen
db_query("DELETE FROM `prefix_config` WHERE `schl` = 'gbook_koms_for_inserts'");
db_query("DELETE FROM `prefix_config` WHERE `schl` = 'gallery_img_koms'");
db_query("DELETE FROM `prefix_config` WHERE `schl` = 'archiv_down_userupload'");

//Allg Option Kalender Kommentar
$sql = <<<SQL

INSERT INTO `prefix_config` (`schl`, `typ`, `typextra`, `kat`, `frage`, `wert`, `pos`, `hide`, `helptext`) VALUES
('Kgkoms', 'r2', NULL, 'Kalender Optionen', 'D&uuml;rfen G&auml;ste Kommentare schreiben?', '0', 0, 0, NULL),
('Kukoms', 'r2', NULL, 'Kalender Optionen', 'D&uuml;rfen User Kommentare schreiben?', '1', 0, 0, NULL),
('GBgkoms', 'r2', NULL, 'G&auml;stebuch Optionen', 'D&uuml;rfen G&auml;ste Kommentare schreiben?', '0', 0, 0, NULL),
('GBukoms', 'r2', NULL, 'G&auml;stebuch Optionen', 'D&uuml;rfen User Kommentare schreiben?', '1', 0, 0, NULL),
('Ggkoms', 'r2', NULL, 'Gallery Optionen', 'D&uuml;rfen G&auml;ste Kommentare schreiben?', '0', 0, 0, NULL),
('Gukoms', 'r2', NULL, 'Gallery Optionen', 'D&uuml;rfen User Kommentare schreiben?', '1', 0, 0, NULL),
('archiv_down_userupload', 'r2', NULL, 'Download Optionen', 'D&uuml;rfen User Dateien hochladen?', '1', 0, 0, NULL),
('Dgkoms', 'r2', NULL, 'Download Optionen', 'D&uuml;rfen G&auml;ste Kommentare schreiben?', '0', 0, 0, NULL),
('Dukoms', 'r2', NULL, 'Download Optionen', 'D&uuml;rfen User Kommentare schreiben?', '1', 0, 0, NULL)

SQL;
db_query($sql);


$rev='239';
$update_messages[$rev][] = 'Kommentare f&uuml;r Downloads, Gallery, GBook, Kalender, Last-Wars, News';
$update_messages[$rev][] = 'Allgemeine Optionen f&uuml;r Kommentare ge&auml;ndert / hinzugef&uuml;gt';
$update_messages[$rev][] = 'Design Kommentarfunktion &uuml;berarbeitet';
$update_messages[$rev][] = 'Kommentarbox mit Rechtepr&uuml;fung erstellt';
$update_messages[$rev][] = 'Neue Funktion get_komsavatar($name) in func/user.php';