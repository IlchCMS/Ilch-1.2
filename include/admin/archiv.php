<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

if (!empty($_REQUEST['f']) and substr($_REQUEST['f'], 0, 23) != 'include/downs/downloads') {
    die('dont try to hack');
}

function get_upload_linked ($v) {
    $l = '';
    $i = 1;
    $erg = db_query("SELECT `id`, `name` FROM `prefix_downloads` WHERE `url` = '" . $v . "'");
    if (@db_num_rows($erg) > 0) {
        while ($r = db_fetch_assoc($erg)) {
            $l .= '[<a href="javascript:showDDetails(' . $r['id'] . ')" title="' . $r['name'] . '">' . $i . '</a>], ';
            $i++;
        }
    }
    return ($l);
}

function uploadMoveFile_getdirlist ($f = 'include/downs/downloads', $list = '<option value="/">/</option>') {
    $o = opendir($f);
    while ($v = readdir($o)) {
        if ($v == '.' OR $v == '..' OR !is_dir($f . '/' . $v)) {
            continue;
        }
        $dirn = str_replace('include/downs/downloads/', '', $f . '/' . $v);
        $list .= '<option value="' . $dirn . '">' . $dirn . '</option>';
        $list = uploadMoveFile_getdirlist ($f . '/' . $v , $list);
    }
    return ($list);
}

function upload_getdirlist ($f = 'include/downs/downloads', $list = '') {
    $o = opendir($f);
    while ($v = readdir($o)) {
        if ($v == '.' OR $v == '..' OR !is_dir($f . '/' . $v)) {
            continue;
        }
        $dirn = str_replace('include/downs/downloads/', '', $f . '/' . $v);
        $list .= '<tr><td class="Cnorm"><a href="?archiv-downloads-upload=0&f=' . $f . '/' . $v . '">' . $dirn . '</a></td></tr>';
        $list = upload_getdirlist ($f . '/' . $v , $list);
    }
    return ($list);
}

function get_downloads_ar ($ar = null, $f = null) {
    if (is_null ($ar)) {
        $ar = array();
    }
    if (is_null ($f)) {
        $f = 'include/downs/downloads';
    }

    $o = opendir($f);
    while ($v = readdir($o)) {
        if ($v != '.' AND $v != '..') {
            if (is_dir ($f . '/' . $v)) {
                $ar = get_downloads_ar ($ar, $f . '/' . $v);
            } else {
                $ar[$f . '/' . $v] = $v;
            }
        }
    }
    closedir($o);
    return ($ar);
}

function archiv_downs_admin_showcats ($id , $stufe) {
    $q = "SELECT `id`,`name`,`pos`,`cat` FROM `prefix_downcats` WHERE `ca`t = " . $id . " ORDER BY `pos`";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            echo '<tr class="Cmite"><td>' . $stufe . '- <a href="admin.php?archiv-downloads-S' . $row->id . '">' . $row->name . '</a></td>';
            echo '<td align="center"><a href="admin.php?archiv-downloads-E' . $row->id . '#edit"><img src="include/images/icons/edit.gif" border="0"></a></td>';
            echo '<td align="center"><a href="javascript:Kdel(' . $row->id . ')"><img src="include/images/icons/del.gif" border="0"></a></td>';
            echo '<td align="center"><a href="admin.php?archiv-downloads-S' . $row->id . '-O' . $row->id . '-' . $row->pos . '-' . $row->cat . '"><img src="include/images/icons/pfeilo.gif" border="0"></a></td>';
            echo '<td align="center"><a href="admin.php?archiv-downloads-S' . $row->id . '-U' . $row->id . '-' . $row->pos . '-' . $row->cat . '"><img src="include/images/icons/pfeilu.gif" border="0"></a></td></tr>';
            archiv_downs_admin_showcats($row->id, $stufe . ' &nbsp; &nbsp;');
        }
    }
}

function archiv_downs_admin_selectcats ($id, $stufe, &$output, $sel = 0) {
    $q = "SELECT `id`,`name`,`pos`,`cat` FROM `prefix_downcats` WHERE `cat` = " . $id . " ORDER BY `pos`";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            $output .= '<option value="' . $row->id . '"' . ($sel == $row->id?' selected="selected"':'') . '>' . $stufe . ' ' . $row->name . '</option>';
            archiv_downs_admin_selectcats($row->id, $stufe . '&raquo;', $output, $sel);
        }
    }
}

function archiv_links_admin_showcats ($id , $stufe) {
    $q = "SELECT * FROM `prefix_linkcats` WHERE `cat` = " . $id . " ORDER BY `pos`";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            echo '<tr class="Cmite"><td>' . $stufe . '- <a href="admin.php?archiv-links-S' . $row->id . '">' . $row->name . '</a></td>';
            echo '<td align="center"><a href="?archiv-links-E' . $row->id . '#edit"><img src="include/images/icons/edit.gif" border="0"></a></td>';
            echo '<td align="center"><a href="javascript:Kdel(' . $row->id . ')"><img src="include/images/icons/del.gif" border="0"></a></td>';
            echo '<td align="center"><a href="admin.php?archiv-links-S' . $row->id . '-O' . $row->id . '-' . $row->pos . '-' . $row->cat . '"><img src="include/images/icons/pfeilo.gif" border="0"></a></td>';
            echo '<td align="center"><a href="admin.php?archiv-links-S' . $row->id . '-U' . $row->id . '-' . $row->pos . '-' . $row->cat . '"><img src="include/images/icons/pfeilu.gif" border="0"></a></td></tr>';
            archiv_links_admin_showcats($row->id, $stufe . ' &nbsp; &nbsp;');
        }
    }
}

