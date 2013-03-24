<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr['title'] . ' :: Regeln';
$hmenu = 'Regeln';
$design = new design($title, $hmenu);
$design->header();
$tpl = new tpl('rules');
// -----------------------------------------------------------|
$erg = db_query('SELECT `zahl`,`titel`,`text` FROM `prefix_rules` ORDER BY `zahl`');
while ($row = db_fetch_row($erg)) {
    $tpl->set('zahl', $row[0]);
    $tpl->set('titel', $row[1]);
    $tpl->set('text', bbcode($row[2]));
    $tpl->out(0);
}

$design->footer();
