<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: Users :: Profil';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Profil' . $extented_forum_menu_sufix;
$header = Array( 'jquery/pstrength-min.1.2.js', 'jquery/pstrength.css' );
$design = new design ($title , $hmenu, 1);

if ($_SESSION['authright'] <= - 1) {
    if (empty ($_POST['submit'])) {
        $design->header( $header );
        $abf = 'SELECT email,wohnort,homepage,aim,msn,icq,yahoo,avatar,status,staat,gebdatum,sig,opt_pm_popup,opt_pm,opt_mail,geschlecht,spezrank FROM `prefix_user` WHERE id = "' . $_SESSION['authid'] . '"';
        $erg = db_query($abf);
        if (db_num_rows($erg) > 0) {
            $row = db_fetch_assoc($erg);

            $tpl = new tpl ('user/profil_edit');
            $row['staat'] = '<option></option>' . arliste ($row['staat'] , get_nationality_array() , $tpl , 'staat');
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

            $row['avatarbild'] = (file_exists ($row['avatar']) ? '<img src="' . $row['avatar'] . '" alt=""><br />' : '');
            $row['Fabreite'] = $allgAr['Fabreite'];
            $row['Fahohe'] = $allgAr['Fahohe'];
            $row['Fasize'] = $allgAr['Fasize'];
            $row['forum_max_sig'] = $allgAr['forum_max_sig'];
            $row['uid'] = $_SESSION['authid'];
            $row['forum_usergallery'] = $allgAr['forum_usergallery'];
            $tpl->set_ar_out($row, 0);
            if ($allgAr['forum_avatar_upload']) $tpl->out(1);
            $tpl->set_ar_out($row, 2);
            profilefields_change ($_SESSION['authid']);
            $tpl->out(3);
        } else {
            $tpl = new tpl ('user/login.htm');
            $tpl->set_out('WDLINK', 'index.php', 0);
        }
    } else { // submit
        // change poassword
        if (!empty($_POST['np1']) AND !empty($_POST['np2']) AND !empty($_POST['op'])) {
            if ($_POST['np1'] == $_POST['np2']) {
                $akpw = db_result(db_query("SELECT pass FROM prefix_user WHERE id = " . $_SESSION['authid']), 0);
                if ($akpw == md5($_POST['op'])) {
                    $newpw = md5($_POST['np1']);
                    db_query("UPDATE prefix_user SET pass = '" . $newpw . "' WHERE id = " . $_SESSION['authid']);
                    setcookie(session_und_cookie_name(), $_SESSION['authid'] . '=' . $newpw, time() + 31104000, "/");
                    $fmsg = $lang['passwortchanged'];
                } else {
                    $fmsg = $lang['passwortwrong'];
                }
            } else {
                $fmsg = $lang['passwortnotequal'];
            }
        }
        // avatar speichern START
        $avatar_sql_update = '';
        if (!empty ($_FILES['avatarfile']['name']) AND $allgAr['forum_avatar_upload']) {
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
                $fmsg = $lang['avatarcannotupload'];
                if ($file_size <= $allgAr['Fasize'] AND $breite <= $allgAr['Fabreite'] AND $hoehe <= $allgAr['Fahohe']) {
                    $neuer_name = 'include/images/avatars/' . $_SESSION['authid'] . '.' . $endung;
                    @unlink (db_result(db_query("SELECT avatar FROM prefix_user WHERE id = " . $_SESSION['authid']), 0));
                    move_uploaded_file ($file_tmpe , $neuer_name);
                    @chmod($neuer_name, 0777);
                    $avatar_sql_update = "avatar = '" . $neuer_name . "',";
                    $fmsg = $lang['pictureuploaded'];
                }
            }
        } elseif (isset($_POST['avatarloeschen'])) {
            $fmsg = $lang['picturedelete'];
            @unlink (db_result(db_query("SELECT avatar FROM prefix_user WHERE id = " . $_SESSION['authid']), 0));
            $avatar_sql_update = "avatar = '',";
        }
        // avatar speichern ENDE
        // email aendern
        if ($_POST['email'] != $_POST['aemail']) {
            $id = $_SESSION['authid'] . '||' . md5 (uniqid (rand()));
            db_query("INSERT INTO prefix_usercheck (`check`,email,datime,ak)
    VALUES ('" . $id . "','" . escape($_POST['email'], 'string') . "',NOW(),3)");
            $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
            $text = $lang['changedthemail'] . sprintf ($lang['registconfirmlink'], $page, $id);
            icmail ($_POST['email'], $lang['mail'] . ' ' . $lang['changed'], $text);
            $fmsg = $lang['pleaseconfirmmail'];
        }

        // remove account
        if (isset($_POST['removeaccount'])) {
            $id = $_SESSION['authid'] . '-remove-' . md5 (uniqid (rand()));
            db_query("INSERT INTO prefix_usercheck (`check`,email,datime,ak)
    VALUES ('" . $id . "','" . escape($_POST['email'], 'string') . "',NOW(),5)");
            $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
            $text = $lang['removeconfirm'] . sprintf ($lang['registconfirmlink'], $page, $id);
            icmail ($_POST['email'], html_entity_decode($lang['removeaccount']), $text);
            $fmsg = $lang['pleaseconfirmremove'];
        }
        // remove account
        // statische felder speichern
        db_query("UPDATE prefix_user
			  SET
          homepage = '" . get_homepage(escape($_POST['homepage'], 'string')) . "',
          wohnort = '" . escape($_POST['wohnort'], 'string') . "',
          icq = '" . escape($_POST['icq'], 'string') . "',
          msn = '" . escape($_POST['msn'], 'string') . "',
          yahoo = '" . escape($_POST['yahoo'], 'string') . "',
          " . $avatar_sql_update . "
          aim = '" . escape($_POST['aim'], 'string') . "',
          staat = '" . escape($_POST['staat'], 'string') . "',
          geschlecht = '" . escape($_POST['geschlecht'], 'string') . "',
          status = '" . escape($_POST['status'], 'string') . "',
          opt_mail = '" . escape($_POST['opt_mail'], 'string') . "',
          opt_pm = '" . escape($_POST['opt_pm'], 'string') . "',
          opt_pm_popup = '" . escape($_POST['opt_pm_popup'], 'string') . "',
          gebdatum = '" . get_datum(escape($_POST['gebdatum'], 'string')) . "',
          sig = '" . substr(escape($_POST['sig'], 'string'), 0, $allgAr['forum_max_sig']) . "'
				WHERE id = " . $_SESSION['authid']
            );
        // change other profil fields
        profilefields_change_save ($_SESSION['authid']);
        $design->header();
        // definie and print msg
        $fmsg = (isset($fmsg) ? $fmsg : $lang['changesuccessful']);
        wd('?user-8' , $fmsg , 3);
    }
} else {
    $tpl = new tpl ('user/login');
    $tpl->set_out('WDLINK', '?user-8', 0);
}

$design->footer();

?>