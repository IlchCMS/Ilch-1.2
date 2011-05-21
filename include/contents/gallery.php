<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// mini config
$img_per_site = $allgAr[ 'gallery_imgs_per_site' ];
$img_per_line = $allgAr[ 'gallery_imgs_per_line' ];

// Recht für Kommentare pruefen
$komsOK = 1;
if ($allgAr[ 'Ggkoms' ] == 0) { if (loggedin()) { $komsOK = true; } else { $komsOK = false; } }
if ($allgAr[ 'Gukoms' ] == 0) { $komsOK = false; }

function get_cats_title($catsar) {
    $l = '';
    foreach ($catsar as $k => $v) {
        if ($k != '' AND $v != '') {
            $l = $v . ' :: ' . $l;
        }
    }
    return ($l);
}

function get_cats_urls($catsar) {
    $l = '';
    foreach ($catsar as $k => $v) {
        if ($k != '' AND $v != '') {
            $l = '<a class="smalfont" href="?gallery-' . $k . '">' . $v . '</a><b> &raquo; </b>' . $l;
        }
    }
    return ($l);
}

function count_files($cid) {
    $zges = 0;
    $e = db_query("SELECT `id` FROM `prefix_gallery_cats` WHERE `cat` = " . $cid);
    if (db_num_rows($e) > 0) {
        while ($r = db_fetch_assoc($e)) {
            $zges = $zges + count_files($r[ 'id' ]);
        }
    }
    $zges = $zges + db_count_query("SELECT COUNT(*) FROM `prefix_gallery_imgs` WHERE `cat` = " . $cid);
    return ($zges);
}

