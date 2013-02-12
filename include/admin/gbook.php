<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2013 ilch.de
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Gästebuch', '', 2);
$tpl = new tpl('gbook', 1);
$design->header();

$um = $menu->get(1);
switch ($um) {

    default:
    case 'edit':
        $id = escape($menu->get(2), 'integer');
        $tpl->set('id', $id);
        // Limit Liste
        $limit = 20;
        $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
        $MPL = db_make_sites($page, '', $limit, "admin.php?gbook", 'gbook');
        $anfang = ($page - 1) * $limit;
        // Formular absenden	    
        if (isset($_POST['sub']) and chk_antispam('adminuser_action', true)) {
            $name = escape($_POST['name'], 'string');
            $mail = escape($_POST['mail'], 'string');
            $page = escape($_POST['page'], 'string');
            $text = escape($_POST['txt'], 'string');
            if ($id == 0) {
                db_query("INSERT INTO `prefix_gbook` (`name`, `mail`, `page`, `txt`, `time`, `show`) 
                VALUES ('" . $name . "','" . $mail . "','" . $page . "','" . $text . "', '" . time() . "', '" . $allgAr['gbook_show'] . "')");
            } else {
                db_query("UPDATE `prefix_gbook` SET `name` = '" . $name . "', `mail` = '" . $mail . "', `page` = '" . $page . "', `txt` = '" . $text . "' WHERE `id` = " . $id);
            }
            wd('admin.php?gbook', 'Eintrag erfolgreich.', 2);
            break;
        }
        // Start
        $tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
        $tpl->out(0);
        // Formular leer
        if ($id == 0) {
            $r = array('name' => '', 'mail' => '', 'page' => '', 'text' => '', 'id' => '0', 'sub' => 'Absenden');
            // Formular bearbeiten
        } else {
            $r = db_fetch_assoc(db_query("SELECT `id`, `name`, `mail`, `page`, `txt` as `text` FROM `prefix_gbook` WHERE `id` = " . $id));
        }
        // Vorschau des Formulars anzeigen
        if (isset($_POST['preview'])) {
            $r['text'] = escape($_POST['txt'], 'string');
            $r['name'] = escape($_POST['name'], 'string');
            $r['mail'] = escape($_POST['mail'], 'string');
            $r['page'] = escape($_POST['page'], 'string');
            $r['sub'] = 'Ändern';
            $tpl->set('TEXT', bbcode($r['text']));
            $tpl->out('preview');
        }
        // Vorschau eines Eintrages anzeigen
        if (isset($_POST['preview1'])) {
            $r = db_fetch_assoc(db_query("SELECT `id`, `name`, `mail`, `page`, `txt` as `text` FROM `prefix_gbook` WHERE `id` = " . $id));
            $r['sub'] = 'Ändern';
            $tpl->set('TEXT', bbcode($r['text']));
            $tpl->out('preview');
        }
        // Formular fuellen
        $tpl->set_ar_out($r, 'insert_start');
        $tpl->out('insert_end');
        // Liste aller Eintraege
        $class = '';
        $erg = db_query("SELECT `name`, `mail`, `txt`, `id`, `show`, `ip`, `time` FROM `prefix_gbook` ORDER BY `time` DESC LIMIT " . $anfang . "," . $limit);
        while ($row = db_fetch_assoc($erg)) {
            $row['class'] = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $row['text'] = substr(preg_replace("/\015\012|\015|\012/", " ", stripslashes($row['txt'])), 0, 30) . '...';
            $row['showimg'] = ($row['show'] == 1) ? 'jep.gif' : 'nop.gif';
            $row['showtxt'] = ($row['show'] == 1) ? 'aktiv' : 'inaktiv';
            $row['datum'] = date('d.m.Y - H:i', $row['time']);
            $row['MPL'] = $MPL;
            $tpl->set_ar_out($row, 'liste_start');
        }
        $tpl->out('liste_end');
        break;

    case 'del':
        // Eintrag loeschen
        $id = escape($menu->get(2), 'integer');
        db_query("DELETE FROM `prefix_gbook` WHERE `id` = " . $id . " LIMIT 1");
        db_query("DELETE FROM `prefix_koms` WHERE `uid` = " . $id . " AND `cat` = 'GBOOK'");
        wd('admin.php?gbook', 'Erfolgreich gelöscht.', 2);
        break;

    case 'show':
        // aktiv / inaktiv schalten
        $id = escape($menu->get(2), 'integer');
        $show = (db_result(db_query("SELECT `show` FROM `prefix_gbook` WHERE `id` = " . $id)) == 1) ? '0' : '1';
        db_query("UPDATE `prefix_gbook` SET `show` = '" . $show . "' WHERE `id` = " . $id);
        wd('admin.php?gbook', 'Erfolgreich bearbeitet.', 2);
        break;
}

$design->footer();
