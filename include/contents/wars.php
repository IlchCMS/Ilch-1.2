<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

function get_erg_liste($wid) {
    $list = '';
    $enar = array ('jpg', 'gif', 'png', 'jpeg');
    $erg = db_query("SELECT * FROM prefix_warmaps WHERE `wid` = " . $wid);
    while ($row = db_fetch_assoc($erg)) {
        if ($row['opp'] == $row['owp']) {
            $farbe = 'FDFBB7'; #pat
        } elseif ($row['opp'] < $row['owp']) {
            $farbe = 'C8E1B8'; #win
        } elseif ($row['opp'] > $row['owp']) {
            $farbe = 'D8B9B9'; #los
        }
        foreach($enar as $v) {
            if (file_exists ('include/images/wars/' . $wid . '_' . $row['mnr'] . '.' . $v)) {
                $size = getimagesize('include/images/wars/' . $wid . '_' . $row['mnr'] . '.' . $v);
                $breite = $size[0];
                $hoehe = $size[1];
                $row['map'] = '<a href="#" onClick="javascript:window.open(\'include/images/wars/' . $wid . '_' . $row['mnr'] . '.' . $v . '\',\'bild\',\'height=' . $hoehe . ',width=' . $breite . '\')">' . $row['map'] . '</a>';
                break;
            }
        }
        $list .= '<tr bgcolor="#' . $farbe . '">';
        $list .= '<td><font color="#000000">' . $row['map'] . '</font></td>';
        $list .= '<td><font color="#000000">' . $row['opp'] . '</font></td>';
        $list .= '<td><font color="#000000">' . $row['owp'] . '</font></td>';
        $list .= '</tr>';
    }
    return ($list);
}

function lastwars_get_memberlist ($id) {
    $l = '';
    $erg = db_query("SELECT prefix_user.id,prefix_user.name FROM prefix_user LEFT JOIN prefix_warmember ON prefix_warmember.uid = prefix_user.id AND prefix_warmember.wid = " . $id . " WHERE wid = " . $id . " ORDER BY prefix_user.name ASC");
    while ($r = db_fetch_assoc($erg)) {
        $l .= '<a href="index.php?user-details-' . $r['id'] . '">' . $r['name'] . '</a>, ';
    }
    return (substr($l, 0, - 2));
}

