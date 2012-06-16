<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

function user_get_group_list($uid) {
    $l = 'Mitglied in Gruppen:<br />';
    $erg = db_query("SELECT `prefix_groups`.`name` FROM `prefix_groupusers` LEFT JOIN `prefix_groups` ON `prefix_groups`.`id` = `prefix_groupusers`.`gid` WHERE `prefix_groupusers`.`uid` = " . $uid);
    while ($r = db_fetch_assoc($erg)) {
        $l .= '- ' . $r[ 'name' ] . '<br />';
    }
    return ($l);
}

function user_get_mod_list($uid, $recht, $modulenames, $modulerights) {
    $mods = $mr = $gr = array();
	    	debug($modulerights);
    foreach ($modulerights as $row) {
		if (!isset($modulenames[$row['mid']])) {
			continue;
		}
        if ($row['uid'] == $recht) {
            $gr[] = $modulenames[$row['mid']];
        } elseif ($row['uid'] == $uid) {
            $mr[] = $modulenames[$row['mid']];
        } elseif($row['uid'] > $uid) {
            break;
        }
    }
    $mods = array_merge(array_diff($gr, $mr), array_diff($mr, $gr));
    natsort($mods);
    return implode(', ', $mods);
}

function getfl($gid) {
    $liste = '';
    $erg = db_query("SELECT `view`,`name`,`reply`,`start`,`mods` FROM `prefix_forums` WHERE `view` = " . $gid . " OR `reply` = " . $gid . " OR `start` = " . $gid . " OR `mods` = " . $gid);
    while ($row = db_fetch_assoc($erg)) {
        $v = ($row[ 'view' ] == $gid ? 'sehen/lesen,' : '');
        $r = ($row[ 'reply' ] == $gid ? 'antworten,' : '');
        $s = ($row[ 'start' ] == $gid ? 'Themen starten,' : '');
        $m = ($row[ 'mods' ] == $gid ? 'Moderieren,' : '');
        $liste .= $row[ 'name' ] . '<span class="smalfont">(' . $v . $r . $s . $m . ')</span>&nbsp;';
    }
    return ($liste);
}

if (isset($_POST[ 'action' ])) {
    $design = new design('Ilch Admin-Control-Panel :: User', '', 0);
    $design->header();
    $wdtext = 'Es ist ein Fehler aufgetreten.';
    $jsadd = '';
    if (chk_antispam('adminuser_action', true) and isset($_POST[ 'uid' ])) {
        $uid = escape($_POST[ 'uid' ], 'integer');
        switch ($_POST[ 'action' ]) {
            // einen user komplett loeschen
            case 'deleteUser':
                $name = get_n($uid);
                if ($uid != 1 and !empty($name)) {
                    user_remove($uid);
                    $wdtext = 'Der User ' . $name . ' wurde erfolgreich gel&ouml;scht.';
                }
                break;
            // das recht eines users aendern
            case 'changeRight':
                $altes_recht = db_result(db_query("SELECT recht FROM prefix_user WHERE id = " . $uid), 0);
                $neues_recht = escape($_POST[ 'newright' ], 'integer');
                if (($neues_recht > $_SESSION[ 'authright' ] AND $altes_recht > $_SESSION[ 'authright' ]) OR ($_SESSION[ 'authid' ] == 1 AND $uid != 1)) {
                    $q = "UPDATE prefix_user SET recht = " . $neues_recht . " WHERE id = " . $uid;
                    db_query($q);
                } else {
                    $jsadd .= 'parent.resetUserRight('. $uid.','.$altes_recht.'); parent.alert(unescape(\'Es ist ein Fehler beim %C4ndern des Rechts aufgetreten\')); ';
                }
                $wdtext = false;
                break;
            // modulrechte fuer einen user aendern
            case 'changeModulRight':
                $modul = escape($_POST[ 'modul' ], 'integer');
                $aktion = $_POST[ 'giveremove' ];
                if ($aktion == 'give' AND 0 == db_result(db_query("SELECT COUNT(*) FROM prefix_modulerights WHERE mid = '" . $modul . "' AND uid = " . $uid), 0)) {
                    db_query("INSERT INTO prefix_modulerights (mid,uid) VALUES ('" . $modul . "'," . $uid . ")");
                } elseif ($aktion == 'remove' AND 1 == db_result(db_query("SELECT COUNT(*) FROM prefix_modulerights WHERE mid = '" . $modul . "' AND uid = " . $uid), 0)) {
                    db_query("DELETE FROM prefix_modulerights WHERE mid = '" . $modul . "' AND uid = " . $uid);
                }
                $wdtext = false;
                break;
        }
    }
    if ($wdtext === false) {
        $antispam = get_antispam('adminuser_action', 0, true);

        ?><script type="text/javascript"><!--
		    function updateParent() { parent.setNewAntispam(document.getElementById('tmp').childNodes[0].value); <?php echo $jsadd; ?>}
		    window.onload = function() { updateParent(); };
		    //--></script>
		    <div id="tmp"><?php
        echo $antispam;

        ?></div>
		<?php
        exit;
    }
    wd('admin.php?' . $menu->get_complete(), $wdtext, 5);
    $design->footer(1);
}

