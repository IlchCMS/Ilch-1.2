<?php
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Smartytest';
$hmenu = 'Smartytest';
$design = new design($title, $hmenu);
$design->header();

include 'include/includes/class/iSmarty.php';
$smarty = new iSmarty();

$smarty->assign('var', 'Wirklich');
$smarty->display('testsmarty.tpl');

$design->footer();
?>