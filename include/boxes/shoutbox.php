<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/shoutbox');

if (!AJAXCALL) {
    echo '<div id="icShoutbox">';

    //Smilies
    $zeilen = 5; $i = 0; $s = '';
    $erg = db_query('SELECT `emo`, `ent`, `url` FROM `prefix_smilies`');
    while ($row = db_fetch_object($erg) )
    {
        $s .= '<a href="javascript:ic.shoutboxInsert(\''.addslashes($row->ent).'\', \'\')">';
        $s .= '<img style="border: 0px; padding: 5px;" src="include/images/smiles/'.$row->url.'" title="'.$row->emo.'"></a>';
        $i++; if($i%$zeilen == 0 AND $i <> 0) { $s .= '<br /><br />'; }
    }
    $tpl->set_out('smilies', $s, 4);
}

if (!isset($_SESSION[ 'last_shoutbox' ])) {
    $_SESSION[ 'last_shoutbox' ] = '';
}

if (has_right($allgAr[ 'sb_recht' ])) {
    //Formular
    if (!empty($_POST[ 'shoutbox_submit' ]) AND chk_antispam('shoutbox')) {
        if ($_SESSION['authid'] == 0) {
            $shoutbox_nickname = substr(escape_nickname($_POST[ 'shoutbox_nickname' ]), 0, 8) . ' (Gast)';
        } else {
            $shoutbox_nickname = substr($_SESSION[ 'authname' ], 0, 15);
        }
        $shoutbox_textarea = escape($_POST[ 'shoutbox_textarea' ], 'textarea');
        $shoutbox_textarea = preg_replace("/\[.?(url|b|i|u|img|code|quote)[^\]]*?\]/i", "", $shoutbox_textarea);
        $shoutbox_textarea = strip_tags($shoutbox_textarea);
        if (!empty($shoutbox_textarea) AND $_SESSION[ 'last_shoutbox' ] != $shoutbox_textarea) {
            $_SESSION[ 'last_shoutbox' ] = $shoutbox_textarea;
            db_query('INSERT INTO `prefix_shoutbox` (`uid`,`nickname`,`textarea`,`time`) VALUES ('.$_SESSION['authid'].', "' . $shoutbox_nickname . '" , "' . $shoutbox_textarea . '", "'.date('Y-m-d H:i:s').'" ) ');
        }
    }
    $antispam = get_antispam ('shoutbox', 0);
    if (!empty($antispam)) {
        $antispam .= '<br />';
    }
    $tpl->set_ar_out(array(
        'action' => $menu->get_complete(),
        'nickname' => $_SESSION['authname'],
        'antispam' => $antispam
    ), 0);
}

//Einträge
$erg = db_query('SELECT * FROM `prefix_shoutbox` ORDER BY `id` DESC LIMIT ' . (is_numeric($allgAr[ 'sb_limit' ]) ? $allgAr[ 'sb_limit' ] : 5));
$class = 'Cnorm';
$tpl->out(1);
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row['class'] = $class;
    $time = strtotime($row['time']);
    if ($time != 0) {
        $dateformat = (date('d.m.Y') == date('d.m.Y', $time)) ? 'H:i' : 'd.m. - H:i';
        $row['time'] = date($dateformat, $time);
    } else {
        $row['time'] = 0;
    }
    $row['text'] = BBCode_onlySmileys($row[ 'textarea' ], $allgAr[ 'sb_maxwordlength' ]);
    $tpl->set_ar_out($row, 2);
}
$tpl->out(3);

if (!AJAXCALL) {
    echo '</div>';
}
?>