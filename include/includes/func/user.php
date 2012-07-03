<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */

// hier werden alle user spezifischen funktionen
// definert..
function user_identification($m) {
    user_auth();
    user_login_check();
    user_update_database($m);
    user_check_url_rewrite();
}

function user_auth() {
    debug('user - auth gestartet: ' . session_id());
    $cn = session_und_cookie_name();
    if (!user_key_in_db() OR !isset($_SESSION[ 'authid' ]) OR (isset($_SESSION[ 'authsess' ]) AND $_SESSION[ 'authsess' ] != $cn)) {
        debug('user - nicht in db oder nicht authid');

        user_set_guest_vars();
        user_set_user_online();
        // wenn cn cookie vorhanden
        // dann checken ob er sich damit einloggen darf
        if (isset($_COOKIE[ $cn ])) {
            if (!user_login_check(true)) {
                // gruppen, und modulzugehoerigkeit setzten (gäste)
                user_set_grps_and_modules();
            };
        }
    }
}

function user_check_url_rewrite() {
    global $allgAr;
    if (!loggedin() AND $allgAr[ 'show_session_id' ] == 0) {
        // loescht die sessionid von allen urls
        // auch urls wie formulare usw. damit
        // suchmaschienen bots nicht iritiert sind ;)
        // output_reset_rewrite_vars ist eine php funktion
        // nicht unnoetig dannach suchen ;) ...
        output_reset_rewrite_vars();
    }
}

function user_update_database($m) {
    $dif = date('Y-m-d H:i:s', time() - 7200);
	global $allgAr;
	if (empty($m)) {
		$m = $allgAr['smodul']. ' (Startseite)';
	}
    db_query('UPDATE `prefix_online` SET `uptime` = "' . date('Y-m-d H:i:s') . '",
										`content` = "'.$m.'"  WHERE `sid` = "' . session_id() . '"');

	if (function_exists('content_stats')) {
	  content_stats($m);
	}
	debug('"'.$m.'" als Aufenthaltsort erkannt');
    db_query('DELETE FROM `prefix_online` WHERE `uptime` < "' . $dif . '"');
    if (loggedin()) {
        db_query("UPDATE `prefix_user` SET `llogin` = '" . time() . "' WHERE `id` = '" . $_SESSION[ 'authid' ] . "'");
    }
}

function user_set_user_online() {
    global $allgAr;
    $qry = db_query('SELECT `ipa` FROM `prefix_online` WHERE `sid` = "' . session_id() . '"');
    if (db_num_rows($qry) == 0) {
        db_query('INSERT INTO `prefix_online` (`sid`,`uptime`,`ipa`) VALUES ("' . session_id() . '", "' . date('Y-m-d H:i:s') . '", "' . getip() . '")');
    } elseif (db_result($qry) != getip()) {
        db_query('UPDATE `prefix_online` SET `uptime` = "' . date('Y-m-d H:i:s') . '", `ipa` =  "' . getip() . '", uid = 0 WHERE `sid` = "' . session_id() . '"');
    }
    if (!isset($_SESSION['authgfx'])) {
        $_SESSION['authgfx'] = $allgAr['gfx'];
    }
}

/**
* Prüft ob die Session Id in der Datenbank vorhanden (mit der IP des Users) in der Datenbank steht
*/
function user_key_in_db() {
    if (1 == db_result(db_query('SELECT COUNT(*) FROM `prefix_online` WHERE `sid` = "' . session_id() . '" AND ipa = "' . getip() . '"'), 0)) {
        return true;
    } else {
        return false;
    }
}

function session_und_cookie_name() {
	return (md5(dirname($_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]) . DBPREF));
}


