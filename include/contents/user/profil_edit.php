<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

require_once('include/includes/func/profile_image.php');

$title = $allgAr[ 'title' ] . ' :: Users :: Profil';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Profil' . $extented_forum_menu_sufix;
$header = Array(
    'jquery/pstrength-min.1.2.js',
    'jquery/pstrength.css',
	'jquery/jquery.validate.js',
	'forms/profiledit.js'
    );
$design = new design($title, $hmenu, 1);

if ($_SESSION[ 'authright' ] <= - 1) {
    if (empty($_POST[ 'submit' ])) {
        $design->header($header);
        $abf = 'SELECT * FROM `prefix_user` WHERE `id` = "' . $_SESSION[ 'authid' ] . '"';
        $erg = db_query($abf);
        if (db_num_rows($erg) > 0) {
            $row = db_fetch_assoc($erg);

            $tpl = new tpl('user/profil_edit');
            $row[ 'staat' ] = '<option></option>' . arliste($row[ 'staat' ], get_nationality_array(), $tpl, 'staat');
            $row[ 'geschlecht0' ] = ($row[ 'geschlecht' ] < 1 ? 'checked' : '');
            $row[ 'geschlecht1' ] = ($row[ 'geschlecht' ] == 1 ? 'checked' : '');
            $row[ 'geschlecht2' ] = ($row[ 'geschlecht' ] == 2 ? 'checked' : '');
            // TODO diesen code ins template auslagern mit ({_if_})
            if ($row[ 'status' ] == 1) {
                $row[ 'status1' ] = 'checked';
                $row[ 'status0' ] = '';
            } else {
                $row[ 'status1' ] = '';
                $row[ 'status0' ] = 'checked';
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
            // Avatar
            $row[ 'avatarbild' ] = (file_exists($row[ 'avatar' ]) ? '<img src="' . $row[ 'avatar' ] . '" alt=""><br />' : '');
            $row[ 'Fabreite' ] = $allgAr[ 'Fabreite' ];
            $row[ 'Fahohe' ] = $allgAr[ 'Fahohe' ];
            // Userpic
            $row[ 'userpic' ] = (file_exists($row[ 'userpic' ]) ? '<img src="' . $row[ 'userpic' ] . '" alt=""><br />' : '');
            $row[ 'userpic_Fabreite' ] = $allgAr[ 'userpic_Fabreite' ];
            $row[ 'userpic_Fahohe' ] = $allgAr[ 'userpic_Fahohe' ];
            $row[ 'forum_max_sig' ] = $allgAr[ 'forum_max_sig' ];
            $row[ 'uid' ] = $_SESSION[ 'authid' ];
            $row[ 'forum_usergallery' ] = $allgAr[ 'forum_usergallery' ];
            $tpl->set_ar_out($row, 0);
            if ($allgAr[ 'forum_avatar_upload' ])
                $tpl->out(1);
            $tpl->set_ar_out($row, 2);
            profilefields_change($_SESSION[ 'authid' ]);
            $tpl->out(3);
        } else {
            $tpl = new tpl('user/login.htm');
            $tpl->set_out('WDLINK', 'index.php', 0);
        }
    } else { // submit
        // change password
        if (!empty($_POST[ 'np1' ]) AND !empty($_POST[ 'np2' ]) AND !empty($_POST[ 'op' ])) {
            if ($_POST[ 'np1' ] == $_POST[ 'np2' ]) {
                $akpw = mysql_fetch_array(db_query("SELECT `pass`, `salt` FROM prefix_user WHERE id = " . $_SESSION[ 'authid' ]));
                if (($akpw['salt'].$akpw['pass']) == crypt($_POST[ 'op' ], $akpw['salt'])) {
                    $newpw = explode('$', crypt($_POST['np1'], $akpw['salt']));
					$new_salt = $salt = '$'.$newpw[0].'$rounds='.mt_rand(1000,999999999).'$'.genkey(16, WITH_NUMBERS).'$';
                    db_query("UPDATE prefix_user SET pass = '" . $newpw[3] . "', `salt` = '".$new_salt."' WHERE id = " . $_SESSION[ 'authid' ]);
                    setcookie(session_und_cookie_name(), $_SESSION[ 'authid' ] . '=' . $newpw, time() + 31104000, "/");
                    $fmsg = $lang[ 'passwortchanged' ];
                } else {
                    $fmsg = $lang[ 'passwortwrong' ];
                }
            } else {
                $fmsg = $lang[ 'passwortnotequal' ];
            }
        }
		// avatar START
        $avatar_sql_update = '';
        if (!empty($_FILES[ 'avatarfile' ][ 'name' ]) AND $allgAr[ 'forum_avatar_upload' ]) 
        {
        $fende = preg_replace("/.+\.([a-zA-Z]+)$/", "\\1", $_FILES[ 'avatarfile' ][ 'name' ]); 
        $fende = $endung = strtolower($fende); 
        $name = substr($_FILES[ 'avatarfile' ][ 'name' ],0,-1*(strlen($fende)+1)); 
        $size = @getimagesize ($_FILES[ 'avatarfile' ][ 'tmp_name' ]);	
          if (!empty($_FILES[ 'avatarfile' ][ 'name' ]) AND $size[0] > 10 AND $size[1] > 10 
          												AND ($size[2] == 2 OR $size[2] == 3 OR $size[2] == 1)
          												AND ($fende == 'gif' OR $fende == 'jpg' OR $fende == 'jpeg' OR $fende == 'png'))
          {
          $bild_url = 'include/images/avatars/'.$_SESSION[ 'authid' ].'_org.'.$endung;
          $bild_thumb = 'include/images/avatars/'.$_SESSION[ 'authid' ].'.'.$endung;
          @unlink($bild_url); 
		  @unlink(db_result(db_query("SELECT `avatar` FROM `prefix_user` WHERE `id` = " . $_SESSION[ 'authid' ]), 0));
            if (@move_uploaded_file ($_FILES[ 'avatarfile' ][ 'tmp_name' ], $bild_url)) 
            {
            create_avatar ($bild_url, $bild_thumb, $allgAr[ 'Fabreite' ], $allgAr[ 'Fahohe' ] );
            @chmod($bild_url, 0777); @chmod($bild_thumb, 0777);
            $avatar_sql_update = "avatar = '" . $bild_thumb . "',";
			@unlink($bild_url);
            $fmsg = $lang[ 'pictureuploaded' ];
            } 
            else			
            { 
            $fmsg = $lang[ 'avatarcannotupload' ]; 
            }
          }
          else
          {
          $fmsg = $lang[ 'avatarisnopicture' ];
          }
        } 
        elseif (isset($_POST[ 'avatarloeschen' ])) 
        {
        $fmsg = $lang[ 'picturedelete' ];
        @unlink(db_result(db_query("SELECT `avatar` FROM `prefix_user` WHERE `id` = " . $_SESSION[ 'authid' ]), 0));
        $avatar_sql_update = "avatar = '',";
        }
		// avatar ENDE
		// userpic START
        $userpic_sql_update = '';
        if (!empty($_FILES[ 'userpicfile' ][ 'name' ]) AND $allgAr[ 'forum_avatar_upload' ]) 
        {
        $fende = preg_replace("/.+\.([a-zA-Z]+)$/", "\\1", $_FILES[ 'userpicfile' ][ 'name' ]); 
        $fende = $endung = strtolower($fende); 
        $name = substr($_FILES[ 'userpicfile' ][ 'name' ],0,-1*(strlen($fende)+1)); 
        $size = @getimagesize ($_FILES[ 'userpicfile' ][ 'tmp_name' ]);	
          if (!empty($_FILES[ 'userpicfile' ][ 'name' ]) AND $size[0] > 10 AND $size[1] > 10 
          												AND ($size[2] == 2 OR $size[2] == 3 OR $size[2] == 1)
          												AND ($fende == 'gif' OR $fende == 'jpg' OR $fende == 'jpeg' OR $fende == 'png'))
          {
          $bild_url = 'include/images/userpics/'.$_SESSION[ 'authid' ].'_org.'.$endung;
          $bild_thumb = 'include/images/userpics/'.$_SESSION[ 'authid' ].'.'.$endung;
          @unlink($bild_url);
		  @unlink(db_result(db_query("SELECT `avatar` FROM `prefix_user` WHERE `id` = " . $_SESSION[ 'authid' ]), 0));
            if (@move_uploaded_file ($_FILES[ 'userpicfile' ][ 'tmp_name' ], $bild_url)) 
            {
            create_avatar ($bild_url, $bild_thumb, $allgAr[ 'userpic_Fabreite' ], $allgAr[ 'userpic_Fahohe' ] );
            @chmod($bild_url, 0777); @chmod($bild_thumb, 0777);
            $userpic_sql_update = "userpic = '" . $bild_thumb . "',";
			@unlink($bild_url);
            $fmsg = $lang[ 'pictureuploaded' ];
            } 
            else			
            { 
            $fmsg = $lang[ 'userpiccannotupload' ]; 
            }
          }
          else
          {
          $fmsg = $lang[ 'userpicisnopicture' ];
          }
        } 
        elseif (isset($_POST[ 'userpicloeschen' ])) 
        {
        $fmsg = $lang[ 'picturedelete' ];
        @unlink(db_result(db_query("SELECT `userpic` FROM `prefix_user` WHERE `id` = " . $_SESSION[ 'authid' ]), 0));
        $userpic_sql_update = "userpic = '',";
        }
		// userpic ENDE
        // email aendern
        if ($_POST[ 'email' ] != $_POST[ 'aemail' ]) {
            $id = $_SESSION[ 'authid' ] . '||' . md5(uniqid(rand()));
            db_query("INSERT INTO `prefix_usercheck` (`check`,`email`,`datime`,`ak`)
    VALUES ('" . $id . "','" . escape($_POST[ 'email' ], 'string') . "',NOW(),3)");
            $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
            $text = $lang[ 'changedthemail' ] . sprintf($lang[ 'registconfirmlink' ], $page, $id);
            icmail($_POST[ 'email' ], $lang[ 'mail' ] . ' ' . $lang[ 'changed' ], $text);
            $fmsg = $lang[ 'pleaseconfirmmail' ];
        }
        // remove account
        if (isset($_POST[ 'removeaccount' ])) {
            $id = $_SESSION[ 'authid' ] . '-remove-' . md5(uniqid(rand()));
            db_query("INSERT INTO `prefix_usercheck` (`check`,`email`,`datime`,`ak`)
    VALUES ('" . $id . "','" . escape($_POST[ 'email' ], 'string') . "',NOW(),5)");
            $page = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ];
            $text = $lang[ 'removeconfirm' ] . sprintf($lang[ 'registconfirmlink' ], $page, $id);
            icmail($_POST[ 'email' ], html_entity_decode($lang[ 'removeaccount' ]), $text);
            $fmsg = $lang[ 'pleaseconfirmremove' ];
        }
        // remove account
        // statische felder speichern
        db_query("UPDATE prefix_user
			  SET
          homepage = '" . get_homepage(escape($_POST[ 'homepage' ], 'string')) . "',
          wohnort = '" . escape($_POST[ 'wohnort' ], 'string') . "',
          icq = '" . escape($_POST[ 'icq' ], 'string') . "',
          msn = '" . escape($_POST[ 'msn' ], 'string') . "',
          yahoo = '" . escape($_POST[ 'yahoo' ], 'string') . "',
          " . $avatar_sql_update . "
		  " . $userpic_sql_update . "
          aim = '" . escape($_POST[ 'aim' ], 'string') . "',
          staat = '" . escape($_POST[ 'staat' ], 'string') . "',
          geschlecht = '" . escape($_POST[ 'geschlecht' ], 'string') . "',
          status = '" . escape($_POST[ 'status' ], 'string') . "',
          opt_mail = '" . escape($_POST[ 'opt_mail' ], 'string') . "',
          opt_pm = '" . escape($_POST[ 'opt_pm' ], 'string') . "',
          opt_pm_popup = '" . escape($_POST[ 'opt_pm_popup' ], 'string') . "',
          gebdatum = '" . get_datum(escape($_POST[ 'gebdatum' ], 'string')) . "',
          sig = '" . substr(escape($_POST[ 'sig' ], 'string'), 0, $allgAr[ 'forum_max_sig' ]) . "'
				WHERE id = " . $_SESSION[ 'authid' ]);
        // change other profil fields
        profilefields_change_save($_SESSION[ 'authid' ]);
        $design->header();
        // definie and print msg
        $fmsg = (isset($fmsg) ? $fmsg : $lang[ 'changesuccessful' ]);
        wd('?user-8', $fmsg, 3);
    }
} else {
    $tpl = new tpl('user/login');
    $tpl->set_out('WDLINK', '?user-8', 0);
}

$design->footer();
