<?php
// update-datei für das sql. Siehe Entwicklerdokumentation
$files = read_ext("update/revision", "php", 0, 0);

// updatefiles bestimmen
$updateFiles = array();
$versions = array();


foreach($files as $file) {
	if(preg_match("/^revision_[0-9]+/", $file)) {
		$updateFiles[] = $file;
		$parts = explode("_", $file);
		$versions[$file] = $parts[1];
	}
}
// sortieren nach versionen
asort($versions);

if(!isset($allgAr["revision"])) {
	$currentverison = 0;
} else {
	$currentversion = $allgAr["revision"];
}

foreach($versions as $key => $version) {
	if($currentversion < $version) {
		// dann müssen wir ein update machen
		include_once("update/revision/" . $key. ".php");
		$currentversion = $version;
	}
}

// aktuelle version setzen
db_query(sprintf("UPDATE `prefix_config` SET `wert` =  '%d' WHERE `schl` = 'revision';", $currentversion));
?>