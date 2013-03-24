<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr['title'] . ' :: Statistik';
$hmenu = 'Statistik';
$design = new design($title, $hmenu);
$design->header();
$tpl = new tpl('statistik');
$tpl->out(0);

$anzahlShownTage = 7;
$max_in = 0;
$ges = 0;
$dat = array();
$max_width = 200;

$maxErg = db_query('SELECT MAX(`count`) FROM `prefix_counter`');
$max_in = db_result($maxErg, 0);

$erg = db_query("SELECT `count`, DATE_FORMAT(`date`,'%a der %d. %b') as `datum` FROM `prefix_counter` ORDER BY `date` DESC LIMIT " . $anzahlShownTage);
while ($row = db_fetch_row($erg)) {
    $value = $row[0];
    if (empty($value)) {
        $bwidth = 0;
    } else {
        $bwidth = $value / $max_in * $max_width;
        $bwidth = round($bwidth, 0);
    }
    $tpl->set('date', $row[1]);
    $tpl->set('bwidth', $bwidth);
    $tpl->set('anz', $value);
    $tpl->out(1);
    $ges += $value;
}

$gesBesucher = db_query('SELECT SUM(`count`) FROM `prefix_counter`');
$gesBesucher = @db_result($gesBesucher, 0);
$tpl->set('ges', $ges);
$tpl->set('gesBesucher', $gesBesucher);
$tpl->set('max_in', $max_in);
$tpl->out(2);

$design->footer();
