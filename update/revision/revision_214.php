

<?php
db_query("ALTER TABLE `prefix_online` CHANGE `content` `content` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '(Startseite)'");

$rev='213';
$update_messages[$rev][] = 'online-Tabelle Standard-Value zu "content" hinzugefügt da sonst error in der debug ausgegeben wurde';