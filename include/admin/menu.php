<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Navigation', '', 2);
$design->header();
// function show menu ( 1 == links, 2 == rechts )
function show_menu($wo) {
    $erg = db_query("SELECT * FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos`");
    $x = 0;
    $class = '';
    echo '<table class="border" cellpadding="3" cellspacing="1" border="0">';
    while ($row = db_fetch_assoc($erg)) {
        $subhauptx = $row[ 'was' ];
        $whileMenP = ($subhauptx >= 7 ? true : false);
        $class = ($class == 'Cdark' ? 'Cmite' : 'Cdark');

        echo '<tr class="' . $class . '"><td>' . $row[ 'pos' ] . '</td><td>' . ($whileMenP ? '' : '<b>') . ($whileMenP ? str_repeat('-&nbsp;', $row[ 'ebene' ] + 1) : '') . $row[ 'name' ] . ($whileMenP ? '' : '</b>') . '</td>';
        echo '<td><a href="?menu-' . $row[ 'wo' ] . '-l-' . $row[ 'pos' ] . '"><img src="include/images/icons/pfeill.png" alt="" border="0" title="nach links"></a></td>';
        echo '<td><a href="?menu-' . $row[ 'wo' ] . '-r-' . $row[ 'pos' ] . '"><img src="include/images/icons/pfeilr.png" alt="" border="0" title="nach rechts"></a></td>';
        echo '<td><a href="?menu-' . $row[ 'wo' ] . '-o-' . $row[ 'pos' ] . '"><img src="include/images/icons/pfeilo.png" alt="" border="0" title="nach oben"></a></td>';
        echo '<td><a href="?menu-' . $row[ 'wo' ] . '-u-' . $row[ 'pos' ] . '"><img src="include/images/icons/pfeilu.png" alt="" border="0" title="nach unten"></a></td>';
        echo '<td><a href="javascript:delcheck(\'' . $row[ 'pos' ] . '\',\'' . $row[ 'wo' ] . '\')"><img src="include/images/icons/del.png" alt="" border="0" title="l&ouml;schen"></a></td>';
        echo '<td><a href="?menu-' . $row[ 'wo' ] . '-edit-' . $row[ 'pos' ] . '"><img src="include/images/icons/edit.png" alt="" border="0" title="&auml;ndern"></a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

function menu_update_menupos_reparieren($wo) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos` ASC";
    $e = db_query($q);
    $i = - 127;
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . $i . " WHERE `pos` = " . $r[ 'pos' ] . " AND `wo` = " . $wo) or die(mysql_error());
        $i++;
    }
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos` ASC";
    $e = db_query($q);
    $i = 0;
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . $i . " WHERE `pos` = " . $r[ 'pos' ] . " AND `wo` = " . $wo) or die(mysql_error());
        $i++;
    }
}
function menu_update_menupos($wo) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos` ASC";
    $e = db_query($q);
    $i = 0;
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . $i . " WHERE `pos` = " . $r[ 'pos' ] . " AND `wo` = " . $wo) or die(mysql_error());
        $i++;
    }
}
function menu_update_menupos_p1($wo, $pos) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` >= " . $pos . " ORDER BY `pos` DESC";
    $e = db_query($q);
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . ($r[ 'pos' ] + 1) . " WHERE `pos` = " . $r[ 'pos' ] . " AND `wo` = " . $wo);
    }
}
function menu_update_menupos_m1($wo, $pos) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` > " . $pos . " ORDER BY `pos` ASC";
    $e = db_query($q);
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . ($r[ 'pos' ] - 1) . " WHERE `pos` = " . $r[ 'pos' ] . " AND `wo` = " . $wo);
    }
}
function get_boxes_array() {
    $ar = array();
    $handle = opendir('include/boxes');
    while ($ver = readdir($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/boxes/' . $ver)) {
            $ar[ $ver ] = $ver;
        }
    }
    closedir($handle);
    $handle = opendir('include/contents/selfbp/selfb');
    while ($ver = readdir($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/selfbp/selfb/' . $ver)) {
            $ar[ 'self_' . $ver ] = 'self_' . $ver;
        }
    }
    closedir($handle);
    asort($ar);
    return ($ar);
}

function get_links_array() {
    $ar = array();
    $handle = opendir('include/contents');
    while ($ver = readdir($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/' . $ver)) {
            $n = explode('.', $ver);
            $ar[ $n[ 0 ] ] = $ver;
        }
    }
    closedir($handle);
    $handle = opendir('include/contents/selfbp/selfp');
    while ($ver = readdir($handle)) {
        if ($ver == "." OR $ver == ".." OR is_dir('include/contents/selfbp/selfp/' . $ver)) {
            continue;
        }
        $n = explode('.', $ver);
        if (file_exists('include/contents/' . $ver) OR file_exists('include/contents/' . $n[ 0 ] . '.php')) {
            $n[ 0 ] = 'self-' . $n[ 0 ];
        }
        $ar[ $n[ 0 ] ] = 'self_' . $ver;
    }
    closedir($handle);
    asort($ar);
    return ($ar);
}
// navigation
$aktion = $menu->get(2);
$wo = $menu->get(1);
if ($wo == '' or !is_numeric($wo)) {
    $wo = 1;
}
// eintragen aendern
if ($aktion == 'an') {
    $ebene = escape($_REQUEST[ 'cwebene' ], 'integer');
    $was = escape($_REQUEST[ 'was' ], 'integer');
    $wo = escape($_REQUEST[ 'cwmenu' ], 'integer');
    $name = escape($_REQUEST[ 'name' ], 'string');
    $apos = escape($_REQUEST[ 'apos' ], 'integer');
    $posi = escape($_REQUEST[ 'posi' ], 'integer');
    $awo = escape($_REQUEST[ 'awo' ], 'integer');
    $link = escape($_REQUEST[ 'link' ], 'string');
    $link1 = escape($_REQUEST[ 'link1' ], 'string');
    $link2 = escape($_REQUEST[ 'link2' ], 'string');
    $grecht = escape($_REQUEST[ 'grecht' ], 'integer');
    $menutyp = escape($_REQUEST[ 'menutyp' ], 'integer');
    $recht_type = escape($_REQUEST['recht_type'], 'integer');

    if ($was == 7) {
        $link = $link1;
    } elseif ($was == 8 OR $was == 9) {
        $link = $link2;
    } elseif ($was == 2) {
        if ($menutyp == 2) {
            $was = 3;
        } elseif ($menutyp == 3) {
            $was = 4;
        }
        $link = '';
    }

    //Für Teamrecht
    if ($recht_type == 3) {
        $grecht = escape($_REQUEST['teams'], 'integer');
    }

    if ($apos == '' AND $awo == '') {
        // eintragen
        $npos = db_result(db_query("SELECT COUNT(*) FROM `prefix_menu` WHERE `wo` = " . $wo), 0, 0);
        if ($posi == '' OR intval($posi) != $posi OR $posi > $npos) {
            $posi = $npos;
        } else {
            if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` + 1 WHERE `wo` = " . $wo . " AND `pos` >= " . $posi . " ORDER BY `pos` DESC")) {
                menu_update_menupos_p1($wo, $posi);
            }
        }

        $q = "INSERT INTO prefix_menu (wo,pos,was,ebene,recht,recht_type,name,path)
    VALUES (" . $wo . "," . $posi . "," . $was . "," . $ebene . "," . $grecht . ", ".$recht_type." ,'" . $name . "','" . $link . "')";
        db_query($q);
    } else {
        // aendern
        $xpos = $apos;
        if ($awo != $wo) {
            $npos = db_result(db_query("SELECT COUNT(*) FROM `prefix_menu` WHERE `wo` = " . $wo), 0, 0);
            if ($posi == '' OR !is_numeric($posi) OR $posi > $npos) {
                $posi = $npos;
            } else {
                if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` + 1 WHERE `wo` = " . $wo . " AND `pos` >= " . $posi . " ORDER BY `pos` DESC")) {
                    menu_update_menupos_p1($wo, $posi);
                }
            }
        } elseif ($posi != $apos AND $awo == $wo) {
            $xpos = $posi;
            $posi = $apos;
        }

        $q = "UPDATE `prefix_menu` SET `wo` = " . $wo . ", `name` = '" . $name . "', `path` = '" . $link . "', `pos` = " . $posi . ", `recht` = " . $grecht . ", `recht_type` = " . $recht_type . ", `was` = " . $was . ", `ebene` = " . $ebene . " WHERE `pos` = " . $apos . " AND `wo` = " . $awo;
        db_query($q);
        if ($awo != $wo) {
            if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` - 1 WHERE `pos` > " . $apos . " AND `wo` = " . $awo . " ORDER BY `pos` ASC")) {
                menu_update_menupos_m1($awo, $apos);
            }
        } elseif ($xpos != $apos AND $awo == $wo) {
            $npos = db_result(db_query("SELECT COUNT(*) FROM `prefix_menu` WHERE `wo` = " . $awo), 0, 0);
            if ($posi != '' AND is_numeric($xpos) AND $xpos < $npos) {
                db_query("UPDATE `prefix_menu` SET `pos` = -1 WHERE `pos` = " . $apos . " AND `wo` = " . $wo);

                if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` -1 WHERE `pos` > " . $apos . " AND `wo` = " . $wo . " ORDER BY `pos` ASC")) {
                    menu_update_menupos_m1($wo, $apos);
                }

                if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` +1 WHERE `pos` >= " . $xpos . " AND `wo` = " . $wo . " ORDER BY `pos` DESC")) {
                    menu_update_menupos_p1($wo, $xpos);
                }
                db_query("UPDATE `prefix_menu` SET `pos` = " . $xpos . " WHERE `pos` = -1 AND `wo` = " . $wo);
            }
        }
    }
}
// nach rechts oder links verschieben
if ($aktion == 'r' OR $aktion == 'l') {
    $ebene = db_result(db_query("SELECT `ebene` FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` = " . $menu->get(3)), 0, 0);
    $was = db_result(db_query("SELECT `was` FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` = " . $menu->get(3)), 0, 0);
    if ($was >= 7) {
        $nebene = ($aktion == 'r' ? $ebene + 1 : $ebene - 1);
        if ($nebene == - 1) {
            $nebene = 0;
        }
        if ($nebene == 5) {
            $nebene = 4;
        }
        db_query("UPDATE `prefix_menu` SET `ebene` = " . $nebene . " WHERE `wo` = " . $wo . " AND `pos` = " . $menu->get(3));
    }
}
// reparieren
if ($menu->get(1) == 'reparieren') {
    for ($i = 1; $i <= 5; $i++) {
        menu_update_menupos_reparieren($i);
    }
}
// nach unten oder oben verschieben
if ($aktion == 'o' OR $aktion == 'u') {
    $pos = $menu->get(3);
    $ges = db_result(db_query("SELECT COUNT(*) FROM `prefix_menu` WHERE `wo` = " . $wo), 0, 0);
    if ($aktion == 'o') {
        $npos = $pos - 1;
    } else {
        $npos = $pos + 1;
    }
    if ($npos < $ges AND $pos >= 0) {
        db_query("UPDATE `prefix_menu` SET `pos` = -1 WHERE `pos` = " . $pos . " AND `wo` = " . $wo);
        db_query("UPDATE `prefix_menu` SET `pos` = " . $pos . " WHERE `pos` = " . $npos . " AND `wo` = " . $wo);
        db_query("UPDATE `prefix_menu` SET `pos` = " . $npos . " WHERE `pos` = -1 AND `wo` = " . $wo);
    }
}
// loeschen
if ($aktion == 'delete') {
    $pos = $menu->get(3);
    db_query("DELETE FROM `prefix_menu` WHERE `pos` = " . $pos . " AND `wo` = " . $wo);
    if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` - 1 WHERE `pos` > " . $pos . " AND `wo` = " . $wo . " ORDER BY `pos` ASC")) {
        menu_update_menupos($wo);
    }
}
// aendern / anzeigen vorbereiten
if ($aktion == 'edit') {
    $pos = $menu->get(3);
    $row = db_fetch_assoc(db_query("SELECT * FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` = " . $pos));
    $ar = array(
        'allboxes' => $row[ 'path' ],
        'getfuerB' => $row[ 'recht' ],
        'bname' => $row[ 'name' ],
        'link2' => $row[ 'path' ],
        'posi' => $row[ 'pos' ],
        'cwmenu' => $row[ 'wo' ],
        'cwebene' => $row[ 'ebene' ],
        'awo' => $row[ 'wo' ],
        'was' => $row[ 'was' ],
        'apos' => $row[ 'pos' ],
        'alllinkss' => $row[ 'path' ],
        'menutyp' => 1,
        'recht_type' => $row['recht_type'],
        'team' => $row['recht_type'] == 3 ? $row[ 'recht' ] : 0
        );
    if ($ar[ 'was' ] == 3) {
        $ar[ 'menutyp' ] = 2;
        $ar[ 'was' ] = 2;
    } elseif ($ar[ 'was' ] == 4) {
        $ar[ 'menutyp' ] = 3;
        $ar[ 'was' ] = 2;
    }
} else {
    $ar = array(
        'allboxes' => '',
        'was' => '',
        'getfuerB' => '',
        'cwebene' => '',
        'cwmenu' => $wo,
        'allmenus' => '',
        'bname' => '',
        'posi' => '',
        'apos' => '',
        'awo' => '',
        'link2' => '',
        'alllinkss' => '',
        'menutyp' => '',
        'recht_type' => 0,
        'team' => 0
        );
}

