<?php
// Copyright by Manuel
// Support www.ilch.de
// codeedit by Rolf Berleth
defined ('main') or die ('no direct access');
// ##Teambildauswahl###
function get_teampic_ar () {
    $ar = array();
    $o = opendir('include/images/teams');
    while ($f = readdir($o)) {
        if ($f != '.' AND $f != '..') {
            $ar['include/images/teams/' . $f] = $f;
        }
    }
    closedir($o);
    return ($ar);
}

?>