<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined ('main') or die ('no direct access');
// Klasse laden
require_once 'include/includes/class/bbcode.php';
// Farbliste erstellen
function colorliste ($ar) {
    $l = '';
    foreach($ar as $k => $v) {
        $l .= '<td width="10" style="background-color: ' . $k . ';"><a href="javascript:bbcode_code_insert(\'color\',\'' . $k . '\'); hide_color();"><img src="include/images/icons/bbcode/transparent.gif" border="0" height="10" width="10" alt="' . $v . '" title="' . $v . '"></td>';
    }
    return ($l);
}
function getBBCodeButtons() {
    return bbcode::getBBCodeButtons();
}
function BBcode($s, $maxLength = 0, $maxImgWidth = 0, $maxImgHeight = 0) {
    $bbcode = bbcode::getInstance();
    return $bbcode->parse($s, $maxLength, $maxImgWidth, $maxImgHeight);
}
function BBCode_onlySmileys($text, $maxLength = 0){
    $bbcode = bbcode::getInstance();
    return $bbcode->onlySmileys($text, $maxLength);
}

?>