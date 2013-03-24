<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// Einstellungen
$limit = 3;       // Wieviele Bilder sollen angezeigt werden?
//

$tpl = new tpl('boxes/lastgallery');

$ILCH_HEADER_ADDITIONS .=
        "\n" . '<script language="Javascript" type="text/javascript"> ' .
        "\n" . '$(document).ready(function() { ' .
        "\n" . '    $("a#fancyframecomment").fancybox({ ' .
        "\n" . '        \'overlayShow\' : true, ' .
        "\n" . '        \'width\' : \'90%\', ' .
        "\n" . '        \'height\' : \'90%\', ' .
        "\n" . '        \'autoScale\' : false, ' .
        "\n" . '        \'transitionIn\' : \'elastic\', ' .
        "\n" . '        \'transitionOut\' : \'elastic\', ' .
        "\n" . '        \'type\' : \'iframe\', ' .
        "\n" . '        \'titleShow\' : false, ' .
        "\n" . '        \'centerOnScroll\' : true ' .
        "\n" . '    }); ' .
        "\n" . '}); ' .
        "\n" . '</script> ' .
        "\n";

$abf = "SELECT `id`, `endung`, `datei_name` FROM prefix_gallery_imgs ORDER BY `id` DESC LIMIT 0, " . $limit;
$erg = db_query($abf);

while ($row = db_fetch_object($erg)) {
    $thumb = 'include/images/gallery/img_thumb_' . $row->id . '.' . $row->endung;
    $norm = 'include/images/gallery/img_norm_' . $row->id . '.' . $row->endung;
    if (file_exists($thumb) && file_exists($norm)) {
        $cid = db_result(db_query('SELECT `cat` FROM `prefix_gallery_imgs` WHERE `id` = ' . $row->id), 0);
        $anz = db_result(db_query('SELECT COUNT(*) FROM `prefix_gallery_imgs` WHERE `id` < ' . $row->id . ' AND `cat` = ' . $cid), 0);
        $tpl->set('link', 'index.php?gallery-show-' . $cid . '-p' . $anz);
        $tpl->set('img', $thumb);
        $tpl->set('name', $row->datei_name);
        $tpl->out('pics');
    } else {
        $tpl->out('nopics');
    }
}
