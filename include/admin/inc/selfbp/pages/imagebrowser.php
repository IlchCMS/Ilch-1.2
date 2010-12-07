<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$cat = 0;
if ($menu->get(2) != '') {
    $cat = escape($menu->get(2), 'integer');
}

$abf = "SELECT `id`,`besch`,`datei_name`,`endung` FROM `prefix_gallery_imgs` WHERE `cat` = " . $cat;
$erg = db_query($abf);
$i = 0;
$design = new design('Ilch Admin-Control-Panel :: Bilder', '', 0);
$design->header();
$tpl = new tpl('selfbp-imagebrowser', 1);
$tpl->out(0);
gallery_admin_showcats(0, '');
$tpl->out(1);
while ($row = db_fetch_assoc($erg)) {
    if ($i != 0 AND ($i % $allgAr[ 'gallery_imgs_per_line' ]) == 0) {
        echo '</tr><tr>';
    }
    $toput = 'include/images/gallery/img_' . $row[ 'id' ] . '.' . $row[ 'endung' ];
    $pfad = 'include/images/gallery/img_thumb_' . $row[ 'id' ] . '.' . $row[ 'endung' ];
    $tpl->set('toput', $toput);
    $tpl->set('pfad', $pfad);
    $tpl->out(2);
    $i++;
}
$design->footer(1);

?>