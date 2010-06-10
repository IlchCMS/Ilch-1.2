<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

function unescape($var) {
    $var = stripslashes($var);
    return ($var);
}
// moegliche typ vars
// - integer
// - string
// - textarea
function escape($var, $type) {
    switch ($type) {
        case 'integer':
            $var = intval($var);
            break;
        case 'string':
            $var = (get_magic_quotes_gpc() ? stripslashes($var) : $var);
            $var = strip_tags($var);
            $var = addslashes($var);
            break;
        case 'textarea':
            $var = (get_magic_quotes_gpc() ? stripslashes($var) : $var);
            $var = addslashes($var);

            break;
    }
    return ($var);
}

function escape_nickname($t) {
    $t = preg_replace("/[^a-zA-Z0-9-\[\]\*\ \+=\._\|]/", "", $t);
    $t = substr($t, 0, 15);
    $t = escape($t, 'string');
    return ($t);
}

function escape_for_email($t, $leerzeichen = false) {
    if ($leerzeichen === true) {
        $t = preg_replace("/\015\012|\015|\012|\072|\074|\076/", "", $t);
    } else {
        $t = preg_replace("/\015\012|\015|\012|\072|\074|\076|\040/", "", $t);
    }
    return ($t);
}

function escape_for_fields($t) {
    // $t = str_replace ('<', '&lt;', str_replace('>', '&gt;', $t));
    // $t = str_replace ('<', '&lt;', str_replace('>', '&gt;', $t));
    // $t = str_replace ('<', '&lt;', str_replace('>', '&gt;', $t));
    $t = htmlentities($t);

    return ($t);
}

function escape_email_to_show($str) {
    $ret = "";
    $arr = unpack("C*", $str);
    foreach ($arr as $char) {
        $ret .= sprintf("%%%X", $char);
    }
    return $ret;
}

?>