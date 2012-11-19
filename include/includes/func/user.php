<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
/**
 * Authentifiziert den Benutzer, legt die Rechte fest und aktualisiert Datenbankeinträge zum Onlinestatus
 * @param string $menuComplete QueryString (menu::get_complete()) des Seitenaufrufs
 */
function user_identification($menuComplete)
{
    user_auth();
    user_login_check();
    user_update_database($menuComplete);
    user_check_url_rewrite();
}

/**
 * Benutzer authentifizieren
 */
function user_auth()
{
    debug('user - auth gestartet: ' . session_id());
    $cn = session_und_cookie_name();
    if (!user_key_in_db() OR !isset($_SESSION['authid']) OR (isset($_SESSION['authsess']) AND $_SESSION['authsess'] != $cn)) {
        debug('user - nicht in db oder nicht authid');

        user_set_guest_vars();
        user_set_user_online();
        // wenn cn cookie vorhanden
        // dann checken ob er sich damit einloggen darf
        if (isset($_COOKIE[$cn])) {
            if (!user_login_check(true)) {
                // gruppen, und modulzugehoerigkeit setzten (gäste)
                user_set_grps_and_modules();
            };
        }
    }
}

/**
 * Entfernt die SessionId aus der URL, wenn die Option dafür gesetzt ist
 * @global array $allgAr
 */
function user_check_url_rewrite()
{
    global $allgAr;
    if (!loggedin() AND $allgAr['show_session_id'] == 0) {
        output_reset_rewrite_vars();
    }
}

/**
 * Setzt Aufenthaltsort und Zeit des Seitenaufrufs des Benutzers in der Datenbank
 * @global array $allgAr
 * @param string $menuComplete QueryString des Seitenaufrufs
 */
function user_update_database($menuComplete)
{
    $dif = date('Y-m-d H:i:s', time() - 7200);
    global $allgAr;
    if (empty($menuComplete)) {
        $menuComplete = $allgAr['smodul'] . ' (Startseite)';
    }
    db_query(
        'UPDATE `prefix_online` SET `uptime` = "' . date('Y-m-d H:i:s') . '",'
        . '`content` = "' . $menuComplete . '"  WHERE `sid` = "' . session_id() . '"'
    );

    if (function_exists('content_stats')) {
        content_stats($menuComplete);
    }
    debug('"' . $menuComplete . '" als Aufenthaltsort erkannt');
    db_query('DELETE FROM `prefix_online` WHERE `uptime` < "' . $dif . '"');
    if (loggedin()) {
        db_query("UPDATE `prefix_user` SET `llogin` = '" . time() . "' WHERE `id` = '" . $_SESSION['authid'] . "'");
    }
}

/**
 * Speichert eintrag für Benutzer in der online Tabelle
 * @global array $allgAr
 */
