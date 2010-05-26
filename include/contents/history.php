<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: History';
$hmenu = 'History';
$design = new design($title, $hmenu);
$design->header();

$tpl = new tpl('history');
$tpl->out(0);
$class = '';
$abf = "SELECT `id`,DATE_FORMAT(date,'%d.%m.%Y') as `date1`,`title`,`txt` FROM `prefix_history` ORDER BY `date`";
$erg = db_query($abf);
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row[ 'class' ] = $class;
    $row[ 'txt' ] = bbcode($row[ 'txt' ]);
    $tpl->set_ar_out($row, 1);
}
$tpl->out(2);

$design->footer();

?>