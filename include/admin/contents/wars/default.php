<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
$design = new design('Ilch Admin-Control-Panel :: Wars', '', 2);
$design->header();
?>
Folgende Auswahlm&ouml;glichkeiten:
<ul>
<li><a href="admin.php?wars-last">Lastwars</a></li>
<li><a href="admin.php?wars-next">Nextwars</a></li>
</ul>
<?php
$design->footer();
?>