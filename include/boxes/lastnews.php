<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$abf = 'SELECT *
	        FROM prefix_news
					WHERE news_recht >= ' . $_SESSION['authright'] . '
					ORDER BY news_time DESC
					LIMIT 0,5';
$erg = db_query($abf);
echo '<table>';
while ($row = db_fetch_object($erg)) {
    echo '<tr><td valign="top"><b> &raquo; </b></td><td><a class="box" href="index.php?news-' . $row->news_id . '">' . $row->news_title . '</a></td></tr>';
}
echo '</table>';

?>