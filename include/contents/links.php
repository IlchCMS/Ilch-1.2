<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

function get_cats_title ($catsar) {
    $l = '';
    foreach($catsar as $k => $v) {
        if ($k != '' AND $v != '') {
            $l = $v . ' :: ' . $l;
        }
    }
    return ($l);
}

function get_cats_urls ($catsar) {
    $l = '';
    foreach($catsar as $k => $v) {
        if ($k != '' AND $v != '') {
            $l = '<a class="smalfont" href="?links-' . $k . '">' . $v . '</a><b> &raquo; </b>' . $l;
        }
    }
    return ($l);
}

function count_files ($cid) {
    $zges = 0;
    $e = db_query("SELECT id FROM prefix_linkcats WHERE cat = " . $cid);
    if (db_num_rows($e) > 0) {
        while ($r = db_fetch_assoc($e)) {
            $zges = $zges + count_files ($r['id']);
        }
    }
    $zges = $zges + db_count_query("SELECT COUNT(*) FROM prefix_links WHERE cat = " . $cid);
    return ($zges);
}

function get_cats_array ($cid , $ar) {
    if (empty($cid)) {
        return ($ar);
    } else {
        $erg = db_query("SELECT cat,id,name FROM prefix_linkcats WHERE id = " . $cid);
        $row = db_fetch_assoc($erg);
        $ar[$row['id']] = $row['name'];
        return (get_cats_array($row['cat'], $ar));
    }
    if ($r) {
        return ($l);
    }
}

switch ($menu->getA(1)) {
    default :
        $cid = ($menu->get(1) ? escape($menu->get(1), 'integer') : 0);
        $erg = db_query("SELECT cat,name FROM prefix_linkcats WHERE id = " . $cid);
        if (db_num_rows($erg) > 0) {
            $row = db_fetch_assoc($erg);
            $array = get_cats_array($row['cat'], '');
            if (!empty($array)) {
                $titelzw = get_cats_title($array);
                $namezw = get_cats_urls($array);
            } else {
                $titelzw = '';
                $namezw = '';
            }
            $cattitle = ':: ' . $titelzw . $row['name'];
            $catname = '<b> &raquo; </b>' . $namezw . $row['name'];
            $catname2 = ' - ' . $row['name'];
        } else {
            $cattitle = '';
            $catname = '';
            $catname2 = '';
        }
        $title = $allgAr['title'] . ' :: links ' . $cattitle;
        $hmenu = '<a class="smalfont" href="?links">Links</a>' . $catname;
        $design = new design ($title , $hmenu);
        $design->header();
        $tpl = new tpl ('links');
        $erg = db_query("SELECT id,name,`desc` FROM prefix_linkcats WHERE cat = $cid ORDER BY pos");
        if (db_num_rows($erg) > 0) {
            $tpl->out(1);
            $class = 'Cnorm';
            while ($row = db_fetch_assoc($erg)) {
                $row['links'] = count_files ($row['id']);
                $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
                $row['class'] = $class;
                $tpl->set_ar_out($row, 2);
            }
            $tpl->out(3);
        }

        $erg = db_query("select id,name,link,banner,`desc`,hits from prefix_links WHERE cat = $cid ORDER BY pos");
        if (db_num_rows($erg) > 0) {
            $tpl->set_out('catname', $catname2, 4);
            $class = 'Cnorm';
            while ($row = db_fetch_assoc($erg)) {
                $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
                $row['class'] = $class;
                $row['desc'] = (!empty($row['desc']) ? '<br /><span class="smalfont">&raquo;&nbsp;' . $row['desc'] . '</span>' : '');
                if (!empty($row['banner'])) {
                    $row['name'] = '<img src="' . $row['banner'] . '" border="0" alt="' . $row['name'] . '" title="' . $row['name'] . '">';
                }
                $tpl->set_ar_out($row, 5);
            }
            $tpl->out(6);
        }
        $design->footer();
        break;
    case 's' :
        $lid = $menu->getE(1);
        db_query("UPDATE prefix_links SET hits = hits +1 WHERE id = " . $lid);
        $row = db_fetch_assoc(db_query("SELECT link FROM prefix_links WHERE id = " . $lid));
        header('location: ' . $row['link']);
        break;
}

?>