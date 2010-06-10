<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$abf = 'SELECT *
	        FROM `prefix_news`
					WHERE `news_recht` >= ' . $_SESSION[ 'authright' ] . '
					ORDER BY `news_time` DESC
					LIMIT 0,5';
$erg = db_query($abf);
echo '<table>';
while ($row = db_fetch_object($erg)) {
    echo '<tr><td v><b> &raquo; </b></td><td><a class="box" href="index.php?news-' . $row->news_id . '">' . $row->news_title . '</a></td></tr>';
}
echo '</table>';

?>