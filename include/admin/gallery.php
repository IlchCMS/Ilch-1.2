<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

require_once('include/includes/func/gallery.php');

function gallery_admin_showcats($id, $stufe) {
    global $menu;
    $q = "SELECT * FROM `prefix_gallery_cats` WHERE `cat` = " . $id . " ORDER BY `pos`";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            if ($menu->getE('S') == $row->id) {
                $row->name = '<strong>' . $row->name . '</strong>';
            }
            echo '<tr class="Cmite"><td>' . $stufe . '- <a href="?gallery-S' . $row->id . '">' . $row->name . '</a></td>';
            echo '<td><a href="javascript:uploadImages(' . $row->id . ')"><img src="include/images/icons/upload.png" title="Bilder in diese Kategorie hochladen" alt="Bilder in diese Kategorie hochladen" border="0"></td>';
            echo '<td><a href="javascript:reloadImages(' . $row->id . ')"><img src="include/images/icons/reload.png" title="Bilder in diese Kategorie erneuern / einlesen" alt="Bilder in diese Kategorie erneuern / einlesen" border="0"></a></td>';
            echo '<td><a href="admin.php?gallery-E' . $row->id . '#edit"><img src="include/images/icons/edit.png" border="0" alt="&auml;ndern" title="&auml;ndern"></a></td>';
            echo '<td><a href="javascript:Kdel(' . $row->id . ')"><img src="include/images/icons/del.png" border="0" alt="l&ouml;schen" title="l&ouml;schen"></a></td>';
            echo '<td><a href="admin.php?gallery-M' . $row->id . '-o' . $row->pos . '"><img src="include/images/icons/pfeilo.png" border="0" title="hoch" alt="hoch"></a></td>';
            echo '<td><a href="admin.php?gallery-M' . $row->id . '-u' . $row->pos . '"><img src="include/images/icons/pfeilu.png" border="0" title="runter" alt="runter"></a></td></tr>';
            gallery_admin_showcats($row->id, $stufe . ' &nbsp; &nbsp;');
        }
    }
}