function user_login_check($auto=false) {
    global $allgAr, $menu;
    $formpassed = false;
    $cn = session_und_cookie_name();
	$crypt = new PasswdCrypt();

    if (isset($_POST[ 'user_login_sub' ]) and isset($_POST[ 'email' ]) and isset($_POST[ 'pass' ])) {
        debug('posts vorhanden');
        // prüfen ob Eingabe = Email oder Username
        if (preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $_POST[ 'email' ], $matsch)) {
            $lower = get_lower($_POST[ 'email' ]);
            $value = escape_for_email($lower);
            $term = "email = BINARY '" . $value . "'";
            debug('Login mit Email: ' . $value);
        } else {
            $lower = get_lower($_POST[ 'email' ]);
            $value = escape($lower , 'string');
            $term = "name_clean = '" . $value . "'";
            debug('Login mit Nickname: ' . $value);
        }
        if ($lower != $value) {
            return false;
        }
        $formpassed = true;
    } elseif ($auto) {
        $dat = explode('=', $_COOKIE[ $cn ]);
        $id = $pw = 0;
        if (isset($dat[0])) {
            $id = escape($dat[0], 'integer');
        }
        if (isset($dat[1])) {
            $pw = $dat[1];
        }
        debug('Login mit Cookie - id: ' . $id . ' - hash: ' . $pw);
        $term = '`id` = ' . $id;
    }
    if (!isset($term)) {
        return;
    }
    $erg = db_query("SELECT `name`,`id`,`recht`,`pass`,`llogin`, `sperre` FROM `prefix_user` WHERE " . $term);
    mysql_error();
    if (isset($erg) and db_num_rows($erg) == 1) {
        $row = db_fetch_assoc($erg);
		debug('user gefunden... ' . $row['name']);

        if ($row['sperre'] == 1) {
            debug('user gesperrt... ' . $row['name']);
            return false;
        } elseif ((!$auto and $crypt->checkPasswd($_POST['pass'], $row['pass']))
		or (($auto and $row['pass']) and $crypt->checkPasswd($row['pass'],$pw))) {
            debug('passwort stimmt ... ' . $row['name']);
            $_SESSION['authname'] = $row['name'];
            $_SESSION['authid'] = (int) $row['id'];
            $_SESSION['authright'] = (int) $row['recht'];
            $_SESSION['authlang'] = $allgAr['lang'];
            $_SESSION['lastlogin'] = (int) $row['llogin'];
            $_SESSION['authsess'] = $cn;
            $_SESSION['sperre'] = $row['sperre'];
            db_query('DELETE FROM `prefix_online` WHERE `uid` = ' . $_SESSION['authid'] . ' AND `sid` != "' . session_id() . '"');
            db_query('UPDATE `prefix_online` SET `uid` = ' . $_SESSION[ 'authid' ] . ' WHERE `sid` = "' . session_id() . '"');
			//Fals noch einfaches MD5 in DB, dem User ne PM schicken
			if(!preg_match('/^\$([156]|2a)\$?/',$row['pass'])){
                $erg = db_query("SELECT `id` FROM `prefix_user` WHERE `recht` = -9 ORDER BY `id` ASC");
                if($admin = mysql_fetch_row($erg)){
                    sendpm($admin[0], $row['id'], 'Bitte dein Passwort ändern', "Sehr geehrter User,
wir möchten Sie darauf hinweisen, dass aus Sicherheitsgründen die Art wie Ihr Passwort in der Datenbank gespeichert wird geändert wurde. Daher bitten wir Sie unter
[url]http://finke.extrem-mods.de/ilch1_2/index.php?user-8[/url]
Ihr Passwort mindestens ein mal zu ändern.

Anmerkung:
Dies ist eine automatisch vom System Generierte PM, Bitte Antworten Sie nicht darauf. Diese PM wirst du bei jedem Login erhalten, bis Ihr Passwort geändert wurde.");
                }
			}
            //Cookie setzen, wenn User eingeloggt bleiben will
            if (isset($_POST['cookie'])) {
                $cookiepath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if (strlen($cookiepath) > 1) {
                    $cookiepath .= '/';
                }
                setcookie($cn, $row[ 'id' ] . '=' . $crypt->cryptPasswd($row['pass']), strtotime('+1 year'), $cookiepath, '', false, true);
            }
            user_set_grps_and_modules();
            return true;
        }
    }
    if ($formpassed) {
        $menu->set_url(0, 'user');
        $menu->set_url(1, 'login');
    }
    return false;
}

