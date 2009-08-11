<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$m = date('n');
$j = date('Y');

$where1 = mktime(0 , 0, 0, $m, 1, $j);
$where2 = mktime(24, 0, 0, $m, date('t', $where1), $j);

$data = array();

$result = db_query('SELECT *
	FROM `prefix_kalender`
	WHERE (`time` > ' . $where1 . ' AND `time` < ' . $where2 . ')
		AND ' . $_SESSION['authright'] . ' <= `recht`
	ORDER BY `time` LIMIT 50');
while ($row = db_fetch_assoc($result)) {
    $t_id = $row['id'];
    $t_d = date('j', $row['time']);
    $t_m = date('n', $row['time']);
    $t_y = date('Y', $row['time']);
    $date = mktime(0, 0, 0, $t_m, $t_d, $t_y);
    $data[$date][] = $row;
}

echo getCalendar($m, $j, '?kalender-v1-m{mon}-y{jahr}-d{tag}', '?kalender-v0-m{mon}-y{jahr}', $data, 1);

?>