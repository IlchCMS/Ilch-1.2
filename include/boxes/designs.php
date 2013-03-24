<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/designs');

if (!empty($_POST['temp_ch'])) {
    $_SESSION['authgfx'] = $_POST['temp_ch'];
    wd('', '', 0);
} else {
    $tpl->set_out('url', $menu->get_complete(), 'start');
    $o = opendir('include/designs');
    while ($f = readdir($o)) {
        if (!preg_match("/\\..*/", $f) AND is_dir('include/designs/' . $f)) {
            $s = ($f == $_SESSION['authgfx'] ? ' selected' : '');
            $tpl->set('select', $s);
            $tpl->set('name', $f);
            $tpl->out('choice');
        }
    }
    $tpl->out('end');
}
