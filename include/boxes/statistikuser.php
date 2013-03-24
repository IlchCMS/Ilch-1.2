<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/statistikuser');

$row->gesMember = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `recht` < -2'), 0);
$row->gesAktiv = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `recht` < -2 AND `status` = 1'), 0);
$row->gesInaktiv = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `recht` < -2 AND `status` = 0'), 0);
$row->gesUser = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `recht` >= -2'), 0);
$row->gesE = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `geschlecht` = 0'), 0);
$row->gesM = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `geschlecht` = 1'), 0);
$row->gesW = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user` WHERE `geschlecht` = 2'), 0);
$row->gesCom = @db_result(db_query('SELECT COUNT(id) FROM `prefix_user`'), 0);

$tpl->set_ar_out($row, 0);
