<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

if (loggedin()) {
    $shoutbox_VALUE_name = $_SESSION['authname'];
} else {
    $shoutbox_VALUE_name = 'Nickname';
}
if (has_right($allgAr['sb_recht'])) {
    if (!empty($_POST['shoutbox_submit']) AND chk_antispam ('shoutbox')) {
        $shoutbox_nickname = escape($_POST['shoutbox_nickname'], 'string');
        $shoutbox_nickname = substr($shoutbox_nickname, 0, 15);
        $shoutbox_textarea = escape($_POST['shoutbox_textarea'], 'textarea');
        $shoutbox_textarea = preg_replace("/\[.?(url|b|i|u|img|code|quote)[^\]]*?\]/i", "", $shoutbox_textarea);
        $shoutbox_textarea = strip_tags($shoutbox_textarea);
        if (!empty($shoutbox_nickname) AND !empty($shoutbox_textarea)) {
            db_query('INSERT INTO `prefix_shoutbox` (`nickname`,`textarea`) VALUES ( "' . $shoutbox_nickname . '" , "' . $shoutbox_textarea . '" ) ');
        }
    }
    echo '<form action="index.php?' . $menu->get_complete() . '" method="post">';
    echo '<input type="text" size="15" name="shoutbox_nickname" value="' . $shoutbox_VALUE_name . '" onFocus="if (value == \'' . $shoutbox_VALUE_name . '\') {value = \'\'}" onBlur="if (value == \'\') {value = \'' . $shoutbox_VALUE_name . '\'}" maxlength="15" />';
    echo '<br /><textarea style="width: 80%" cols="15" rows="2" name="shoutbox_textarea"></textarea><br />';
    echo get_antispam ('shoutbox', 0);
    echo '<input type="submit" value="' . $lang['formsub'] . '" name="shoutbox_submit" />';
    echo '</form>';
}
echo '<table width="90%" class="border" cellpadding="2" cellspacing="1" border="0">';
$erg = db_query('SELECT * FROM `prefix_shoutbox` ORDER BY id DESC LIMIT ' . (is_numeric($allgAr['sb_limit'])?$allgAr['sb_limit']:5));
$class = 'Cnorm';
while ($row = db_fetch_object($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    echo '<tr class="' . $class . '"><td><b>' . $row->nickname . ':</b> ' . preg_replace('/([^\s]{' . $allgAr['sb_maxwordlength'] . '})(?=[^\s])/', "$1\n", $row->textarea) . '</td></tr>';
}
echo '</table><a class="box" href="index.php?shoutbox">' . $lang['archiv'] . '</a>';

?>