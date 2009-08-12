<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Admins Area', 'Admins Area', 2);
$design->header();
// function show menu ( 1 == links, 2 == rechts )
function show_menu ($wo) {
    $erg = db_query("SELECT * FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos`");
    $x = 0;
    $class = '';
    echo '<table class="border" cellpadding="3" cellspacing="1" border="0">';
    while ($row = db_fetch_assoc($erg)) {
        $subhauptx = $row['was'];
        $whileMenP = ($subhauptx >= 7 ? true : false);
        $class = ($class == 'Cdark' ? 'Cmite' : 'Cdark');

        echo '<tr class="' . $class . '"><td>' . $row['pos'] . '</td><td>' . ($whileMenP?'':'<b>') . ($whileMenP?str_repeat('-&nbsp;', $row['ebene'] + 1):'') . $row['name'] . ($whileMenP?'':'</b>') . '</td>';
        echo '<td><a href="?menu-' . $row['wo'] . '-l-' . $row['pos'] . '"><img src="include/images/icons/pfeill.gif" alt="" border="0" title="nach links"></a></td>';
        echo '<td><a href="?menu-' . $row['wo'] . '-r-' . $row['pos'] . '"><img src="include/images/icons/pfeilr.gif" alt="" border="0" title="nach rechts"></a></td>';
        echo '<td><a href="?menu-' . $row['wo'] . '-o-' . $row['pos'] . '"><img src="include/images/icons/pfeilo.gif" alt="" border="0" title="nach oben"></a></td>';
        echo '<td><a href="?menu-' . $row['wo'] . '-u-' . $row['pos'] . '"><img src="include/images/icons/pfeilu.gif" alt="" border="0" title="nach unten"></a></td>';
        echo '<td><a href="javascript:delcheck(\'' . $row['pos'] . '\',\'' . $row['wo'] . '\')"><img src="include/images/icons/del.gif" alt="" border="0" title="l&ouml;schen"></a></td>';
        echo '<td><a href="?menu-' . $row['wo'] . '-edit-' . $row['pos'] . '"><img src="include/images/icons/edit.gif" alt="" border="0" title="&auml;ndern"></a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

function menu_update_menupos_reparieren ($wo) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos` ASC";
    $e = db_query($q);
    $i = - 127;
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . $i . " WHERE `pos` = " . $r['pos'] . " AND `wo` = " . $wo) or die (mysql_error());
        $i++;
    }
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos` ASC";
    $e = db_query($q);
    $i = 0;
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . $i . " WHERE `pos` = " . $r['pos'] . " AND `wo` = " . $wo) or die (mysql_error());
        $i++;
    }
}
function menu_update_menupos ($wo) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " ORDER BY `pos` ASC";
    $e = db_query($q);
    $i = 0;
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . $i . " WHERE `pos` = " . $r['pos'] . " AND `wo` = " . $wo) or die (mysql_error());
        $i++;
    }
}
function menu_update_menupos_p1 ($wo, $pos) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` >= " . $pos . " ORDER BY `pos` DESC";
    $e = db_query($q);
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . ($r['pos'] + 1) . " WHERE `pos` = " . $r['pos'] . " AND `wo` = " . $wo);
    }
}
function menu_update_menupos_m1 ($wo, $pos) {
    $q = "SELECT `pos` FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` > " . $pos . " ORDER BY `pos` ASC";
    $e = db_query($q);
    while ($r = db_fetch_assoc($e)) {
        db_query("UPDATE `prefix_menu` SET `pos` = " . ($r['pos'] - 1) . " WHERE `pos` = " . $r['pos'] . " AND `wo` = " . $wo);
    }
}
function get_boxes_array () {
    $ar = array ();
    $handle = opendir('include/boxes');
    while ($ver = readdir ($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/boxes/' . $ver) AND strpos($ver, 'adminmenu') === false) {
            $ar[$ver] = $ver;
        }
    }
    closedir($handle);
    $handle = opendir('include/contents/selfbp/selfb');
    while ($ver = readdir ($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/selfbp/selfb/' . $ver)) {
            $ar['self_' . $ver] = 'self_' . $ver;
        }
    }
    closedir($handle);
    asort($ar);
    return ($ar);
}

