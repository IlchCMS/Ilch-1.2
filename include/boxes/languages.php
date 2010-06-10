<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
// -----------------------------------------------------------|
if (!empty($_POST[ 'lang_ch' ])) {
    $_SESSION[ 'authlang' ] = $_POST[ 'lang_ch' ];
    wd('', '', 0);
} else {
    echo '<form action="index.php?' . $menu->get_complete() . '" method="POST">';
    echo '<div align="center">';
    echo '<select name="lang_ch" onchange="this.form.submit();">';
    $o = opendir('include/includes/lang');
    while ($f = readdir($o)) {
        if ($f != '.' AND $f != '..' AND is_dir('include/includes/lang/' . $f)) {
            $s = ($f == $_SESSION[ 'authlang' ] ? ' selected' : '');
            echo '<option' . $s . '>' . $f . '</option>';
        }
    }
    echo '</select></div></form>';
}

?>