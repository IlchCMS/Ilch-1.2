<?php
// hier werden alle user spezifischen funktionen
// definert...
function user_identification () {
    user_auth();
    user_login_check();
    user_update_database();
    user_check_url_rewrite();
}

function user_auth () {
    debug ('user - auth gestartet' . session_id());
    $cn = session_und_cookie_name();
    if (!user_key_in_db()
            OR !isset($_SESSION['authid'])
            OR (isset($_SESSION['authsess']) AND $_SESSION['authsess'] != $cn)) {
        debug ('user - nicht in db oder nicht authid');

        user_set_guest_vars();
        user_set_user_online ();
        // wenn cn cookie vorhanden
        // dann checken ob er sich damit einloggen darf
        if (isset($_COOKIE[$cn])) {
            user_auto_login_check();
        }
        // gruppen, und modulzugehoerigkeit setzten
        user_set_grps_and_modules();
    }
}

function user_check_url_rewrite() {
    global $allgAr;
    if (!loggedin() AND $allgAr['show_session_id'] == 0) {
        // loescht die sessionid von allen urls
        // auch urls wie formulare usw. damit
        // suchmaschienen bots nicht iritiert sind ;)
        // output_reset_rewrite_vars ist eine php funktion
        // nicht unnoetig dannach suchen ;) ...
        output_reset_rewrite_vars ();
    }
}

function user_update_database () {
    $dif = date('Y-m-d H:i:s', time() - 7200);
    db_query("UPDATE prefix_online SET uptime = now() WHERE sid = '" . session_id() . "'");
    db_query("DELETE FROM prefix_online WHERE uptime < '" . $dif . "'");
    if (loggedin()) {
        db_query("UPDATE prefix_user SET llogin = '" . time() . "' WHERE id = '" . $_SESSION['authid'] . "'");
    }
}

function user_set_user_online () {
    global $allgAr;
    if (0 == db_result(db_query("SELECT COUNT(*) FROM prefix_online WHERE sid = '" . session_id() . "'"), 0)) {
        db_query("INSERT INTO prefix_online (sid,uptime,ipa) VALUES ('" . session_id() . "',now(),'" . getip() . "')");
    }
    $_SESSION['authgfx'] = $allgAr['gfx'];
}

function user_key_in_db() {
    if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_online WHERE sid = '" . session_id() . "'"), 0)) {
        return (true);
    } else {
        return (false);
    }
}