function user_set_guest_vars() {
	global $allgAr;
	$_SESSION[ 'authname' ] = 'Gast';
	$_SESSION[ 'authid' ] = 0;
	$_SESSION[ 'authright' ] = 0;
	$_SESSION[ 'authlang' ] = $allgAr[ 'lang' ];
	$_SESSION[ 'lastlogin' ] = time();
	$_SESSION[ 'authgrp' ] = array();
	$_SESSION[ 'authmod' ] = array();
	$_SESSION[ 'authsess' ] = session_und_cookie_name();
}

function user_markallasread() {
	$_SESSION[ 'lastlogin' ] = time();
}

function user_logout() {
    // global $allgAr;
    // $_SESSION = array();
    // $_SESSION['authgfx'] = $allgAr['gfx'];
    user_set_guest_vars();
    db_query("UPDATE `prefix_online` SET `uid` = " . $_SESSION[ 'authid' ] . " WHERE `sid` = '" . session_id() . "'");
    setcookie(session_und_cookie_name(), "", time() - 999999999999, "/");
    // if (isset($_COOKIE[session_name()])) {
    // setcookie(session_name(), '', time()-99999999999931104000, '/');
    // }
    // setcookie(session_und_cookie_name(), "", time()-999999999999, "/" );
    // session_destroy();
}

