<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');
// ##Teambildauswahl###
function get_teampic_ar() {
    $ar = array();
    $o = opendir('include/images/teams');
    while ($f = readdir($o)) {
        if ($f != "." AND $f != ".." AND $f != '.svn' AND is_file('include/images/teams/' . $f)) {
            $ar[ 'include/images/teams/' . $f ] = $f;
        }
    }
    closedir($o);
    return ($ar);
}

?>