$um = $menu->get(1);
switch ($um) {
    default:
        $design = new design('Ilch Admin-Control-Panel :: User', '', 2);
        $design->header();
        $q = '';
        if (isset($_REQUEST[ 'q' ])) {
            $q = escape($_REQUEST[ 'q' ], 'string');
        }
        $tpl = new tpl('user/user', 1);
        $tpl->set('anzmods', db_result(db_query("SELECT COUNT(*) FROM `prefix_modules` WHERE `fright` = 1"), 0));
        $tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
        $tpl->set_out('q', unescape($q), 0);

        $q = str_replace('*', '%', $q);
        if (strpos($q, '%') === false) {
            $q = $q . '%';
        }

        $limit = 15; // Limit
        $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
        $MPL = db_make_sites($page, "WHERE `name` LIKE '" . $q . "'", $limit, 'admin.php?user', 'user');
        $anfang = ($page - 1) * $limit;
        $class = '';
        $grundrechte = simpleArrayFromQuery('SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` ASC');
        $users = allRowsFromQuery('SELECT `name`,`recht`,`id` FROM `prefix_user` WHERE `name` LIKE "' . $q . '" ORDER BY `recht`,`posts` DESC LIMIT ' . $anfang . ',' . $limit, 'id');
        $userids = array_keys($users);
        $modulerights = allRowsFromQuery('SELECT * FROM `prefix_modulerights` WHERE `uid` < 1 OR `uid` IN ('.implode(',', $userids).') ORDER BY `uid`');
        $modulenames = simpleArrayFromQuery('SELECT `id`, `name` FROM `prefix_modules` WHERE `fright` = 1');
        foreach ($users as $row){
            $class = $class == 'Cmite' ? 'Cnorm' : 'Cmite';
            $row['class']       = $class;
            $row['grouplist']   = user_get_group_list($row['id']);
            $row['modslist']    = user_get_mod_list($row['id'], $row['recht'], $modulenames, $modulerights);
            if (strlen($row['modslist']) > 90) {
                $row['modslist']    = substr($row['modslist'], 0, 87).'...';
            }
            $row['recht']       = arlistee($row['recht'], $grundrechte);
            $tpl->set_ar_out($row, 1);
        }
        $tpl->set_out('MPL', $MPL, 2);
        $design->footer();
        break;
    // gruppen zugehoerigkeiten eines users aendern
    case 'gruppen':
        $uid = $menu->get(2);
        if (isset($_POST[ 'usergroups' ]) and chk_antispam('adminuser_action', true)) {
            $erg = db_query("SELECT `id` FROM `prefix_groups`");
            while ($row = db_fetch_assoc($erg)) {
                $ck = db_count_query("SELECT COUNT(`uid`) FROM `prefix_groupusers` WHERE `uid` = " . $uid . " AND `gid` = " . $row[ 'id' ]);
                if ($ck == 0 AND isset($_POST[ 'grprhave' ][ $row[ 'id' ] ][ $uid ])) {
                    db_query("INSERT INTO `prefix_groupusers` (`uid`,`gid`,`fid`) VALUES ( " . $uid . ", " . $row[ 'id' ] . ", 3 )");
                } elseif ($ck == 1 AND !isset($_POST[ 'grprhave' ][ $row[ 'id' ] ][ $uid ])) {
                    db_query("DELETE FROM `prefix_groupusers` WHERE `uid` = " . $uid . " AND `gid` = " . $row[ 'id' ]);
                }
            }
        }

        $user_name = db_result(db_query("SELECT `name` FROM `prefix_user` WHERE `id` = " . $uid), 0);
        $tpl = new tpl('user/gruppen', 1);
        $tpl->set_ar_out(array(
                'username' => $user_name,
                'userid' => $uid,
				'ANTISPAM' => get_antispam('adminuser_action', 0, true)
                ), 0);
        $class = 'Cnorm';
        $erg = db_query("SELECT `name`,`id` FROM `prefix_groups`");
        while ($row = db_fetch_assoc($erg)) {
            $ck = db_count_query("SELECT COUNT(`uid`) FROM `prefix_groupusers` WHERE `uid` = " . $uid . " AND `gid` = " . $row[ 'id' ]);
            $row[ 'ck' ] = ($ck == 0 ? '' : 'checked');
            $class = ($class == 'Cnorm' ? 'Cmite' : 'Cnorm');
            $row[ 'class' ] = $class;
            $tpl->set_ar_out($row, 1);
        }
        $tpl->out(2);
        break;
    // details eines users anzeigen
    case 1:
        $design = new design('Ilch Admin-Control-Panel :: Userdetails', '- Details', 2);
        $design->header();
        if (isset($_REQUEST[ 'uID' ])) {
            $uid = $_REQUEST[ 'uID' ];
        } else {
            $uid = $menu->get(2);
        }
        $erg = db_query("SELECT `name`,`email`,`id`,`recht`,`wohnort`,`homepage`,`aim`,`msn`,`icq`,`yahoo`,`status`, `sperre`,`staat`,`gebdatum`,`sig`,`opt_pm`,`opt_pm_popup`,`opt_mail`,`geschlecht`,`spezrank`,`avatar` FROM `prefix_user` WHERE `id` = '" . $uid . "'");
        if (db_num_rows($erg) == 0) {
            die('Fehler: Username nicht gefunden <a href="admin.php?user">zur&uuml;ck</a>');
        } else {
            $row = db_fetch_assoc($erg);

            $tpl = new tpl('user/details', 1);
            $row[ 'recht' ] = dbliste($row[ 'recht' ], $tpl, 'recht', "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` ASC");
            $row[ 'staat' ] = '<option></option>' . arliste($row[ 'staat' ], get_nationality_array(), $tpl, 'staat');
            $row[ 'spezrank' ] = '<option></option>' . dbliste($row[ 'spezrank' ], $tpl, 'spezrank', "SELECT `id`, `bez` FROM `prefix_ranks` WHERE `spez` = 1");

            $row[ 'geschlecht0' ] = ($row[ 'geschlecht' ] < 1 ? 'checked' : '');
            $row[ 'geschlecht1' ] = ($row[ 'geschlecht' ] == 1 ? 'checked' : '');
            $row[ 'geschlecht2' ] = ($row[ 'geschlecht' ] == 2 ? 'checked' : '');
            if ($row[ 'status' ] == 1) {
                $row[ 'status1' ] = 'checked';
                $row[ 'status0' ] = '';
            } else {
                $row[ 'status1' ] = '';
                $row[ 'status0' ] = 'checked';
            }
            if ($row[ 'sperre' ] == 1) {
                $row[ 'sperre1' ] = 'checked';
            } else {
                $row[ 'sperre1' ] = '';
            }
            if ($row[ 'opt_mail' ] == 1) {
                $row[ 'opt_mail1' ] = 'checked';
                $row[ 'opt_mail0' ] = '';
            } else {
                $row[ 'opt_mail1' ] = '';
                $row[ 'opt_mail0' ] = 'checked';
            }
            if ($row[ 'opt_pm' ] == 1) {
                $row[ 'opt_pm1' ] = 'checked';
                $row[ 'opt_pm0' ] = '';
            } else {
                $row[ 'opt_pm1' ] = '';
                $row[ 'opt_pm0' ] = 'checked';
            }
            if ($row[ 'opt_pm_popup' ] == 1) {
                $row[ 'opt_pm_popup1' ] = 'checked';
                $row[ 'opt_pm_popup0' ] = '';
            } else {
                $row[ 'opt_pm_popup1' ] = '';
                $row[ 'opt_pm_popup0' ] = 'checked';
            }
            if (@file_exists($row[ 'avatar' ])) {
                $row[ 'avatar' ] = '<img src="' . $row[ 'avatar' ] . '" border="0" /><br />';
            } else {
                $row[ 'avatar' ] = '';
            }
            $row[ 'ANTISPAM' ] = get_antispam('adminuser_action', 0, true);
            $tpl->set_ar_out($row, 0);

            profilefields_change($row[ 'id' ]);

            $tpl->out(1);
        }
        $design->footer();
        break;
    // details des users aendern
    case 2:
        $design = new design('Ilch Admin-Control-Panel :: Userdetails', '- Details', 2);
        $design->header();
        $changeok = true;
        $uid = escape($_POST[ 'uID' ], 'integer');

        $altes_recht = db_result(db_query("SELECT `recht` FROM `prefix_user` WHERE `id` = " . $uid), 0);
        $neues_recht = escape($_POST[ 'urecht' ], 'integer');
        if (($neues_recht <= $_SESSION[ 'authright' ] OR $altes_recht <= $_SESSION[ 'authright' ]) AND $_SESSION[ 'authid' ] > 1) {
            $changeok = false;
        }
        $sperrinfo = '';
        if ($changeok and chk_antispam('adminuser_action', true)) {
            if (isset($_POST[ 'userdel' ])) {
                user_remove($uid);
                wd('admin.php?user', 'User wurde erfolgreich gel&ouml;scht');
            } else {
                $abf = "SELECT * FROM `prefix_user` WHERE `id` = '" . $uid . "'";
                $erg = db_query($abf);
                $row = db_fetch_object($erg);

                if (isset($_POST[ 'passw' ])) {
                    $newPass = genkey(8);
                    $newPassMD5 = md5($newPass);
                    icmail($row->email, 'neues Password', "Hallo\n\nDein Password wurde soeben von einem Administrator geändert es ist nun:\n\n" . $newPass . "\n\nGruß der Administrator");
                    db_query('UPDATE `prefix_user` SET `pass` = "' . $newPassMD5 . '" WHERE `id` = "' . escape($_POST[ 'uID' ], 'integer') . '"');
                }
                if ($_POST['setnewpw'] != '') {
                    $newPassMD5 = md5(escape($_POST['setnewpw'], 'string'));
                    db_query('UPDATE `prefix_user` SET `pass` = "' . $newPassMD5 . '" WHERE `id` = "' . escape($_POST[ 'uID' ], 'integer') . '"');
                }
                // avatar speichern START
                $avatar_sql_update = '';
                if (!empty($_FILES[ 'avatarfile' ][ 'name' ])) {
                    $file_tmpe = $_FILES[ 'avatarfile' ][ 'tmp_name' ];
                    $rile_type = ic_mime_type($_FILES[ 'avatarfile' ][ 'tmp_name' ]);
                    $file_type = $_FILES[ 'avatarfile' ][ 'type' ];
                    $file_size = $_FILES[ 'avatarfile' ][ 'size' ];
                    $fmsg = $lang[ 'avatarisnopicture' ];
                    $size = @getimagesize($file_tmpe);
                    $endar = array(1 => 'gif',
                        2 => 'jpg',
                        3 => 'png'
                        );
                    if (($size[ 2 ] == 1 OR $size[ 2 ] == 2 OR $size[ 2 ] == 3) AND $size[ 0 ] > 10 AND $size[ 1 ] > 10 AND substr($file_type, 0, 6) == 'image/' AND substr($rile_type, 0, 6) == 'image/') {
                        $endung = $endar[ $size[ 2 ] ];
                        $breite = $size[ 0 ];
                        $hoehe = $size[ 1 ];
                        $neuer_name = 'include/images/avatars/' . $uid . '.' . $endung;
                        @unlink(db_result(db_query("SELECT `avatar` FROM `prefix_user` WHERE `id` = " . $uid), 0));
                        move_uploaded_file($file_tmpe, $neuer_name);
                        @chmod($neuer_name, 0777);
                        $avatar_sql_update = ', avatar = "' . $neuer_name . '"';
                        $fmsg = $lang[ 'pictureuploaded' ];
                    }
                } elseif (isset($_POST[ 'avatardel' ])) {
                    $fmsg = $lang[ 'picturedelete' ];
                    @unlink(db_result(db_query("SELECT `avatar` FROM `prefix_user` WHERE `id` = " . $uid), 0));
                    $avatar_sql_update = ', avatar = ""';
                }
                // avatar speichern ENDE
                profilefields_change_save(escape($_POST[ 'uID' ], 'integer'));
                $usaName1 = escape($_POST[ 'usaName1' ], 'string');
                $email = escape($_POST[ 'email' ], 'string');
                $homepage = escape($_POST[ 'homepage' ], 'string');
                $wohnort = escape($_POST[ 'wohnort' ], 'string');
                $icq = escape($_POST[ 'icq' ], 'string');
                $msn = escape($_POST[ 'msn' ], 'string');
                $yahoo = escape($_POST[ 'yahoo' ], 'string');
                $aim = escape($_POST[ 'aim' ], 'string');
                $staat = escape($_POST[ 'staat' ], 'string');
                $spezrank = escape($_POST[ 'spezrank' ], 'integer');
                $geschlecht = escape($_POST[ 'geschlecht' ], 'integer');
                $status = escape($_POST[ 'status' ], 'integer');
                $sperre = escape($_POST[ 'usersperre' ], 'integer');
                $opt_mail = escape($_POST[ 'opt_mail' ], 'integer');
                $opt_pm = escape($_POST[ 'opt_pm' ], 'integer');
                $opt_pm_popup = escape($_POST[ 'opt_pm_popup' ], 'integer');
                $gebdatum = escape($_POST[ 'gebdatum' ], 'string');
                $sig = escape($_POST[ 'sig' ], 'string');
                // Name im Forum aendern
                if ($_POST[ 'forumname' ] == 'on') {
                    $oldname = db_count_query("SELECT `name` FROM `prefix_user` WHERE `id` =" . $uid);
                    if ($oldname != $usaName1) {
                        db_query("UPDATE `prefix_posts` SET `erst` = '" . $usaName1 . "' WHERE `erstid` = " . $uid);
                        db_query("UPDATE `prefix_topics` SET `erst` = '" . $usaName1 . "' WHERE `erst` = '" . $oldname . "'");
                    }
                }
                db_query('UPDATE `prefix_user`
			  SET
					`name`  = "' . $usaName1 . '",
					`recht` = "' . $neues_recht . '",
					`email` = "' . $email . '",
		          `homepage` = "' . $homepage . '",
		          `wohnort` = "' . $wohnort . '",
		          `icq` = "' . $icq . '",
		          `msn` = "' . $msn . '",
		          `yahoo` = "' . $yahoo . '",
		          `aim` = "' . $aim . '",
		          `staat` = "' . $staat . '",
		          `spezrank` = "' . $spezrank . '",
		          `geschlecht` = "' . $geschlecht . '",
		          `status` = "' . $status . '",
		          `sperre` = "' . $sperre . '",
		          `opt_mail` = "' . $opt_mail . '",
		          `opt_pm` = "' . $opt_pm . '",
		          `opt_pm_popup` = "' . $opt_pm_popup . '",
		          `gebdatum` = "' . $gebdatum . '",
		          `sig` = "' . $sig . '"
		          ' . $avatar_sql_update . '
				WHERE `id` = "' . $uid . '"');
            }
            if ($sperre == 1) {
                @db_query("DELETE FROM `prefix_online` WHERE uid = '" . $uid . "' ");
                $sperrinfo = ' und User wurde ausgeloggt';
            }
        }
        wd('admin.php?user-1-' . $uid, 'Das Profil wurde erfolgreich ge&auml;ndert' . $sperrinfo, 2);
        $design->footer();
        break;
    // mal kurz nen neuen user anlegen
    case 'createNewUser':
        $msg = '';
        if (!empty($_POST[ 'name' ]) and !empty($_POST[ 'pass' ]) and !empty($_POST[ 'email' ]) and chk_antispam('adminuser_action', true)) {
            $_POST[ 'name' ] = escape($_POST[ 'name' ], 'string');
            $_POST[ 'recht' ] = escape($_POST[ 'recht' ], 'integer');
            $_POST[ 'email' ] = escape($_POST[ 'email' ], 'string');
            $erg = db_query("SELECT `id` FROM `prefix_user` WHERE `name_clean` = BINARY '" . get_lower($_POST[ 'name' ]) . "'");
            if (db_num_rows($erg) > 0) {
                $msg = 'Der Name ist leider schon vorhanden!';
            } else {
                $new_pass = $_POST[ 'pass' ];
                $md5_pass = md5($new_pass);
                db_query("INSERT INTO `prefix_user` (`name`,`name_clean`,`pass`,`recht`,`regist`,`llogin`,`email`)
		    VALUES('" . $_POST[ 'name' ] . "','" . get_lower($_POST[ 'name' ]) . "','" . $md5_pass . "'," . $_POST[ 'recht' ] . ",'" . time() . "','" . time() . "','" . $_POST[ 'email' ] . "')");
                $userid = db_last_id();
                db_query("INSERT INTO `prefix_userfields` (`uid`,`fid`,`val`) VALUES (" . $userid . ",2,'1')");
                db_query("INSERT INTO `prefix_userfields` (`uid`,`fid`,`val`) VALUES (" . $userid . ",3,'1')");

                if (isset($_POST[ 'info' ])) {
                    $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
                    $page = str_replace('admin.php', 'index.php', $page);
                    $tpl = new tpl('user/new_user_email', 1);
                    $tpl->set('name', $_POST[ 'name' ]);
                    $tpl->set('pass', $_POST[ 'pass' ]);
                    $tpl->set('page', $page);
                    $txt = $tpl->get(0);
                    unset($tpl);
                    icmail($_POST[ 'email' ], 'Admin hat dich angelegt', $txt);
                }
                $msg = 'Benutzer angelegt <a href="javascript:self.parent.ic.modalDialogClose();">Fenster schlie&szlig;en</a>';
            }
        } elseif (isset($_POST['sub']) and chk_antispam('adminuser_action', true)) {
            $msg = 'Du musst Name, Passwort und eine Emailadresse angeben!<br />';
        }
        $pass = '';
        $email = '';
        $recht = '';
        if (isset($_POST[ 'pass' ])) {
            $pass = $_POST[ 'pass' ];
        }
        if (isset($_POST[ 'email' ])) {
            $email = $_POST[ 'email' ];
        }
        if (isset($_POST[ 'recht' ])) {
            $recht = $_POST[ 'recht' ];
        } else {
            $recht = '-1';
        }

        $design = new design('Admin', '', 0);
        $tpl = new tpl('user/new_user', 1);

        $design->addheader($tpl->get(0));
        $design->header();

        $tpl->set('msg', $msg);
        $tpl->set('pass', $pass);
        $tpl->set('email', $email);
        $tpl->set('recht', dblistee($recht, "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC"));
		$tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
        $tpl->out(1);
        $design->footer();
        break;
    case 'cmr':
        require_once 'include/includes/class/iSmarty.php';
        $id = (int)$menu->get(2);
        list($name, $recht) = db_fetch_row(db_query('SELECT `name`, `recht` FROM `prefix_user` WHERE `id` = ' . $id));
        $data = allRowsFromQuery('SELECT m.*, (ISNULL(grmr.mid)+ISNULL(umr.mid))%2 AS hasright, IF(ISNULL(grmr.mid),2,1) AS rightfrom FROM `prefix_modules` m
        LEFT JOIN `prefix_modulerights` grmr ON m.id = grmr.mid AND grmr.uid = ' . $recht . '
        LEFT JOIN `prefix_modulerights` umr ON m.id = umr.mid AND umr.uid = ' . $id . '
        WHERE m.fright = 1
        ORDER BY hasright DESC, m.name', 'id');

        $design = new design('', '', 0);
        $design->header();

        $smarty = new iSmarty();
        $smarty->assign('site', $menu->get(0));
        $smarty->assign('id', $id);
        $smarty->assign('name', $name);

        if (isset($_POST['subCMR'])) {
            if (isset($_POST['mid']) and is_array($_POST['mid'])) {
                //Aenderungen vornehmen
                foreach ($_POST['mid'] as $mid) {
                    if ($data[$mid]['hasright'] == 1) {
                        continue; //Recht schon gesetzt
                    } else {
                        //Recht setzen
                        db_query('INSERT INTO `prefix_modulerights` (`uid`, `mid`) VALUE ('.$id.','.$mid.')');
                    }
                }
            }
            //Prüfe auf geloeschte Rechte
            foreach ($data as $row){
                if ($row['hasright'] == 1) {
                    if (isset($_POST['mid']) and !in_array($row['id'], $_POST['mid'])) {
                        //Recht entfernen
                        if ($row['rightfrom'] == 1) { //Entfernen, wenn vom Grundrecht gegeben (einfuegen als Modulrecht)
                            db_query('INSERT INTO `prefix_modulerights` (`uid`, `mid`) VALUE ('.$id.','.$mid.')');
                        } else { //Entfernen, wenn als Modulrecht
                            db_query('DELETE FROM `prefix_modulerights` WHERE `mid` = ' . $row['id'] . ' AND `uid` = ' . $id);
                        }
                    }
                } else {
                    break;
                }
            }
            //Neu auslesen
            $data = allRowsFromQuery('SELECT m.*, (ISNULL(grmr.mid)+ISNULL(umr.mid))%2 AS hasright FROM `prefix_modules` m
            LEFT JOIN `prefix_modulerights` grmr ON m.id = grmr.mid AND grmr.uid = ' . $recht . '
            LEFT JOIN `prefix_modulerights` umr ON m.id = umr.mid AND umr.uid = ' . $id . '
            WHERE m.fright = 1
            ORDER BY hasright DESC, m.name', 'id');
            $smarty->assign('info', '&Auml;nderungen wurden gespeichert.');
        }

        $smarty->assign('data', $data);

        $smarty->display('modulrechte.tpl');
        $design->footer(1);
        break;
}

?>