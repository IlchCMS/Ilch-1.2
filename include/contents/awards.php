<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Awards';
$hmenu = 'Awards';
$design = new design($title, $hmenu);
$design->header();

$tpl = new tpl('awards.htm');
$tpl->out(0);
$class = 'Cnorm';
$erg = db_query("SELECT `platz`, `text`, `wofur`, `team`, `bild`, DATE_FORMAT(time, '%d.%m.%Y') as `time` FROM `prefix_awards` ORDER BY `time` DESC");
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    if ($row[ 'bild' ] != '' AND trim($row[ 'bild' ]) != 'http://') {
        $row[ 'bildutime' ] = '<span style="float: left; margin-right: 10px;"><img src="' . $row[ 'bild' ] . '" alt="' . $row[ 'wofur' ] . '" title="' . $row[ 'wofur' ] . '"/><br /><font class="smalfont">' . $row[ 'time' ] . '</font></span><br />';
    } else {
        $row[ 'bildutime' ] = $lang[ 'date' ] . ': ' . $row[ 'time' ] . '<br />';
    }
    $row[ 'class' ] = $class;
    $tpl->set_ar_out($row, "tabelle");
}
$tpl->out("ende");

$design->footer();