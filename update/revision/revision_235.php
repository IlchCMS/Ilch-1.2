<?php
/* UPDATE FÜR REVISION 235 */

// in forum_avatar_upload -Avatar Upload?- zu -Avatar und Userpic Upload?- geändert
db_query("UPDATE `prefix_config` SET `frage` = 'Avatar und Userpic Upload?' WHERE `schl` = 'forum_avatar_upload'");
// Size für Avatar & Userpic entfernt, da nicht mehr notwendig
db_query("DELETE FROM `prefix_config` WHERE `schl` = 'Fasize'");
db_query("DELETE FROM `prefix_config` WHERE `schl` = 'userpic_Fasize'");

$rev='235';
$update_messages[$rev][] = 'Konfigurationsfrage um Userpic erweitert';
$update_messages[$rev][] = 'Size für Avatar & Userpic entfernt';