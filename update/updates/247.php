<?php
/**
 * Add key "user_upload_max_size" and remove cat "Archiv".
 * 
 * @author Dominik Meyer <kinimodmeyer@gmail.com>
 */
$sql = 'INSERT INTO `prefix_config`
        (
            `schl`,
            `typ`,
            `typextra`,
            `kat`,
            `frage`,
            `wert`,
            `pos`,
            `hide`,
            `helptext`
        )
        VALUES
        (
            "user_upload_max_size",
            "input",
            NULL,
            "Allgemein",
            "Maximale Uploadgröße (in Byte)",
            "2097000",
            2,
            0,
            NULL
        )';
db_query($sql);

$sql = 'UPDATE `prefix_config`
        SET `kat` = "Allgemein", `pos` = 1
        WHERE `schl` = "archiv_down_userupload"';
db_query($sql);

$sql = 'UPDATE `prefix_config`
        SET `kat` = "Allgemein", `pos` = 3
        WHERE `schl` = "Aanz"';
db_query($sql);

$sql = 'UPDATE `prefix_config`
        SET `kat` = "Allgemein", `pos` = 4
        WHERE `schl` = "Aart"';
db_query($sql);


$rev = '247';
$update_messages[$rev][] = 'Uploadgröße über den Adminbereich steuerbar';