function gallery_admin_selectcats($id, $stufe, &$output, $sel = 0) {
    $q = "SELECT * FROM `prefix_gallery_cats` WHERE `cat` = " . $id . " ORDER BY `pos`";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            $output .= '<option value="' . $row->id . '"' . ($sel == $row->id ? ' selected="selected"' : '') . '>' . $stufe . ' ' . $row->name . '</option>';
            gallery_admin_selectcats($row->id, $stufe . '&raquo;', $output, $sel);
        }
    }
}
// Bilder einer Kategorie erneuern oder einlesen
if ($menu->get(1) == 'reloadImages') {
    $msg = '';
    if (isset($_POST[ 'do_aktion' ]) AND $_POST[ 'do_aktion' ] == 'yes') {
        // wenn keine aktion gewaehlt wurde
        if (empty($_POST[ 'aktion' ])) {
            $msg = 'Bitte eine Aktion ausw&auml;hlen<br />';
            // aktion alle bilder eines ordners einlesen
        } elseif ($_POST[ 'aktion' ] == 'ins') {
            if (is_dir($_POST[ 'dir' ])) {
                $msg .= 'Bilder aus Ordner ' . $_POST[ 'dir' ] . ' eingefugt<br />';
                $o = opendir($_POST[ 'dir' ]);
                while ($f = readdir($o)) {
                    if ($f == '.' OR $f == '..') {
                        continue;
                    }
                    $imgpath = $_POST[ 'dir' ] . '/' . $f;
                    $size = getimagesize($imgpath);
                    if ($size[ 2 ] == 2 OR $size[ 2 ] == 3) {
                        $name = basename($imgpath);
                        $tmp = explode('.', $name);
                        $tm1 = count($tmp) - 1;
                        $endung = $tmp[ $tm1 ];
                        unset($tmp[ $tm1 ]);
                        $name = implode('', $tmp);
                        $id = db_result(db_query("SHOW TABLE STATUS FROM `" . DBDATE . "` LIKE 'prefix_gallery_imgs'"), 0, 'Auto_increment');
                        $bild_url = 'include/images/gallery/img_' . $id . '.' . $endung;
                        if (@copy($imgpath, $bild_url)) {
                            db_query("INSERT INTO `prefix_gallery_imgs` (`cat`,`datei_name`,`endung`,`besch`) VALUES (" . $menu->get(2) . ",'" . $name . "','" . $endung . "','')");
                            $msg .= '- ' . $imgpath . '<br />';
                            $bild_thumb = 'include/images/gallery/img_thumb_' . $id . '.' . $endung;
                            $bild_norm = 'include/images/gallery/img_norm_' . $id . '.' . $endung;
                            create_thumb($bild_url, $bild_thumb, $allgAr[ 'gallery_preview_width' ]);
                            create_thumb($bild_url, $bild_norm, $allgAr[ 'gallery_normal_width' ]);
                        }
                    }
                }
            } else {
                $msg = 'Konnte den Ordner ' . $_POST[ 'dir' ] . ' nicht finden<br />';
            }
            // aktion alle bilder erneuern mit oder ohne ueberschreiben
        } elseif ($_POST[ 'aktion' ] == 'alle' OR $_POST[ 'aktion' ] == 'alle_no') {
            $erg = db_query("SELECT id,endung FROM prefix_gallery_imgs WHERE cat = " . $menu->get(2));
            while ($r = db_fetch_assoc($erg)) {
                $endung = $r[ 'endung' ];
                $id = $r[ 'id' ];
                $bild_url = 'include/images/gallery/img_' . $id . '.' . $endung;
                if (file_exists($bild_url)) {
                    $bild_thumb = 'include/images/gallery/img_thumb_' . $id . '.' . $endung;
                    $bild_norm = 'include/images/gallery/img_norm_' . $id . '.' . $endung;
                    if ($_POST[ 'aktion' ] == 'alle' OR !file_exists($bild_thumb)) {
                        create_thumb($bild_url, $bild_thumb, $allgAr[ 'gallery_preview_width' ]);
                    }
                    if ($_POST[ 'aktion' ] == 'alle' OR !file_exists($bild_norm)) {
                        create_thumb($bild_url, $bild_norm, $allgAr[ 'gallery_normal_width' ]);
                    }
                }
            }

            if ($_POST[ 'aktion' ] == 'alle') {
                $msg = 'Alle Bilder erneuert';
            } else {
                $msg = 'Alle Bilder erneuert, nicht &uuml;berschrieben';
            }
        }
    }
    // anzeigen
    $tpl = new tpl('gallery/images_reload', 1);
    $cname = 'keine Kategorie';
    if ($menu->get(2) > 0) {
        $cname = db_result(db_query("SELECT `name` FROM `prefix_gallery_cats` WHERE `id` = " . $menu->get(2)), 0, 0);
    }
    $tpl->set('cat', $menu->get(2));
    $tpl->set('cname', $cname);
    $tpl->set('msg', $msg);
    $tpl->out(0);
    exit();
}
// Bilder in eine Kategorie hochladen
if ($menu->get(1) == 'uploadImages') {
    $msg = '';
    if (isset($_POST[ 'hochladen' ]) AND $_POST[ 'hochladen' ] == 'yes') {
        foreach ($_FILES[ 'file' ][ 'name' ] AS $k => $v) {
            if (!empty($_FILES[ 'file' ][ 'name' ][ $k ])) {
                $name = $_FILES[ 'file' ][ 'name' ][ $k ];
                $tmp = explode('.', $name);
                $tm1 = count($tmp) - 1;
                $endung = $tmp[ $tm1 ];
                unset($tmp[ $tm1 ]);
                $name = implode('', $tmp);
                $besch = escape($_POST[ 'besch' ][ $k ], 'string');
                $id = db_result(db_query("SHOW TABLE STATUS FROM `" . DBDATE . "` LIKE 'prefix_gallery_imgs'"), 0, 'Auto_increment');
                $bild_url = 'include/images/gallery/img_' . $id . '.' . $endung;
                if (@move_uploaded_file($_FILES[ 'file' ][ 'tmp_name' ][ $k ], $bild_url)) {
                    @chmod($bild_url, 0777);
                    db_query("INSERT INTO `prefix_gallery_imgs` (`cat`,`datei_name`,`endung`,`besch`) VALUES (" . $menu->get(2) . ",'" . $name . "','" . $endung . "','" . $besch . "')");
                    $msg .= 'Datei ' . $name . '.' . $endung . ' erfolgreich hochgeladen<br />';
                    $bild_thumb = 'include/images/gallery/img_thumb_' . $id . '.' . $endung;
                    $bild_norm = 'include/images/gallery/img_norm_' . $id . '.' . $endung;
                    create_thumb($bild_url, $bild_thumb, $allgAr[ 'gallery_preview_width' ]);
                    @chmod($bild_thumb, 0777);
                    create_thumb($bild_url, $bild_norm, $allgAr[ 'gallery_normal_width' ]);
                    @chmod($bild_norm, 0777);
                } else {
                    $msg .= 'Datei ' . $name . '.' . $endung . ' konnte nicht hochgeladen werden<br />';
                }
            }
        }
    }
    // bilder hochladen
    $anzb = 5;
    if (isset($_GET[ 'anzb' ]) AND is_numeric($_GET[ 'anzb' ])) {
        $anzb = $_GET[ 'anzb' ];
    }
    $tpl = new tpl('gallery/images_upload', 1);
    $tpl->set('cat', $menu->get(2));
    $tpl->set('msg', $msg);
    $tpl->out(0);
    $class = 'Cmite';
    for ($i = 1; $i <= $anzb; $i++) {
        $tpl->set('class', ($class == 'Cmite' ? 'Cnorm' : 'Cmite'));
        $tpl->out(1);
    }
    $tpl->out(2);
    exit();
}
// Kategorie löschen
if ($menu->getA(1) == 'D') {
    $tpl = new tpl('gallery/delkat', 1);
    $tpl->out(0);
    // Kategorie und alle Bilder löschen
    if ($menu->get(2) == 'delall') {
        $r = db_fetch_assoc(db_query("SELECT `id`, `pos`, `cat` FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'"));
        db_query("DELETE FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'");
        db_query("UPDATE `prefix_gallery_cats` SET `pos` = `pos` - 1 WHERE `pos` > " . $r[ 'pos' ] . " AND `cat` = " . $r[ 'cat' ]);
        $sql = db_query("SELECT * FROM `prefix_gallery_imgs` WHERE `cat` = '{$r['id']}'");
        while ($r2 = db_fetch_assoc($sql)) {
            @unlink('include/images/gallery/img_' . $r2[ 'id' ] . '.' . $r2[ 'endung' ]);
            @unlink('include/images/gallery/img_thumb_' . $r2[ 'id' ] . '.' . $r2[ 'endung' ]);
            @unlink('include/images/gallery/img_norm_' . $r2[ 'id' ] . '.' . $r2[ 'endung' ]);
        }
        db_query("DELETE FROM `prefix_gallery_imgs` WHERE `cat` = '" . $r[ 'id' ] . "'");
        echo 'Kategorie und Bilder gel&ouml;scht<br />';
        $tpl->out(2);
    } elseif ($menu->get(2) == 'delkat') {
        $r = db_fetch_assoc(db_query("SELECT `id`, `pos`, `cat` FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'"));
        db_query("DELETE FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'");
        db_query("UPDATE `prefix_gallery_cats` SET `pos` = `pos` - 1 WHERE `pos` > " . $r[ 'pos' ] . " AND `cat` = " . $r[ 'cat' ]);
        db_query("DELETE FROM `prefix_gallery_imgs` WHERE `cat` = '" . $r[ 'id' ] . "'");
        echo 'Nur Kategorie gel&ouml;scht, Bilder noch auf dem FTP<br />';
        $tpl->out(2);
    } elseif (isset($_POST[ 'move' ]) AND $_POST[ 'cat' ] != $menu->getE(1)) {
        $_POST[ 'cat' ] = escape($_POST[ 'cat' ], 'integer');
        $r = db_fetch_assoc(db_query("SELECT `id`, `pos`, `cat` FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'"));
        db_query("DELETE FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'");
        db_query("UPDATE `prefix_gallery_cats` SET `pos` = `pos` - 1 WHERE `pos` > " . $r[ 'pos' ] . " AND `cat` = " . $r[ 'cat' ]);
        db_query("UPDATE `prefix_gallery_imgs` SET `cat` = '{$_POST['cat']}' WHERE `cat` = '" . $r[ 'id' ] . "'");
        echo 'Bilder in Kategorie "' . @db_result(db_query("SELECT `name` FROM `prefix_gallery_cats` WHERE `id` = '{$_POST['cat']}'"), 0) . '" verschoben und alte Kategorie gel&ouml;scht.<br />';
        $tpl->out(2);
    } else {
        $row = array(
            'id' => $menu->getE(1)
            );
        gallery_admin_selectcats('0', '', $row[ 'cats' ], $row[ 'cats' ]);
        $row[ 'cats' ] = '<option value="0">Keine</option>' . $row[ 'cats' ];
        $tpl->set_ar_out($row, 1);
    }
    $tpl->out(3);
    exit();
}
// Bilder verschieben
if (isset($_POST[ 'movepics' ])) {
    if (count($_POST[ 'img' ]) > 0) {
        $pics = implode(',', $_POST[ 'img' ]);
        $cat = escape($_POST[ 'movecat' ], 'integer');
        db_query("UPDATE `prefix_gallery_imgs` SET `cat` = " . $cat . " WHERE `id` IN (" . $pics . ");");
        $menu->set_url(1, 'S' . $cat);
    }
}

