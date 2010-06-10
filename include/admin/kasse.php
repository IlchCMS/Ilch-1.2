<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Kasse', '', 2);
$design->header();

if (isset($_POST[ 'ksub' ]) AND !empty($_POST[ 'kontodaten' ])) {
    $kontodaten = escape($_POST[ 'kontodaten' ], 'textarea');
    db_query("UPDATE prefix_allg SET t1 = '" . $kontodaten . "' WHERE k = 'kasse_kontodaten'");
} elseif (isset($_POST[ 'sub' ])) {
    $name = escape($_POST[ 'name' ], 'string');
    $verwendung = escape($_POST[ 'verwendung' ], 'string');
    $betrag = str_replace(',', '.', $_POST[ 'betrag' ]);
    $datum = get_datum($_POST[ 'datum' ]);
    if (!is_numeric($betrag)) {
        echo 'der Betrag is keine Nummer?.. !!';
    } elseif (is_numeric($menu->get(1))) {
        if (db_query("UPDATE `prefix_kasse` SET `name` = '" . $name . "', `datum` = '" . $datum . "', `betrag` = '" . $betrag . "', `verwendung` = '" . $verwendung . "' WHERE `id` = " . $menu->get(1)))
            echo 'Buchung wurde ge&auml;ndert ... ';
        else
            echo 'Es ist ein Fehler aufgetreten, Buchung nicht ge&auml;ndert';
        $menu->set_url(1, '');
    } else {
        db_query("INSERT INTO `prefix_kasse` (`datum`,`name`,``verwendung`,`betrag`) VALUES ('" . $datum . "','" . $name . "','" . $verwendung . "'," . $betrag . ")");
        echo 'Buchung wurde gespeichert ... ';
    }
}

$kontodaten = db_result(db_query("SELECT `t1` FROM `prefix_allg` WHERE `k` = 'kasse_kontodaten'"), 0);
$kontodaten = unescape($kontodaten);

if (is_numeric($menu->get(1))) {
    $r = db_fetch_assoc(db_query("SELECT `name`,`betrag`,`verwendung`,DATE_FORMAT(`datum`,'%d.%m.%Y') as `datum` FROM `prefix_kasse` WHERE `id` = " . $menu->get(1)));
    $r[ 'id' ] = '-' . $menu->get(1);
} else {
    $r = array(
        'id' => '',
        'name' => '',
        'betrag' => '',
        'datum' => date('d.m.Y'),
        'verwendung' => ''
        );
}
$tpl = new tpl('kasse', 1);
$r[ 'kontodaten' ] = $kontodaten;
$tpl->set_ar_out($r, 0);

$design->footer();

?>