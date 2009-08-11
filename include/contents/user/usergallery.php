<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

if ($allgAr['forum_usergallery'] == 0) {
    exit();
}

$uid = escape($menu->get(2), 'integer');
$img_per_site = $allgAr['gallery_imgs_per_site'];
$img_per_line = $allgAr['gallery_imgs_per_line'];
// zeige alle gallery
if (empty($uid)) {
    $title = $allgAr['title'] . ' :: Users :: Gallery';
    $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b>Gallery' . $extented_forum_menu_sufix;
    $design = new design ($title , $hmenu, 1);
    $design->header();
    $i = 0;
    $class = 'Cmite';
    $x = '';
    if (loggedin()) {
        $x .= '<a href="index.php?user-usergallery-' . $_SESSION['authid'] . '">Meine Gallery</a><br /><br />';
    }
    $erg = db_query("SELECT uid, prefix_user.name as uname, COUNT(*) as anz FROM prefix_usergallery LEFT JOIN prefix_user ON prefix_usergallery.uid = prefix_user.id GROUP BY uid, uname ORDER BY anz DESC");
    while ($r = db_fetch_assoc($erg)) {
        $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
        $x .= '<div class="' . $class . '" style="float: left; padding: 5px;"><a href="index.php?user-usergallery-' . $r['uid'] . '">' . $r['uname'] . '</a><br /><span class="smalfont">Anzahl Bilder: ' . $r['anz'] . '</span></a></div>';
        if ($i != 0 AND ($i % 5) == 0) {
            $x .= '<br />';
        }
    }
    $tpl = new tpl ('user/gallery');
    $tpl->set_out('x', $x, 4);
    $design->footer();
    exit();
}
// user gallery zeigen
$uname = db_result(db_query("SELECT name FROM prefix_user WHERE id = " . $uid), 0, 0);

$title = $allgAr['title'] . ' :: Users :: Gallery';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?user">Users</a><b> &raquo; </b><a class="smalfont" href="?user-usergallery">Gallery</a><b> &raquo; </b>von ' . $uname . $extented_forum_menu_sufix;
$header = Array ('jquery/lightbox.js','jquery/lightbox.css');
$design = new design ($title , $hmenu, 1);
$design->header( $header );

$tpl = new tpl ('user/gallery');
$tpl->set('uid', $uid);
$tpl->set('uname', $uname);
// bild loeschen...
if ($menu->getA(4) == 'd' AND is_numeric($menu->getE(4)) AND loggedin() AND (is_siteadmin() OR $uid == $_SESSION['authid'])) {
    $delid = escape($menu->getE(4), 'integer');
    $x = @db_result(db_query("SELECT endung FROM prefix_usergallery WHERE uid = " . $uid . " AND id = " . $delid), 0, 0);
    if (!empty($x)) {
        @unlink ('include/images/usergallery/img_thumb_' . $delid . '.' . $x);
        @unlink ('include/images/usergallery/img_' . $delid . '.' . $x);
        @db_query("DELETE FROM prefix_usergallery WHERE uid = " . $uid . " AND id = " . $delid);
    }
}
// bild hochladen
if (!empty($_FILES['file']['name']) AND is_writeable('include/images/usergallery') AND loggedin() AND $uid == $_SESSION['authid'] AND substr (ic_mime_type($_FILES['file']['tmp_name']) , 0 , 6) == 'image/') {
    require_once('include/includes/func/gallery.php');
    $size = @getimagesize ($_FILES['file']['tmp_name']);
    $fende = preg_replace("/.+\.([a-zA-Z]+)$/", "\\1", $_FILES['file']['name']);
    $fende = strtolower($fende);
    if (!empty($_FILES['file']['name']) AND $size[0] > 10 AND $size[1] > 10 AND ($size[2] == 2 OR $size[2] == 3 OR $size[2] == 1) AND ($fende == 'gif' OR $fende == 'jpg' OR $fende == 'jpeg' OR $fende == 'png')) {
        $name = $_FILES['file']['name'];
        $tmp = explode('.', $name);
        $tm1 = count($tmp) - 1;
        $endung = escape($tmp[$tm1], 'string');
        unset($tmp[$tm1]);
        $name = escape(implode('', $tmp), 'string');
        $besch = escape($_POST['text'], 'string');
        $id = db_result(db_query("SHOW TABLE STATUS FROM `" . DBDATE . "` LIKE 'prefix_usergallery'"), 0, 'Auto_increment');
        $bild_url = 'include/images/usergallery/img_' . $id . '.' . $endung;
        if (@move_uploaded_file ($_FILES['file']['tmp_name'], $bild_url)) {
            @chmod($bild_url, 0777);
            db_query("INSERT INTO prefix_usergallery (uid,name,endung,besch) VALUES (" . $uid . ",'" . $name . "','" . $endung . "','" . $besch . "')");
            $bild_thumb = 'include/images/usergallery/img_thumb_' . $id . '.' . $endung;
            create_thumb ($bild_url, $bild_thumb, $allgAr['gallery_preview_width']);
            @chmod($bild_thumb, 0777);
            echo '<b>Datei ' . $name . '.' . $endung . ' erfolgreich hochgeladen</b><br />';
            $page = $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]);
            echo 'Bildlink: <a target="_blank" href="http://' . $page . '/' . $bild_url . '">http://' . $page . '/' . $bild_url . '</a><br />';
            echo 'Oder klein: <a target="_blank" href="http://' . $page . '/' . $bild_thumb . '">http://' . $page . '/' . $bild_thumb . '</a><br /><br />';
        }
    }
}
// bilder abfragen
$limit = $img_per_site;
$page = ($menu->getA(3) == 'p' ? $menu->getE(3) : 1);
$MPL = db_make_sites ($page , '' , $limit , 'index.php?user-usergallery-' . $uid , "usergallery WHERE uid = " . $uid);
$anfang = ($page - 1) * $limit;
$erg = db_query("SELECT name, besch, endung, id FROM prefix_usergallery WHERE uid = " . $uid . " ORDER BY id DESC LIMIT " . $anfang . "," . $limit);

$tpl->set('imgperline', $allgAr['gallery_imgs_per_line']);
$tpl->set('MPL', $MPL);
$tpl->out(0);
$class = 'Cnorm';
$i = 0;
if (db_num_rows($erg) > 0) {
    while ($row = db_fetch_assoc($erg)) {
        $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
        $row['class'] = $class;
		$row['besch'] = unescape($row['besch']);
        if (loggedin() AND (is_siteadmin() OR $uid == $_SESSION['authid'])) {
            $row['besch'] = '<a href=\'index.php?user-usergallery-' . $uid . '-p' . $page . '-d' . $row['id'] . '\'><img src=\'include/images/icons/del.gif\' border=\'0\' alt=\'l&ouml;schen\' title=\'l&ouml;schen\' /></a> '.$row['besch'];
        }
        $row['width'] = round(100 / $img_per_line);
        if ($i != 0 AND ($i % $img_per_line) == 0) {
            echo '</tr><tr>';
        }
        $tpl->set_ar_out($row, 1);
        $i++;
    }
    if ($i % $img_per_line != 0) {
        $anzahl = $img_per_line - ($i % $img_per_line);
        for($x = 1;$x <= $anzahl;$x++) {
            echo '<td class="' . $class . '"></td>';
        }
    }
}
$tpl->out(2);
// bilder abfragen
// bild hochladen
if (is_writeable('include/images/usergallery') AND loggedin() AND $uid == $_SESSION['authid']) {
    $tpl->out(3);
}

$design->footer();

?>