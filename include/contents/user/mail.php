<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$abf = "SELECT * FROM `prefix_user` WHERE `id` = " . $menu->get(2);
$erg = db_query($abf);
$DA_IS_WAS_FAUL = false;
if (@db_num_rows($erg) != 1) {
    $DA_IS_WAS_FAUL = true;
}
$row = db_fetch_assoc($erg);
if ($row[ 'opt_mail' ] == 0) {
    $DA_IS_WAS_FAUL = true;
}
if ($DA_IS_WAS_FAUL === true) {
    header('location: index.php?' . $allAr[ 'smodul' ]);
    exit();
}

$title = $allgAr[ 'title' ] . ' :: Users :: eMail an ' . $row[ 'name' ];
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> eMail an ' . $row[ 'name' ] . $extented_forum_menu_sufix;
$design = new design($title, $hmenu, 1);
$design->header();

if (!array_key_exists('klicktime', $_SESSION)) {
    $_SESSION[ 'klicktime' ] = '';
}
// vars definieren
$_POST[ 'email' ] = (isset($_POST[ 'email' ]) ? trim($_POST[ 'email' ]) : '');
$_POST[ 'bet' ] = (isset($_POST[ 'bet' ]) ? trim($_POST[ 'bet' ]) : '');
$_POST[ 'txt' ] = (isset($_POST[ 'txt' ]) ? trim($_POST[ 'txt' ]) : '');

if (empty($_POST[ 'bet' ]) OR empty($_POST[ 'email' ]) OR empty($_POST[ 'txt' ]) OR $_SESSION[ 'klicktime' ] > (time() - 60)) {
    if (!empty($_POST[ 'send' ])) {
        $fehler = '<font color="#FF0000">Fehler:</font><br/>';
        if ($_SESSION[ 'klicktime' ] > (time() - 60)) {
            $fehler .= '&nbsp; - Bitte nicht so schnell eMails Schreiben<br/>';
        }
        if (trim($_POST[ 'bet' ]) == '') {
            $fehler .= '&nbsp; - Bitte einen Betreff angeben<br/>';
        }
        if (trim($_POST[ 'email' ]) == '') {
            $fehler .= '&nbsp; - Bitte eine eMail angeben<br/>';
        }
        if (trim($_POST[ 'txt' ]) == '') {
            $fehler .= '&nbsp; - Bitte eine Nachricht angeben<br/>';
        }
    } else {
        $fehler = '';
    }
    echo $fehler;

    ?>
	<form action="index.php?user-mail-<?php
    echo $menu->get(2);

    ?>" method="POST">
	<table width="100%" border="0" cellspacing="1" cellpadding="5" class="border">
    <tr class="Chead">
      <th colspan="2">eMail an Benutzer <?php
    echo $row[ 'name' ];

    ?></th>
    <tr>
      <td class="Cmite">Betreff</td>
			<td class="Cnorm"><input type="text" name="bet" value="<?php
    echo $_POST[ 'bet' ];

    ?>"></td>
		</tr><tr class="Cnorm">
		  <td class="Cmite">Deine eMail</td>
			<td class="Cnorm"><input type="text" name="email" value="<?php
    echo $_POST[ 'email' ];

    ?>"></td>
		</tr><tr class="Cnorm">
		  <td class="Cmite" v>Nachricht</td>
		  <td class="Cnorm"><textarea cols="40" rows="10" name="txt"><?php
    echo $_POST[ 'txt' ];

    ?></textarea></td>
		</tr><tr class="Cdark">
		  <td></td>
			<td><input type="submit" name="send" value="<?php
    echo $lang[ 'formsub' ];

    ?>"></td>
    </tr>
  </table></form>
  <?php
} else {
    $_SESSION[ 'klicktime' ] = time();
    if (1 == $row[ 'opt_mail' ]) {
        icmail($row[ 'email' ], strip_tags($_POST[ 'bet' ]), strip_tags($_POST[ 'txt' ]), 'SeitenKontakt <' . escape_for_email($_POST[ 'email' ]) . '>');
        wd('index.php?forum', 'Die eMail wurde erfolgreich versendet');
    } else {
        header('location: index.php?' . $allAr[ 'smodul' ]);
        exit();
    }
}

$design->footer();

?>