function user_set_grps_and_modules() {
    $_SESSION['authgrp'] = array();
    $_SESSION['authmod'] = array();
    if (loggedin()) {
        $erg = db_query("SELECT `gid` FROM `prefix_groupusers` WHERE `uid` = " . $_SESSION['authid']);
        while ($row = db_fetch_assoc($erg)) {
            $_SESSION['authgrp'][$row['gid']] = true;
        }
        $erg = db_query('SELECT m.`url` FROM `prefix_modules` m
        LEFT JOIN `prefix_modulerights` grmr ON m.id = grmr.mid AND grmr.uid = ' . $_SESSION['authright'] . '
        LEFT JOIN `prefix_modulerights` umr ON m.id = umr.mid AND umr.uid = ' . $_SESSION['authid'] . '
        WHERE m.fright = 1 AND (ISNULL(grmr.mid)+ISNULL(umr.mid))%2 = 1');
        while ($row = db_fetch_assoc($erg)) {
            $_SESSION['authmod'][$row['url']] = true;
        }
    } else {
        $erg = db_query('SELECT m.`url` FROM `prefix_modules` m
        INNER JOIN `prefix_modulerights` grmr ON m.id = grmr.mid AND grmr.uid = 0
        WHERE m.fright = 1');
        while ($row = db_fetch_assoc($erg)) {
            $_SESSION['authmod'][$row['url']] = true;
        }
    }
}

function loggedin() {
    return has_right(-1);
}
function is_admin() {
    return has_right(-9);
}
function is_coadmin() {
    return has_right(-8);
}
function is_siteadmin($m = null) {
    if (has_right(-7)) {
        return (true);
    }
    if (!is_null($m) AND has_right(null, $m)) {
        return (true);
    }
    return (false);
}
// diese funktion liefert immer true wenn es ein admin ist.
// wenn kein kein admin wird geprueft ob der user
// entweder ein angegebenes recht oder in einer angegebene
// gruppe ist. oder ob er fals angegben das modulrecht hat.
// wenn eines von diesen 3 kriterien stimmt wird true ansonsten
// wenn keins uebereinstimmt false zurueck gegeben.
// wenn $strict true übergeben wird, erhält auch ein Admin nicht automatisch das Recht (nur bei Teams und Modulen relevant)
function has_right($recht, $modul = '', $strict = false) {
    if (!is_array($recht) AND !is_null($recht)) {
        $recht = array($recht);
    }

    if (! $strict and $_SESSION['authright'] == - 9) {
        return true;
    }

    if (!is_null($recht)) {
        foreach ($recht as $v) {
            if (($v <= 0 AND $v >= $_SESSION['authright']) OR (isset($_SESSION['authgrp'][$v]) AND $_SESSION['authgrp'][$v] === true)) {
                return true;
            }
        }
    }

    if (!empty($modul) AND isset($_SESSION['authmod'][$modul]) AND $_SESSION['authmod'][$modul] === true) {
        return true;
    }

    return false;
}
// ## admin
// wenn der 2. parameter weggelassen wird oder auf true gesetzt wird
// dann wird ein login formular angezeigt, wenn der user kein admin ist.
// wird der parameter auf false gesetzt wird das login formular nicht angezeigt.
// erste parameter ist das menu objekt...
function user_has_admin_right(&$menu, $sl = true) {
    if ($_SESSION[ 'authright' ] <= - 8) { // co leader...
        return true;
    } else {
        $uri_to_check1 = $menu->get(0);
        $uri_to_check2 = $menu->get(1);
        if (count($_SESSION[ 'authmod' ]) < 1 OR !loggedin()) {
            if ($sl === true) {
                if (!loggedin()) {
                    $design = new design('', '', 0);
                    $menu->set_url(0, 'user');
                    load_modul_lang();
                    $tpl = new tpl('user/login.htm');
                    $design->addheader($tpl->get(0));
                    $design->header();
                    $tpl->set_out('WDLINK', 'admin.php', 1);
                    $design->footer();
                } else {
                    echo '<strong>Keine Berechtigung!</strong> <a href="index.php">Startseite</a>';
                }
            }
            return (false);
        } elseif ((isset($_SESSION[ 'authmod' ][ $uri_to_check1 ]) AND $_SESSION[ 'authmod' ][ $uri_to_check1 ] == true) OR (isset($_SESSION[ 'authmod' ][ $uri_to_check1 . '-' . $uri_to_check2 ]) AND $_SESSION[ 'authmod' ][ $uri_to_check1 . '-' . $uri_to_check2 ] == true)) {
            return (true);
        } elseif (count($_SESSION[ 'authmod' ]) > 0 AND loggedin()) {
            if ($sl === true) {
                foreach ($_SESSION[ 'authmod' ] as $k => $v) {
                    $x = $k;
                    break;
                }
                $x = explode('-', $x);
                $menu->set_url(0, $x[ 0 ]);
                if (isset($x[ 1 ])) {
                    $menu->set_url(1, $x[ 1 ]);
                }
            }
            return (true);
        }
    }
    return (false);
}

function user_regist($name, $mail, $pass) {
    global $allgAr, $lang;

	$crypt = new PasswdCrypt();

    $name_clean = get_lower($name);
    $erg = db_query("SELECT `id` FROM `prefix_user` WHERE `name_clean` = BINARY '" . $name_clean . "'");
    if (db_num_rows($erg) > 0) {
        return (false);
    }

    $mail = get_lower($mail);
    $erg = db_query("SELECT `id` FROM `prefix_user` WHERE `email` = BINARY '" . $mail . "'");
    if (db_num_rows($erg) > 0) {
        return (false);
    }

    if ($allgAr[ 'forum_regist_user_pass' ] == 0) {
        $new_pass = PasswdCrypt::getRndString(8, WITH_NUMBERS | WITH_SPECIAL_CHARACTERS);
    } else {
        $new_pass = $pass;
    }

    $confirmlinktext = '';
    // confirm insert in confirm tb not confirm insert in user tb
    if ($allgAr[ 'forum_regist_confirm_link' ] == 1) {
        // confirm link + text ... bit of shit put it in languages file
        $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
        $id = md5(uniqid(rand()));
		$crypted_pass = $crypt->cryptPasswd($new_pass);
        $confirmlinktext = "\n" . $lang[ 'registconfirm' ] . "\n\n" . sprintf($lang[ 'registconfirmlink' ], $page, $id);
        db_query("INSERT INTO `prefix_usercheck` (`check`,`name`,`email`,`pass`, `datime`,`ak`)
		VALUES ('" . $id . "','" . $name . "','" . $mail . "','" . $crypted_pass . "',NOW(),1)");
    } else {
        db_query("INSERT INTO `prefix_user` (`name`,`name_clean`,`pass`, `recht`,`regist`,`llogin`,`email`,`status`,`opt_mail`,`opt_pm`)
		VALUES('" . $name . "','" . $name_clean . "','" . $crypted_pass . "',-1,'" . time() . "','" . time() . "','" . $mail . "',1,1,1)");
        $userid = db_last_id();
    }
    $regmail = sprintf($lang[ 'registemail' ], $name, $confirmlinktext, $mail, $new_pass);

    icmail($mail, 'Anmeldung', $regmail); // email an user
    return (true);
}

function user_remove($uid) {
    $row = @db_fetch_object(db_query("SELECT `recht`,`avatar` FROM `prefix_user` WHERE `id` = " . $uid));
    if ($uid != 1 AND ($_SESSION[ 'authid' ] == $uid OR $_SESSION[ 'authid' ] == 1 OR (is_coadmin() AND $_SESSION[ 'authright' ] < $row->recht))) {
        db_query("DELETE FROM `prefix_user` WHERE `id` = " . $uid);
        db_query("DELETE FROM `prefix_userfields` WHERE `uid` = " . $uid);
        db_query("DELETE FROM `prefix_groupusers` WHERE `uid` = " . $uid);
        db_query("DELETE FROM `prefix_modulerights` WHERE `uid` = " . $uid);
        db_query("DELETE FROM `prefix_pm` WHERE `eid` = " . $uid);
        db_query("DELETE FROM `prefix_online` WHERE `uid` = " . $uid);
        // Usergallery entfernen
        $sql = db_query("SELECT `id`,`endung` FROM `prefix_usergallery` WHERE `uid` = " . $uid);
        while ($r = db_fetch_object($sql)) {
            @unlink("include/images/usergallery/img_" . $r->id . "." . $r->endung);
            @unlink("include/images/usergallery/img_thumb_" . $r->id . "." . $r->endung);
        }
        db_query("DELETE FROM `prefix_usergallery` WHERE `uid` = " . $uid);
        // Avatar
        @unlink($row->avatar);
    }
}

function sendpm($sid, $eid, $ti, $te, $status = 0) {
    $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
    // Testen, ob Array. Sonst umwandeln.
    if (!is_array($eid)) {
        $eid = array($eid);
    }
    // Alle Emfänger durchlaufen
    foreach ($eid AS $empf) {
        // PM schreiben und ID speichern
        db_query("INSERT INTO `prefix_pm` (`sid`,`eid`,`time`,`titel`,`txt`,`status`) VALUES (" . $sid . "," . $empf . ",'" . time() . "','" . $ti . "','" . $te . "'," . $status . ")");
        $last_id = db_last_id();
        // Alle Zeiten der letzten PMs abfragen, die nach dem letzten Login des Empfängers verschickt wurden
        $erg = db_query("SELECT `b`.`time` FROM `prefix_user` AS `a` LEFT JOIN `prefix_pm` AS `b` ON `a`.`id` = `b`.`eid` AND `b`.`id` != " . $last_id . " WHERE `a`.`id` = " . $empf . " AND `a`.`llogin` < `b`.`time`");
        // Wenn keine PM gefunden wurde, Email schreiben
        if (db_num_rows($erg) == 0) {
            // Email-Adresse abfragen und Email verschicken
            $mail = db_result(db_query("SELECT `email` FROM `prefix_user` WHERE `id` = " . $empf), 0);
            if (!empty($mail)) {
                icmail($mail, "Du hast eine neue Nachricht", "Hallo,\ndu hast eben eine Neue Nachricht mit dem Betreff '" . $ti . "' bekommen. Diese Nachricht kannst du nun unter folgender Adresse mit Deinen Logindaten aufrufen: " . $page . "?forum-privmsg-showmsg-" . $last_id . "\n\nWir wünschen Dir noch einen schönen Tag!");
            }
        }
    }
}

function get_avatar($id) {
	$pfad = 'include/images/avatars/';
	if (is_numeric($id) and $id != 0)
	{
		$row = db_fetch_assoc(db_query('SELECT `avatar`, `geschlecht` FROM `prefix_user` WHERE `id` = ' . $id));
		if 		(isset($row['avatar']) and file_exists($row['avatar'])) { $avatar = $row['avatar']; }
		elseif 	($row['geschlecht'] == 1) 								{ $avatar = $pfad . 'maennlich.jpg'; }
		elseif 	($row['geschlecht'] == 2) 								{ $avatar = $pfad . 'weiblich.jpg'; }
		else															{ $avatar = $pfad . 'wurstegal.jpg'; }
	} else {
		$avatar = $pfad . 'wurstegal.jpg';
	}
	return $avatar;
}
