<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$farbe = '';
$farb2 = '';

echo '<table width="100%" border="0" cellpadding="2" cellspacing="0">';
$erg = db_query('SELECT * FROM `prefix_wars` WHERE `status` = "3" ORDER BY `datime` DESC LIMIT 3');
while ($row = db_fetch_object($erg)) {
    $row->tag = (empty($row->tag) ? $row->gegner : $row->tag);

    if ($row->wlp == 1) {
        $bild = 'include/images/icons/win.gif';
    } elseif ($row->wlp == 2) {
        $bild = 'include/images/icons/los.gif';
    } elseif ($row->wlp == 3) {
        $bild = 'include/images/icons/pad.gif';
    }

    echo '<tr><td>' . get_wargameimg($row->game) . '</td><td align="left">';
    echo '<a href="index.php?wars-more-' . $row->id . '">';
    echo $row->owp . ' ' . $lang['at2'] . ' ' . $row->opp . ' - ' . $row->tag . '</a></td><td><img src="' . $bild . '"></td></tr>';
}
echo '</table>';

?>