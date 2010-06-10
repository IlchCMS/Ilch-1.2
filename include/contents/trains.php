<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Trainingszeiten';
$hmenu = 'Trainingszeiten';
$design = new design($title, $hmenu);
$design->header();
$tpl = new tpl('trains');

$row = db_fetch_object(db_query("SELECT `t1` FROM `prefix_allg` WHERE `k` = 'trainzeiten'"));
$dbe = explode('#', $row->t1);
$ar = array(
    'MON' => bbcode($dbe[ 0 ]),
    'DIE' => bbcode($dbe[ 1 ]),
    'MIT' => bbcode($dbe[ 2 ]),
    'DON' => bbcode($dbe[ 3 ]),
    'FRE' => bbcode($dbe[ 4 ]),
    'SAM' => bbcode($dbe[ 5 ]),
    'SON' => bbcode($dbe[ 6 ])
    );
$tpl->set_ar_out($ar, 0);

$design->footer();

?>