<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/newsletter');

if (empty($_POST['newsletter'])) {
    $tpl->set_out('url', $menu->get_complete(), 'start');
} else {
    $email = escape($_POST['newsletter'], 'string');
    $erg = db_query("SELECT COUNT(*) FROM `prefix_newsletter` WHERE `email` = '" . $email . "'");
    $anz = db_result($erg, 0);
    if ($anz == 1) {
        db_query("DELETE FROM `prefix_newsletter` WHERE `email` = '" . $email . "'");
        $tpl->out('delete');
    } else {
        db_query("INSERT INTO `prefix_newsletter` (`email`) VALUES ('" . $email . "')");
        $tpl->out('insert');
    }
}