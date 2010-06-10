<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Statistik';
$hmenu = 'Statistik';
$design = new design($title, $hmenu);
$design->header();

$anzahlShownTage = 7;

echo '<br /><table width=90%" align="center" class="border" cellpadding="0" cellspacing="1" border="0"><tr><td>';
echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">';
echo '<tr class="Chead"><td colspan="3" align="center"><b>Site Statistic</b></td></tr>';

$max_in = 0;
$ges = 0;
$dat = array();
$max_width = 200;

$maxErg = db_query('SELECT MAX(`count`) FROM `prefix_counter`');
$max_in = db_result($maxErg, 0);

$erg = db_query("SELECT `count`, DATE_FORMAT(`date`,'%a der %d. %b') as `datum` FROM `prefix_counter` ORDER BY `date` DESC LIMIT " . $anzahlShownTage);
while ($row = db_fetch_row($erg)) {
    $value = $row[ 0 ];

    if (empty($value)) {
        $bwidth = 0;
    } else {
        $bwidth = $value / $max_in * $max_width;
        $bwidth = round($bwidth, 0);
    }

    echo '<tr class="Cnorm">';
    echo '<td>' . $row[ 1 ] . '</td>';
    echo '<td><table width="' . $bwidth . '" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr><td height="2" class="border"></td></tr></table>';
    echo '</td><td align="right">' . $value . '</td></tr>';

    $ges += $value;
}

$gesBesucher = db_query('SELECT SUM(`count`) FROM `prefix_counter`');
$gesBesucher = @db_result($gesBesucher, 0);

echo '<tr class="Cmite"><td colspan="3"><div align="right">';
echo $lang[ 'weeksum' ] . ': ' . $ges . '</div>';
echo $lang[ 'wholevisitor' ] . ' ' . $gesBesucher . ' &nbsp; ' . $lang[ 'max' ] . ' ' . $max_in . '<br /><br />';
echo '</td></tr></table></td></tr></table><br />';

$design->footer();

?>