<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Loader-Datenbank', '- Loader-Datenbank', 2);
$design->header();
// Funktionen
// // Alle Tasks auffuehren
function getTasks($akt = '') {
    $tasks = '';
    $erg = db_query("SELECT DISTINCT `task` FROM `prefix_loader` ORDER BY `task` ASC");
    while ($row = db_fetch_object($erg)) {
        if (trim($row->task) == trim($akt)) {
            $sel = ' selected="select"';
        } else {
            $sel = '';
        }
        $tasks .= '<option value="' . $row->task . '"' . $sel . ' onclick="toggle(\'neu\', \'verbergen\')">' . $row->task . '</option>';
    }

    return ($tasks);
}

$aid = $menu->get(2);
switch ($aid) {
    default:
        // Modul aendern oder hinzufuegen
        if (isset($_POST[ 'submit' ]) and chk_antispam('adminuser_action', true)) {
            // POST-daten escapen
            $lid = $menu->get(3);
            $task = escape($_POST[ 'task' ], 'string');
            $newtask = escape($_POST[ 'newtask' ], 'string');
            $file = escape($_POST[ 'file' ], 'string');
            $description = escape($_POST[ 'description' ], 'textarea');
            // Wenn neuer Task
            if (!empty($newtask) AND $task == 'neu') {
                $task = $newtask;
            }
            // Wenn neue Task, aber neuer Name fehlt
            if (!empty($newkat) AND $task == 'neu') {
                $task = '';
            }
            // Query schreiben
            if (empty($lid)) {
                $pos = db_result(db_query('SELECT COUNT(*) FROM `prefix_modules`'), 0, 0);
                $sql = 'INSERT INTO `prefix_loader` (`pos`, `task`, `file`, `description`) VALUES ("' . $pos . '", "' . $task . '", "' . $file . '", "' . $description . '")';
            } else {
                $sql = 'UPDATE `prefix_loader` SET `task` = "' . $task . '", `file` = "' . $file . '", `description` = "' . $description . '" WHERE `id` = ' . $lid;
            }
            // Aenderungen speichern
            db_query($sql);
            // Weiterleitung und Footer
            wd('admin.php?modules-loader', 'Eintrag gespeichert');
            $design->footer(1);
        }
        // Tasks verschieben
        if ($menu->getA(2) == 'U' OR $menu->getA(2) == 'O') {
            $pos = $menu->getE(2);
            $nps = ($menu->getA(2) == 'U' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_loader`"), 0);

            if ($nps < $anz AND $pos >= 0) {
                db_query("UPDATE `prefix_loader` SET `pos` = -1 WHERE `pos` = " . $pos);
                db_query("UPDATE `prefix_loader` SET `pos` = " . $pos . " WHERE `pos` = " . $nps);
                db_query("UPDATE `prefix_loader` SET `pos` = " . $nps . " WHERE `pos` = -1");
            }
        }
        // Class
        $class = 'Cmite';
        // Template laden
        $tpl = new tpl('modules/loader', 1);
        // Template-Header
        $tpl->out(0);
        // Module abfragen und Ausgeben
        $erg = db_query("SELECT `id`, `pos`, `task`, `file`, `description` FROM `prefix_loader` ORDER BY `pos` ASC");

        if (db_num_rows($erg) > 0) {
            $tpl->out(3);
            while ($row = db_fetch_assoc($erg)) {
                $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
                $row[ 'class' ] = $class;
                $tpl->set_ar_out($row, 4);
            }
        }
        // Tabellenuebergang
        $tpl->out(1);
        // Aendern oder Einfuegen
        if ($aid == 'edit') {
            $lid = $menu->get(3);
            $erg = db_query('SELECT `task`, `file`, `description` FROM `prefix_loader` WHERE `id` = ' . $lid);
            $row = db_fetch_assoc($erg);
            $task = getTasks($row[ 'task' ]);
            $tpl->set_ar_out(Array(
                    'aname' => 'Eintrag bearbeiten',
                    'task' => $task,
                    'file' => $row[ 'file' ],
                    'description' => $row[ 'description' ],
					'ANTISPAM' => get_antispam('adminuser_action', 0, true)
                    ), 5);
        } else {
            $task = getTasks('');
            $tpl->set_ar_out(Array(
                    'aname' => 'Eintrag hinzuf&uuml;gen',
                    'task' => $task,
                    'file' => '',
                    'description' => '',
					'ANTISPAM' => get_antispam('adminuser_action', 0, true)
                    ), 5);
        }
        // Template-Footer
        $tpl->out(2);

        break;

    case 'del':
        // Betroffene Task-ID
        $lid = $menu->get(3);
        // Wert entfernen
        db_query('DELETE FROM `prefix_loader` WHERE `id` = ' . $lid);

        wd('admin.php?modules-loader', 'Eintrag gel&ouml;scht');
        $design->footer(1);
        break;
}

$design->footer();

?>