$design = new design('Ilch Admin-Control-Panel :: Gallery', '', 2);
$design->header();
$tpl = new tpl('gallery/gallery', 1);
// Bild loeschen
if ($menu->getA(1) == 'd') {
    $id = $menu->getE(1);
    $row = db_fetch_assoc(db_query("SELECT `endung`,`cat` FROM `prefix_gallery_imgs` WHERE `id` = " . $id));
    $endung = $row[ 'endung' ];
    @unlink('include/images/gallery/img_' . $id . '.' . $endung);
    @unlink('include/images/gallery/img_thumb_' . $id . '.' . $endung);
    @unlink('include/images/gallery/img_norm_' . $id . '.' . $endung);
    db_query("DELETE FROM prefix_gallery_imgs WHERE id = " . $id);
    $azk = $row[ 'cat' ];
}
// Bild Beschreibung aendern
if ($menu->getA(1) == 'e') {
    $id = $menu->getE(1);
    $besch = escape($_REQUEST[ 'besch' ], 'string');
    $row = db_fetch_assoc(db_query("SELECT `cat` FROM `prefix_gallery_imgs` WHERE `id` = " . $id));
    db_query("UPDATE `prefix_gallery_imgs` SET `besch` = '" . $besch . "' WHERE `id` = " . $id);
    $azk = $row[ 'cat' ];
}
// Bild erneuern
if ($menu->getA(1) == 'r') {
    $id = $menu->getE(1);
    $row = db_fetch_assoc(db_query("SELECT `endung`,`cat` FROM `prefix_gallery_imgs` WHERE `id` = " . $id));
    $endung = $row[ 'endung' ];
    $bild_url = 'include/images/gallery/img_' . $id . '.' . $endung;
    if (file_exists($bild_url)) {
        $bild_thumb = 'include/images/gallery/img_thumb_' . $id . '.' . $endung;
        $bild_norm = 'include/images/gallery/img_norm_' . $id . '.' . $endung;
        create_thumb($bild_url, $bild_thumb, $allgAr[ 'gallery_preview_width' ]);
        create_thumb($bild_url, $bild_norm, $allgAr[ 'gallery_normal_width' ]);
    }
    $azk = $row[ 'cat' ];
}