function get_cats_array($cid, $ar) {
    if (empty($cid)) {
        return ($ar);
    } else {
        $erg = db_query("SELECT `cat`,`id`,`name` FROM `prefix_gallery_cats` WHERE `id` = " . $cid);
        $row = db_fetch_assoc($erg);
        $ar[ $row[ 'id' ] ] = $row[ 'name' ];
        return (get_cats_array($row[ 'cat' ], $ar));
    }
    if ($r) {
        return ($l);
    }
}
// original groesse anzeigen
if ($menu->get(1) == 'showOrig') {
    $tpl = new tpl('gallery_show');
    $tpl->set('id', escape($menu->get(2), 'integer'));
    $tpl->set('endung', $menu->get(3));
    $tpl->out(4);
    // normale groesse anzeigen
} elseif ($menu->get(1) == 'show') {
    $page = ($menu->getA(3) == 'p' ? escape($menu->getE(3), 'integer') : 1);
    $cid = escape($menu->get(2), 'integer');
    $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_gallery_imgs` WHERE `prefix_gallery_imgs`.`cat` = " . $cid), 0);
    $erg = db_query("SELECT `prefix_gallery_imgs`.`id`,`prefix_gallery_imgs`.`cat`,`datei_name`,`endung`,`prefix_gallery_imgs`.`besch`,`klicks`,`vote_wertung`,`vote_klicks` FROM `prefix_gallery_imgs` LEFT JOIN `prefix_gallery_cats` ON `prefix_gallery_imgs`.`cat` = `prefix_gallery_cats`.`id` WHERE `prefix_gallery_imgs`.`cat` = " . $cid . " AND (`recht` >= " . $_SESSION[ 'authright' ] . " OR `recht` IS NULL) ORDER BY `id` ASC LIMIT " . $page . ",1");
    $row = db_fetch_assoc($erg);
    $size = getimagesize('include/images/gallery/img_' . $row[ 'id' ] . '.' . $row[ 'endung' ]);
    $breite = $size[ 0 ] + 5;
    $hoehe = $size[ 1 ] + 5;
    // vote zahlen
    if (isset($_GET[ 'doVote' ]) AND is_numeric($_GET[ 'doVote' ]) AND !isset($_SESSION[ 'galleryDoVote' ][ $row[ 'id' ] ])) {
        $_SESSION[ 'galleryDoVote' ][ $row[ 'id' ] ] = 'o';
        $row[ 'vote_wertung' ] = round((($row[ 'vote_wertung' ] * $row[ 'vote_klicks' ]) + $_GET[ 'doVote' ]) / ($row[ 'vote_klicks' ] + 1), 3);
        $row[ 'vote_klicks' ]++;
        db_query("UPDATE `prefix_gallery_imgs` SET `vote_wertung` = " . $row[ 'vote_wertung' ] . ", `vote_klicks` = " . $row[ 'vote_klicks' ] . " WHERE `id` = " . $row[ 'id' ]);
    }
    // klicks zaehlen
    if (!isset($_SESSION[ 'galleryDoKlick' ][ $row[ 'id' ] ])) {
        $_SESSION[ 'galleryDoKlick' ][ $row[ 'id' ] ] = 'o';
        db_query("UPDATE `prefix_gallery_imgs` SET `klicks` = `klicks` + 1 WHERE `id` = " . $row[ 'id' ]);
    }
    // page vor und ruck dev
    $next = $page + 1;
    $last = $page - 1;
    if ($next >= $anz) {
        $next = 0;
    }
    if ($last < 0) {
        $last = $anz - 1;
    }
    // diashow einstellungen
    $diashow_html = '';
    $diashow = $next . '=0&amp;diashow=start';
    if (isset($_GET[ 'diashow' ]) AND ($_GET[ 'diashow' ] == 'start' OR $_GET[ 'diashow' ] == 'shownext')) {
        $sek = 4;
        if (isset($_GET[ 'sek' ])) {
            $sek = $_GET[ 'sek' ];
        }
        $diashow_html = '<meta http-equiv="refresh" content="' . $sek . '; URL=index.php?gallery-show-' . $cid . '-p' . $next . '=0&amp;diashow=shownext&amp;sek=' . $sek . '">';
        $diashow = $page . '=0&amp;diashow=stop';
    }

	// Header definieren
	$header = '';
	$js = read_ext('include/includes/js/global', 'js');
	$css = read_ext('include/includes/css/global', 'css');
    // Dynamisch CSS + JS laden
    foreach ($css as $file) {$header .= "\n" . '<link rel="stylesheet" type="text/css" href="include/includes/css/global/' . $file . '" />';}
    $header .= "\n" . '<link rel="stylesheet" type="text/css" href="include/admin/templates/style.css">';
	foreach ($js as $file) {$header .= "\n" . '<script type="text/javascript" src="include/includes/js/global/' . $file . '"></script>';}
	$header .= "\n" . '<script type="text/javascript" src="include/includes/js/jquery/jquery.validate.js"></script>';
    $header .= "\n" . '<script type="text/javascript" src="include/includes/js/forms/gallery_show.js"></script>';
	// anzeigen
    $tpl = new tpl('gallery_show');
    $arr = array(
		'header' => $header,
        'cid' => $cid,
        'last' => $last,
        'next' => $next,
        'diashow' => $diashow,
        'diashow_html' => $diashow_html,
        'endung' => $row[ 'endung' ],
        'id' => $row[ 'id' ],
        'vote_wertung' => $row[ 'vote_wertung' ],
        'vote_klicks' => $row[ 'vote_klicks' ],
        'bildr' => $page,
        'besch' => unescape($row[ 'besch' ]),
        'breite' => $breite,
        'hoehe' => $hoehe
        );
    $tpl->set_ar_out($arr, 0);
    // kommentare
    if ($komsOK) {
        // eintragen
        if ((loggedin() or isset($_POST['name'])) and !empty($_POST['text']) and $antispam = chk_antispam('gallery')) {
            if (loggedin()) { $name = $_SESSION['authname']; $userid = $_SESSION['authid']; } 
			else { $name = escape($_POST['name'], 'string').' (Gast)'; $userid = 0; }
            $text = escape($_POST['text'], 'string');
            db_query("INSERT INTO prefix_koms (`name`,`userid`,`text`,`time`,`uid`,`cat`) VALUES ('" . $name . "'," . $userid . ",'" . $text . "','" . time() . "'," . $row['id'] . ",'GALLERYIMG')");
        }
        // loeschen
        if (isset($_GET['delete']) AND is_siteadmin()) {
            db_query("DELETE FROM prefix_koms WHERE id = " . escape($_GET['delete'], 'integer'));
        }
        // zeigen
        if (!empty($insertmsg)) {
            $insertmsg = '<span style="color:red;">' . $insertmsg . '</span><br />';
        }
		if (loggedin()) { 
			$uname = $_SESSION[ 'authname' ]; $readonly = 'readonly'; 
		} else { 
			$uname = ''; $readonly = ''; 
		}

        $tpl->set('insertmsg', $insertmsg);
        $tpl->set('uname', $uname);
        $tpl->set('readonly', $readonly);
        $tpl->set('antispam', get_antispam('gallery', 0));
        $tpl->out("koms_on");
        $erg = db_query("SELECT `id`, `name`, `userid`, `text`, `time` FROM `prefix_koms` WHERE `uid` = " . $row[ 'id' ] . " AND `cat` = 'GALLERYIMG' ORDER BY `id` DESC");
		$anz = db_num_rows($erg);
		if ($anz == 0) { echo $lang[ 'nocomments' ]; } else {
        while ($r = db_fetch_assoc($erg)) {
			if (is_admin()) { $del = ' <a href="index.php?gallery-show-' . $cid . '-p' . $page . '=0&amp;delete=' . $r[ 'id' ] . '"><img src="include/images/icons/del.gif" border="0" title="l&ouml;schen" alt="l&ouml;schen" /></a>'; }
			$r[ 'zahl' ] = $anz;
			$r[ 'avatar' ] = get_avatar($r[ 'userid' ]);
			$r[ 'time' ] = post_date($r[ 'time' ],1).$del;
            $r[ 'text' ] = bbcode($r[ 'text' ]);
            $tpl->set_ar_out($r, "koms_self");
			$anz--;
        } }
        $tpl->out("koms_off");
    }
} else {
    $cid = ($menu->get(1) ? escape($menu->get(1), 'integer') : 0);
    $erg = db_query("SELECT `cat`,`name` FROM `prefix_gallery_cats` WHERE `recht` >= {$_SESSION['authright']} AND `id` = " . $cid);
    $cname = 'Gallery';
    if (db_num_rows($erg) > 0) {
        $row = db_fetch_assoc($erg);
        $array = get_cats_array($row[ 'cat' ], '');
        $cname = $row[ 'name' ];
        if (!empty($array)) {
            $titelzw = get_cats_title($array);
            $namezw = get_cats_urls($array);
        } else {
            $titelzw = '';
            $namezw = '';
        }
        $cattitle = ':: ' . $titelzw . $row[ 'name' ];
        $catname = '<b> &raquo; </b>' . $namezw . $row[ 'name' ];
    } else {
        $cattitle = '';
        $catname = '';
    }
    $title = $allgAr[ 'title' ] . ' :: Gallery ' . $cattitle;
    $hmenu = '<a class="smalfont" href="?gallery">Gallery</a>' . $catname;
    $design = new design($title, $hmenu);
    $design->header();
    $tpl = new tpl('gallery');
    $erg = db_query("SELECT `id`,`name`,`besch` FROM `prefix_gallery_cats` WHERE `recht` >= {$_SESSION['authright']} AND `cat` = " . $cid . " ORDER BY `pos`");
    if (db_num_rows($erg) > 0) {
        $tpl->out(1);
        $class = 'Cnorm';
        while ($row = db_fetch_assoc($erg)) {
            $row[ 'gallery' ] = count_files($row[ 'id' ]);
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $row[ 'class' ] = $class;
            $tpl->set_ar_out($row, 2);
        }
        $tpl->out(3);
    }

    $limit = $img_per_site;
    $page = ($menu->getA(2) == 'p' ? escape($menu->getE(2), 'integer') : 1);
    $MPL = db_make_sites($page, "LEFT JOIN `prefix_gallery_cats` ON `prefix_gallery_imgs`.`cat` = `prefix_gallery_cats`.`id` WHERE `prefix_gallery_imgs`.`cat` = " . $cid . " AND (`recht` >= " . $_SESSION[ 'authright' ] . " OR `recht` IS NULL)", $limit, '?gallery-' . $cid, "gallery_imgs");
    $anfang = ($page - 1) * $limit;
    $erg = db_query("SELECT `prefix_gallery_imgs`.`id`,`prefix_gallery_imgs`.`cat`,`datei_name`,`endung`,`prefix_gallery_imgs`.`besch`,`klicks`,`vote_wertung`,`vote_klicks` FROM `prefix_gallery_imgs` LEFT JOIN `prefix_gallery_cats` ON `prefix_gallery_imgs`.`cat` = `prefix_gallery_cats`.`id` WHERE `prefix_gallery_imgs`.`cat` = " . $cid . " AND (`recht` >= " . $_SESSION[ 'authright' ] . " OR `recht` IS NULL) ORDER BY `id` ASC LIMIT " . $anfang . "," . $limit);
    if (db_num_rows($erg) > 0) {
        $tpl->set('imgperline', $allgAr[ 'gallery_imgs_per_line' ]);
        $tpl->set('cname', $cname);
        $tpl->set('breite', $allgAr[ 'gallery_normal_width' ] + 30);
        $tpl->set('MPL', $MPL);
        $tpl->out(4);
        $class = 'Cnorm';
        $i = 0;
        while ($row = db_fetch_assoc($erg)) {
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $row[ 'class' ] = $class;
            $row[ 'anz_koms' ] = db_result(db_query("SELECT COUNT(*) FROM `prefix_koms` WHERE `uid` = " . $row[ 'id' ] . " AND `cat` = 'GALLERYIMG'"), 0);
            $row[ 'besch' ] = unescape($row[ 'besch' ]);
            $row[ 'width' ] = round(100 / $img_per_line);
            $row[ 'bildr' ] = $i + (($page - 1) * $img_per_site);
            if ($i != 0 AND ($i % $img_per_line) == 0) {
                echo '</tr><tr>';
            }
            $tpl->set_ar_out($row, 5);
            $i++;
        }
        if ($i % $img_per_line != 0) {
            $anzahl = $img_per_line - ($i % $img_per_line);
            for ($x = 1; $x <= $anzahl; $x++) {
                echo '<td class="' . $class . '"></td>';
            }
        }
        $tpl->out(6);
    }
    $design->footer();
}

?>