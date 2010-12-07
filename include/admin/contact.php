<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Kontakt', '', 2);
$design->header();

if (isset($_POST[ 'name' ])) {
    $_POST[ 'name' ] = escape($_POST[ 'name' ], 'string');
}
if (isset($_POST[ 'mail' ])) {
    $_POST[ 'mail' ] = escape($_POST[ 'mail' ], 'string');
}

switch ($menu->get(1)) {
    case 1:
        $row = db_fetch_object(db_query("SELECT `t1` FROM `prefix_allg` WHERE `k` = 'kontakt'"));
        $k = explode('#', $row->t1);
        $k[ $_GET[ 'wo' ] ] = $_POST[ 'mail' ] . '|' . $_POST[ 'name' ];
        $nk = implode('#', $k);
        db_query("UPDATE `prefix_allg` SET `t1` = '" . $nk . "' WHERE `k` = 'kontakt'");
        break;
    case 2:
        $row = db_fetch_object(db_query("SELECT `t1` FROM `prefix_allg` WHERE `k` = 'kontakt'"));
        $k = explode('#', $row->t1);
        unset($k[ $_GET[ 'del' ] ]);
        $nk = implode('#', $k);
        db_query("UPDATE `prefix_allg` SET `t1` = '" . $nk . "' WHERE `k` = 'kontakt'");
        break;
    case 3:
        $row = db_fetch_object(db_query("SELECT `t1` FROM `prefix_allg` WHERE `k` = 'kontakt'"));
        $nk = $row->t1 . '#' . $_POST[ 'mail' ] . '|' . $_POST[ 'name' ];
        db_query("UPDATE `prefix_allg` SET `t1` = '" . $nk . "' WHERE `k` = 'kontakt'");
        break;
    case 5:
        db_query('UPDATE `prefix_allg` SET ' . $feld . ' = "' . $ak . '" WHERE `k` = "kontakt"');
        break;
}

$tpl = new tpl('contact', 1);
$tpl->out(0);

$row = db_fetch_object(db_query("SELECT `t1`,`v2`,`v1` FROM `prefix_allg` WHERE `k` = 'kontakt'"));
$k = explode('#', $row->t1);
$b = explode('#', $row->v2);
$i = 0;
foreach ($k as $a) {
    $e = explode('|', $a);
    if ($e[ 0 ] != '' AND $e[ 1 ] != '') {
        $ar = array(
            'WO' => $i,
            'MAIL' => $e[ 0 ],
            'NAME' => $e[ 1 ]
            );
        $tpl->set_ar_out($ar, 1);
    }
    $i++;
}
$tpl->out(2);
// -----------------------------------------------------------|
$design->footer();

?>