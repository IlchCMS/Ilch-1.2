<?php


/**
 * Checkt die Konsistenz der Sortierung
 * Holt sich alle smilies der Reihe nach (also niedrige `pos` zuerst)
 * und zählt dann mit einem zähler von 1, 2, ... bis n
 * das n-te smilie bekommt dann die position n
 * so bleibt die sortierung konsistent
 * TODO: optimize efficiency
 */
function check_pos_consistency() {
	$erg = db_query("SELECT `id` FROM `prefix_smilies` ORDER BY `pos` ASC");
	$zaehler = 1;
	while($row = db_fetch_assoc($erg)) {
		db_query(sprintf("UPDATE `prefix_smilies` SET `pos` = %d WHERE `id` = %d", $zaehler++, $row['id']));
	}
}

/**
 * Setzt die Positionen der Smilies neu
 * 
 * keys in positions und ids müssen die gleichen sein.
 * @param $positions die neuPositionierungen
 * @param $ids die ids der positionierungen
 */
function set_positions($positions, $ids) {
	foreach($positions as $alteposition => $neueposition) {
		if($alteposition + 1 != $neueposition) {
			// echo "putting " . ($alteposition + 1) . " to " . $neueposition . "<br />";
			db_query(sprintf("UPDATE `prefix_smilies` SET `pos` = %d WHERE `id` = %d", escape($neueposition, "int"), escape($ids[$alteposition], "int")));
		}
	}
}

set_positions($_POST["pos"], $_POST["id"]);
check_pos_consistency();

?>