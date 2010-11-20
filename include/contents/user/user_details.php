<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$uid = intval($menu->get(2));

$abf = 'SELECT * FROM `prefix_user` WHERE id = "' . $uid . '"';
$erg = db_query($abf);
// prüfen ob ID Vorhanden ist
$check = db_num_rows($erg);
if ($check == 1) {
    // User ist vorhanden
    $row = db_fetch_assoc($erg);

    $userpic = '';
    if (file_exists($row[ 'userpic' ])) {
        $userpic = '<img src="' . $row[ 'userpic' ] . '" border="0">';
    }
    if ($row['sperre'] == 1) {
        $usersperr = '<tr>
					<td class="Cmite">Userstatus</td>
					<td class="Cnorm"><strong>gesperrt</strong></td>
				</tr>';
    }
    $regsek = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - $row[ 'regist' ];
    $regday = round($regsek / 86400);
    $postpday = ($regday == 0 ? 0 : round($row[ 'posts' ] / $regday, 2));

    $ar = array(
        'NAME' => $row[ 'name' ],
        'JOINED' => date('d M Y', $row[ 'regist' ]),
        'LASTAK' => date('d M Y - H:i', $row[ 'llogin' ]),
        'POSTS' => $row[ 'posts' ],
        'postpday' => $postpday,
        'RANG' => userrang($row[ 'posts' ], $uid),
        'AVATA' => $userpic,
        'SPERRE' => $usersperr
        );

    $title = $allgAr[ 'title' ] . ' :: Users :: Details von ' . $row[ 'name' ];
    $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Details von ' . $row[ 'name' ] . $extented_forum_menu_sufix;
    $design = new design($title, $hmenu, 1);
    $design->header();

    $tpl = new tpl('user/userdetails');

    $l = profilefields_show($uid);

    $ar[ 'rowspan' ] = 4 + substr_count($l, '<tr><td class="');

    $ar[ 'profilefields' ] = $l;
    $tpl->set_ar_out($ar, 0);
    $design->footer();
} else {
    // User ist nicht (mehr) vorhanden
    $title = $allgAr[ 'title' ] . ' :: Users :: Details von ' . $row[ 'name' ];
    $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b>';
    $design = new design($title, $hmenu, 1);
    $design->header();

    echo ' FEHLER: User nicht (mehr) vorhanden';
    $design->footer();
}

?>