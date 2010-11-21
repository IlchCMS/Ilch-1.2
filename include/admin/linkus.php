<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: LinkUs', '', 2);
$tpl = new tpl('linkus', 1);
$design->header();
$menu_1 = escape($menu->get(1), 'string');
$menu_2 = escape($menu->get(2), 'integer');
$updir = 'include/images/linkus/';
$topbanner = '';
$bannerlist = '';
// dateiupload
if (isset($_POST['setnewbanner'])) {
    // Escapes
    $upload_name = escape($_POST['setbannername'], 'string');
    $upload_link = escape($_POST['setlink'], 'string');

    $error_msg = '';
    // prüfen ob alle Felder ausgefüllt
    if ($_FILES['bannerfield']['error'] == 4 or
        empty($upload_name) or
            $upload_link == 'http://') {
        $error_msg .= 'Bitte alle Felder ausf&uuml;llen <br />';
    }
    // prüfen ob Upload fehler ausspuckte
    if ($_FILES['bannerfield']['error'] > 0) {
        $error_msg .= 'unbekannter Uploadfehler (' . $_FILES['bannerfield']['error'] . ')<br />';
    } else
        // Dateiformate prüfen
        if ($_FILES['bannerfield']['type'] == 'image/png' or
            $_FILES['bannerfield']['type'] == 'image/gif' or
            $_FILES['bannerfield']['type'] == 'image/jpeg') {
        } else {
            $error_msg .= 'Nur png, gif und jpg erlaubt <br />';
        }
        if (!is_writeable($updir)) {
            $error_msg .= 'Uploadordner "' . $updir . '" nicht beschreibbar';
        }
        // Fehler ausgeben, falls vorhanden
        echo $error_msg;
        // alles ok ? dann gehts los :)
        if ($error_msg == '') {
            // Dateiname bereits vorhanden ?
            if (is_readable($updir . $_FILES['bannerfield']['name'])) {
                $bannername = rand(0001, 9999) . '_' . substr($_FILES['bannerfield']['name'], - 20);
                echo 'Bannername bereits existent... benenne um...<br/>';
            } else {
                $bannername = $_FILES['bannerfield']['name'];
            }
            // move
            $move = move_uploaded_file($_FILES['bannerfield']['tmp_name'] , $updir . $bannername);
            if ($move) {
                echo '<strong>Banner erfolgreich hochgeladen</strong>';
                // breite und höhe des bildes
                $dimension = @getimagesize($updir . $bannername);
                $bildbreite = $dimension['0'];
                $bildhohe = $dimension['1'];
                // alles in die Datenbank schreiben
                $insert_banner = db_query("INSERT INTO `prefix_linkus`
							(
								name, datei, hoch, breit, link, views, klicks
							)
								VALUES
							(
								'" . $upload_name . "', '" . $bannername . "', " . $bildbreite . ", " . $bildhohe . ", '" . $upload_link . "', 0, 0
							)");
                if ($insert_banner === false) {
                    echo 'Fehler beim speichern in die Datenbank';
                }
            } else {
                echo 'unbekannter Fehler beim verschieben der Datei';
            }
        }
    }
    // Banner Update
    if (isset($_POST['seteditbanner'])) {
        // escapes
        $edit_id = escape($_POST['hiddeneditid'], 'integer');
        $edit_name = escape($_POST['editbannername'], 'string');
        $edit_link = escape($_POST['editlink'], 'string');

        $get_edit_qry = db_query("SELECT id,name,link,datei FROM `prefix_linkus` WHERE id = " . $edit_id . "");
        $edit_row = db_fetch_assoc($get_edit_qry);

        if ($_FILES['editbannerfield']['error'] == 4 and
            $edit_name == $edit_row['name'] and
            $edit_link == $edit_row['link']) {
            wd('admin.php?linkus', 'Keine &Auml;nderungen vorgenommen', 3);
            $design->footer(1);
        } else
        if ($_FILES['editbannerfield']['error'] == 0) {
            // alten Banner löschen
            @unlink($updir . $row['datei']);
            // Dateiname bereits vorhanden ?
            if (is_readable($updir . $_FILES['editbannerfield']['name'])) {
                $bannername = rand(0001, 9999) . '_' . substr($_FILES['editbannerfield']['name'], - 20);
            } else {
                $bannername = $_FILES['editbannerfield']['name'];
            }
            // move
            move_uploaded_file($_FILES['editbannerfield']['tmp_name'] , $updir . $bannername);
            db_query("UPDATE `prefix_linkus` SET datei = '" . $bannername . "' WHERE id = " . $edit_id . "");
        }
        if ($edit_name != $edit_row['name']) {
            db_query("UPDATE `prefix_linkus` SET name = '" . $edit_name . "' WHERE id = " . $edit_id . "");
        }
        if ($edit_link != $edit_row['link']) {
            db_query("UPDATE `prefix_linkus` SET link = '" . $edit_link . "' WHERE id = " . $edit_id . "");
        }
        wd('admin.php?linkus', 'Banner ge&auml;ndert', 3);
        $design->footer(1);
    }
    // Seitenaufbau
    switch ($menu_1) {
        case 'del':
            // prüfen ob id des banners ok und existent
            if ($menu_2 == 0) {
                wd('admin.php?linkus', 'Banner-ID muss &uuml;bertragen werden', 3);
                $design->footer(1);
            } else
                $idcount = db_count_query("SELECT COUNT(id) FROM `prefix_linkus` WHERE id = " . $menu_2 . "");
            if ($idcount == 0 or $idcount === false) {
                wd('admin.php?linkus', 'Dieser Banner existiert nicht', 3);
                $design->footer(1);
            } else {
                $delname = db_result(db_query("SELECT datei FROM `prefix_linkus` WHERE  id = " . $menu_2 . ""));
                $delqry = db_query("DELETE FROM `prefix_linkus` WHERE id = " . $menu_2 . " ");
                $delbild = @unlink($updir . $delname);
                if ($delqry and $delbild) {
                    wd('admin.php?linkus', 'Banner erfolgreich gel&ouml;scht', 3);
                    $design->footer(1);
                } else
                if ($delbild === false) {
                    wd('admin.php?linkus', 'Fehler beim l&ouml;schen der Bilddatei aus ' . $updir . '', 3);
                    $design->footer(1);
                }
                if ($delqry === false) {
                    wd('admin.php?linkus', 'Fehler beim l&ouml;schen aus der Datenbank', 3);
                    $design->footer(1);
                }
            }
            break;

        default:
            // banner auflisten mit edit/lösch-funktionen
            if (is_readable($updir)) {
                // alle Banner ausgeben die in der DB stehen
                $qry = db_query("SELECT id, name, views, klicks, link FROM `prefix_linkus` ORDER BY id ASC");
                while ($row = db_fetch_assoc($qry)) {
                    $bannerlist .= '<tr>
								<td>' . $row['id'] . '</td>
								  <td><a title="' . $row['name'] . '"><img src="index.php?linkus-view-' . $row['id'] . '-false" /></a></td>
								<td align="right">' . $row['views'] . '</td>
								<td align="right">' . $row['klicks'] . '&nbsp;&nbsp;</td>
								<td><a onClick="editbanner(' . $row['id'] . ',\'' . $row['name'] . '\',\'' . $row['link'] . '\');" style="cursor:pointer;">
								<img src="include/images/icons/edit.png" /></a></td>
								<td><a href="admin.php?linkus-del-' . $row['id'] . '" onClick="return confirm(\'Diesen Banner wirklich l&ouml;schen ? \n Danach ist der Banner nicht mehr erreichbar und alle Statistiken gehen verloren\')">
								<img src="include/images/icons/del.png" /></a></td>
							  </tr>';
                }
            } else {
                echo '<center><strong>FEHLER</strong> : Verzeichnis ist nicht lesbar bzw existiert nicht.
					Bitte wende dich an den Administrator</center>';
            }
            // Top Banner auslesen
            $top_qry = db_query("SELECT id,name,views,klicks FROM `prefix_linkus` WHERE views > 0 ORDER BY klicks");
            while ($top_row = db_fetch_assoc($top_qry)) {
                $topbanner .= '
					<tr>
						<td>' . $top_row['id'] . '&nbsp;</td>
						<td>&nbsp;' . $top_row['name'] . '</td>
						<td>' . $top_row['views'] . '&nbsp;</td>
						<td>&nbsp;' . $top_row['klicks'] . '</td>
					 </tr>';
            }
            break;
    }

    $tpl->set('bannerlist', $bannerlist);
    $tpl->set('topbannerlist', $topbanner);
    $tpl->out(0);
    $design->footer();

    ?>