<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/search');

if (isset($_GET['search'])) {
    $tpl->set('search', escape($_GET['search'], 'string'));
} else {
    $tpl->set('search', '');
}

$tpl->out(0);
