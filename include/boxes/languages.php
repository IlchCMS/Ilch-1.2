<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/languages');

if (!empty($_POST['lang_ch'])) {
    $_SESSION['authlang'] = $_POST['lang_ch'];
    wd('', '', 0);
} else {
    $tpl->set_out('url', $menu->get_complete(), 'start');
    $o = opendir('include/includes/lang');
    while ($f = readdir($o)) {
        if ($f != '.' AND $f != '..' AND is_dir('include/includes/lang/' . $f)) {
            $s = ($f == $_SESSION['authlang'] ? ' selected' : '');
            $tpl->set('select', $s);
            $tpl->set('name', $f);
            $tpl->out('choice');
        }
    }
    $tpl->out('end');
}