$tpl = new tpl('menu', 1);
$boxenArNav = get_boxes_array();
$menuArNav = get_links_array();
$ar_cwmenu = array();
for ($i = 1; $i <= $allgAr[ 'menu_anz' ]; $i++) {
    $ar_cwmenu[ $i ] = 'Men&uuml; 0' . $i;
}
$ar_cwebene = array();
for ($i = 0; $i <= 4; $i++) {
    $ar_cwebene[ $i ] = 'Ebene 0' . ($i + 1);
}
$ar_cwwas = array(1 => 'Box',
    2 => 'Men&uuml;',
    7 => 'Men&uuml;punkt wahl',
    8 => 'Men&uuml;punkt extern',
    9 => 'Men&uuml;punkt intern'
    );
$ar_menutyp = array(2 => 'Vertikal',
    1 => 'Horizontal'
    );
$ar_rechttypes = array('ab', 'für', 'bis', 'für Team');
$ar[ 'allboxes' ] = arliste($ar[ 'allboxes' ], $boxenArNav, $tpl, 'allboxes');
$ar[ 'alllinkss' ] = arliste($ar[ 'alllinkss' ], $menuArNav, $tpl, 'alllinkss');
$ar[ 'getfuerB' ] = dbliste($ar[ 'getfuerB' ], $tpl, 'getfuerB', "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` DESC");
$ar[ 'cwmenu' ] = arliste($ar[ 'cwmenu' ], $ar_cwmenu, $tpl, 'cwmenu');
$ar[ 'cwebene' ] = arliste($ar[ 'cwebene' ], $ar_cwebene, $tpl, 'cwebene');
$ar[ 'cwwas' ] = arliste($ar[ 'was' ], $ar_cwwas, $tpl, 'cwwas');
$ar[ 'menutyp' ] = arliste($ar[ 'menutyp' ], $ar_menutyp, $tpl, 'menutyp');
$ar[ 'rechttype' ] = arliste($ar['recht_type'], $ar_rechttypes, $tpl, 'rechttype');
$ar[ 'teams' ] = dbliste( $ar['team'], $tpl, 'teams', 'SELECT `id`, `name` FROM `prefix_groups` ORDER BY `pos`');

// ausgabe
$tpl->out(0);
show_menu($wo);
$tpl->set_ar($ar);
$tpl->out(1);

$design->footer();

?>