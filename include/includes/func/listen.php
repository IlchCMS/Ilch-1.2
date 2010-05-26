<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

function dbliste($aka, $tpl, $n, $q) {
    $l = '';
    $e = db_query($q);
    while ($r = db_fetch_row($e)) {
        $s = '';
        if ((is_array($aka) AND isset($aka[ $r[ 0 ] ])) OR (is_string($aka) AND $aka == $r[ 0 ])) {
            $s = ' selected';
        }
        $l .= $tpl->list_get($n, array($s,
                $r[ 0 ],
                $r[ 1 ]
                ));
    }
    return ($l);
}

function dblistee($aka, $q) {
    $l = '';
    $e = db_query($q);
    while ($r = db_fetch_row($e)) {
        $s = ($aka == $r[ 0 ] ? ' selected' : '');
        $l .= '<option value="' . $r[ 0 ] . '"' . $s . '>' . $r[ 1 ] . '</option>';
    }
    return ($l);
}

function arliste($aka, $ar, $tpl, $n) {
    $l = '';
    foreach ($ar as $k => $v) {
        $s = ($aka == $k ? ' selected' : '');
        $l .= $tpl->list_get($n, array($s,
                $k,
                $v
                ));
    }
    return ($l);
}

function arlistee($aka, $ar) {
    $l = '';
    foreach ($ar as $k => $v) {
        $s = ($aka == $k ? ' selected' : '');
        $l .= '<option value="' . $k . '"' . $s . '>' . $v . '</option>';
    }
    return ($l);
}

?>