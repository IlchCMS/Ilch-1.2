<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/contentslider');

// Nur Anzeigen, wenn entsprechend konfiguriert
if (($allgAr['sliderShow'] AND !$allgAr['sliderSmodul']) OR ($allgAr['sliderShow'] AND $allgAr['sliderSmodul'] AND $allgAr['smodul'] == $menu->get(0))) {

    $allgAr['sliderAnimation'] == 1 ? $allgAr['sliderAnimation'] = 'slide' : $allgAr['sliderAnimation'] = 'fade';

    // Sliderkonfiguration und Links an Header uebergeben
    $ILCH_HEADER_ADDITIONS .=
            "\n" . '<link rel="stylesheet" type="text/css" href="include/includes/css/contentslider/style.css" />' .
            "\n" . '<script type="text/javascript" src="include/includes/js/contentslider/slider.js"></script>' .
            "\n" . '<script type="text/javascript">' .
            "\n" . '    jQuery(document).ready(function($) { ' .
            "\n" . '        $(\'#contentslider\').bjqs({ ' .
            "\n" . '            width : ' . $allgAr['sliderWidth'] . ', ' .
            "\n" . '            height : ' . $allgAr['sliderHeight'] . ', ' .
            "\n" . '            animtype : \'' . $allgAr['sliderAnimation'] . '\', ' .
            "\n" . '            animduration : ' . $allgAr['sliderDuration'] . ', ' .
            "\n" . '            animspeed : ' . $allgAr['sliderSpeed'] . ', ' .
            "\n" . '            automatic : ' . $allgAr['sliderAutomic'] . ', ' .
            "\n" . '            showcontrols : ' . $allgAr['sliderControl'] . ', ' .
            "\n" . '            showmarkers : ' . $allgAr['sliderMarker'] . ', ' .
            "\n" . '            keyboardnav : ' . $allgAr['sliderKeyboard'] . ', ' .
            "\n" . '            hoverpause : ' . $allgAr['sliderWait'] . ', ' .
            "\n" . '            usecaptions : ' . $allgAr['sliderTitle'] . ', ' .
            "\n" . '            responsive : ' . $allgAr['sliderResize'] .
            "\n" . '        }); ' .
            "\n" . '    }); ' .
            "\n" . '</script> ' .
            "\n";

    $orderBy = ($allgAr['sliderRandom']) ? 'RAND()' : '`pos` ASC';
    $abf = 'SELECT `id`,`name`,`link`,`target`,`banner`,`pos`,`status` FROM `prefix_contentslider` WHERE `status` = 1 ORDER BY ' . $orderBy;
    $erg = db_query($abf);

    if (db_num_rows($erg) > 0) {
        $tpl->out('start');
        while ($r = db_fetch_assoc($erg)) {
            if (!empty($r['link'])) {
                $tpl->set_ar_out($r, 'withlink');
            } else {
                $tpl->set_ar_out($r, 'withoutlink');
            }
        }
        $tpl->out('end');
    }
}
