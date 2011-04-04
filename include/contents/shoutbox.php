<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

if (is_siteadmin()) {
    //Einträge löschen (ajax)
    if (isset($_POST['del'])) {
        if (chk_antispam('shoutboxarchive', true)) {
            if (isset($_POST['all'])) {
                //alle
                $save = escape($_POST['all'], 'i');
                $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_shoutbox`"), 0) - $save;
                if ($anz > 0) {
                    db_query("DELETE FROM `prefix_shoutbox` ORDER BY `id` LIMIT " . $anz);
                }
                echo '"reload"';
            } else {
                //einzeln oder ausgewählte
                $ids = escape($_POST['chk'], 'i');
                if (is_int($ids) and $ids > 0) {
                    $ids = array($ids);
                }
                if (!empty($ids)) {
                    db_query('DELETE FROM `prefix_shoutbox` WHERE `id` IN ('.implode(',', $ids).')');
                    echo json_encode($ids);
                } else {
                    echo '"error"';
                }
            }
        } else {
            echo 'antihack';
        }
        exit;
    }
}


$title = $allgAr[ 'title' ] . ' :: Shoutbox ' . $lang[ 'archiv' ];
$hmenu = 'Shoutbox ' . $lang[ 'archiv' ];
$design = new design($title, $hmenu);
$design->header();

$data = array();

$page = $menu->getA(1) == 'p' ? $menu->getE(1) : 1;
$limit = $allgAr['sb_archive_limit'];
$mpl = db_make_sites($page, '', $limit, 'index.php?shoutbox', 'shoutbox');

$erg = db_query('SELECT * FROM `prefix_shoutbox` ORDER BY id DESC LIMIT '
       . (($page - 1) * $limit) . ', ' . $limit);
while ($row = db_fetch_assoc($erg)) {
    $row['textarea'] = BBCode_onlySmileys($row[ 'textarea' ], $allgAr[ 'sb_maxwordlength' ]);
    $time = strtotime($row['time']);
    if ($time != 0) {
        $dateformat = (date('d.m.Y') == date('d.m.Y', $time)) ? 'H:i' : 'd.m. - H:i';
        $row['time'] = date($dateformat, $time);
    } else {
        $row['time'] = 0;
    }
    $data[$row['id']] = $row;
}

require_once 'include/includes/class/iSmarty.php';

$smarty = new iSmarty();
$smarty->assign(array(
    'data' => $data,
    'lang' => $lang,
    'siteadmin' => is_siteadmin(),
    'antihack' => get_antispam('shoutboxarchive', 0, true),
    'multipages' => $mpl
));
$smarty->display('shoutbox.tpl');

$design->footer();

?>