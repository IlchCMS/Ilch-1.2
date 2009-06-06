<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

function user_get_group_list ($uid) {
    $l = 'Mitglied in Gruppen:<br />';
    $erg = db_query("SELECT prefix_groups.name FROM prefix_groupusers LEFT JOIN prefix_groups ON prefix_groups.id = prefix_groupusers.gid WHERE prefix_groupusers.uid = " . $uid);
    while ($r = db_fetch_assoc($erg)) {
        $l .= '- ' . $r['name'] . '<br />';
    }
    return ($l);
}

function user_get_all_mod_list () {
    $l = '';
    $erg = db_query("SELECT DISTINCT name FROM prefix_modules WHERE fright = 1 ORDER BY prefix_modules.name");
    while ($r = db_fetch_assoc($erg)) {
        $x = $r['name'];
        $l .= '<th style="font-size: 9px; font-weight: normal;" title="' . $r['name'] . '" valign="bottom">' . $x . '</th>';
    }
    return ($l);
}

function user_get_mod_change_list ($uid) {
    $l = '';
    $erg = db_query("SELECT prefix_modules.id, uid FROM prefix_modules LEFT JOIN prefix_modulerights ON prefix_modulerights.mid = prefix_modules.id AND prefix_modulerights.uid = " . $uid . " WHERE fright = 1 ORDER BY prefix_modules.name");
    while ($r = db_fetch_assoc($erg)) {
        if ($r['uid'] == '') {
            $c = '';
        } else {
            $c = ' checked';
        }
        $l .= '<td align="center"><input onChange="changeModulRecht(' . $r['id'] . ',' . $uid . ')" type="checkbox" id="MN' . $r['id'] . '-' . $uid . '" name="MN' . $r['id'] . '-' . $uid . '" ' . $c . ' /></td>';
    }
    return ($l);
}

function user_get_mod_list ($uid) {
    $l = 'Modulrechte:<br />';
    $erg = db_query("SELECT DISTINCT module FROM prefix_modulerights WHERE uid = " . $uid);
    while ($r = db_fetch_assoc($erg)) {
        $l .= '- ' . $r['module'] . '<br />';
    }
    return ($l);
}

function getfl($gid) {
    $liste = '';
    $erg = db_query("SELECT view,name,reply,start,mods FROM prefix_forums WHERE view = " . $gid . " OR reply = " . $gid . " OR start = " . $gid . " OR mods = " . $gid);
    while ($row = db_fetch_assoc($erg)) {
        $v = ($row['view'] == $gid ? 'sehen/lesen,' : '');
        $r = ($row['reply'] == $gid ? 'antworten,' : '');
        $s = ($row['start'] == $gid ? 'Themen starten,' : '');
        $m = ($row['mods'] == $gid ? 'Moderieren,' : '');
        $liste .= $row['name'] . '<span class="smalfont">(' . $v . $r . $s . $m . ')</span>&nbsp;';
    }
    return($liste);
}

