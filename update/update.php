<?php
$updatesDir = 'update/updates/';

// update-datei für das sql. Siehe Entwicklerdokumentation
$updates = read_ext($updatesDir, 'php', 0, 0);
natsort($updates);

/* Versionstabelle anlegen, wenn diese noch nicht existiert */
$qry = db_query('SHOW TABLES LIKE "' . DBPREF . 'dev_dbupdates"');
if (db_num_rows($qry) === 0) {
    db_query('CREATE TABLE `prefix_dev_dbupdates` ('
        . '`update` VARCHAR(30)  NOT NULL,'
        . '`insertTime` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,'
        . 'PRIMARY KEY (`update`)) ENGINE = MyISAM'
    );
    //Schon durchgeführte Updates (aus altem Revisionsschema) eintragen
    if (isset($allgAr['revision'])) {
        $latestRevision = $allgAr['revision'];
        $revisionsToInsert = array();
        foreach ($updates as $update) {
            if (ctype_digit($update) && $update <= $latestRevision) {
                $revisionsToInsert[] = '("' . $update . '")';
            }
        }
        if (!empty($revisionsToInsert)) {
            db_query('INSERT INTO `prefix_dev_dbupdates` (`update`) VALUES '
                . implode(",\n", $revisionsToInsert)
            );
        }
        db_query('DELETE FROM `prefix_config` WHERE `schl`  = "revision"');
    }
}
$oldUpdates = simpleArrayFromQuery('SELECT `update` FROM `prefix_dev_dbupdates`');
$updatesToRun = array_diff($updates, $oldUpdates);

if (!empty($updatesToRun)) {
    $update_messages = array();
    $dbInserts = array();
    foreach($updatesToRun as $update) {
        // Update laden
        include $updatesDir . $update . '.php';
        // Eintragen in dbupdates Tabelle
        $dbInserts[] = '("' . $update . '")';
    }
    
    db_query('INSERT INTO `prefix_dev_dbupdates` (`update`) VALUES '
        . implode(",\n", $dbInserts)
    );
    
    //Update Meldungen ausgeben
    if (!empty($update_messages)) {
        echo '<div style="background-color:#FFFFFF;color:#000000;margin:0 auto;text-align:left;width:1000px;"><ul>';
        foreach ($update_messages as $key => $value) {
            echo '<li>Update "' . $key . '"<ul>';
            foreach ($value as $message) {
                echo '<li>' . $message . '</li>';
            }
            echo '</ul></li>';
        }
        echo '<ul></div>';
    }
    echo '<p>Updates erfolgreich eingespielt, laden sie die Seite erneut.</p>';
    exit;
}