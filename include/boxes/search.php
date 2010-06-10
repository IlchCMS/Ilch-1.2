<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$suchtpl = <<<HTML
<form action="index.php?search" method="GET">
<input type="text" value="{search}" name="search" size="{size}" /><br />
<input type="hidden" name="in" value="2" />
<input type="submit" value="{_lang_search}" /><br />
</form>
<a href="index.php?search">{_lang_exsearch}</a>
HTML;

$tpl = new tpl($suchtpl, 3);
$tpl->set('size', 16);
if (isset($_GET[ 'search' ]))
    $tpl->set('search', escape($_GET[ 'search' ], 'string'));
else
    $tpl->set('search', '');
$tpl->out(0);

?>