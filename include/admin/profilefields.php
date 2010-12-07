<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
// ajax-nachladen der optionen für das profilfeld
if ($menu->get(1) == "loadtype") {
    if ($menu->get(2) == "new") {
        // echo "<td class='Cmite'>hallo</td><td class='Cnorm'></td>";
        $func = escape($menu->get(3), "integer");
        ProfilefieldRegistry::renderAdmin(array("func" => $func));
    } else if ($menu->get(2) == "edit") {
        $func = escape($menu->get(4), "integer");
        $id = escape($menu->get(3), "integer");
        $ar = ProfilefieldRegistry::get($func, $id);
        ProfilefieldRegistry::renderAdmin($ar);
    }
    // damit der rest nicht mehr ausgegeben wird
    die();
}

$design = new design('Ilch Admin-Control-Panel :: Profilfelder', '', 2);
$design->header();

$_POST[ 'show' ] = escape($_POST[ 'show' ], 'string');
$_POST[ 'func' ] = escape($_POST[ 'func' ], 'integer');
$_POST[ 'sid' ] = escape($_POST[ 'sid' ], 'integer');

$show = true;
if (isset($_POST[ 'sub' ])) {
    if (empty($_POST[ 'sid' ])) {
        ProfilefieldRegistry::insert($_POST);
    } else {
        db_query("UPDATE `prefix_profilefields` SET `show` = '" . $_POST[ 'show' ] . "', `func` = " . $_POST[ 'func' ] . "  WHERE `id` = " . $_POST[ 'sid' ]);
    }
}

if ($menu->get(1) == 'delete') {
    $id = $menu->get(2);
    // TODO func in diesem query anpassen
    $anz = db_count_query("SELECT COUNT(`id`) FROM `prefix_profilefields` WHERE `id` = " . $id . " AND `func` != 3 AND `func` != 2");
    if ($anz == 1) {
        $pos = db_result(db_query("SELECT `pos` FROM `prefix_profilefields` WHERE `id` = " . $id), 0);
        db_query("DELETE FROM `prefix_profilefields` WHERE `id` = " . $id);
        db_query("UPDATE `prefix_profilefields` SET `pos` = `pos` - 1 WHERE `pos` > " . $pos);
        db_query("DELETE FROM `prefix_userfields` WHERE `fid` = " . $id);
    }
}

if ($menu->get(1) == 'u' OR $menu->get(1) == 'o') {
    $a = db_count_query("SELECT COUNT(*) as `anz` FROM `prefix_profilefields`");
    $np = ($menu->get(1) == 'o' ? $menu->get(3) - 1 : $menu->get(3) + 1);
    $np = ($np >= ($a - 1) ? ($a - 1) : $np);
    $np = ($np < 0 ? 0 : $np);
    db_query("UPDATE `prefix_profilefields` SET `pos` = " . $menu->get(3) . " WHERE `pos` = " . $np);
    db_query("UPDATE `prefix_profilefields` SET `pos` = " . $np . " WHERE `id` = " . $menu->get(2));
}

if ($menu->get(1) == 'c') {
    $n = ($menu->get(3) == 3 ? 4 : 3);
    db_query("UPDATE `prefix_profilefields` SET `func` = " . $n . " WHERE `id` = " . $menu->get(2));
}

if ($show) {
    $tpl = new tpl('profilefields', 1);
    if ($menu->get(1) != 'edit') {
        $row = array(
            'sub' => 'Eintragen',
            'pos' => '',
            'show' => '',
            'func' => arliste('', profilefields_functions2(), $tpl, 'func'),
            'sid' => ''
            );
    } else {
        $sid = $menu->get(2);
        $abf = 'SELECT `show`,`func`,`id` as `sid` FROM `prefix_profilefields` WHERE `id` = "' . $sid . '"';
        $erg = db_query($abf);
        $row = db_fetch_assoc($erg);
        $row[ 'func' ] = arliste($row[ 'func' ], profilefields_functions2(), $tpl, 'func');
        $row[ 'sub' ] = '&Auml;ndern';
    }

    $tpl->set_ar_out($row, 0);
    $class = 'Cnorm';
    $ar = profilefields_functions();
    $erg = db_query('SELECT * FROM `prefix_profilefields` ORDER BY `pos`');
    while ($r = db_fetch_assoc($erg)) {
        $class = ($class == 'Cnorm' ? 'Cmite' : 'Cnorm');
        $class = ($r[ 'func' ] == 2 ? 'Cdark' : $class);
        echo '<tr class="' . $class . '"><td>' . $r[ 'show' ] . '</td>';
        echo '<td align="center">' . $ar[ $r[ 'func' ] ] . '</td>';
        if ($r[ 'func' ] != 3) { // func == 3 haben die vordefinierten
            echo '<td><a href="?profilefields-edit-' . $r[ 'id' ] . '">&auml;ndern</a></td>';
            echo '<td><a href="javascript:delcheck(' . $r[ 'id' ] . ')">l&ouml;schen</a></td>';
        } else {
            echo '<td colspan="2"><a href="?profilefields-c-' . $r[ 'id' ] . '-' . $r[ 'func' ] . '">' . ($r[ 'func' ] == 3 ? 'verstecken' : 'anzeigen') . '</a></td>';
        }
        echo '<td><a href="?profilefields-o-' . $r[ 'id' ] . '-' . $r[ 'pos' ] . '"><img src="include/images/icons/pfeilo.png" border="0"></a></td>';
        echo '<td><a href="?profilefields-u-' . $r[ 'id' ] . '-' . $r[ 'pos' ] . '"><img src="include/images/icons/pfeilu.png" border="0"></a></td>';
        echo '</tr>';
    }
    $tpl->out(2);
}

$design->footer();

?>