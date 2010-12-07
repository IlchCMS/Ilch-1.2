<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Trainzeiten', '', 2);
$design->header();
$tpl = new tpl('trains', 1);

if (!empty($_POST[ 'send' ])) {
    $mon = str_replace('#', '', escape($_POST[ 'mon' ], 'textarea'));
    $die = str_replace('#', '', escape($_POST[ 'die' ], 'textarea'));
    $mit = str_replace('#', '', escape($_POST[ 'mit' ], 'textarea'));
    $don = str_replace('#', '', escape($_POST[ 'don' ], 'textarea'));
    $fre = str_replace('#', '', escape($_POST[ 'fre' ], 'textarea'));
    $sam = str_replace('#', '', escape($_POST[ 'sam' ], 'textarea'));
    $son = str_replace('#', '', escape($_POST[ 'son' ], 'textarea'));
    $new = $mon . '#' . $die . '#' . $mit . '#' . $don . '#' . $fre . '#' . $sam . '#' . $son;
    db_query("UPDATE `prefix_allg` SET `t1` = '" . $new . "' WHERE `k` = 'trainzeiten'");
    wd('?trains', 'Daten erfolgreich geändert', 2);
} else {
    $row = db_fetch_object(db_query("SELECT `t1` FROM `prefix_allg` WHERE `k` = 'trainzeiten'"));
    $dbe = explode('#', $row->t1);
    $ar = array(
        'MON' => $dbe[ 0 ],
        'DIE' => $dbe[ 1 ],
        'MIT' => $dbe[ 2 ],
        'DON' => $dbe[ 3 ],
        'FRE' => $dbe[ 4 ],
        'SAM' => $dbe[ 5 ],
        'SON' => $dbe[ 6 ]
        );
    $tpl->set_ar_out($ar, 0);
}
$design->footer();

?>