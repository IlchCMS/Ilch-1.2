<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: History', '', 2);
$design->header();

$tpl = new tpl('history', 1);
// delete
if (!empty($_GET[ 'delete' ])) {
    db_query("DELETE FROM `prefix_history` WHERE `id` = '" . $_GET[ 'delete' ] . "'");
}

if (isset($_REQUEST[ 'pkey' ])) {
    $_REQUEST[ 'pkey' ] = escape($_REQUEST[ 'pkey' ], 'integer');
}

if (!empty($_POST[ 'sub' ]) and chk_antispam('adminuser_action', true)) {
    list($d, $m, $y) = explode('.', $_POST[ 'date' ]);
    if (@checkdate($m, $d, $y)) {
        $date = $y . '-' . $m . '-' . $d;
        $txt = escape($_POST[ 'txt' ], 'textarea');
        $title = escape($_POST[ 'title' ], 'string');
        if (empty($_POST[ 'pkey' ])) {
            db_query("INSERT INTO `prefix_history` (`date`,`title`,`txt`) VALUES ('" . $date . "','" . $title . "','" . $txt . "')");
        } else {
            db_query("UPDATE `prefix_history` SET `date` = '" . $date . "',`title` = '" . $title . "',`txt` = '" . $txt . "' WHERE `id` = '" . $_REQUEST[ 'pkey' ] . "'");
        }
    } else {
        echo 'Datum stimmt nicht, bitte im Format DD.MM.YYYY eingeben also z.B. 29.12.2005<br />';
    }
}

if (!empty($_REQUEST[ 'pkey' ])) {
    $erg = db_query("SELECT `id`,DATE_FORMAT(`date`,'%d.%m.%Y') as `date`,`title`,`txt` FROM `prefix_history` WHERE `id` = '" . $_GET[ 'pkey' ] . "'");
    $_ilch = db_fetch_assoc($erg);
    $_ilch[ 'pkey' ] = $_REQUEST[ 'pkey' ];
} else {
    $_ilch = array(
        'pkey' => '',
        'date' => date('d.m.Y'),
        'title' => '',
        'txt' => ''
        );
}
$_ilch[ 'ANTISPAM' ] = get_antispam('adminuser_action', 0, true);
$tpl->set_ar_out($_ilch, 0);

if (empty($_GET[ 'page' ])) {
    $_GET[ 'page' ] = 1;
}
$limit = 20;
$class = '';
$MPL = db_make_sites($_GET[ 'page' ], '', $limit, '?history', 'history');
$anfang = ($_GET[ 'page' ] - 1) * $limit;

$abf = "SELECT `id`,DATE_FORMAT(`date`,'%d.%m.%Y') as `date`,`title` FROM `prefix_history` ORDER BY `date` LIMIT " . $anfang . "," . $limit;
$erg = db_query($abf);
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row[ 'class' ] = $class;
    $tpl->set_ar($row);
    $tpl->out(1);
}
$tpl->set('MPL', $MPL);
$tpl->out(2);

$design->footer();

?>