<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/statistiksite');

$row->news = @db_result(db_query('SELECT COUNT(news_id) FROM `prefix_news`'), 0);
$row->downs = @db_result(db_query('SELECT COUNT(id) FROM `prefix_downloads`'), 0);
$row->koms = @db_result(db_query('SELECT COUNT(id) FROM `prefix_koms`'), 0);
$row->gbook = @db_result(db_query('SELECT COUNT(id) FROM `prefix_gbook`'), 0);
$row->wars = @db_result(db_query('SELECT COUNT(id) FROM `prefix_wars` WHERE `status` = 3'), 0);
$row->topic = @db_result(db_query('SELECT COUNT(id) FROM `prefix_topics`'), 0);
$row->posts = @db_result(db_query('SELECT COUNT(id) FROM `prefix_posts`'), 0);
$row->gallery = @db_result(db_query('SELECT COUNT(id) FROM `prefix_gallery_imgs`'), 0);
$row->usergallery = @db_result(db_query('SELECT COUNT(id) from `prefix_usergallery`'), 0);
$row->partner = @db_result(db_query('SELECT COUNT(id) FROM `prefix_partners`'), 0);

$tpl->set_ar_out($row, 0);
