<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Admin-Navigation', '- Admin-Navigation', 2);
$design->header();
// Funktionen
// // Alle Kategorien auffuehren
function getKats($akt = '') {
    $kats = '';
    $erg = db_query("SELECT DISTINCT `menu` FROM `prefix_modules` WHERE `menu` != '' ORDER BY `menu` ASC");
    while ($row = db_fetch_object($erg)) {
        if (trim($row->menu) == trim($akt)) {
            $sel = ' selected="select"';
        } else {
            $sel = '';
        }
        $kats .= '<option value="' . $row->menu . '"' . $sel . ' onclick="toggle(\'neu\', \'verbergen\')">' . $row->menu . '</option>';
    }

    return ($kats);
}

$aid = $menu->get(2);
switch ($aid) {
    default:
        // Modul aendern
        if (isset($_POST[ 'submit' ])) {
            // POST-daten escapen
            $mid = escape($_POST[ 'modul' ], 'integer');
            $kat = escape($_POST[ 'kat' ], 'string');
            $newkat = escape($_POST[ 'newkat' ], 'string');
            $pos = escape($_POST[ 'pos' ], 'integer');
            // Alte Kat abfragen
            $menu = @db_result(db_query('SELECT `menu` FROM `prefix_modules` WHERE `id` = ' . $mid . ' LIMIT 0, 1'), 0);
            // Wenn neue Kategorie
            if (!empty($newkat) AND $kat == 'neu') {
                $kat = $newkat;
            }
            // Wenn neue Kategorie, aber neuer Name fehlt
            if (!empty($newkat) AND $kat == 'neu') {
                $kat = 'Unbenannt';
            }
            // Position errechnen und Positionen der alten Kat neu speichern
            if (empty($pos) OR $kat != $menu) {
                if (!empty($pos) AND !empty($menu)) {
                    db_query('UPDATE `prefix_modules` SET `pos` = `pos` -1 WHERE `menu` = "' . $menu . '" AND `pos` > ' . $pos);
                }
                $pos = db_result(db_query('SELECT COUNT(*) FROM `prefix_modules` WHERE `menu` = "' . $kat . '"'), 0, 0);
            }
            // Aenderungen speichern
            db_query('UPDATE `prefix_modules` SET `menu` = "' . $kat . '", `pos` = ' . $pos . ' WHERE `id` = ' . $mid);
            // Weiterleitung und Footer
            wd('admin.php?modules-adminmenu', 'Eintrag gespeichert');
            $design->footer(1);
        }
        // Module verschieben
        if ($menu->getA(2) == 'U' OR $menu->getA(2) == 'O') {
            $pos = $menu->get(3);
            $id = $menu->getE(2);
            $cat = db_result(db_query("SELECT `menu` FROM `prefix_modules` WHERE `id` = " . $id), 0);
            $nps = ($menu->getA(2) == 'U' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_modules` WHERE `menu` = '" . $cat . "'"), 0);

            if ($nps < 0) {
                db_query("UPDATE `prefix_modules` SET `pos` = " . $anz . " WHERE `id` = " . $id);
                db_query("UPDATE `prefix_modules` SET `pos` = `pos` -1 WHERE `menu` = '" . $cat . "'");
            }

            if ($nps >= $anz) {
                db_query("UPDATE `prefix_modules` SET `pos` = -1 WHERE `id` = " . $id);
                db_query("UPDATE `prefix_modules` SET `pos` = `pos` +1 WHERE `menu` = '" . $cat . "'");
            }

            if ($nps < $anz AND $nps >= 0) {
                db_query("UPDATE `prefix_modules` SET `pos` = " . $pos . " WHERE `pos` = " . $nps . " AND `menu` = '" . $cat . "'");
                db_query("UPDATE `prefix_modules` SET `pos` = " . $nps . " WHERE `id` = " . $id);
            }
        }
        // Template laden
        $tpl = new tpl('modules/adminmenu', 1);
        // Template-Header
        $tpl->out(0);
        // Module abfragen und Ausgeben
        $erg = db_query("SELECT * FROM `prefix_modules` WHERE `menu` != '' ORDER BY `menu`, `pos` ASC");

        $katname = '';

        while ($row = db_fetch_assoc($erg)) {
            if ($katname != $row[ 'menu' ]) {
                $class = 'Cmite';

                $tpl->set_ar_out(Array(
                        'kat' => $row[ 'menu' ],
                        'url' => $row[ 'url' ]
                        ), 3);
                $katname = $row[ 'menu' ];
            }
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $tpl->set_ar_out(Array(
                    'class' => $class,
                    'id' => $row[ 'id' ],
                    'name' => $row[ 'name' ],
                    'url' => $row[ 'url' ],
                    'pos' => $row[ 'pos' ]
                    ), 4);
        }
        // Tabellenuebergang
        $tpl->out(1);
        // Aendern oder Einfuegen
        if ($aid == 'edit') {
            $mid = $menu->get(3);
            $erg = db_query('SELECT `pos`, `menu` FROM `prefix_modules` WHERE `id` = ' . $mid);
            $row = db_fetch_assoc($erg);
            $kat = getKats($row[ 'menu' ]);
            $modul = dblistee($mid, 'SELECT `id`, `name` FROM `prefix_modules` WHERE (`menu` = "" AND (`gshow` = 1 OR `ashow` = 1)) OR `id` = ' . $mid . ' ORDER BY `name` ASC');
            $tpl->set_ar_out(Array(
                    'aname' => 'Eintrag bearbeiten',
                    'modul' => $modul,
                    'kat' => $kat,
                    'pos' => $row[ 'pos' ]
                    ), 5);
        } else {
            $kat = getKats();
            $modul = dblistee('', 'SELECT `id`, `name` FROM `prefix_modules` WHERE `menu` = "" AND (`gshow` = 1 OR `ashow` = 1) ORDER BY `name` ASC');
            $tpl->set_ar_out(Array(
                    'aname' => 'Eintrag hinzuf&uuml;gen',
                    'modul' => $modul,
                    'kat' => $kat,
                    'pos' => $row[ 'pos' ]
                    ), 5);
        }
        // Template-Footer
        $tpl->out(2);

        break;

    case 'del':
        // Betroffene Modul-ID
        $mid = $menu->get(3);
        // Aktuelle Position und Menus abfragen
        $erg = db_query('SELECT `pos`, `menu` FROM `prefix_modules` WHERE `id` = ' . $mid . ' LIMIT 0, 1');
        $row = db_fetch_assoc($erg);
        // Postitionen des alten Menues neu speichern und Modul-Kat zuruecksetzen
        db_query('UPDATE `prefix_modules` SET `pos` = `pos` -1 WHERE `menu` = "' . $row[ 'menu' ] . '" AND `pos` > ' . $row[ 'pos' ]);
        db_query('UPDATE `prefix_modules` SET `menu` = "", `pos` = NULL WHERE `id` = ' . $mid);

        wd('admin.php?modules-adminmenu', 'Eintrag gel&ouml;scht');
        $design->footer(1);
        break;
}

$design->footer();

?>