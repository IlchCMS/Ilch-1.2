<?php

// Contentslider
defined('main') or die('no direct access');

// Anzeige pruefen
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
            "\n" . '</script>';

    echo "\n" . '<div id="contentslider">';
    echo "\n" . '  <ul class="bjqs">' . "\n";

    if ($allgAr['sliderRandom']) {
        $erg = db_query("SELECT `id`,`name`,`link`,`target`,`banner`,`pos`,`status` FROM `prefix_contentslider` WHERE `status` = 1 ORDER BY RAND()");
    } else {
        $erg = db_query("SELECT `id`,`name`,`link`,`target`,`banner`,`pos`,`status` FROM `prefix_contentslider` WHERE `status` = 1 ORDER BY `pos` ASC");
    }

    $num = db_num_rows($erg);
    while ($r = db_fetch_assoc($erg)) {
        if (!empty($r['link'])) {
            echo '    <li><a href="' . $r['link'] . '" target="' . $r['target'] . '"><img src="' . $r['banner'] . '" alt="' . $r['name'] . '" title="' . $r['name'] . '" /></a></li>' . "\n";
        } else {
            echo '    <li><img src="' . $r['banner'] . '" alt="' . $r['name'] . '" title="' . $r['name'] . '" /></li>' . "\n";
        }
    }

    echo '  </ul>' . "\n";
    echo '</div>' . "\n";
} else {

    // keine Ausgabe
    echo '';
}
?>