<?php
/* 
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$ $Date $Author
 */ 
// update-datei für das sql. Siehe Entwicklerdokumentation
$files = read_ext("update/revision", "php", 0, 0);
// updatefiles bestimmen
$updateFiles = array();
$versions = array();

foreach($files as $file) {
    if (preg_match("/^revision_[0-9]+/", $file)) {
        $updateFiles[] = $file;
        $parts = explode("_", $file);
        $versions[$file] = $parts[1];
    }
}
// sortieren nach versionen
asort($versions);

if (!isset($allgAr["revision"])) {
    $currentversion = 0;
} else {
    $currentversion = $allgAr["revision"];
}

$update_messages = array();

foreach($versions as $key => $version) {
    if ($currentversion < $version) {
        if (file_exists('update/revision/' . $key . '.php')) {
            // dann müssen wir ein update machen
            include_once('update/revision/' . $key . '.php');
            $currentversion = $version;
            // aktuelle version setzen
            db_query(sprintf("UPDATE `prefix_config` SET `wert` =  '%d' WHERE `schl` = 'revision';", $currentversion));
        }
    }
}

if (!empty($update_messages)) {
    echo '<div style="background-color:#FFFFFF;color:#000000;margin:0 auto;text-align:left;width:1000px;"><ul>';
    foreach ($update_messages as $key => $value) {
        echo '<li>Revision ' . $key . ' <ul>';
        foreach ($value as $key2 => $value2) {
            echo '<li>' . $value2 . '</li>';
        }
        echo '</ul></li>';
    }
    echo '<ul></div>';
}