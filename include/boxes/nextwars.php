<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
echo '<table width="100%" border="0" cellpadding="2" cellspacing="0">';
$akttime = date('Y-m-d');
$erg = @db_query("SELECT DATE_FORMAT(`datime`,'%d.%m.%y - %H:%i') as `time`,`tag`,`gegner`, `id`, `game` FROM `prefix_wars` WHERE `status` = 2 AND `datime` > '" . $akttime . "' ORDER BY `datime`");
if (@db_num_rows($erg) == 0) {
    echo '<tr><td>kein War geplant</td></tr>';
} else {
    while ($row = @db_fetch_object($erg)) {
        $row->tag = (empty($row->tag) ? $row->gegner : $row->tag);
        echo '<tr><td>' . get_wargameimg($row->game) . '</td>';
        echo '<td><a class="box" href="index.php?wars-more-' . $row->id . '">';
        echo $row->time . ' - ' . $row->tag . '</a></td></tr>';
    }
}
echo '</table>';

?>