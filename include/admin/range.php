<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Ranks', '', 2);
$design->header();

$um = $menu->get(1);
if (empty($um) AND empty($_GET[ 'um' ]) AND empty($_POST[ 'um' ])) {
    $tpl = new tpl('range', 1);
    $tpl->out(0);

    $clas = 'cbg2';
    $erg = db_query('SELECT * FROM `prefix_ranks` ORDER BY `spez` DESC, `min`');
    while ($row = db_fetch_object($erg)) {
        if ($clas == 'Cmite') {
            $clas = 'Cnorm';
        } else {
            $clas = 'Cmite';
        }
        if ($row->spez == 1) {
            $min = '-';
            $spez = 'ja';
        } else {
            $min = $row->min;
            $spez = 'nein';
        }
        $ar = array(
            'ID' => $row->id,
            'SPEZ' => $spez,
            'CLASS' => $clas,
            'BEZ' => $row->bez,
            'MIN' => $min
            );
        $tpl->set_ar_out($ar, 1);
    }
    $tpl->out(2);
} elseif ($um == 1) {
    db_query('DELETE FROM `prefix_ranks` WHERE `id` = "' . $menu->get(2) . '" LIMIT 1');
    db_query('UPDATE `prefix_user` SET `spezrank` = 0 WHERE `spezrank` = "' . $menu->get(2) . '"');
    wd('admin.php?range', 'Erfolgreich gel&ouml;scht', 1);
} elseif ($um == 2) {
    if (empty($_POST[ 'sub' ])) {
        $rid = $menu->get(2);
        if (empty($rid)) {
            $Fsub = 'Eintragen';
            $Fbez = '';
            $Fmin = '';
            $Fjch = '';
            $Fnch = 'checked';
            $Frid = '';
            $Fakt = 'insert';
        } else {
            $abf = 'SELECT * FROM `prefix_ranks` WHERE `id` = "' . $rid . '"';
            $erg = db_query($abf);
            $row = db_fetch_object($erg);
            $Fsub = '&Auml;ndern';
            $Fbez = $row->bez;
            $Fmin = $row->min;
            if ($row->spez == 1) {
                $Fjch = 'checked';
                $Fnch = '';
            } else {
                $Fnch = 'checked';
                $Fjch = '';
            }
            $Frid = $row->id;
            $Fakt = 'change';
        }
        $tpl = new tpl('range', 1);
        $ar = Array(
            'SUB' => $Fsub,
            'BEZ' => $Fbez,
            'MIN' => $Fmin,
            'JCH' => $Fjch,
            'NCH' => $Fnch,
            'RID' => $Frid,
            'AKT' => $Fakt
            );
        $tpl->set_ar_out($ar, 3);
    } else {
        $_POST[ 'bez' ] = escape($_POST[ 'bez' ], 'string');
        $_POST[ 'min' ] = escape($_POST[ 'min' ], 'integer');
        $_POST[ 'spez' ] = escape($_POST[ 'spez' ], 'integer');
        $_POST[ 'rid' ] = escape($_POST[ 'rid' ], 'integer');
        if (empty($_POST[ 'rid' ])) {
            if ($_POST[ 'spez' ] == 1) {
                $_POST[ 'min' ] = '0';
            }
            db_query('INSERT INTO `prefix_ranks` (`bez`,`min`,`spez`) VALUES ( "' . $_POST[ 'bez' ] . '","' . $_POST[ 'min' ] . '","' . $_POST[ 'spez' ] . '" ) ');
            wd('admin.php?range', 'Erfolgreich eingetragen', 1);
        } else {
            if ($_POST[ 'spez' ] == 1) {
                $_POST[ 'min' ] = '0';
            }
            db_query('UPDATE `prefix_ranks` SET `bez` = "' . $_POST[ 'bez' ] . '", `min` = "' . $_POST[ 'min' ] . '", `spez` = "' . $_POST[ 'spez' ] . '" WHERE `id` = "' . $_POST[ 'rid' ] . '"');
            wd('admin.php?range', 'Erfolgreich ge&auml;ndert', 1);
        }
    }
}

$design->footer();

?>