function user_set_user_online()
{
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
function user_key_in_db()
{
    if (1 == db_result(db_query('SELECT COUNT(*) FROM `prefix_online` WHERE `sid` = "' . session_id() . '" AND ipa = "' . getip() . '"'), 0)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Gibt Session und Cookie Name zurück, der aus Webadresse und Datenbankprefix berechnet wird
 * @return string
 */
function session_und_cookie_name()
{
    return (md5(dirname($_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"]) . DBPREF));
}

/**
 * Loggt den Benutzer ein (von Postdaten 'email' und 'pass' oder über das Cookie)
 * @global array $allgAr
 * @global type $menu
 * @param boolean $auto Gibt an ob POST Daten (false) oder Cookie als Quelle der Daten benutzt wird
 * @return boolean Erfolg des Logins
 */
function user_login_check($auto = false)
{
    global $allgAr, $menu;
    $formpassed = false;
    $cn = session_und_cookie_name();
    $crypt = new PwCrypt();

    if (isset($_POST['user_login_sub']) and isset($_POST['email']) and isset($_POST['pass'])) {
        debug('posts vorhanden');
        // prüfen ob Eingabe = Email oder Username
        if (preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $_POST['email'], $matsch)) {
            $lower = get_lower($_POST['email']);
            $value = escape_for_email($lower);
            $term = "email = BINARY '" . $value . "'";
            debug('Login mit Email: ' . $value);
        } else {
            $lower = get_lower($_POST['email']);
            $value = escape($lower, 'string');
            $term = "name_clean = '" . $value . "'";
            debug('Login mit Nickname: ' . $value);
        }
        if ($lower != $value) {
            return false;
        }
        $formpassed = true;
    } elseif ($auto) {
        $dat = explode('=', $_COOKIE[$cn]);
        if (count($dat) > 1) {
            $id = escape($dat[0], 'integer');
            unset($dat[0]);
            $pw = implode('=', $dat);
            debug('Login mit Cookie - id: ' . $id . ' - hash: ' . $pw);
        } else {
            $id = $pw = 0;
        }
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
            or (($auto and $row['pass']) and $crypt->checkPasswd($row['pass'], $pw))
        ) {
            debug('passwort stimmt ... ' . $row['name']);
            $_SESSION['authname'] = $row['name'];
            $_SESSION['authid'] = (int) $row['id'];
            $_SESSION['authright'] = (int) $row['recht'];
            $_SESSION['authlang'] = $allgAr['lang'];
            $_SESSION['lastlogin'] = (int) $row['llogin'];
            $_SESSION['authsess'] = $cn;
            $_SESSION['sperre'] = $row['sperre'];
            db_query('DELETE FROM `prefix_online` WHERE `uid` = ' . $_SESSION['authid'] . ' AND `sid` != "' . session_id() . '"');
            db_query('UPDATE `prefix_online` SET `uid` = ' . $_SESSION['authid'] . ' WHERE `sid` = "' . session_id() . '"');
            // Falls noch einfaches MD5 in DB, den neuen Hash erstellen und in die Datenbank schreiben,
            // bei Cookie dieses Löschen, um den User zum Login mit Passwort zu zwingen
            if (!PwCrypt::isCryptHash($row['pass'])) {
                if ($auto) {
                    user_remove_cookie();
                } else {
                    $newHash = $crypt->cryptPasswd($_POST['pass']);
                    db_query('UPDATE `prefix_user` SET `pass` = "' . $newHash . '" WHERE id = ' . $row['id']);
                }
            }
            // Cookie setzen, wenn User eingeloggt bleiben will
            if (isset($_POST['cookie'])) {
                $cookiepath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if (strlen($cookiepath) > 1) {
                    $cookiepath .= '/';
                }
                setcookie($cn, $row['id'] . '=' . $crypt->cryptPasswd($row['pass']), strtotime('+1 year'), $cookiepath, '', false, true);
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

/**
 * Setzz Session Variablen für Gäste (oder für ausgeloggte Benutzer)
 * @global array $allgAr
 */
function user_set_guest_vars()
{
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

/**
 * Alles als gelesen markieren (indem Lastlogin auf aktuellen Zeitpunkt gesetzt wird)
 */
function user_markallasread()
{
    $_SESSION['lastlogin'] = time();
}

/**
 * Ausloggen des Benutzers
 */
function user_logout()
{
    user_set_guest_vars();
    db_query("UPDATE `prefix_online` SET `uid` = " . $_SESSION['authid'] . " WHERE `sid` = '" . session_id() . "'");
    user_remove_cookie();
}

/**
 * Löschen des Cookies für den Autologin
 */
function user_remove_cookie()
{
    setcookie(session_und_cookie_name(), "", time() - 999999999999, "/");
}

/**
 * Setzt Gruppen- und Modulrechte für eingeloggte Benutzer
 */
function user_set_grps_and_modules()
{
    $_SESSION['authgrp'] = array();
    $_SESSION['authmod'] = array();
    if (loggedin()) {
        $erg = db_query("SELECT `gid` FROM `prefix_groupusers` WHERE `uid` = " . $_SESSION['authid']);
        while ($row = db_fetch_assoc($erg)) {
            $_SESSION['authgrp'][$row['gid']] = true;
        }
        $erg = db_query(
            'SELECT m.`url` FROM `prefix_modules` m'
            . ' LEFT JOIN `prefix_modulerights` grmr ON m.id = grmr.mid AND grmr.uid = ' . $_SESSION['authright']
            . ' LEFT JOIN `prefix_modulerights` umr ON m.id = umr.mid AND umr.uid = ' . $_SESSION['authid']
            . ' WHERE m.fright = 1 AND (ISNULL(grmr.mid)+ISNULL(umr.mid))%2 = 1'
        );
        while ($row = db_fetch_assoc($erg)) {
            $_SESSION['authmod'][$row['url']] = true;
        }
    } else {
        $erg = db_query(
            'SELECT m.`url` FROM `prefix_modules` m '
            . 'INNER JOIN `prefix_modulerights` grmr ON m.id = grmr.mid AND grmr.uid = 0 '
            . 'WHERE m.fright = 1'
        );
        while ($row = db_fetch_assoc($erg)) {
            $_SESSION['authmod'][$row['url']] = true;
        }
    }
}

/**
 * Prüft, ob der Benutzer eingeloggt ist
 * @return boolean
 */
function loggedin()
{
    return has_right(-1);
}

/**
 * Prüft, ob der Benutzer ein Administrator ist
 * @return boolean
 */
function is_admin()
{
    return has_right(-9);
}

/**
 * Prüft, ob der Benutzer wenigstens ein Co-Administrator ist
 * @return boolean
 */
function is_coadmin()
{
    return has_right(-8);
}

/**
 * Prüft, ob der Benutzer wenigsten Site-Administrationsrechte hat, oder Modulrechte des angegeben Moduls
 * @param string $module Modulname
 * @return boolean
 */
function is_siteadmin($module = null)
{
    if (has_right(-7)) {
        return (true);
    }
    if (!is_null($module) AND has_right(null, $module)) {
        return (true);
    }
    return (false);
}

/**
 * Prüft, ob der Benutzer wenigstens eines der angegebenen Rechte besitzt (als Administrator hat man immer alle Rechte)
 * @param integer | array $recht Grundrechte (0 bis -9) oder Teammitglied (> 0), oder mehrere Angaben in einem Array
 * @param string $modul Modulname
 * @param boolean $strict Wenn $strict auf true steht, hat auch ein Administrator nicht automatisch Modul oder Gruppenrechte
 * @return boolean
 */
function has_right($recht, $modul = '', $strict = false)
{
    if (!is_array($recht) AND !is_null($recht)) {
        $recht = array($recht);
    }

    if (!$strict and $_SESSION['authright'] == - 9) {
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

/**
 * Prüft, ob der Benutzer in den Adminbereich darf, entweder weil er Co-Admin ist Modulrechte inne hat
 * @param menu $menu
 * @param boolean $showLogin Zeigt Loginformular an, wenn die Rechte nicht ausreichen
 * @return boolean
 */
function user_has_admin_right($menu, $showLogin = true)
{
    if ($_SESSION['authright'] <= - 8) {
        return true;
    } else {
        $uri_to_check1 = $menu->get(0);
        $uri_to_check2 = $menu->get(1);
        if (count($_SESSION['authmod']) < 1 OR !loggedin()) {
            if ($showLogin === true) {
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
        } elseif ((isset($_SESSION['authmod'][$uri_to_check1]) AND $_SESSION['authmod'][$uri_to_check1] == true) OR (isset($_SESSION['authmod'][$uri_to_check1 . '-' . $uri_to_check2]) AND $_SESSION['authmod'][$uri_to_check1 . '-' . $uri_to_check2] == true)) {
            return (true);
        } elseif (count($_SESSION['authmod']) > 0 AND loggedin()) {
            if ($showLogin === true) {
                foreach ($_SESSION['authmod'] as $k => $v) {
                    $x = $k;
                    break;
                }
                $x = explode('-', $x);
                $menu->set_url(0, $x[0]);
                if (isset($x[1])) {
                    $menu->set_url(1, $x[1]);
                }
            }
            return (true);
        }
    }
    return (false);
}

/**
 * Benutzer anlegen oder in der usercheck Tabelle vormerken, wenn Registrierung per Mail bestätigt werden muss
 * @global array $allgAr
 * @global array $lang
 * @param string $name Benutzername
 * @param string $mail E-Mail-Adresse
 * @param string $pass Passwort
 * @return boolean
 */
function user_regist($name, $mail, $pass)
{
    global $allgAr, $lang;

    $crypt = new PwCrypt();

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
        $new_pass = PwCrypt::getRndString(8, PwCrypt::LETTERS| PwCrypt::NUMBERS | PwCrypt::SPECIAL_CHARACTERS);
    } else {
        $new_pass = $pass;
    }

    $confirmlinktext = '';
    // confirm insert in confirm tb not confirm insert in user tb
    if ($allgAr['forum_regist_confirm_link'] == 1) {
        // confirm link + text ... bit of shit put it in languages file
        $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
        $id = md5(uniqid(rand()));
        $crypted_pass = $crypt->cryptPasswd($new_pass);
        $confirmlinktext = "\n" . $lang['registconfirm'] . "\n\n" . sprintf($lang['registconfirmlink'], $page, $id);
        db_query(
            'INSERT INTO `prefix_usercheck` (`check`,`name`,`email`,`pass`, `datime`,`ak`) '
            . 'VALUES ("' . $id . '","' . $name . '","' . $mail . '","' . $crypted_pass . '",NOW(),1)'
        );
    } else {
        db_query(
            'INSERT INTO `prefix_user` '
            . '(`name`,`name_clean`,`pass`, `recht`,`regist`,`llogin`,`email`,`status`,`opt_mail`,`opt_pm`) '
            . 'VALUES("' . $name . '","' . $name_clean . '","' . $crypted_pass . '",-1,"' . time() . '","'
            . time() . '","' . $mail . '",1,1,1)'
        );
        $userid = db_last_id();
    }
    $regmail = sprintf($lang['registemail'], $name, $confirmlinktext, $mail, $new_pass);

    icmail($mail, 'Anmeldung', $regmail); // email an user
    return true;
}

/**
 * Benutzer entfernen
 * @param integer $uid BenutzerId
 */
function user_remove($uid)
{
    $row = @db_fetch_object(db_query("SELECT `recht`,`avatar` FROM `prefix_user` WHERE `id` = " . $uid));
    if ($uid != 1 AND ($_SESSION['authid'] == $uid OR $_SESSION['authid'] == 1 OR (is_coadmin() AND $_SESSION['authright'] < $row->recht))) {
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

/**
 * Sendet eine private Nachricht an einem Benutzer
 * @param integer $sid BenutzerId des Senders (0 für Systemnachrichten)
 * @param interger | array $eid BenutzerId des Empfängers (oder mehere in einem Array)
 * @param string $titel
 * @param string $text
 * @param integer $status Status der Nachricht -1 nur bei Sender gelöscht, 0 bei beiden vorhanden,
 * 1 nur bei Empfänger gelöscht
 */
function sendpm($sid, $eid, $titel, $text, $status = 0)
{
    $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
    // Testen, ob Array. Sonst umwandeln.
    if (!is_array($eid)) {
        $eid = array($eid);
    }
    // Alle Emfänger durchlaufen
    foreach ($eid as $empf) {
        // PM schreiben und ID speichern
        db_query("INSERT INTO `prefix_pm` (`sid`,`eid`,`time`,`titel`,`txt`,`status`) VALUES (" . $sid . "," . $empf . ",'" . time() . "','" . $titel . "','" . $text . "'," . $status . ")");
        $last_id = db_last_id();
        // Alle Zeiten der letzten PMs abfragen, die nach dem letzten Login des Empfängers verschickt wurden
        $erg = db_query("SELECT `b`.`time` FROM `prefix_user` AS `a` LEFT JOIN `prefix_pm` AS `b` ON `a`.`id` = `b`.`eid` AND `b`.`id` != " . $last_id . " WHERE `a`.`id` = " . $empf . " AND `a`.`llogin` < `b`.`time`");
        // Wenn keine PM gefunden wurde, Email schreiben
        if (db_num_rows($erg) == 0) {
            // Email-Adresse abfragen und Email verschicken
            $mail = db_result(db_query("SELECT `email` FROM `prefix_user` WHERE `id` = " . $empf), 0);
            if (!empty($mail)) {
                icmail($mail, "Du hast eine neue Nachricht", "Hallo,\ndu hast eben eine Neue Nachricht mit dem Betreff '" . $titel . "' bekommen. Diese Nachricht kannst du nun unter folgender Adresse mit Deinen Logindaten aufrufen: " . $page . "?forum-privmsg-showmsg-" . $last_id . "\n\nWir wünschen Dir noch einen schönen Tag!");
            }
        }
    }
}

/**
 * Gibt den Pfad zum Avatarbild des Benutzer zurück (u.U. Standardavatare, wenn keines hochgeladen wurde)
 * @param integer $id BenutzerId
 * @return string
 */
function get_avatar($id)
{
    $pfad = 'include/images/avatars/';
    if (is_numeric($id) and $id != 0) {
        $row = db_fetch_assoc(db_query('SELECT `avatar`, `geschlecht` FROM `prefix_user` WHERE `id` = ' . $id));
        if (isset($row['avatar']) and file_exists($row['avatar'])) {
            $avatar = $row['avatar'];
        } elseif ($row['geschlecht'] == 1) {
            $avatar = $pfad . 'maennlich.jpg';
        } elseif ($row['geschlecht'] == 2) {
            $avatar = $pfad . 'weiblich.jpg';
        } else {
            $avatar = $pfad . 'wurstegal.jpg';
        }
    } else {
        $avatar = $pfad . 'wurstegal.jpg';
    }
    return $avatar;
}
