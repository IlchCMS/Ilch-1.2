<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Impressum', '', 2);
$design->header();
if (isset($_POST[ 'sub' ]) and chk_antispam('adminuser_action', true)) {
    $t1 = escape($_POST[ 'txt' ], 'textarea');
    $v1 = escape($_POST[ 'v1' ], 'string');
    $v2 = escape($_POST[ 'v2' ], 'string');
    $v3 = escape($_POST[ 'v3' ], 'string');
    $v4 = escape($_POST[ 'v4' ], 'string');
	$sql = "UPDATE `prefix_allg` SET `v1` = '" . $v1 . "', `v2` = '" . $v2 . "', `v3` = '" . $v3 . "', `v4` = '" . $v4 . "', `t1` = '" . $t1 . "' WHERE `k` = 'impressum'";
    db_query($sql);
}

$erg = db_query("SELECT * FROM `prefix_allg` WHERE `k` = 'impressum' LIMIT 1");
$row = db_fetch_assoc($erg);
if ($row[ 't1' ] == '') {
    $f = @implode('', @file('http://disclaimer.de/disclaimer.htm'));
    $f = preg_replace("/.*?<a NAME=\"1\">(.*)<p><b><font size=2>5\..*?/Uis", "<h3><a name=\"1\">\\1<\/p>", $f);
    $f = preg_replace("/<\/?font[^>]*>/is", "", $f);
    $t = $f;
} else {
    $t = $row[ 't1' ];
}

$tpl = new tpl('impressum', 1);
$ar = array(
	't' => $t,
	'v1' => $row['v1'],
	'v2' => $row['v2'],
	'v3' => $row['v3'],
	'v4' => $row['v4'],
	'ANTISPAM' => get_antispam('adminuser_action', 0, true)
	);

$tpl->set_ar_out($ar, 0);

$design->footer();
?>