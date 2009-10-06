<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Ilch Admin-Control-Panel :: Regeln', '', 2);
$design->header();
$um = '';
if (isset($_REQUEST['um'])) {
    $um = $_REQUEST['um'];
}

if (!empty($_POST['sub'])) {
    $text = escape($_POST['text'], 'string');
    $titel = escape($_POST['titel'], 'string');
    $zahl = escape($_POST['zahl'], 'integer');
    if (empty($_POST['sid'])) {
        db_query('INSERT INTO `prefix_rules` (`text`,`titel`,`zahl`) VALUES ( "' . $text . '","' . $titel . '","' . $zahl . '" ) ');
    } else {
        $sid = escape($_POST['sid'], 'integer');
        db_query('UPDATE `prefix_rules` SET `text` = "' . $text . '", `titel` = "' . $titel . '", `zahl` = "' . $zahl . '" WHERE `id` = "' . $sid . '"');
    }
}
if (!empty($_GET['delete'])) {
    $delete = escape($_GET['delete'], 'integer');
    db_query('DELETE FROM `prefix_rules` WHERE `id` = "' . $delete . '" LIMIT 1');
}

if (empty($_GET['sid'])) {
    $row = array();
    $row['sub'] = 'Eintragen';
    $row['zahl'] = '';
    $row['titel'] = '';
    $row['text'] = $row['sid'] = '';
} else {
    $abf = 'SELECT `text`,`zahl`,`titel`,`id` as `sid` FROM `prefix_rules` WHERE `id` = "' . $_GET['sid'] . '"';
    $erg = db_query($abf);
    $row = db_fetch_assoc($erg);
    $row['sub'] = '&Auml;ndern';
}

$clas = '';
$tpl = new tpl ('rules', 1);
$tpl->set_ar_out($row, 0);
$erg = db_query('SELECT * FROM `prefix_rules` ORDER BY `zahl`');
while ($row = db_fetch_assoc($erg)) {
    $clas = ($clas == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row['class'] = $clas;
    $tpl->set_ar_out($row, 1);
}
$tpl->out(2);

$design->footer();

?>