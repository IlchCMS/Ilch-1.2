<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/picofx');

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

$svResult = db_query('SELECT * FROM `prefix_allg` WHERE `k` = \'picofx\'');
while ($saRow = db_fetch_assoc($svResult)) {
    $picofxOpts[$saRow['v1']] = $saRow['v2'];
}

// var_dump($picofxOpts);
$picofxNow = date('Y-m-d');

// pruefen ob das Bild gewechselt werden muss
if ($picofxOpts['nextchange'] == $picofxNow || $picofxOpts['nextchange'] < $picofxNow) {
    if ($picofxOpts['directory'] == 0) {
        $picofxOpts['pic'] = @db_result(db_query("SELECT `id` FROM `prefix_gallery_imgs` ORDER BY RAND() LIMIT 1"), 0);
    } else {
        $picofxOpts['pic'] = @db_result(db_query("SELECT `id` FROM `prefix_gallery_imgs` WHERE `cat` = " . $picofxOpts['directory'] . " ORDER BY RAND() LIMIT 1"), 0);
    }
    if (!empty($picofxOpts['pic'])) {
        $picofxOpts['pic'] .= '.' . @db_result(db_query("SELECT `endung` FROM `prefix_gallery_imgs` WHERE id = " . $picofxOpts['pic']), 0);
    }
    $picofxNextChange = date('Y-m-d', time() + 3600 * 24 * $picofxOpts['interval']);
    // geaendertes pic in db speichern
    db_query('UPDATE `prefix_allg` SET `v2` = \'' . $picofxOpts['pic'] . '\' WHERE `k` = \'picofx\' AND `v1` =\'pic\' LIMIT 1');
    db_query('UPDATE `prefix_allg` SET `v2` = \'' . $picofxNextChange . '\' WHERE `k` = \'picofx\' AND `v1` =\'nextchange\' LIMIT 1');
}

$picofxThumb = 'img_thumb_' . $picofxOpts['pic'];

if ($picofxOpts['pic'] != '' AND file_exists('include/images/gallery/' . $picofxThumb)) {
    $picofxThumb = 'img_thumb_' . $picofxOpts['pic'];
    $picofxImg = getimagesize('include/images/gallery/' . $picofxThumb);
    if ($picofxImg[0] < $picofxOpts['picwidth']) {
        $picofxImg[1] = @ceil(($picofxImg[1] / $picofxImg[0]) * $picofxOpts['picwidth']);
        $picofxImg[0] = $picofxOpts['picwidth'];
    }
    list($id, $endung) = explode('.', $picofxOpts['pic']);
    if ($picofxOpts['directory'] == 0) {
        $cid = db_result(db_query("SELECT `cat` FROM `prefix_gallery_imgs` WHERE `id` = " . $id), 0);
    } else {
        $cid = $picofxOpts['directory'];
    }
    $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_gallery_imgs` WHERE `id` < " . $id . " AND `cat` = " . $cid), 0);
    $name = @db_result(db_query("SELECT `datei_name` FROM `prefix_gallery_imgs` WHERE id = " . $id), 0);
    $weite = $allgAr['gallery_normal_width'] + 30;
    $tpl->set('link', 'index.php?gallery-show-' . $cid . '-p' . $anz);
    $tpl->set('img', 'include/images/gallery/' . $picofxThumb);
    $tpl->set('name', $name);
    $tpl->set('width', $picofxImg[0]);
    $tpl->set('height', $picofxImg[1]);
    $tpl->out('pics');
} else {
    $tpl->out('nopics');
}