$um = $menu->get(1);
switch ($um) {
    default :
        $design = new design ('Admins Area', 'Admins Area', 2);
        $design->header();
        $q = '';
        if (isset($_REQUEST['q'])) {
            $q = escape($_REQUEST['q'], 'string');
        }
        $tpl = new tpl ('user/user', 1);
        $tpl->set('modlall', user_get_all_mod_list());
        $tpl->set('anzmods', db_result(db_query("SELECT COUNT(*) FROM prefix_modules WHERE fright = 1"), 0));
        $tpl->set_out('q', unescape($q), 0);

        $q = str_replace('*', '%', $q);
        if (strpos($q, '%') === false) {
            $q = $q . '%';
        }

        $limit = 15; // Limit
        $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
        $MPL = db_make_sites ($page , "WHERE name LIKE '" . $q . "'" , $limit , '?user' , 'user');
        $anfang = ($page - 1) * $limit;
        $class = '';
        $q = "SELECT name,recht,id FROM `prefix_user` WHERE name LIKE '" . $q . "' ORDER by recht,posts DESC LIMIT " . $anfang . "," . $limit;
        $erg = db_query($q);
        while ($row = db_fetch_object($erg)) {
            if ($class == 'Cmite') {
                $class = 'Cnorm';
            } else {
                $class = 'Cmite';
            }
            $ar = array ('name' => $row->name,
                'class' => $class,
                'id' => $row->id,
                'grouplist' => user_get_group_list($row->id),
                'recht' => dblistee($row->recht, "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC"),
                'modslist' => user_get_mod_change_list($row->id),
                );

            $tpl->set_ar_out($ar, 1);
        }
        $tpl->set_out('MPL', $MPL, 2);
        $design->footer();
        break;
    // modulrechte fuer einen user aendern
    case 'modulrecht' :
        $uid = intval($menu->get(2));
        $modul = intval($_REQUEST['modul']);
        $aktion = $_REQUEST['aktion'];
        if ($aktion == 'eintragen' AND 0 == db_result(db_query("SELECT COUNT(*) FROM prefix_modulerights WHERE mid = '" . $modul . "' AND uid = " . $uid), 0)) {
            db_query("INSERT INTO prefix_modulerights (mid,uid) VALUES ('" . $modul . "'," . $uid . ")");
        } elseif ($aktion == 'loeschen' AND 1 == db_result(db_query("SELECT COUNT(*) FROM prefix_modulerights WHERE mid = '" . $modul . "' AND uid = " . $uid), 0)) {
            db_query("DELETE FROM prefix_modulerights WHERE mid = '" . $modul . "' AND uid = " . $uid);
        }

        ?><html><head><script language="JavaScript" type="text/javascript"><!--  opener.location.reload();
    function closeThisWindow() { opener.focus(); window.close(); } closeThisWindow()
    //--></script></head><body></body></html><?php
        break;
    // gruppen zugehoerigkeiten eines users aendern
    case 'gruppen' :
        $uid = $menu->get(2);
        if (isset($_POST['usergroups'])) {
            $erg = db_query("SELECT id FROM prefix_groups");
            while ($row = db_fetch_assoc($erg)) {
                $ck = db_count_query("SELECT COUNT(uid) FROM prefix_groupusers WHERE uid = " . $uid . " AND gid = " . $row['id']);
                if ($ck == 0 AND isset ($_POST['grprhave'][$row['id']][$uid])) {
                    db_query("INSERT INTO prefix_groupusers (uid,gid,fid) VALUES ( " . $uid . ", " . $row['id'] . ", 3 )");
                } elseif ($ck == 1 AND !isset ($_POST['grprhave'][$row['id']][$uid])) {
                    db_query("DELETE FROM prefix_groupusers WHERE uid = " . $uid . " AND gid = " . $row['id']);
                }
            }
        }

        $user_name = db_result(db_query("SELECT name FROM prefix_user WHERE id = " . $uid), 0);
        $tpl = new tpl ('user/gruppen', 1);
        $tpl->set_ar_out(array('username' => $user_name, 'userid' => $uid), 0);
        $class = 'Cnorm';
        $erg = db_query("SELECT name,id FROM prefix_groups");
        while ($row = db_fetch_assoc($erg)) {
            $ck = db_count_query("SELECT COUNT(uid) FROM prefix_groupusers WHERE uid = " . $uid . " AND gid = " . $row['id']);
            $row['ck'] = ($ck == 0 ? '' : 'checked');
            $class = ($class == 'Cnorm' ? 'Cmite' : 'Cnorm');
            $row['class'] = $class;
            $tpl->set_ar_out($row, 1);
        }
        $tpl->out(2);
        break;
    // das recht eines users aendern
    case 'changeRecht' :
        $uid = $menu->get(2);
        $altes_recht = db_result(db_query("SELECT recht FROM prefix_user WHERE id = " . $uid), 0);
        $neues_recht = escape($_GET['newr'], 'integer');
        if (($neues_recht > $_SESSION['authright'] AND $altes_recht > $_SESSION['authright']) OR ($_SESSION['authid'] == 1 AND $uid != 1)) {
            $q = "UPDATE prefix_user SET recht = " . $neues_recht . " WHERE id = " . $uid;
            db_query($q);
        }

        ?><html><head><script language="JavaScript" type="text/javascript"><!--
    function closeThisWindow() { opener.focus(); window.close(); } closeThisWindow()
    //--></script></head><body></body></html><?php
        break;
    // details eines users anzeigen
    case 1 :
        $design = new design ('Admins Area', 'Admins Area', 2);
        $design->header();
        if (isset ($_REQUEST['uID'])) {
            $uid = $_REQUEST['uID'];
        } else {
            $uid = $menu->get(2);
        }
        $erg = db_query("SELECT name,email,id,recht,wohnort,homepage,aim,msn,icq,yahoo,status,staat,gebdatum,sig,opt_pm,opt_pm_popup,opt_mail,geschlecht,spezrank,avatar FROM prefix_user WHERE id = '" . $uid . "'");
        if (db_num_rows($erg) == 0) {
            die ('Fehler: Username nicht gefunden <a href="?user">zur&uuml;ck</a>');
        } else {
            $row = db_fetch_assoc($erg);

            $tpl = new tpl ('user/details', 1);
            $row['recht'] = dbliste ($row['recht'] , $tpl, 'recht', "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC");
            $row['staat'] = '<option></option>' . arliste ($row['staat'] , get_nationality_array() , $tpl , 'staat');
            $row['spezrank'] = '<option></option>' . dbliste ($row['spezrank'], $tpl, 'spezrank', "SELECT id, bez FROM prefix_ranks WHERE spez = 1");

            $row['geschlecht0'] = ($row['geschlecht'] < 1 ? 'checked' : '');
            $row['geschlecht1'] = ($row['geschlecht'] == 1 ? 'checked' : '');
            $row['geschlecht2'] = ($row['geschlecht'] == 2 ? 'checked' : '');
            if ($row['status'] == 1) {
                $row['status1'] = 'checked';
                $row['status0'] = '';
            } else {
                $row['status1'] = '';
                $row['status0'] = 'checked';
            }
            if ($row['opt_mail'] == 1) {
                $row['opt_mail1'] = 'checked';
                $row['opt_mail0'] = '';
            } else {
                $row['opt_mail1'] = '';
                $row['opt_mail0'] = 'checked';
            }
            if ($row['opt_pm'] == 1) {
                $row['opt_pm1'] = 'checked';
                $row['opt_pm0'] = '';
            } else {
                $row['opt_pm1'] = '';
                $row['opt_pm0'] = 'checked';
            }
            if ($row['opt_pm_popup'] == 1) {
                $row['opt_pm_popup1'] = 'checked';
                $row['opt_pm_popup0'] = '';
            } else {
                $row['opt_pm_popup1'] = '';
                $row['opt_pm_popup0'] = 'checked';
            }
            if (@file_exists($row['avatar'])) {
                $row['avatar'] = '<img src="' . $row['avatar'] . '" border="0" /><br />' ;
            }else {
                $row['avatar'] = '';
            }
            $tpl->set_ar_out ($row, 0);

            profilefields_change ($row['id']);

            $tpl->out(1);
        }
        $design->footer();
        break;
    // details des users aendern
    case 2 :
        $design = new design ('Admins Area', 'Admins Area', 2);
        $design->header();
        $changeok = true;
        $uid = escape($_POST['uID'], 'integer');

        $altes_recht = db_result(db_query("SELECT recht FROM prefix_user WHERE id = " . $uid), 0);
        $neues_recht = escape($_POST['urecht'], 'integer');
        if (($neues_recht <= $_SESSION['authright'] OR $altes_recht <= $_SESSION['authright']) AND $_SESSION['authid'] > 1) {
            $changeok = false;
        }

        if ($changeok) {
            if (isset($_POST['userdel'])) {
                user_remove($uid);
                wd ('?user', 'User wurde erfolgreich gel&ouml;scht');
            } else {
                $abf = "SELECT * FROM prefix_user WHERE id = '" . $uid . "'";
                $erg = db_query($abf);
                $row = db_fetch_object($erg);

                if (isset($_POST['passw'])) {
                    $newPass = genkey (8);
                    $newPassMD5 = md5($newPass);
                    icmail ($row->email , 'neues Password' , "Hallo\n\nDein Password wurde soeben von einem Administrator g‰endert es ist nun:\n\n$newPass\n\nGruﬂ der Administrator");
                    db_query('UPDATE `prefix_user` SET pass = "' . $newPassMD5 . '" WHERE id = "' . escape($_POST['uID'], 'integer') . '"');
                }
                // avatar speichern START
                $avatar_sql_update = '';
                if (!empty ($_FILES['avatarfile']['name'])) {
                    $file_tmpe = $_FILES['avatarfile']['tmp_name'];
                    $rile_type = ic_mime_type ($_FILES['avatarfile']['tmp_name']);
                    $file_type = $_FILES['avatarfile']['type'];
                    $file_size = $_FILES['avatarfile']['size'];
                    $fmsg = $lang['avatarisnopicture'];
                    $size = @getimagesize ($file_tmpe);
                    $endar = array (1 => 'gif', 2 => 'jpg', 3 => 'png');
                    if (($size[2] == 1 OR $size[2] == 2 OR $size[2] == 3) AND $size[0] > 10 AND $size[1] > 10 AND substr ($file_type , 0 , 6) == 'image/' AND substr ($rile_type , 0 , 6) == 'image/') {
                        $endung = $endar[$size[2]];
                        $breite = $size[0];
                        $hoehe = $size[1];
                        $neuer_name = 'include/images/avatars/' . $uid . '.' . $endung;
                        @unlink (db_result(db_query("SELECT avatar FROM prefix_user WHERE id = " . $uid), 0));
                        move_uploaded_file ($file_tmpe , $neuer_name);
                        @chmod($neuer_name, 0777);
                        $avatar_sql_update = ', avatar = "' . $neuer_name . '"';
                        $fmsg = $lang['pictureuploaded'];
                    }
                } elseif (isset($_POST['avatardel'])) {
                    $fmsg = $lang['picturedelete'];
                    @unlink (db_result(db_query("SELECT avatar FROM prefix_user WHERE id = " . $uid), 0));
                    $avatar_sql_update = ', avatar = ""';
                }
                // avatar speichern ENDE
                profilefields_change_save (escape($_POST['uID'], 'integer'));
                $usaName1 = escape($_POST['usaName1'], 'string');
                $email = escape($_POST['email'], 'string');
                $homepage = escape($_POST['homepage'], 'string');
                $wohnort = escape($_POST['wohnort'], 'string');
                $icq = escape($_POST['icq'], 'string');
                $msn = escape($_POST['msn'], 'string');
                $yahoo = escape($_POST['yahoo'], 'string');
                $aim = escape($_POST['aim'], 'string');
                $staat = escape($_POST['staat'], 'string');
                $spezrank = escape($_POST['spezrank'], 'integer');
                $geschlecht = escape($_POST['geschlecht'], 'integer');
                $status = escape($_POST['status'], 'integer');
                $opt_mail = escape($_POST['opt_mail'], 'integer');
                $opt_pm = escape($_POST['opt_pm'], 'integer');
                $opt_pm_popup = escape($_POST['opt_pm_popup'], 'integer');
                $gebdatum = escape($_POST['gebdatum'], 'string');
                $sig = escape($_POST['sig'], 'string');
                // Name im Forum ‰ndern
                if ($_POST['forumname'] == 'on') {
                    $oldname = db_count_query("SELECT name FROM `prefix_user` WHERE id =" . $uid);
                    if ($oldname != $usaName1) {
                        db_query("UPDATE `prefix_posts` SET erst = '$usaName1' WHERE erstid = " . $uid);
                        db_query("UPDATE `prefix_topics` SET erst = '$usaName1' WHERE erst = '$oldname'");
                    }
                }
                db_query('UPDATE prefix_user
			  SET
					name  = "' . $usaName1 . '",
					recht = "' . $neues_recht . '",
					email = "' . $email . '",
          homepage = "' . $homepage . '",
          wohnort = "' . $wohnort . '",
          icq = "' . $icq . '",
          msn = "' . $msn . '",
          yahoo = "' . $yahoo . '",
          aim = "' . $aim . '",
          staat = "' . $staat . '",
          spezrank = "' . $spezrank . '",
          geschlecht = "' . $geschlecht . '",
          status = "' . $status . '",
          opt_mail = "' . $opt_mail . '",
          opt_pm = "' . $opt_pm . '",
          opt_pm_popup = "' . $opt_pm_popup . '",
          gebdatum = "' . $gebdatum . '",
          sig = "' . $sig . '"
          ' . $avatar_sql_update . '
				WHERE id = "' . $uid . '"');
            }
        }
        wd('admin.php?user-1-' . $uid, 'Das Profil wurde erfolgreich geaendert', 2);
        $design->footer();
        break;
    // mal kurz nen neuen user anlegen
    case 'createNewUser' :
        $msg = '';
        if (!empty($_POST['name']) AND !empty($_POST['pass']) AND !empty($_POST['email'])) {
            $_POST['name'] = escape($_POST['name'], 'string');
            $_POST['recht'] = escape($_POST['recht'], 'integer');
            $_POST['email'] = escape($_POST['email'], 'string');
            $erg = db_query("SELECT id FROM prefix_user WHERE name = BINARY '" . $_POST['name'] . "'");
            if (db_num_rows($erg) > 0) {
                $msg = 'Der Name ist leider schon vorhanden!';
            } else {
                $new_pass = $_POST['pass'];
                $md5_pass = md5($new_pass);
                db_query("INSERT INTO prefix_user (name,pass,recht,regist,llogin,email)
		    VALUES('" . $_POST['name'] . "','" . $md5_pass . "'," . $_POST['recht'] . ",'" . time() . "','" . time() . "','" . $_POST['email'] . "')");
                $userid = db_last_id();
                db_query("INSERT INTO prefix_userfields (uid,fid,val) VALUES (" . $userid . ",2,'1')");
                db_query("INSERT INTO prefix_userfields (uid,fid,val) VALUES (" . $userid . ",3,'1')");

                if (isset($_POST['info'])) {
                    $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
                    $page = str_replace('admin.php', 'index.php', $page);
                    $tpl = new tpl ('user/new_user_email', 1);
                    $tpl->set('name', $_POST['name']);
                    $tpl->set('pass', $_POST['pass']);
                    $tpl->set('page', $page);
                    $txt = $tpl->get(0);
                    unset($tpl);
                    icmail ($_POST['email'], 'Admin hat dich angelegt', $txt);
                }
                $msg = 'Benutzer angelegt <a href="javascript:closeThisWindow()">Fenster schlieﬂen</a>';
            }
        }
        $pass = '';
        $email = '';
        $recht = '';
        if (isset($_POST['pass'])) {
            $pass = $_POST['pass'];
        }
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
        }
        if (isset($_POST['recht'])) {
            $recht = $_POST['recht'];
        } else {
            $recht = '-1';
        }
        $tpl = new tpl ('user/new_user', 1);
        $tpl->set('msg', $msg);
        $tpl->set('pass', $pass);
        $tpl->set('email', $email);
        $tpl->set('recht', dblistee($recht, "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC"));
        $tpl->out(0);
        break;
    // einen user komplett loeschen
    case 'deleteUser' :
        $uid = $menu->get(2);
        if ($uid != 1) {
            user_remove($uid);

            ?><html><head><script language="JavaScript" type="text/javascript"><!--
      function closeThisWindow() { opener.location.reload(); opener.focus(); window.close(); } closeThisWindow()
      //--></script></head><body></body></html><?php
        }
        break;
}

?>