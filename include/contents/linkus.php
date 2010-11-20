<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: LinkUs';
$hmenu = 'LinkUs';
$design = new design($title, $hmenu);
// Variablen
$dir = 'include/images/linkus/';
$host = 'http://' . $_SERVER['SERVER_NAME'] . '/';
// Menüklasse escapen
$menu_1 = escape($menu->get(1), 'string');
$menu_2 = escape($menu->get(2), 'integer');
$menu_3 = escape($menu->get(3), 'string');
// SeitenFunktionen
switch ($menu_1) {
    case 'view':
        // prüfen ob ID + Datei vorhanden
        $checkid = db_count_query("SELECT COUNT(id) FROM `prefix_linkus` WHERE id = " . $menu_2 . "");
        if ($checkid != 0) {
            // auslesen des banner-namens
            $banner = db_result(db_query("SELECT datei FROM `prefix_linkus` WHERE id = " . $menu_2 . ""));
            // prüfen ob banner lesbar und existent
            if (is_readable($dir . $banner)) {
                if ($menu_3 == 'true') {
                    // views-wert +1
                    db_query("UPDATE `prefix_linkus` SET views = views + 1 WHERE id = " . $menu_2 . "");
                }
                // weiterleiten $_SERVER['host_name']
                Header("Location: " . $dir . $banner . "");
                exit();
            } else {
            }
        } else {
            $design->header();
            echo '<center><strong>FEHLER</strong> : Banner-ID nicht vorhanden</center>';
            $design->footer();
        }
        break;
    // ################################################################
    case 'click':
        // prüfen ob ID + Datei vorhanden
        $checkid = db_count_query("SELECT COUNT(id) FROM `prefix_linkus` WHERE id = " . $menu_2 . "");
        if ($checkid != 0) {
            // Auslesen der Ziel-URL
            $link = db_result(db_query("SELECT link FROM `prefix_linkus` WHERE id = " . $menu_2 . ""));

            if ($menu_3 == 'true') {
                // click-wert +1
                db_query("UPDATE `prefix_linkus` SET klicks = klicks + 1 WHERE id = " . $menu_2 . "");
            }
            // weiterleiten
            Header("Location: " . $link . "");
            exit();
        } else {
            $design->header();
            echo '<center><strong>FEHLER</strong> : Banner-ID nicht vorhanden</center>';
            $design->footer();
        }
        break;
    // ################################################################
    default:
        $tpl = new tpl('linkus');

        $tpl->set('bannerlist', '');
        $design->header();
        $tpl->out(0);

        if (is_readable($dir)) {
            // alle Banner ausgeben die in der DB stehen
            $qry = db_query("SELECT id,name FROM `prefix_linkus` ORDER BY breit ASC");
            while ($row = db_fetch_assoc($qry)) {
                $row['host'] = $host;
                $row['bb-link'] = '[url=' . $host . 'index.php=linkus-click-' . $row['id'] . '-true]
									[img]' . $host . 'index.php?linkus-view-' . $row['id'] . '-true[/img]
								[/url]';
                $row['html-link'] = htmlentities('<a href="' . $host . 'index.php?linkus-click-' . $row['id'] . '-true">
					<img src="' . $host . 'index.php?linkus-view-' . $row['id'] . '-true" alt="' . $row['name'] . '" border="0">
					</a>');

                $tpl->set_ar_out($row, 1);
            }
        } else {
            echo '<center><strong>FEHLER</strong> : Verzeichnis ist nicht lesbar bzw existiert nicht.
						Bitte wende dich an den Administrator</center>';
        }

        $design->footer();
        break;
        // ################################################################
}

?>