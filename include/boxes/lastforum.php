<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$query = "SELECT a.id, a.name, a.rep, c.erst as last, c.id as pid, c.time
FROM prefix_topics a
  LEFT JOIN prefix_forums b ON b.id = a.fid
  LEFT JOIN prefix_posts c ON c.id = a.last_post_id
  LEFT JOIN prefix_groupusers vg ON vg.uid = " . $_SESSION['authid'] . " AND vg.gid = b.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = " . $_SESSION['authid'] . " AND rg.gid = b.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = " . $_SESSION['authid'] . " AND sg.gid = b.start
WHERE ((" . $_SESSION['authright'] . " <= b.view AND b.view < 1)
   OR (" . $_SESSION['authright'] . " <= b.reply AND b.reply < 1)
   OR (" . $_SESSION['authright'] . " <= b.start AND b.start < 1)
	 OR vg.fid IS NOT NULL
	 OR rg.fid IS NOT NULL
	 OR sg.fid IS NOT NULL
	 OR -9 >= " . $_SESSION['authright'] . ")
ORDER BY c.time DESC
LIMIT 0,5";
echo '<table>';
$resultID = db_query($query);
while ($row = db_fetch_assoc($resultID)) {
    $row['date'] = date('d.m.y - H:i', $row['time']);
    $row['page'] = ceil (($row['rep'] + 1) / $allgAr['Fpanz']);
    echo '<tr><td valign="top"><b> &raquo; </b></td><td><a href="?forum-showposts-' . $row['id'] . '-p' . $row['page'] . '#' . $row['pid'] . '" title="' . $row['date'] . '">' . ((strlen($row['name']) < 18) ? $row['name'] : substr($row['name'], 0, 15) . '...') . '<br /><span class="smalfont"> von ' . $row['last'] . '</span></a></td></tr>';
}
echo '</table>';

?>