<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
$design = new design('Ilch Admin-Control-Panel :: Warsinfo', '', 2);
$design->header();
$erg = db_query("SELECT DATE_FORMAT(`datime`,'%d.%m.%Y.%H.%i.%s') as `datime`, `id`,`status`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt` FROM `prefix_wars` WHERE `id` = '" . intval($menu->get(2)) . "'");
$_ilch = db_fetch_assoc($erg);
list($_ilch[ 'day' ], $_ilch[ 'mon' ], $_ilch[ 'jahr' ], $_ilch[ 'stu' ], $_ilch[ 'min' ], $_ilch[ 'sek' ]) = explode('.', $_ilch[ 'datime' ]);
$tpl = new tpl('wars/info', 1);
$tpl->set_ar_out($_ilch, 0);
$design->footer();
?>