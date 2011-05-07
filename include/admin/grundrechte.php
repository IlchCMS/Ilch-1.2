<?php
/**
 *
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

if (!is_admin()) {
    $design = new design('Ilch Admin-Control-Panel :: Grundrechte', '', 2);
    $design->header();
    echo 'Dieser Bereich ist nicht fuer dich...';
    $design->footer(1);
}

require_once 'include/includes/class/iSmarty.php';

//Modulrechte für die Grundrechte (Popup)
if ($menu->get(1) == 'cmr') {
    $id = (int)$menu->get(2) * -1;

    $data = allRowsFromQuery('SELECT m.*, (ISNULL(mr.mid)+1)%2 AS hasright FROM `prefix_modules` m
    LEFT JOIN `prefix_modulerights` mr ON m.id = mr.mid AND mr.uid = ' . $id . '
    WHERE m.fright = 1
    ORDER BY hasright DESC, m.name', 'id');

    $design = new design('', '', 0);
    $design->header();

    $smarty = new iSmarty();
    $smarty->assign('site', $menu->get(0));
    $smarty->assign('id', abs($id));
    $smarty->assign('name', db_result(db_query('SELECT `name` FROM `prefix_grundrechte` WHERE `id` = ' . $id)));

    if (isset($_POST['subCMR'])) {
        if (isset($_POST['mid']) and is_array($_POST['mid'])) {
            //Änderungen vornehmen
            foreach ($_POST['mid'] as $mid) {
                if ($data[$mid]['hasright'] == 1) {
                    continue; //Recht schon gesetzt
                } else {
                    //Recht setzen
                    db_query('INSERT INTO `prefix_modulerights` (`uid`, `mid`) VALUE ('.$id.','.$mid.')');
                    db_query('DELETE FROM `prefix_modulerights` WHERE `mid` = ' . $mid . ' AND `uid` IN
                (SELECT `id` FROM `prefix_user` WHERE `recht` = '.$id.')');
                }
            }
        }
        //Prüfe auf gelöschte Rechte
        foreach ($data as $row){
            if ($row['hasright'] == 1) {
                if (!isset($_POST['mid']) or !in_array($row['id'], $_POST['mid'])) {
                    //Recht entfernen
                    db_query('DELETE FROM `prefix_modulerights` WHERE `mid` = ' . $row['id'] . ' AND `uid` = ' . $id);
                    db_query('DELETE FROM `prefix_modulerights` WHERE `mid` = ' . $row['id'] . ' AND `uid` IN
                (SELECT `id` FROM `prefix_user` WHERE `recht` = '.$id.')');
                }
            } else {
                break;
            }
        }
        //Neu auslesen
        $data = allRowsFromQuery('SELECT m.*, (ISNULL(mr.mid)+1)%2 AS hasright FROM `prefix_modules` m
        LEFT JOIN `prefix_modulerights` mr ON m.id = mr.mid AND mr.uid = ' . $id . '
        WHERE m.fright = 1
        ORDER BY hasright DESC, m.name', 'id');
        $smarty->assign('info', 'Änderungen wurden gespeichert.');
    }

    $smarty->assign('data', $data);

    $smarty->display('modulrechte.tpl');
    $design->footer(1);
    exit;
}

$design = new design('Ilch Admin-Control-Panel :: Grundrechte', '', 2);
$design->header();

$arb = array(-9 => 'Dieser User hat alle Rechte :-)',
             -8 => 'Dieser User darf alles mit einer paar Ausnahmen: er darf User &uuml;ber ihm nicht l&ouml;schen, '.
                   'diesen Bereich nicht &auml;ndern, kein Backup machen, die Konfiguration nicht ver&auml;ndern.',
             -7 => 'Der User darf alles auf der Seite administrieren. Also z.B. alle Foren Moderieren in die er rein kommt, '.
                   'Kommentare l&ouml;schen, Userbilder verwalten, War zu oder Absagen l&ouml;schen... '.
                   'Im Adminbereich hat er allerdings nur &uuml;ber Modulrechte etwas zu sagen.',
             -6 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
             -5 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
             -4 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
             -3 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
             -2 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
             -1 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
              0 => 'Dieses Recht bekommen alle G&auml;ste, also Besucher die nicht registriert sind'
            );

//Änderung von Grundrechten
if (isset($_POST['subchange'])) {
    $erg = db_query("SELECT * FROM `prefix_grundrechte` ORDER BY `id` ASC");
    while ($r = db_fetch_assoc($erg)) {
        if ($r[ 'name' ] != $_POST[ 'gr' ][ $r[ 'id' ] ]) {
            db_query("UPDATE `prefix_grundrechte` SET `name` = '" . escape($_POST[ 'gr' ][ $r[ 'id' ] ], 'string') . "' WHERE `id` = " . $r[ 'id' ]);
        }
    }
    echo 'Die &Auml;nderungen wurden gespeichert<br /><br />';
}

//Templateausgabe
$smarty = new iSmarty();
$smarty->assign('grundrechte', allRowsFromQuery('SELECT * FROM `prefix_grundrechte` ORDER BY `id` ASC'));
$smarty->assign('descs', $arb);
$smarty->display('grundrechte.tpl');

$design->footer();
?>