function get_links_array () {
    $ar = array ();
    $handle = opendir('include/contents');
    while ($ver = readdir ($handle)) {
        if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/' . $ver)) {
            $n = explode('.', $ver);
            $ar[$n[0]] = $ver;
        }
    }
    closedir($handle);
    $handle = opendir('include/contents/selfbp/selfp');
    while ($ver = readdir ($handle)) {
        if ($ver == "." OR $ver == ".." OR is_dir('include/contents/selfbp/selfp/' . $ver)) {
            continue;
        }
        $n = explode('.', $ver);
        if (file_exists ('include/contents/' . $ver) OR file_exists ('include/contents/' . $n[0] . '.php')) {
            $n[0] = 'self-' . $n[0];
        }
        $ar[$n[0]] = 'self_' . $ver;
    }
    closedir($handle);
    asort ($ar);
    return ($ar);
}

/*

######################################

Funktionen werden nicht benutzt

######################################

# ck_post_ci
# diese funktion prueft beim eintragen und aendern ob die ebene passt
# daher das sofern es sich um ein menupunkt handelt die ebenen struktur
# korekt ist. ein menupunkt muss also 1. in einem menu sein, 2. muessen
# die ebenen davor und dannach passen, so dass nicht einfach ebene 2
# nach 0 etc kommen kann
function menu_ck_pos_ci ($pos, $wo, $ebene = NULL, $was = NULL, $pos_vor = NULL, $pos_nac = NULL, $richten = FALSE) {
  if (is_null($was)) {
    $was = db_result(db_query("SELECT was FROM prefix_menu WHERE wo = ".$wo." AND pos = ".$pos),0,0);
  }

  if (is_null ($ebene)) {
    $ebene = db_result(db_query("SELECT ebene FROM prefix_menu WHERE wo = ".$wo." AND pos = ".$pos),0,0);
  }

  # wenn kein menupunkt immer wahr, weil box und menu nur 0 haben koennen als ebene
  if ($was <= 4) { return (true); }

  $anz       = db_result(db_query("SELECT COUNT(*) FROM prefix_menu WHERE wo = ".$wo ),0,0);
  $anz       = $anz - 1;
  $ebene_nac = 0;
  $ebene_vor = 0;
  $was_vor   = 1;
  if (is_null($pos_vor)) {
    $pos_vor   = $pos - 1;
  }
  if (is_null($pos_nac)) {
    $pos_nac   = $pos + 1;
  }
  if ($ebene_nac == 0 AND $pos != $anz) {
    $ebene_nac = db_result(db_query("SELECT ebene FROM prefix_menu WHERE wo = ".$wo." AND pos = ".$pos_nac),0,0);
  }

  if ($ebene_vor == 0 AND $pos != 0) {
    $ebene_vor = db_result(db_query("SELECT ebene FROM prefix_menu WHERE wo = ".$wo." AND pos = ".$pos_vor),0,0);
  }
  if ($was_vor == 1 AND $pos != 0) {
    $was_vor   = db_result(db_query("SELECT was FROM prefix_menu WHERE wo = ".$wo." AND pos = ".$pos_vor),0,0);
  }

  # erklaerung zur abfrage
  # die ebene davor (ebene_vor) darf groeser oder gleich der ebene sein ORDER eins kleiner
  # die ebene danach (ebene_nac) darf kleiner oder gleich der ebene sein ODER eins groesser
  # UND der punkt davor muss ein menupunkt sein wenn die ebene > 0 ist
  # ODER menupunkt oder menu sein wenn die ebene == 0 ist.
  if (($ebene_vor >= $ebene OR $ebene_vor == ($ebene -1)) AND ($ebene_nac <= $ebene OR $ebene_nac == ($ebene +1)) AND (($was_vor >= 7 AND $ebene > 0) OR ($was_vor >= 3 AND $ebene == 0))) {
    if ($richten === TRUE) {
      if ($was_vor >= 3) {
        db_query("UPDATE prefix_menu SET ebene = ".$ebene_vor." WHERE wo = ".$wo." AND pos = ".$pos);
      } else {
        return (false);
      }
    }
    return (true);
  }

  return (false);
}

# diese funktion prueft vor dem eintragen oder aendern ob an der neuen position
# das richtige was vorhanden ist und an der alten keine luecke zurueck gelassen wird
# W I C H T I G : Diese funktion alleine ist fehlerhaft sie muss I M M E R am ende
# nach der eigentlichen Aktion mit der Funktion "menu_ck_pos_ci" vervollstaendigt werden.
function menu_pre_pos_ci ($npos, $nwo, $apos = NULL, $awo = NULL) {
  if (is_null($apos) AND is_null($awo)) {
    $q = "SELECT was FROM prefix_menu WHERE pos = ".$npos." AND wo = ".$wo;
    if (db_result(db_query($q),0,0) >= 3) {
      return (true);
    }
  }

  $q = "SELECT was FROM prefix_menu WHERE pos = ".$npos." AND wo = ".$wo;
  if (db_result(db_query($q),0,0) >= 3 AND menu_ck_pos_ci($pos+1, $awo, NULL, NULL, $pos-1, NULL)) {
    return (true);
  }

  return (false);
}
*/
// navigation
$aktion = $menu->get(2);
$wo = $menu->get(1);
if ($wo == '' or !is_numeric($wo)) {
    $wo = 1;
}
// eintragen aendern
if ($aktion == 'an') {
    $ebene = escape($_REQUEST['cwebene'], 'integer');
    $was = escape($_REQUEST['was'], 'integer');
    $wo = escape($_REQUEST['cwmenu'], 'integer');
    $name = escape($_REQUEST['name'], 'string');
    $apos = escape($_REQUEST['apos'], 'integer');
    $posi = escape($_REQUEST['posi'], 'integer');
    $awo = escape($_REQUEST['awo'], 'integer');
    $link = escape($_REQUEST['link'], 'string');
    $link1 = escape($_REQUEST['link1'], 'string');
    $link2 = escape($_REQUEST['link2'], 'string');
    $grecht = escape($_REQUEST['grecht'], 'integer');
    $menutyp = escape($_REQUEST['menutyp'], 'integer');

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
    }

    if ($apos == '' AND $awo == '') {
        // eintragen
        $npos = db_result(db_query("SELECT COUNT(*) FROM `prefix_menu` WHERE `wo` = " . $wo), 0, 0);
        if ($posi == '' OR intval($posi) != $posi OR $posi > $npos) {
            $posi = $npos;
        } else {
            if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` + 1 WHERE `wo` = " . $wo . " AND `pos` >= " . $posi . " ORDER BY `pos` DESC")) {
                menu_update_menupos_p1 ($wo, $posi);
            }
        }

        $q = "INSERT INTO `prefix_menu` (`wo`,`pos`,`was`,`ebene`,`recht`,`name`,`path`)
    VALUES (" . $wo . "," . $posi . "," . $was . "," . $ebene . "," . $grecht . ",'" . $name . "','" . $link . "')";
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
                    menu_update_menupos_p1 ($wo, $posi);
                }
            }
        } elseif ($posi != $apos AND $awo == $wo) {
            $xpos = $posi;
            $posi = $apos;
        }

        $q = "UPDATE `prefix_menu` SET `wo` = " . $wo . ", `name` = '" . $name . "', `path` = '" . $link . "', `pos` = " . $posi . ", `recht` = " . $grecht . ", `was` = " . $was . ", `ebene` = " . $ebene . " WHERE `pos` = " . $apos . " AND `wo` = " . $awo;
        db_query($q);
        if ($awo != $wo) {
            if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` - 1 WHERE `pos` > " . $apos . " AND `wo` = " . $awo . " ORDER BY `pos` ASC")) {
                menu_update_menupos_m1 ($awo, $apos);
            }
        } elseif ($xpos != $apos AND $awo == $wo) {
            $npos = db_result(db_query("SELECT COUNT(*) FROM `prefix_menu` WHERE `wo` = " . $awo), 0, 0);
            if ($posi != '' AND is_numeric($xpos) AND $xpos < $npos) {
                db_query("UPDATE `prefix_menu` SET `pos` = -1 WHERE `pos` = " . $apos . " AND `wo` = " . $wo);

                if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` -1 WHERE `pos` > " . $apos . " AND `wo` = " . $wo . " ORDER BY `pos` ASC")) {
                    menu_update_menupos_m1 ($wo, $apos);
                }

                if (!@db_query("UPDATE `prefix_menu` SET `pos` = `pos` +1 WHERE `pos` >= " . $xpos . " AND `wo` = " . $wo . " ORDER BY `pos` DESC")) {
                    menu_update_menupos_p1 ($wo, $xpos);
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
    for($i = 1;$i <= 5;$i++) {
        menu_update_menupos_reparieren ($i);
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
        menu_update_menupos ($wo);
    }
}
// aendern / anzeigen vorbereiten
if ($aktion == 'edit') {
    $pos = $menu->get(3);
    $row = db_fetch_assoc(db_query("SELECT * FROM `prefix_menu` WHERE `wo` = " . $wo . " AND `pos` = " . $pos));
    $ar = array (
        'allboxes' => $row['path'],
        'getfuerB' => $row['recht'],
        'bname' => $row['name'],
        'link2' => $row['path'],
        'posi' => $row['pos'],
        'cwmenu' => $row['wo'],
        'cwebene' => $row['ebene'],
        'awo' => $row['wo'],
        'was' => $row['was'],
        'apos' => $row['pos'],
        'alllinkss' => $row['path'],
        'menutyp' => 1,
        );
    if ($ar['was'] == 3) {
        $ar['menutyp'] = 2;
        $ar['was'] = 2;
    } elseif ($ar['was'] == 4) {
        $ar['menutyp'] = 3;
        $ar['was'] = 2;
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
        'menutyp' => ''
        );
}

$tpl = new tpl ('menu', 1);
$boxenArNav = get_boxes_array ();
$menuArNav = get_links_array ();
$ar_cwmenu = array ();
for($i = 1;$i <= 5;$i++) {
    $ar_cwmenu[$i] = 'Men&uuml; 0' . $i;
}
$ar_cwebene = array ();
for($i = 0;$i <= 4;$i++) {
    $ar_cwebene[$i] = 'Ebene 0' . ($i + 1);
}
$ar_cwwas = array (1 => 'Box',
    2 => 'Men&uuml;',
    7 => 'Men&uuml;punkt wahl',
    8 => 'Men&uuml;punkt extern',
    9 => 'Men&uuml;punkt intern',
    );
$ar_menutyp = array (2 => 'Vertikal',
    1 => 'Horizontal',
    );
$ar['allboxes'] = arliste($ar['allboxes'], $boxenArNav, $tpl, 'allboxes');
$ar['alllinkss'] = arliste($ar['alllinkss'], $menuArNav, $tpl, 'alllinkss');
$ar['getfuerB'] = dbliste($ar['getfuerB'], $tpl, 'getfuerB', "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` DESC");
$ar['cwmenu'] = arliste($ar['cwmenu'], $ar_cwmenu , $tpl, 'cwmenu');
$ar['cwebene'] = arliste($ar['cwebene'], $ar_cwebene, $tpl, 'cwebene');
$ar['cwwas'] = arliste($ar['was'], $ar_cwwas, $tpl, 'cwwas');
$ar['menutyp'] = arliste($ar['menutyp'], $ar_menutyp, $tpl, 'menutyp');
// ausgabe
$tpl->out(0);
show_menu($wo);
$tpl->set_ar($ar);
$tpl->out(1);

$design->footer();

?>