if ($menu->getA(1) == 'M') {
    $pos = $menu->getE(2);
    $id = $menu->getE(1);
    $cat = db_result(db_query("SELECT `cat` FROM `prefix_gallery_cats` WHERE `id` = " . $id), 0);
    $nps = ($menu->getA(2) == 'u' ? $pos + 1 : $pos - 1);
    $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_gallery_cats` WHERE `cat` = " . $cat), 0);

    if ($nps < 0) {
        db_query("UPDATE `prefix_gallery_cats` SET `pos` = " . $anz . " WHERE `id` = " . $id);
        db_query("UPDATE `prefix_gallery_cats` SET `pos`  = `pos` -1 WHERE `cat` = " . $cat);
    }
    if ($nps >= $anz) {
        db_query("UPDATE `prefix_gallery_cats` SET `pos`  = -1 WHERE `id` = " . $id);
        db_query("UPDATE `prefix_gallery_cats` SET `pos`  = `pos` +1 WHERE `cat` = " . $cat);
    }

    if ($nps < $anz AND $nps >= 0) {
        db_query("UPDATE `prefix_gallery_cats` SET `pos`  = " . $pos . " WHERE `pos` = " . $nps . " AND `cat` = " . $cat);
        db_query("UPDATE `prefix_gallery_cats` SET `pos`  = " . $nps . " WHERE `id` = " . $id);
    }
}
// kategorie eintrage speichern oder aendern.
if (isset($_POST[ 'Csub' ])) {
    if (empty($_POST[ 'Ccat' ])) {
        $_POST[ 'Ccat' ] = 0;
    }
    if (empty($_POST[ 'Cpkey' ])) {
        $nextpos = db_result(db_query("SELECT COUNT(*) FROM `prefix_gallery_cats` WHERE `cat` = " . $_POST[ 'Ccat' ]), 0, 0);
        db_query("INSERT INTO `prefix_gallery_cats` (`cat`,`name`,`besch`,`pos`,`recht`) VALUES (" . $_POST[ 'Ccat' ] . ",'" . $_POST[ 'Cname' ] . "','" . $_POST[ 'Cdesc' ] . "','" . $nextpos . "'," . $_POST[ 'Crecht' ] . ")");
    } else {
        $r = db_fetch_assoc(db_query("SELECT `cat`, `pos` FROM `prefix_gallery_cats` WHERE `id` = " . $_POST[ 'Cpkey' ]));

        $bool = true;
        $tc = $_POST[ 'Ccat' ];
        while ($tc > 0) {
            if ($tc == $_POST[ 'Cpkey' ]) {
                $bool = false;
            }
            $tc = @db_result(db_query("SELECT `cat` FROM `prefix_gallery_cats` WHERE `id` = " . $tc));
        }
        if ($bool) {
            $epos = $r[ 'pos' ];
            $akc = $r[ 'cat' ];
            $npos = $epos;
            if ($akc != $_POST[ 'Ccat' ]) {
                $npos = db_result(db_query("SELECT COUNT(*) FROM `prefix_gallery_cats` WHERE `cat` = " . $_POST[ 'Ccat' ]), 0, 0);
            }
            db_query("UPDATE `prefix_gallery_cats` SET `cat` = '" . $_POST[ 'Ccat' ] . "',`pos`=" . $npos . ",`recht`=" . $_POST[ 'Crecht' ] . ",`name` = '" . $_POST[ 'Cname' ] . "',`besch` = '" . $_POST[ 'Cdesc' ] . "' WHERE `id` = '" . $_POST[ 'Cpkey' ] . "'");
            if ($akc != $_POST[ 'Ccat' ]) {
                db_query("UPDATE `prefix_gallery_cats` SET `pos`  = `pos` - 1 WHERE `pos` > " . $epos . " AND `cat` = " . $akc);
            }
        }
    }
    $azk = $_POST[ 'Ccat' ];
}

if (!isset($azk)) {
    $azk = 0;
    if ($menu->getA(1) == 'S' OR $menu->getA(1) == 'E') {
        $azk = $menu->getE(1);
    }
}

$tpl->out(0);
$class = 0;
$abf = "SELECT `id`,`besch`,`datei_name`,`endung` FROM `prefix_gallery_imgs` WHERE `cat` = " . $azk;
$erg = db_query($abf);
$i = 0;
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row[ 'class' ] = $class;
    if ($i != 0 AND ($i % $allgAr[ 'gallery_imgs_per_line' ]) == 0) {
        echo '</tr><tr>';
    }
    $tpl->set_ar_out($row, 1);
    $i++;
}
// links
$tpl->out(2);
// cat
if ($menu->getA(1) == 'E') {
    $erg = db_query("SELECT `id`,`cat` as `Ccat`, `recht` as `Crecht`, `name` as `Cname`,`pos` as `Cpos`,`besch` as `Cdesc` FROM `prefix_gallery_cats` WHERE `id` = '" . $menu->getE(1) . "'");
    $_Cilch = db_fetch_assoc($erg);
    $_Cilch[ 'Cpkey' ] = $menu->getE(1);
} else {
    $_Cilch = array(
        'Ccat' => '',
        'Cpkey' => '',
        'Cpos' => '',
        'Cname' => '',
        'Crecht' => '',
        'Cdesc' => ''
        );
}
// $_Cilch['Crecht'] = arlistee($_Cilch['Crecht'],getFuerAr());
gallery_admin_selectcats('0', '', $_Cilch[ 'Ccat' ], $_Cilch[ 'Ccat' ]);
$_Cilch[ 'Ccat' ] = '<option value="0">Keine</option>' . $_Cilch[ 'Ccat' ];
$_Cilch[ 'Crecht' ] = dblistee($_Cilch[ 'Crecht' ], "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` DESC");
gallery_admin_showcats(0, '');

$tpl->set_ar($_Cilch);
$tpl->out(3);

$design->footer();

?>