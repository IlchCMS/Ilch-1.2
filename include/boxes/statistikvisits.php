<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/statistikvisits');

$heute = date('Y-m-d');
$time = time();
$useroneregist = @db_result(db_query('SELECT `regist` FROM `prefix_user` ORDER BY `regist` ASC'), 0);
$row->ges_visits = db_result(db_query('SELECT SUM(`count`) FROM `prefix_counter`'), 0);
$row->ges_heute = @db_result(db_query('SELECT `count` FROM `prefix_counter` WHERE `date` = "' . $heute . '"'), 0);
$row->ges_gestern = @db_result(db_query('SELECT `count` FROM `prefix_counter` WHERE `date` < "' . $heute . '" ORDER BY `date` DESC LIMIT 1'), 0);
$row->day_visits = round($row->ges_visits / (($time - $useroneregist) / (60 * 60 * 24)));
$row->ges_online = ges_online();

$tpl->set_ar_out($row, 0);