function archiv_links_admin_selectcats ($id, $stufe, &$output, $sel = 0) {
    $q = "SELECT * FROM `prefix_linkcats` WHERE `cat` = " . $id . " ORDER BY `pos`";
    $erg = db_query($q);
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            $output .= '<option value="' . $row->id . '"' . ($sel == $row->id?' selected="selected"':'') . '>' . $stufe . ' ' . $row->name . '</option>';
            archiv_links_admin_selectcats($row->id, $stufe . '&raquo;', $output, $sel);
        }
    }
}

$um = $menu->get(1);

switch ($um) {
    case 'downloads' :
        if ($menu->get(2) == 'upload') {
            $msg = '';
            // file rechte pruefen
            if (!is_writeable ('include/downs/downloads')) {
                $msg = '<b>Bevor du hier eine Datei hochladen/verwalten kannst muss der Ordner include/downs/<b>downloads</b>/ erstellt werden und er muss Schreibrechte ( chmod 777 ) erhalten !!! Wenn das geschehen ist einfach nochmal hier auf aktualisieren klicken</b>';
            }
            // file hochladen
            if (isset ($_FILES['file']['name'])) {
                $pathinfo = pathinfo($_FILES['file']['name']);
                if (substr($pathinfo['extension'], 0, 3) == 'php') {
                    $msg = '<font color="#FF0000">Es können keine PHP Dateien hochgeladen werden.</font><br />';
                } elseif (move_uploaded_file ($_FILES['file']['tmp_name'], $_REQUEST['f'] . '/' . $_FILES['file']['name'])) {
                    @chmod($_REQUEST['f'] . '/' . $_FILES['file']['name'], 0777);
                    $msg = 'Datei (' . $_FILES['file']['name'] . ' ) <font color="#00FF00">erfolgreich hochgeladen</font><br />';
                } else {
                    $msg = 'Datei ( ' . $_FILES['file']['name'] . ' ) <font color="#FF0000">nicht erfolgreich hochgeladen</font><br />';
                }
            }
            // datei loeschen
            if (isset ($_REQUEST['d'])) {
                unlink ($_REQUEST['f'] . '/' . $_REQUEST['d']);
            }
            // datei umbenennen
            if (isset ($_REQUEST['r'])) {
                $pathinfo = pathinfo($_REQUEST['r']);
                if (substr($pathinfo['extension'], 0, 3) == 'php') {
                    $msg = '<font color="#FF0000">Es können keine PHP Dateien erzeugt werden.</font><br />';
                } elseif (@rename ($_REQUEST['f'] . '/' . $_REQUEST['v'], $_REQUEST['f'] . '/' . $_REQUEST['r'])) {
                    db_query("UPDATE prefix_downloads SET url = '" . $_REQUEST['f'] . '/' . $_REQUEST['r'] . "' WHERE url = '" . $_REQUEST['f'] . '/' . $_REQUEST['v'] . "'");
                    db_query("UPDATE prefix_downloads SET surl = '" . $_REQUEST['f'] . '/' . $_REQUEST['r'] . "' WHERE surl = '" . $_REQUEST['f'] . '/' . $_REQUEST['v'] . "'");
                    db_query("UPDATE prefix_downloads SET ssurl = '" . $_REQUEST['f'] . '/' . $_REQUEST['r'] . "' WHERE ssurl = '" . $_REQUEST['f'] . '/' . $_REQUEST['v'] . "'");
                    $msg = '<font color="#00FF00">Erfolgreich umbenannt...</font><br />';
                } else {
                    $msg = '<font color="#FF0000">Konnte Datei nicht umbennen</font></br />';
                }
            }

            if (isset ($_REQUEST['n'])) {
                $neudir = 'include/downs/downloads/' . str_replace('.', '', $_REQUEST['n']);
                if ($_REQUEST['n'] == '/') {
                    $neudir = 'include/downs/downloads';
                }
                if (is_dir ($neudir) AND is_writeable ($neudir)) {
                    if (@rename ($_REQUEST['f'] . '/' . $_REQUEST['v'], $neudir . '/' . $_REQUEST['v'])) {
                        db_query("UPDATE prefix_downloads SET url = '" . $neudir . '/' . $_REQUEST['v'] . "' WHERE url = '" . $_REQUEST['f'] . '/' . $_REQUEST['v'] . "'");
                        db_query("UPDATE prefix_downloads SET surl = '" . $neudir . '/' . $_REQUEST['v'] . "' WHERE surl = '" . $_REQUEST['f'] . '/' . $_REQUEST['v'] . "'");
                        db_query("UPDATE prefix_downloads SET ssurl = '" . $neudir . '/' . $_REQUEST['v'] . "' WHERE ssurl = '" . $_REQUEST['f'] . '/' . $_REQUEST['v'] . "'");
                        $msg = '<font color="#00FF00">Erfolgreich verschoben...</font><br />';
                        $_REQUEST['f'] = $neudir;
                    } else {
                        $msg = '<font color="#FF0000">Konnte Datei nicht verschieben</font></br />';
                    }
                } else {
                    $msg = '<font color="#FF0000">Der angegebene Ordner ist nicht vorhanden...</font><br />';
                }
            }
            // files anzeigen von ordner X... wenn x nicht definiert nimm downs/downloads...
            // sonst halt downs/downloads/x/x1/x2...
            $f = 'include/downs/downloads';
            if (isset ($_REQUEST['f']) AND !empty($_REQUEST['f'])) {
                $f = $_REQUEST['f'];
                if (strpos ($f, '.') !== false) {
                    $f = dirname ($f);
                }
            }
            // positions liste definieren... (wo bin ich ;-))
            $str_fl = '';
            if ($f != 'include/downs/downloads') {
                $ar_f = explode('/', str_replace('include/downs/downloads/', '', $f));
                $str_nfl = '';
                foreach ($ar_f as $v) {
                    $str_fl .= '<b> &raquo; </b><a href="?archiv-downloads-upload=0&amp;f=include/downs/downloads/' . $str_nfl . $v . '">' . $v . '</a>';
                    $str_nfl .= $v . '/';
                }
            }
            // template oeffnen
            $tpl = new tpl ('archiv/upload', 1);
            $tpl->set_ar_out(array('posi' => $str_fl, 'msg' => $msg), 0);

            if (is_dir ($f)) {
                // dir oeffnen und arrays fuellen einmal
                // arrays mit ordner einmal mit files
                $ar_files = array();
                $ar_dirs = array();
                $o = opendir ($f);
                while ($v = readdir ($o)) {
                    if ($v == '.' OR $v == '..') {
                        continue;
                    }
                    $is_dir = is_dir ($f . '/' . $v);
                    if ($is_dir AND is_writeable ($f . '/' . $v)) {
                        $ar_dirs[] = $v;
                    } elseif (!$is_dir) {
                        $ar_files[] = $v;
                    }
                }

                $class = 'Cmite';
                // arrays durchlaufen und mit entsprechenden aktionen bzw. links versehen.
                // zuerstmal das dirs array dann das files...
                foreach($ar_dirs as $v) {
                    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
                    echo '<tr class="' . $class . '"><td colspan="5"><a href="?archiv-downloads-upload=0&amp;f=' . $f . '/' . $v . '">' . $v . '/</a></td></tr>';
                }
                foreach($ar_files as $v) {
                    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
                    echo '<tr class="' . $class . '">';
                    echo '<td>' . $v . '</td>';
                    echo '<td><a href="javascript:deleteFile(\'' . $f . '\',\'' . $v . '\')"><img src="include/images/icons/del.gif" title="l&ouml;schen" border="0" /></a></td>';
                    echo '<td><a href="javascript:moveFile(\'' . $f . '\',\'' . $v . '\')"><img src="include/images/icons/pfeila.gif" title="verschieben" border="0" /></a></td>';
                    echo '<td><a href="javascript:renFile(\'' . $f . '\',\'' . $v . '\')"><img src="include/images/icons/edit.gif" title="umbennen" border="0" /></a></td>';
                    echo '<td>' . get_upload_linked ($f . '/' . $v) . '<a href="javascript:waehleThisFile(\'' . $f . '/' . $v . '\')">w&auml;hlen</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="Cmite">Verzeichnis nicht gefunden... <a href="?archiv-downloads-upload">&Uuml;bersicht</a></td></tr>';
            }

            $tpl->set('f', $f);
            $tpl->out(1);
            // ordner liste
            echo upload_getdirlist ();

            $tpl->out(2);
        }
        // ##################
        // upload move file
        if ($menu->get(2) == 'uploadMoveFile') {
            $tpl = new tpl ('archiv/upload_move_file', 1);
            $tpl->set('v', $_REQUEST['v']);
            $tpl->set('f', $_REQUEST['f']);
            $tpl->set('dirlist', uploadMoveFile_getdirlist());
            $tpl->out(0);
        }

        if ($menu->get(2) == 'upload' OR $menu->get(2) == 'uploadMoveFile') {
            exit ();
        }

        $design = new design ('Admins Area', 'Admins Area', 2);
        $design->header();

        $tpl = new tpl ('archiv/downloads', 1);
        // kategorie und download eintraege loeschen
        if ($menu->getA(2) == 'D') {
            $azk = db_result(db_query("SELECT `cat` FROM `prefix_downcats` WHERE `id` = '" . $menu->getE(2) . "'"), 0);
            $pos = db_result(db_query("SELECT `pos` FROM `prefix_downcats` WHERE `id` = '" . $menu->getE(2) . "'"), 0);
            db_query("DELETE FROM `prefix_downcats` WHERE `id` = '" . $menu->getE(2) . "'");
            db_query("UPDATE `prefix_downcats` SET `pos` = `pos` - 1 WHERE `pos` > " . $pos . " AND `cat` = " . $azk);
        }

        if ($menu->getA(2) == 'd' AND 1 == db_result(db_query("SELECT COUNT(*) FROM `prefix_downloads` WHERE `id` = " . intval($menu->getE(2))), 0)) {
            $r = db_fetch_assoc(db_query("SELECT `cat`, `pos`, `url`, `surl`, `ssurl` FROM `prefix_downloads` WHERE `id` = " . $menu->getE(2)));
            $azk = $r['cat'];
            $pos = $r['pos'];

            unset ($r['cat']);
            unset ($r['pos']);
            // wenn url nur noch in diesem download vorhanden dann loeschen
            foreach ($r as $k => $v) {
                $qc = "SELECT COUNT(*) FROM `prefix_downloads` WHERE " . $k . " = '" . $v . "'";
                if (db_result(db_query($qc), 0) == 1 AND @file_exists($v)) {
                    @unlink($v);
                }
            }

            db_query("DELETE FROM `prefix_downloads` WHERE `id` = '" . $menu->getE(2) . "'");
            db_query("UPDATE `prefix_downloads` SET `pos` = `pos` - 1 WHERE `pos` > " . $pos . " AND `cat` = " . $azk);
        }
        // download eintraege speichern oder aendern.
        if (!empty($_POST['sub'])) {
            $_POST['url'] = $_POST['newurl'];

            $_POST['cat'] = escape($_POST['cat'], 'integer');
            $_POST['creater'] = escape($_POST['creater'], 'string');
            $_POST['version'] = escape($_POST['version'], 'string');
            $_POST['url'] = escape($_POST['url'], 'string');
            $_POST['surl'] = escape($_POST['surl'], 'string');
            $_POST['ssurl'] = escape($_POST['ssurl'], 'string');
            $_POST['name'] = escape($_POST['name'], 'string');
            $_POST['desc'] = escape($_POST['desc'], 'string');
            $_POST['descl'] = escape($_POST['descl'], 'string');

            if (empty ($_POST['pkey'])) {
                $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_downloads` WHERE `cat` = " . $_POST['cat']), 0);
                db_query("INSERT INTO `prefix_downloads` (`time`,`cat`,`creater`,`version`,`url`,surl,`ssurl`,`name`,`desc`,`descl`,`pos`) VALUES (NOW(),'" . $_POST['cat'] . "','" . $_POST['creater'] . "','" . $_POST['version'] . "','" . $_POST['url'] . "','" . $_POST['surl'] . "','" . $_POST['ssurl'] . "','" . $_POST['name'] . "','" . $_POST['desc'] . "','" . $_POST['descl'] . "','" . $pos . "')");
            } else {
                $alt_row = db_fetch_assoc(db_query("SELECT `cat`,`pos` FROM `prefix_downloads` WHERE `id` = " . $_POST['pkey']));
                if ($alt_row['cat'] != $_POST['cat']) {
                    $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_downloads` WHERE `cat` = " . $_POST['cat']), 0);
                } else {
                    $pos = $alt_row['pos'];
                }
                if ($_POST['refdate'] == 'on') {
                    $datum = '`time` = NOW(), ';
                } else {
                    $datum = '';
                }
                db_query("UPDATE `prefix_downloads` SET " . $datum . "`pos` = " . $pos . ", `cat` = '" . $_POST['cat'] . "',`creater` = '" . $_POST['creater'] . "',`version` = '" . $_POST['version'] . "',`url` = '" . $_POST['url'] . "',`surl` = '" . $_POST['surl'] . "',`ssurl` = '" . $_POST['ssurl'] . "',`name` = '" . $_POST['name'] . "',`desc` = '" . $_POST['desc'] . "',`descl` = '" . $_POST['descl'] . "' WHERE `id` = '" . $_POST['pkey'] . "'");
                if ($alt_row['cat'] != $_POST['cat']) {
                    db_query("UPDATE `prefix_downloads` SET `pos` = `pos` - 1 WHERE `pos` > " . $alt_row['pos'] . " AND `cat` = " . $alt_row['cat']);
                }
            }
            $azk = $_POST['cat'];
        }
        // kategorie eintrage speichern oder aendern.
        if (isset ($_POST['Csub'])) {
            if (empty($_POST['Ccat'])) {
                $_POST['Ccat'] = 0;
            }
            if (empty ($_POST['Cpkey'])) {
                $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_downcats` WHERE `cat` = " . $_POST['Ccat']), 0);
                db_query("INSERT INTO `prefix_downcats` (`cat`,`name`,`desc`,`pos`,`recht`) VALUES (" . $_POST['Ccat'] . ",'" . $_POST['Cname'] . "','" . $_POST['Cdesc'] . "','" . $pos . "','" . $_POST['Crecht'] . "')");
            } else {
                $alt_row = db_fetch_assoc(db_query("SELECT `cat`,`pos` FROM `prefix_downcats` WHERE `id` = " . $_POST['Cpkey']));
                $bool = true;
                $tc = $_POST['Ccat'];
                while ($tc > 0) {
                    if ($tc == $_POST['Cpkey']) {
                        $bool = false;
                    }
                    $tc = @db_result(db_query("SELECT `cat` FROM `prefix_downcats` WHERE `id` = ".$tc));
                }
                if ($bool) {
                    if ($alt_row['cat'] != $_POST['Ccat']) {
                        $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_downcats` WHERE `cat` = " . $_POST['Ccat']), 0);
                    } else {
                        $pos = $alt_row['pos'];
                    }

                    db_query("UPDATE prefix_downcats SET `cat` = '" . $_POST['Ccat'] . "',`name` = '" . $_POST['Cname'] . "',`pos` = '" . $pos . "',`desc` = '" . $_POST['Cdesc'] . "', `recht` = '" . $_POST['Crecht'] . "' WHERE `id` = '" . $_POST['Cpkey'] . "'");
                    if ($alt_row['cat'] != $_POST['Ccat']) {
                        db_query("UPDATE `prefix_downcats` SET `pos` = `pos` - 1 WHERE `pos` > " . $alt_row['pos'] . " AND `cat` = " . $alt_row['cat']);
                    }
                }
            }
            $azk = $_POST['Ccat'];
        }
        // downloadeintrage verschieben.
        if ($menu->getA(3) == 'u' OR $menu->getA(3) == 'o') {
            $pos = $menu->get(4);
            $id = $menu->getE(3);
            $nps = ($menu->getA(3) == 'u' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_downloads` WHERE `cat` = " . $menu->getE(2)), 0);

            if ($nps < 0) {
                db_query("UPDATE `prefix_downloads` SET `pos` = " . $anz . " WHERE `id` = " . $id);
                db_query("UPDATE `prefix_downloads` SET `pos` = `pos` -1 WHERE `cat` = " . $menu->getE(2));
            }
            if ($nps >= $anz) {
                db_query("UPDATE `prefix_downloads` SET `pos` = -1 WHERE `id` = " . $id);
                db_query("UPDATE `prefix_downloads` SET `pos` = `pos` +1 WHERE `cat` = " . $menu->getE(2));
            }

            if ($nps < $anz AND $nps >= 0) {
                db_query("UPDATE `prefix_downloads` SET `pos` = " . $pos . " WHERE `pos` = " . $nps . " AND `cat` = " . $menu->getE(2));
                db_query("UPDATE `prefix_downloads` SET `pos` = " . $nps . " WHERE `id` = " . $id);
            }
        }
        // download kategorien verschieben
        if ($menu->getA(3) == 'U' OR $menu->getA(3) == 'O') {
            $pos = $menu->get(4);
            $id = $menu->getE(3);
            $cat = db_result(db_query("SELECT `cat` FROM `prefix_downcats` WHERE `id` = " . $id), 0);
            $nps = ($menu->getA(3) == 'U' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_downcats` WHERE `cat` = " . $cat), 0);

            if ($nps < 0) {
                db_query("UPDATE `prefix_downcats` SET `pos` = " . $anz . " WHERE `id` = " . $id);
                db_query("UPDATE `prefix_downcats` SET `pos` = `pos` -1 WHERE `cat` = " . $cat);
            }
            if ($nps >= $anz) {
                db_query("UPDATE `prefix_downcats` SET `pos` = -1 WHERE `id` = " . $id);
                db_query("UPDATE `prefix_downcats` SET `pos` = `pos` +1 WHERE `cat` = " . $cat);
            }

            if ($nps < $anz AND $nps >= 0) {
                db_query("UPDATE `prefix_downcats` SET `pos` = " . $pos . " WHERE `pos` = " . $nps . " AND `cat` = " . $cat);
                db_query("UPDATE `prefix_downcats` SET `pos` = " . $nps . " WHERE `id` = " . $id);
            }
        }
        // downs
        if ($menu->getA(2) == 'e') {
            $erg = db_query("SELECT `id`,`cat`,`creater`,`surl`,`ssurl`,`pos`,`version`,`url`,`name`,`desc`,`descl` FROM `prefix_downloads` WHERE `id` = '" . $menu->getE(2) . "'");
            $_ilch = db_fetch_assoc($erg);
            $_ilch['pkey'] = $menu->getE(2);
            $azk = $_ilch['cat'];
            $_ilch['datum'] = '<input type="checkbox" name="refdate" /><font color="white">Datum aktualisieren</font>';
        } else {
            if (isset ($azk)) {
                $c = $azk;
            } elseif ($menu->getA(2) == 'S' OR $menu->getA(2) == 'E') {
                $c = $menu->getE(2);
            } else {
                $c = 0;
            }
            $_ilch = array (
                'cat' => $c,
                'creater' => '',
                'surl' => '',
                'ssurl' => '',
                'pkey' => '',
                'pos' => '',
                'version' => '',
                'name' => '',
                'url' => '',
                'desc' => '',
                'descl' => '',
                'datum' => ''
                );
            unset($c);
        }
        // wenn der link von archiv upload kommt ist dllink gesetzt
        $dllink = '';
        if (isset($_REQUEST['dllink'])) {
            $dllink = $_REQUEST['dllink'];
        } else {
            $dllink = $_ilch['url'];
        }

        $_ilch['newurl'] = $_ilch['url'];

        $_ilch['url'] = arlistee ($dllink, get_downloads_ar());
        $_ilch['url'] = '<option value="neu">andere:</option>' . $_ilch['url'];

        archiv_downs_admin_selectcats('0', '', $_ilch['cat'], $_ilch['cat']);
        $_ilch['cat'] = '<option value="0">Keine</option>' . $_ilch['cat'];

        if (!isset($azk)) {
            $azk = 0;
            if ($menu->getA(2) == 'S' OR $menu->getA(2) == 'E') {
                $azk = $menu->getE(2);
                if ($menu->get(2) == 'Sa') {
                    $azk = - 1;
                }
            }
        }
        // wenn userupload on und writeable dann koennen user
        // dateien hochladen, also wird als kategorie link noch ein "freischalt" link hinzugefueght.
        $frei = '';
        if ($allgAr['archiv_down_userupload'] == 1 AND is_writeable ('include/downs/downloads/user_upload')) {
            $frei = '<tr class="Cmite"><td colspan="5"><a href="?archiv-downloads-Sa">User-Uploads freischalten</a></td></tr>';
        }

        $tpl->out(0);
        $class = 0;
        $abf = "SELECT `id`,`cat`,`version`,`name`,`pos` FROM `prefix_downloads` WHERE `cat` = " . $azk . " ORDER BY `pos`";
        $erg = db_query($abf);
        while ($row = db_fetch_assoc($erg)) {
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $row['class'] = $class;
            $tpl->set_ar ($row);
            $tpl->out(1);
        }
        // downs
        $tpl->set_out('frei', $frei, 2);
        // cat
        if ($menu->getA(2) == 'E') {
            $erg = db_query("SELECT `id`,`cat` as `Ccat`, `recht` as `Crecht`, `name` as `Cname`,`pos` as `Cpos`,`desc` as `Cdesc` FROM `prefix_downcats` WHERE `id` = '" . $menu->getE(2) . "'");
            $_Cilch = db_fetch_assoc($erg);
            $_Cilch['Cpkey'] = $menu->getE(2);
        } else {
            $_Cilch = array (
                'Ccat' => '',
                'Cpkey' => '',
                'Cpos' => '',
                'Cname' => '',
                'Crecht' => '',
                'Cdesc' => ''
                );
        }
        $_Cilch['Crecht'] = dblistee($_Cilch['Crecht'], "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` DESC");
        archiv_downs_admin_selectcats('0', '', $_Cilch['Ccat'], $_Cilch['Ccat']);
        $_Cilch['Ccat'] = '<option value="0">Keine</option>' . $_Cilch['Ccat'];

        archiv_downs_admin_showcats (0 , '');

        $tpl->set_ar($_ilch);
        $tpl->set_ar($_Cilch);
        $tpl->out(3);

        $design->footer();
        break;
    // # # # # # # # # # # # # # # # # # #
    // Links
    case 'links' :
        $design = new design ('Admins Area', 'Admins Area', 2);
        $design->header();

        $tpl = new tpl ('archiv/links', 1);
        // kategorie und link eintraege loeschen
        if ($menu->getA(2) == 'D') {
            $azk = db_result(db_query("SELECT `cat` FROM `prefix_linkcats` WHERE `id` = '" . $menu->getE(2) . "'"), 0);
            $pos = db_result(db_query("SELECT `pos` FROM `prefix_linkcats` WHERE `id` = '" . $menu->getE(2) . "'"), 0);
            db_query("DELETE FROM `prefix_linkcats` WHERE `id` = '" . $menu->getE(2) . "'");
            db_query("UPDATE `prefix_linkcats` SET `pos` = `pos` - 1 WHERE `pos` > " . $pos . " AND `cat` = " . $azk);
        }

        if ($menu->getA(2) == 'd') {
            $azk = db_result(db_query("SELECT `cat` FROM `prefix_links` WHERE `id` = '" . $menu->getE(2) . "'"), 0);
            $pos = db_result(db_query("SELECT `pos` FROM `prefix_links` WHERE `id` = '" . $menu->getE(2) . "'"), 0);
            db_query("DELETE FROM `prefix_links` WHERE `id` = " . $menu->getE(2));
            db_query("UPDATE `prefix_links` SET `pos` = `pos` - 1 WHERE `pos` > " . $pos . " AND `cat` = " . $azk);
        }
        // link eintraege speichern oder aendern.
        if (!empty($_POST['sub'])) {
            $_POST['cat'] = escape($_POST['cat'], 'integer');
            $_POST['name'] = escape($_POST['name'], 'string');
            $_POST['banner'] = escape($_POST['banner'], 'string');
            $_POST['desc'] = escape($_POST['desc'], 'string');
            $_POST['link'] = get_homepage(escape($_POST['link'], 'string'));

            if (empty ($_POST['pkey'])) {
                $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_links` WHERE `cat` = " . $_POST['cat']), 0);
                db_query("INSERT INTO `prefix_links` (`cat`,`name`,`banner`,`desc`,`link`,`pos`) VALUES ('" . $_POST['cat'] . "','" . $_POST['name'] . "','" . $_POST['banner'] . "','" . $_POST['desc'] . "','" . $_POST['link'] . "','" . $pos . "')");
            } else {
                $alt_row = db_fetch_assoc(db_query("SELECT `cat`,`pos` FROM `prefix_links` WHERE `id` = " . $_POST['pkey']));
                if ($alt_row['cat'] != $_POST['cat']) {
                    $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_links` WHERE `cat` = " . $_POST['cat']), 0);
                } else {
                    $pos = $alt_row['pos'];
                }
                db_query("UPDATE `prefix_links` SET `cat` = '" . $_POST['cat'] . "',`name` = '" . $_POST['name'] . "',`pos` = " . $pos . ", `banner` = '" . $_POST['banner'] . "',`desc` = '" . $_POST['desc'] . "',`link` = '" . $_POST['link'] . "' WHERE `id` = '" . $_POST['pkey'] . "'");
                if ($alt_row['cat'] != $_POST['cat']) {
                    db_query("UPDATE `prefix_links` SET `pos` = `pos` - 1 WHERE `pos` > " . $alt_row['pos'] . " AND `cat` = " . $alt_row['cat']);
                }
            }
            $azk = $_POST['cat'];
        }
        // kategorie eintrage speichern oder aendern.
        if (isset ($_POST['Csub'])) {
            if (empty($_POST['Ccat'])) {
                $_POST['Ccat'] = 0;
            }
            if (empty ($_POST['Cpkey'])) {
                $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_linkcats` WHERE `cat` = " . $_POST['Ccat']), 0);
                db_query("INSERT INTO `prefix_linkcats` (`cat`,`name`,`desc`,`pos`) VALUES (" . $_POST['Ccat'] . ",'" . $_POST['Cname'] . "','" . $_POST['Cdesc'] . "','" . $pos . "')");
            } else {
                $alt_row = db_fetch_assoc(db_query("SELECT `cat`,`pos` FROM `prefix_linkcats` WHERE `id` = " . $_POST['Cpkey']));
                $tc = $_POST['Ccat'];
                $bool = true;
                while ($tc > 0) {
                    if ($tc == $_POST['Cpkey']) {
                        $bool = false;
                    }
                    $tc = @db_result(db_query("SELECT `cat` FROM `prefix_linkcats` WHERE `id` = ".$tc));
                }
                if ($bool) {
                    if ($alt_row['cat'] != $_POST['Ccat']) {
                        $pos = db_result(db_query("SELECT COUNT(*) FROM `prefix_linkcats` WHERE `cat` = " . $_POST['Ccat']), 0);
                    } else {
                        $pos = $alt_row['pos'];
                    }
                    db_query("UPDATE `prefix_linkcats` SET `cat` = '" . $_POST['Ccat'] . "',`name` = '" . $_POST['Cname'] . "',`pos` = '" . $pos . "',`desc` = '" . $_POST['Cdesc'] . "' WHERE `id` = '" . $_POST['Cpkey'] . "'");
                    if ($alt_row['cat'] != $_POST['Ccat']) {
                        db_query("UPDATE prefix_linkcats SET pos = pos - 1 WHERE pos > " . $alt_row['pos'] . " AND cat = " . $alt_row['cat']);
                    }
                }
            }
            $azk = $_POST['Ccat'];
        }
        // verschieben
        if ($menu->getA(3) == 'u' OR $menu->getA(3) == 'o') {
            $pos = $menu->get(4);
            $id = $menu->getE(3);
            $nps = ($menu->getA(3) == 'u' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_links` WHERE `cat` = " . $menu->getE(2)), 0);

            if ($nps < 0) {
                db_query("UPDATE `prefix_links` SET `pos` = " . $anz . " WHERE `id` = " . $id);
                db_query("UPDATE `prefix_links` SET `pos` = `pos` -1 WHERE `cat` = " . $menu->getE(2));
            }
            if ($nps >= $anz) {
                db_query("UPDATE `prefix_links` SET `pos` = -1 WHERE `id` = " . $id);
                db_query("UPDATE `prefix_links` SET `pos` = `pos` +1 WHERE `cat` = " . $menu->getE(2));
            }

            if ($nps < $anz AND $nps >= 0) {
                db_query("UPDATE `prefix_links` SET `pos` = " . $pos . " WHERE `pos` = " . $nps . " AND `cat` = " . $menu->getE(2));
                db_query("UPDATE `prefix_links` SET `pos` = " . $nps . " WHERE `id` = " . $id);
            }
        }
        // link kategorien verschieben
        if ($menu->getA(3) == 'U' OR $menu->getA(3) == 'O') {
            $pos = $menu->get(4);
            $id = $menu->getE(3);
            $cat = db_result(db_query("SELECT `cat` FROM `prefix_linkcats` WHERE `id` = " . $id), 0);
            $nps = ($menu->getA(3) == 'U' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_linkcats` WHERE `cat` = " . $cat), 0);

            if ($nps < 0) {
                db_query("UPDATE `prefix_linkcats` SET `pos` = " . $anz . " WHERE `id` = " . $id);
                db_query("UPDATE `prefix_linkcats` SET `pos` = `pos` -1 WHERE `cat` = " . $cat);
            }
            if ($nps >= $anz) {
                db_query("UPDATE `prefix_linkcats` SET `pos` = -1 WHERE `id` = " . $id);
                db_query("UPDATE `prefix_linkcats` SET `pos` = `pos` +1 WHERE `cat` = " . $cat);
            }

            if ($nps < $anz AND $nps >= 0) {
                db_query("UPDATE `prefix_linkcats` SET `pos` = " . $pos . " WHERE `pos` = " . $nps . " AND `cat` = " . $cat);
                db_query("UPDATE `prefix_linkcats` SET `pos` = " . $nps . " WHERE `id` = " . $id);
            }
        }
        // links
        if ($menu->getA(2) == 'e') {
            $erg = db_query("SELECT `id`,`cat`,`desc`,`name`,`banner`,`link` FROM `prefix_links` WHERE `id` = '" . $menu->getE(2) . "'");
            $_ilch = db_fetch_assoc($erg);
            $_ilch['pkey'] = $menu->getE(2);
            $azk = $_ilch['cat'];
        } else {
            if (isset ($azk)) {
                $c = $azk;
            } elseif ($menu->getA(2) == 'S' OR $menu->getA(2) == 'E') {
                $c = $menu->getE(2);
            } else {
                $c = 0;
            }
            $_ilch = array (
                'pkey' => '',
                'id' => '',
                'banner' => '',
                'name' => '',
                'desc' => '',
                'link' => '',
                'cat' => $c
                );
            unset($c);
        }

        archiv_links_admin_selectcats('0', '', $_ilch['cat'], $_ilch['cat']);
        $_ilch['cat'] = '<option value="0">Keine</option>' . $_ilch['cat'];

        if (!isset($azk)) {
            $azk = 0;
            if ($menu->getA(2) == 'S' OR $menu->getA(2) == 'E') {
                $azk = $menu->getE(2);
            }
        }

        $tpl->out(0);
        $class = 0;
        $abf = "SELECT `id`,`name`,`link`,`cat`,`pos` FROM `prefix_links` WHERE `cat` = " . $azk . " ORDER BY `pos`";
        $erg = db_query($abf);
        while ($row = db_fetch_assoc($erg)) {
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $row['class'] = $class;
            $tpl->set_ar ($row);
            $tpl->out(1);
        }
        // links
        $tpl->out(2);
        // cat
        if ($menu->getA(2) == 'E') {
            $erg = db_query("SELECT `id`,`cat` as `Ccat`, `name` as `Cname`,`pos` as `Cpos`,`desc` as `Cdesc` FROM `prefix_linkcats` WHERE `id` = '" . $menu->getE(2) . "'");
            $_Cilch = db_fetch_assoc($erg);
            $_Cilch['Cpkey'] = $menu->getE(2);
        } else {
            $_Cilch = array (
                'Ccat' => '',
                'Cpkey' => '',
                'Cpos' => '',
                'Cname' => '',
                'Cdesc' => ''
                );
        }
        archiv_links_admin_selectcats('0', '', $_Cilch['Ccat'], $_Cilch['Ccat']);
        $_Cilch['Ccat'] = '<option value="0">Keine</option>' . $_Cilch['Ccat'];

        archiv_links_admin_showcats (0 , '');

        $tpl->set_ar($_ilch);
        $tpl->set_ar($_Cilch);
        $tpl->out(3);

        $design->footer();
        break;
    // # # # # # # # # # # # # # # # # # #
    // Partners
    case 'partners' :
        $design = new design ('Admins Area', 'Admins Area', 2);
        $design->header();

        $tpl = new tpl ('archiv/partners', 1);
        // loeschen
        if ($menu->getA(2) == 'd') {
            $pos = db_result(db_query("SELECT `pos` FROM `prefix_partners` WHERE `id` = " . $menu->getE(2)), 0);
            db_query("DELETE FROM `prefix_partners` WHERE `id` = " . $menu->getE(2));
            db_query("UPDATE `prefix_partners` SET `pos` = `pos` -1 WHERE `pos` > " . $pos);
        }
        // aendern / eintragen
        if (isset($_POST['sub'])) {
            $_POST['name'] = escape($_POST['name'], 'string');
            $_POST['banner'] = escape($_POST['banner'], 'string');
            $_POST['link'] = get_homepage(escape($_POST['link'], 'string'));

            if (empty ($_POST['pkey'])) {
                $_POST['pos'] = db_result(db_query("SELECT COUNT(*) FROM prefix_partners"), 0);
                db_query("INSERT INTO `prefix_partners` (`name`,`banner`,`link`,`pos`) VALUES ('" . $_POST['name'] . "','" . $_POST['banner'] . "','" . $_POST['link'] . "','" . $_POST['pos'] . "')");
            } else {
                db_query("UPDATE `prefix_partners` SET `name` = '" . $_POST['name'] . "',`banner` = '" . $_POST['banner'] . "',`link` = '" . $_POST['link'] . "' WHERE `id` = '" . $_POST['pkey'] . "'");
            }
        }
        // verschieben
        if ($menu->getA(2) == 'o' OR $menu->getA(2) == 'u') {
            $pos = $menu->get(3);
            $id = $menu->getE(2);
            $nps = ($menu->getA(2) == 'u' ? $pos + 1 : $pos - 1);
            $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_partners`"), 0);

            if ($nps < 0) {
                db_query("UPDATE `prefix_partners` SET `pos` = " . $anz . " WHERE `id` = " . $id);
                db_query("UPDATE `prefix_partners` SET `pos` = `pos` -1");
            }
            if ($nps >= $anz) {
                db_query("UPDATE `prefix_partners` SET `pos` = -1 WHERE `id` = " . $id);
                db_query("UPDATE `prefix_partners` SET `pos` = `pos` +1");
            }

            if ($nps < $anz AND $nps >= 0) {
                db_query("UPDATE `prefix_partners` SET `pos` = " . $pos . " WHERE `pos` = " . $nps);
                db_query("UPDATE `prefix_partners` SET `pos` = " . $nps . " WHERE `id` = " . $id);
            }
        }
        // aendern vorbereiten.
        if ($menu->getA(2) == 'e') {
            $erg = db_query("SELECT `id`,`name`,`banner`,`link` FROM `prefix_partners` WHERE `id` = '" . $menu->getE(2) . "'");
            $_ilch = db_fetch_assoc($erg);
            $_ilch['pkey'] = $menu->getE(2);
        } else {
            $_ilch = array (
                'pkey' => '',
                'id' => '',
                'banner' => '',
                'name' => '',
                'link' => ''
                );
        }

        $tpl->set_ar_out($_ilch, 0);
        $page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1);
        $limit = 20;
        $class = 'Cnorm';
        $MPL = db_make_sites ($page , '' , $limit , '?archiv-partners' , 'partners');
        $anfang = ($page - 1) * $limit;
        $abf = "SELECT `id`,`name`,`link`, `pos` FROM `prefix_partners` ORDER BY `pos` ASC LIMIT " . $anfang . "," . $limit;
        $erg = db_query($abf);
        while ($row = db_fetch_assoc($erg)) {
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $row['class'] = $class;
            $tpl->set_ar ($row);
            $tpl->out(1);
        }
        $tpl->set ('MPL', $MPL);
        $tpl->out(2);

        $design->footer();
        break;
}

?>