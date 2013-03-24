<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/statistik');

$heute = date('Y-m-d');

$ges_visits = db_result(db_query("SELECT SUM(`count`) FROM `prefix_counter`"), 0);
$ges_heute = @db_result(db_query("SELECT `count` FROM `prefix_counter` WHERE `date` = '" . $heute . "'"), 0);
$ges_gestern = @db_result(db_query('SELECT `count` FROM `prefix_counter` WHERE `date` < "' . $heute . '" ORDER BY `date` DESC LIMIT 1'), 0);

$tpl->set('ges_visits', $ges_visits);
$tpl->set('ges_heute', $ges_heute);
$tpl->set('ges_gestern', $ges_gestern);
$tpl->set('ges_online', ges_online());

$tpl->out(0);