function session_und_cookie_name () {
    return (md5(dirname($_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"]) . DBPREF));
}

function user_login_check () {
	global $allgAr;
	
    if (isset( $_POST['user_login_sub'] ) AND ( isset( $_POST['email'] ) OR isset( $_POST['name'] ) ) AND isset( $_POST['pass'] )) {
        debug ('posts vorhanden');
		
		if (isset( $_POST['email'] )){
			$lower = get_lower ($_POST['email']);
			$value = escape_for_email ($lower);
			$term = "email = BINARY '" . $value . "'";
		} else if (isset( $_POST['name'] )){
			$lower = get_lower ($_POST['name']);
			$value = escape_nickname ($lower);
			$term = "name_clean = BINARY '" . $value . "'";
		}
		
		if( $lower != $value ){
			return false;
		}
		
        $erg = db_query("SELECT name,id,recht,pass,llogin FROM prefix_user WHERE ".$term);
        if (db_num_rows($erg) == 1) {
            debug ('user gefunden');
            $row = db_fetch_assoc($erg);
            if ($row['pass'] == md5($_POST['pass'])) {
                debug ('passwort stimmt ... ' . $row['name']);
                $_SESSION['authname'] = $row['name'];
                $_SESSION['authid'] = $row['id'];
                $_SESSION['authright'] = $row['recht'];
				$_SESSION['authlang'] = $allgAr['lang'];
                $_SESSION['lastlogin'] = $row['llogin'];
                $_SESSION['authsess'] = session_und_cookie_name();
                db_query("UPDATE prefix_online SET uid = " . $_SESSION['authid'] . " WHERE sid = '" . session_id() . "'");
                setcookie($_SESSION['authsess'], $row['id'] . '=' . $row['pass'] , time() + 31104000, "/");
                user_set_grps_and_modules();
                return (true);
            }
        }
        global $menu;
        $menu->set_url (0, 'user');
        $menu->set_url (1, 'login');
    }
    return (false);
}

function user_auto_login_check () {
	global $allgAr;
    $cn = session_und_cookie_name();
    $dat = explode('=', $_COOKIE[$cn]);
    $id = $pw = 0;
    if (isset($dat[0])) {
        $id = escape($dat[0], 'integer');
    }
    if (isset($dat[1])) {
        $pw = $dat[1];
    }
    debug (' pw ' . $pw);
    debug (' id ' . $id);
    $erg = db_query("SELECT name,id,recht,pass,llogin FROM prefix_user WHERE id = " . $id);
    if (db_num_rows($erg) == 1) {
        debug ('benutzer gefunden');
        $row = db_fetch_assoc($erg);
        if ($row['pass'] == $pw) {
            debug ('passwoerter stimmen');
            debug ($row['name']);
            $_SESSION['authname'] = $row['name'];
            $_SESSION['authid'] = $row['id'];
            $_SESSION['authright'] = $row['recht'];
			$_SESSION['authlang'] = $allgAr['lang'];
            $_SESSION['lastlogin'] = $row['llogin'];
            $_SESSION['authsess'] = $cn;
            db_query("UPDATE prefix_online SET uid = " . $_SESSION['authid'] . " WHERE sid = '" . session_id() . "'");
            setcookie($cn, $row['id'] . '=' . $row['pass'], time() + 31104000, "/");
            return (true);
        }
    }

    user_logout ();
    return (false);
}

function user_set_guest_vars() {
	global $allgAr;
    $_SESSION['authname'] = 'Gast';
    $_SESSION['authid'] = 0;
    $_SESSION['authright'] = 0;
	$_SESSION['authlang'] = $allgAr['lang'];
    $_SESSION['lastlogin'] = time();
    $_SESSION['authgrp'] = array();
    $_SESSION['authmod'] = array();
    $_SESSION['authsess'] = session_und_cookie_name();
}

function user_markallasread () {
    $_SESSION['lastlogin'] = time();
}

function user_logout () {
    // global $allgAr;
    // $_SESSION = array();
    // $_SESSION['authgfx'] = $allgAr['gfx'];
    user_set_guest_vars();
    db_query("UPDATE prefix_online SET uid = " . $_SESSION['authid'] . " WHERE sid = '" . session_id() . "'");
    setcookie(session_und_cookie_name(), "", time() - 999999999999, "/");
    // if (isset($_COOKIE[session_name()])) {
    // setcookie(session_name(), '', time()-99999999999931104000, '/');
    // }
    // setcookie(session_und_cookie_name(), "", time()-999999999999, "/" );
    // session_destroy();
}

function user_set_grps_and_modules () {
    $_SESSION['authgrp'] = array();
    $_SESSION['authmod'] = array();
    if (loggedin()) {
        $erg = db_query("SELECT gid FROM prefix_groupusers WHERE uid = " . $_SESSION['authid']);
        while ($row = db_fetch_assoc ($erg)) {
            $_SESSION['authgrp'][$row['gid']] = true;
        }
        $erg = db_query("SELECT DISTINCT url
    FROM prefix_modulerights
    left join prefix_modules on prefix_modules.id = prefix_modulerights.mid
    WHERE uid = " . $_SESSION['authid']);
        while ($row = db_fetch_assoc ($erg)) {
            $_SESSION['authmod'][$row['url']] = true;
        }
    }
}

function loggedin () {
    if (has_right(- 1)) {
        return (true);
    } else {
        return (false);
    }
}
function is_admin () {
    if (has_right(- 9)) {
        return (true);
    } else {
        return (false);
    }
}
function is_coadmin () {
    if (has_right(- 8)) {
        return (true);
    } else {
        return (false);
    }
}
function is_siteadmin ($m = null) {
    if (has_right(- 7)) {
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
function has_right ($recht, $modul = '') {
    if (!is_array($recht) AND !is_null($recht)) {
        $recht = array ($recht);
    }

    if ($_SESSION['authright'] == - 9) {
        return (true);
    }

    if (!is_null($recht)) {
        foreach ($recht as $v) {
            if (($v <= 0 AND $v >= $_SESSION['authright']) OR (isset($_SESSION['authgrp'][$v]) AND $_SESSION['authgrp'][$v] === true)) {
                return (true);
            }
        }
    }

    if (!empty($modul) AND isset($_SESSION['authmod'][$modul]) AND $_SESSION['authmod'][$modul] === true) {
        return (true);
    }

    return (false);
}
// ## admin
// wenn der 2. parameter weggelassen wird oder auf true gesetzt wird
// dann wird ein login formular angezeigt, wenn der user kein admin ist.
// wird der parameter auf false gesetzt wird das login formular nicht angezeigt.
// erste parameter ist das menu objekt...
function user_has_admin_right (&$menu, $sl = true) {
    if ($_SESSION['authright'] <= - 8) { // co leader...
        return (true);
    } else {
        $uri_to_check1 = $menu->get(0);
        $uri_to_check2 = $menu->get(1);
        if (count($_SESSION['authmod']) < 1 OR !loggedin()) {
            if ($sl === true) {
                if (!loggedin()) {
                    $tpl = new tpl ('user/login.htm');
                    $tpl->set_out('WDLINK', 'admin.php', 0);
                } else {
                    echo '<strong>Keine Berechtigung!</strong> <a href="index.php">Startseite</a>';
                }
            }
            return (false);
        } elseif ((isset($_SESSION['authmod'][$uri_to_check1]) AND $_SESSION['authmod'][$uri_to_check1] == true)
                OR (isset($_SESSION['authmod'][$uri_to_check1 . '-' . $uri_to_check2]) AND $_SESSION['authmod'][$uri_to_check1 . '-' . $uri_to_check2] == true)) {
            return (true);
        } elseif (count($_SESSION['authmod']) > 0 AND loggedin()) {
            if ($sl === true) {
                foreach($_SESSION['authmod'] as $k => $v) {
                    $x = $k;
                    break;
                }
                $x = explode('-', $x);
                $menu->set_url (0, $x[0]);
                if (isset($x[1])) {
                    $menu->set_url (1, $x[1]);
                }
            }
            return (true);
        }
    }
    return (false);
}

function user_regist ($name, $mail, $pass) {
    global $allgAr, $lang;
	
	$name_clean = get_lower( $name );
    $erg = db_query("SELECT id FROM prefix_user WHERE name_clean = BINARY '" . $name_clean . "'");
    if (db_num_rows($erg) > 0) {
        return (false);
    }

	$mail = get_lower( $mail );
    $erg = db_query("SELECT id FROM prefix_user WHERE email = BINARY '" . $mail . "'");
    if (db_num_rows($erg) > 0) {
        return (false);
    }

    if ($allgAr['forum_regist_user_pass'] == 0) {
        $new_pass = genkey(8);
    } else {
        $new_pass = $pass;
    }

    $md5_pass = md5($new_pass);
    $confirmlinktext = '';
    // confirm insert in confirm tb not confirm insert in user tb
    if ($allgAr['forum_regist_confirm_link'] == 1) {
        // confirm link + text ... bit of shit put it in languages file
        $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
        $id = md5 (uniqid (rand()));
        $confirmlinktext = "\n" . $lang['registconfirm'] . "\n\n" . sprintf($lang['registconfirmlink'], $page, $id);
        db_query("INSERT INTO prefix_usercheck (`check`,name,email,pass,datime,ak)
		VALUES ('" . $id . "','" . $name . "','" . $mail . "','" . $md5_pass . "',NOW(),1)");
    } else {
        db_query("INSERT INTO prefix_user (name,name_clean,pass,recht,regist,llogin,email,status,opt_mail,opt_pm)
		VALUES('" . $name . "','" . $name_clean . "','" . $md5_pass . "',-1,'" . time() . "','" . time() . "','" . $mail . "',1,1,1)");
        $userid = db_last_id();
    }
    $regmail = sprintf($lang['registemail'], $name, $confirmlinktext, $mail, $new_pass);

    icmail($mail, 'Anmeldung', $regmail); # email an user

    return (true);
}

function user_remove($uid) {
    $row = @db_fetch_object(db_query("SELECT recht,avatar FROM prefix_user WHERE id = " . $uid));
    if ($uid != 1 AND ($_SESSION['authid'] == $uid OR $_SESSION['authid'] == 1 OR (is_coadmin() AND $_SESSION['authright'] < $row->recht))) {
        db_query("DELETE FROM prefix_user WHERE id = " . $uid);
        db_query("DELETE FROM prefix_userfields WHERE uid = " . $uid);
        db_query("DELETE FROM prefix_groupusers WHERE uid = " . $uid);
        db_query("DELETE FROM prefix_modulerights WHERE uid = " . $uid);
        db_query("DELETE FROM prefix_pm WHERE eid = " . $uid);
        db_query("DELETE FROM prefix_online WHERE uid = " . $uid);
        // Usergallery entfernen
        $sql = db_query("SELECT id,endung FROM prefix_usergallery WHERE uid = " . $uid);
        while ($r = db_fetch_object($sql)) {
            @unlink("include/images/usergallery/img_$r->id.$r->endung");
            @unlink("include/images/usergallery/img_thumb_$r->id.$r->endung");
        }
        db_query("DELETE FROM prefix_usergallery WHERE uid = " . $uid);
        // Avatar
        @unlink($row->avatar);
    }
}

function sendpm ($sid, $eid, $ti, $te, $status = 0) {
    db_query("INSERT INTO `prefix_pm` (sid,eid,time,titel,txt,status) VALUES (" . $sid . "," . $eid . ",'" . time() . "','" . $ti . "','" . $te . "'," . $status . ")");
}

?>