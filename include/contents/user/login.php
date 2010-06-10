<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: ' . $lang['login'];
$hmenu = $extented_forum_menu . $lang['login'] . $extented_forum_menu_sufix;

$tpl = new tpl('user/login.htm');
if (loggedin()) {
    $design = new design($title, $hmenu, 0);
    $design->header();
    if (isset($_POST[ 'wdlink' ])) {
        $wd = $_POST[ 'wdlink' ];
    } else {
        $wd = 'index.php?' . $allgAr[ 'smodul' ];
    }
    wd($wd, $lang[ 'yourareloged' ]);
    $design->footer();
} else {
    $design = new design($title, $hmenu);
    $design->addheader($tpl->get(0));
    $design->header();
    $tpl = new tpl('user/login.htm');
    $tpl->set_out('WDLINK', 'index.php?' . $allgAr[ 'smodul' ], 1);
    $design->footer();
}

?>