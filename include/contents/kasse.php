<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: Clankasse';
$hmenu = 'Clankasse';
$design = new design ($title , $hmenu);
$design->header();
// loeschen
if ($menu->getA(1) == 'd' AND is_numeric($menu->getE(1)) AND has_right(- 8, 'kasse')) {
    db_query("DELETE FROM prefix_kasse WHERE id = " . $menu->getE(1));
}

$m = date('m');
$y = date('Y');
if ($menu->getA(1) == 'm' AND is_numeric($menu->getE(1))) {
    $m = escape($menu->getE(1), 'integer');
}
if ($menu->getA(2) == 'y' AND is_numeric($menu->getE(2))) {
    $y = escape($menu->getE(2), 'integer');
}

$pm = $m - 1;
$nm = $m + 1;
$py = $y;
$ny = $y;
if ($pm <= 0) {
    $pm = 12;
    $py = $y - 1;
}
if ($nm > 12) {
    $nm = 1;
    $ny = $y + 1;
}

$akt = mktime(0, 0, 0, $m, 1, $y); # aktuelle timestamp
$aka = date('Y-m-d', $akt);
$ake = date('Y-m-d', mktime(0, 0, 0, $m, date('t', $akt), $y));
$jakt = mktime(0, 0, 0, 1, 1, $y); # atkueller jahr timestamp
$jaka = date('Y-m-d', $jakt);
$jake = date('Y-m-d', mktime(0, 0, 0, 12, date('t', mktime(0, 0, 0, 12, 1, $y)), $y));

$kontodaten = db_result(db_query("SELECT t1 FROM prefix_allg WHERE k = 'kasse_kontodaten'"), 0);
$kontodaten = unescape($kontodaten);
$kontodaten = bbcode($kontodaten);

$tpl = new tpl ('kasse.htm');

$tpl->set('kontodaten', $kontodaten);

$tpl->set('minus', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE betrag < 0"), 0));
$tpl->set('plus', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE betrag > 0"), 0));
$tpl->set('saldo', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse"), 0));

$tpl->set('Jminus', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE betrag < 0 AND datum >= '" . $jaka . "' AND datum <= '" . $jake . "'"), 0));
$tpl->set('Jplus', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE betrag > 0 AND datum >= '" . $jaka . "' AND datum <= '" . $jake . "'"), 0));
$tpl->set('Jsaldo', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE datum >= '" . $jaka . "' AND datum <= '" . $jake . "'"), 0));

$tpl->set('Mminus', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE betrag < 0 AND datum >= '" . $aka . "' AND datum <= '" . $ake . "'"), 0));
$tpl->set('Mplus', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE betrag > 0 AND datum >= '" . $aka . "' AND datum <= '" . $ake . "'"), 0));
$tpl->set('Msaldo', db_result(db_query("SELECT ROUND(SUM(betrag),2) FROM prefix_kasse WHERE datum >= '" . $aka . "' AND datum <= '" . $ake . "'"), 0));

$tpl->set('month', $lang[date('F', $akt)]);
$tpl->set('pm', $pm);
$tpl->set('nm', $nm);
$tpl->set('py', $py);
$tpl->set('ny', $ny);
$tpl->set('jahr', $y);

$tpl->out(0);

$class = '';
$erg = db_query("SELECT name, verwendung, id, ROUND(betrag,2) as betrag FROM prefix_kasse WHERE datum >= '" . $aka . "' AND datum <= '" . $ake . "' ORDER BY datum DESC");
while ($r = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $r['class'] = $class;
    if (has_right(- 8, 'kasse')) {
        $r['verwendung'] .= '<span style="float: right;">
    <a href="admin.php?kasse-' . $r['id'] . '"><img src="include/images/icons/edit.gif" border="0" title="' . $lang['change'] . '" alt="' . $lang['change'] . '" /></a>
    <a href="index.php?kasse-d' . $r['id'] . '"><img src="include/images/icons/del.gif" border="0" title="' . $lang['delete'] . '" alt="' . $lang['delete'] . '" /></a>
    </span>';
    }
    $tpl->set_ar_out($r, 1);
}
$tpl->out(2);
$design->footer();

?>