if ($menu->get(2) == '' OR $menu->getA(2) == 'p') {
    $title = $allgAr['title'] . ' :: Wars';
    $hmenu = 'Wars';
    $design = new design ($title , $hmenu);
    $design->header();

    $out = array('GES' => 0, 'WIN' => 0, 'LOS' => 0, 'PAT' => 0, 'TITLE' => $allgAr['title']);

    if (isset($_POST['tid']) and is_numeric($_POST['tid'])) {
        $tid = escape($_POST['tid'], 'integer');
        $where1 = 'AND tid = ' . $tid;
        $where2 = 'AND a.tid = ' . $tid;
        $out['TITLE'] = db_result(db_query('SELECT `name` FROM `prefix_groups` WHERE `id` = ' . $tid));
    } else {
        $where1 = $where2 = '';
    }

    $keys = array(1 => 'WIN', 2 => 'LOS', 3 => 'PAT');
    $qry = db_query('SELECT `wlp`, COUNT(`id`) AS sum FROM `prefix_wars` WHERE `status` = 3 ' . $where1 . ' GROUP BY `wlp`');
    while ($r = db_fetch_assoc($qry)) {
        $out[$keys[$r['wlp']]] = $r['sum'];
        $out['GES'] += $r['sum'];
    }
    $tpl = new tpl ('wars.htm');
    $tpl->set_ar_out ($out , 0);
    $akttime = date('Y-m-d');
    $class = '';
    $erg = db_query('SELECT a.id,a.gegner,a.page,a.game,b.name as team,DATE_FORMAT(a.datime,"%d.%m.%Y - %H:%i:%s") as time FROM prefix_wars a left join prefix_groups b ON a.tid = b.id WHERE a.status = 2 AND a.datime >= "' . $akttime . '" ' . $where2 . ' ORDER BY a.datime');
    if (db_num_rows ($erg) == 0) {
        echo '<tr class="Cmite"><td colspan="4"><strong>kein Next War vorhanden</strong></td></tr>';
    } else {
        while ($row = db_fetch_assoc($erg)) {
            if ($class == 'Cmite') {
                $class = 'Cnorm';
            } else {
                $class = 'Cmite';
            }
            $row['page'] = get_homepage($row['page']);
            $row['team'] = get_wargameimg($row['game']) . '&nbsp;' . $row['team'];
            $row['class'] = $class;
            $tpl->set_ar_out($row, 1);
        }
    }
    $tpl->out(2);
    $class = '';
    $wlps = array(1 => $lang['win'], 2 => $lang['los'], 3 => $lang['pat']);
    $sqla = 'WHERE status = 3 AND ';
    $wheres = array();
    if (isset($_POST['tid']) and !empty($_POST['tid'])) {
        $teams = dblistee ($_POST['tid'], "SELECT `id`, `name` FROM `prefix_groups` WHERE `zeigen` = 1 ORDER BY `name`");
        $wheres[] = 'tid = ' . escape($_POST['tid'], 'integer');
        // wlps einschränken
        $qry = db_query('SELECT DISTINCT wlp FROM prefix_wars ' . (count($wheres) ? $sqla . implode(' AND ', $wheres) : ''));
        $dbwlps = array();
        while ($r = db_fetch_assoc($qry)) {
            $dbwlps[] = (int)$r['wlp'];
        }
        foreach ($wlps as $k => $v) {
            if (!in_array($k, $dbwlps)) {
                unset($wlps[$k]);
            }
        }
    } else {
        $teams = dblistee ('', "SELECT `id`, `name` FROM `prefix_groups` WHERE `zeigen` = 1 ORDER BY `name`");
    }
    if (isset($_POST['wlp']) and !empty($_POST['wlp'])) {
        $wlp = arlistee($_POST['wlp'], $wlps);
        $wheres[] = 'wlp = ' . escape($_POST['wlp'], 'integer');
    } else {
        $wlp = arlistee('', $wlps);
    }
    if (isset($_POST['spiel']) and !empty($_POST['spiel'])) {
        $game = dblistee ($_POST['spiel'], "SELECT DISTINCT `game`,`game` FROM `prefix_wars` " . (count($wheres) ? $sqla . implode(' AND ', $wheres) : '') . " ORDER BY `game`");
        $wheres[] = 'game = "' . escape($_POST['spiel'], 'string') . '"';
    } else {
        $game = dblistee ('', "SELECT DISTINCT `game`,`game` FROM `prefix_wars` " . (count($wheres) ? $sqla . implode(' AND ', $wheres) : '') . " ORDER BY `game`");
    }
    if (isset($_POST['typ']) and !empty($_POST['typ'])) {
        $mtyp = dblistee ($_POST['typ'], "SELECT DISTINCT `mtyp`,`mtyp` FROM `prefix_wars` " . (count($wheres) ? $sqla . implode(' AND ', $wheres) : '') . " ORDER BY `mtyp`");
        $wheres[] = 'mtyp = "' . escape($_POST['typ'], 'string') . '"';
    } else {
        $mtyp = dblistee ('', "SELECT DISTINCT `mtyp`,`mtyp` FROM `prefix_wars` " . (count($wheres) ? $sqla . implode(' AND ', $wheres) : '') . " ORDER BY `mtyp`");
    }

    $tpl->set_ar_out (array('tid' => $teams, 'game' => $game, 'typ' => $mtyp, 'wlp' => $wlp) , 3);
    if ($menu->get(1) == 'last') {
        $tpl->out(4);
        $sqla = 'WHERE status = 3 ' . (!empty($wheres) ? ' AND ' . implode(' AND ', $wheres) : '');
        // seiten funktion
        $limit = $allgAr['wars_last_limit']; // Limit
        if (isset($_POST['page']) and is_numeric($_POST['page']) and $_POST['page'] >= 1) {
            $menu->set_url(2, 'p' . intval($_POST['page']));
        }
        $page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1);
        $MPL = db_make_sites ($page , $sqla , $limit , "?wars-last" , 'wars');

        $MPL = preg_replace('%-p(\d+)"%', '$0 onclick="return loadLWPage($1);"', $MPL);

        $anfang = ($page - 1) * $limit;
        // seiten funktion
        $farbe1wlpar = array(1 => 'C8E1B8', 2 => 'D8B9B9', 3 => 'FDFBB7');
        $farbe2wlpar = array(1 => '00FF00', 2 => 'FF0000', 3 => 'FFFF00');
        $erg = db_query("SELECT a.owp,a.opp,a.wlp,a.land,a.mtyp,a.game,a.id,a.gegner,a.page,b.name as team,DATE_FORMAT(datime,'%d.%m.%Y') as time FROM prefix_wars a left join prefix_groups b ON a.tid = b.id " . $sqla . " ORDER BY a.datime DESC, id DESC LIMIT " . $anfang . "," . $limit);
        while ($row = db_fetch_assoc($erg)) {
            $row['erg'] = $row['opp'] . ':' . $row['owp'];
            $row['farbe'] = $farbe1wlpar[$row['wlp']];
            $row['farbe2'] = $farbe2wlpar[$row['wlp']];
            if ($class == 'Cmite') {
                $class = 'Cnorm';
            } else {
                $class = 'Cmite';
            }
            $row['page'] = get_homepage($row['page']);
            $row['team'] = get_wargameimg($row['game']) . '&nbsp;' . $row['team'];
            $row['class'] = $class;
            $tpl->set_ar_out($row, 5);
        }
        $tpl->set_out('MPL', $MPL, 6);
    }
    $design->footer();
} elseif (is_numeric($menu->get(2))) {
    $_GET['mehr'] = escape($menu->get(2), 'integer');

    $erg = @db_query("SELECT
	DATE_FORMAT(datime,'%d.%m.%Y') as datum,
	tid, status, owp, opp, wlp,
	DATE_FORMAT(datime,'%H:%i:%s') as zeit,
	gegner, tag, page, mail, icq, wo, prefix_wars.`mod`, mtyp,
	game, land, txt, prefix_wars.id,
	name as team
	FROM prefix_wars
	left join prefix_groups ON prefix_wars.tid = prefix_groups.id
	WHERE prefix_wars.id = " . $_GET['mehr']);

    db_check_erg ($erg);

    $row = db_fetch_assoc($erg);
    $row['page'] = get_homepage($row['page']);
    $row['txt'] = bbcode($row['txt']);
    if ($row['status'] == 2) {
        // nextwars
        $title = $allgAr['title'] . ' :: Wars :: Nextwars';
        $hmenu = '<a href="?wars" class="smalfont">Wars</a><b> &raquo; </b>Nextwars';
        $design = new design ($title , $hmenu);
        $design->header();
        $tpl = new tpl ('wars_next');
        $row['tag'] = (empty($row['tag']) ? $row['gegner'] : $row['tag']);
        if ($_SESSION['authright'] <= - 3) {
            $row['mail'] = $row['mail'];
            $row['icq'] = $row['icq'];
            $row['wo'] = $row['wo'];
            $row['txt'] = $row['txt'];
        } else {
            $row['icq'] = 'locked';
            $row['mail'] = 'locked';
            $row['wo'] = 'locked';
            $row['txt'] = 'locked';
        }
        $tpl->set_ar_out($row, 0);

        if ($_SESSION['authright'] <= - 2) {
            // get benoetige member
            $bm = substr($row['mod'], 0, 3);
            $needed = '';
            for($i = 0;$i < strlen($bm);$i++) {
                if (is_numeric($bm {
                            $i}
                        )) {
                    $needed .= $bm {
                        $i} ;
                }
            }

            $uid = $_SESSION['authid'];
            if ($menu->get(3) == 'delete') {
                $uid = $menu->get(4);
            }
            $ck = db_count_query("SELECT COUNT(wid) FROM prefix_warmember WHERE wid = " . $_GET['mehr'] . " AND uid = " . $uid);
            // eine zu bzw. absage loeschen
            if ($menu->get(3) == 'delete' AND ((has_right(array($row['tid'])) === true AND $uid == $_SESSION['authid']) OR is_siteadmin('wars')) AND $ck == 1) {
                db_query("DELETE FROM prefix_warmember WHERE wid = " . $_GET['mehr'] . " AND uid = " . $uid);
                $ck = 0;
            }

            $available = db_count_query("SELECT COUNT(uid) FROM prefix_warmember WHERE wid = " . $_GET['mehr'] . " AND aktion = 1");
            $aout1 = array (
                'needed' => $needed,
                'available' => $available,
                'id' => $_GET['mehr']
                );
            $tpl->set_ar_out($aout1, 1);
            if ($ck == 0 AND has_right(array($row['tid'])) === true) {
                if (isset ($_POST['sub'])) {
                    $aktion = ($_POST['sub'] == 'zusagen' ? 1 : 0);
                    $kom = escape($_POST['kom'], 'string');
                    db_query("INSERT INTO prefix_warmember (uid,wid,aktion,kom) VALUES (" . $_SESSION['authid'] . "," . $_GET['mehr'] . "," . $aktion . ",'" . $kom . "')");
                } else {
                    $tpl->out(2);
                }
            }
            $class = '';
            $aktionar = array ('<font style="color:#FF0000; background:#666666; font-weight:bold;">abgesagt</font>', '<font style="font-weight:bold; color:#00FF00; background:#666666;">zugesagt</font>');
            $erg1 = db_query("SELECT b.id as uid, b.name, a.aktion, a.kom FROM prefix_warmember a left join prefix_user b ON b.id = a.uid WHERE a.wid = " . $_GET['mehr']);
            while ($row1 = db_fetch_assoc($erg1)) {
                if ($class == 'Cmite') {
                    $class = 'Cnorm';
                } else {
                    $class = 'Cmite';
                }
                $row1['class'] = $class;
                $row1['aktion'] = $aktionar[$row1['aktion']];
                if ($row1['uid'] == $_SESSION['authid'] OR is_siteadmin('wars')) {
                    $row1['name'] = '<a href="index.php?wars-more-' . $_GET['mehr'] . '-delete-' . $row1['uid'] . '"><img src="include/images/icons/del.gif" border="0" title="l&ouml;schen" /></a> &nbsp; ' . $row1['name'];
                }
                $tpl->set_ar_out($row1, 3);
            }
        }
        $tpl->out(4);
    } elseif ($row['status'] == 3) {
        // lastwars
        $row['memberliste'] = lastwars_get_memberlist($_GET['mehr']);
        $wlpar = array(1 => 'gewonnen', 2 => 'verloren', 3 => 'unentschieden');
        $row['erg'] = $row['owp'] . ' zu ' . $row['opp'];
        $row['ergliste'] = get_erg_liste($_GET['mehr']);
        $row['wlp'] = $wlpar[$row['wlp']];
        $title = $allgAr['title'] . ' :: Wars :: Lastwars';
        $hmenu = '<a href="?wars" class="smalfont">Wars</a><b> &raquo; </b>Lastwars';
        $design = new design ($title , $hmenu);
        $design->header();
        $tpl = new tpl ('wars_last');
        $row['tag'] = (empty($row['tag']) ? $row['gegner'] : $row['tag']);
        $tpl->set_ar_out($row, 0);
        // kommentare fuer lastwars
        if ($allgAr['wars_last_komms'] < 0 AND has_right ($allgAr['wars_last_komms'])) {
            // aktion
            if (isset ($_POST['kommentar_fuer_last_wars'])) {
                $name = $_SESSION['authname'];
                $text = escape($_POST['text'], 'textarea');
                db_query("INSERT INTO prefix_koms (name,cat,text,uid) VALUES ('" . $name . "','WARSLAST', '" . $text . "', " . $_GET['mehr'] . " )");
            }
            if (isset ($_GET['kommentar_fuer_last_wars_loeschen']) AND is_siteadmin('wars')) {
                db_query("DELETE FROM prefix_koms WHERE cat = 'WARSLAST' AND uid = " . $_GET['mehr'] . " AND id = " . $_GET['kommentar_fuer_last_wars_loeschen']);
            }
            // anzeigen
            $tpl->out(1);
            $class = '';
            $erg = db_query("SELECT name,text,id FROM prefix_koms WHERE cat = 'WARSLAST' AND uid = " . $_GET['mehr'] . " ORDER BY id DESC");
            while ($r = db_fetch_assoc($erg)) {
                $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
                $r['text'] = bbcode($r['text']);
                if (is_siteadmin('wars')) {
                    $r['text'] .= '<a href="index.php?wars-more-' . $_GET['mehr'] . '=0&amp;kommentar_fuer_last_wars_loeschen=' . $r['id'] . '"><img src="include/images/icons/del.gif" title="l&ouml;schen" alt="l&ouml;schen" border="0"></a>';
                }
                $r['class'] = $class;
                $tpl->set_ar_out($r, 2);
            }
            $tpl->out(3);
        }
    }
    $design->footer();
}

?>