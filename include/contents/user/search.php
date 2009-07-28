<?php
// Copyright by: Florian Koerner
// Support: www.ilch.de
defined ('main') or die ('no direct access');

// Wert escapen und umwandeln
$_POST = get_lower($_POST);
$name_clean = escape($_POST['value'], 'string');

// Abbrechen, wenn keine Übergabe stattgefunden hat
if (!isset($name_clean) || $name_clean == '') exit;

// Datenbank nach Nutzer durchsuchen
$found = Array();
$erg = db_query("SELECT `name` FROM `prefix_user` WHERE `name_clean` LIKE '".$name_clean."%' ORDER BY `name` ASC LIMIT 10");
while ($row = db_fetch_assoc($erg)) {
	$found[] = array(
		"value" => $row['name'], 
	);
}

// JSON encode the array for return
echo json_encode($found);
?>