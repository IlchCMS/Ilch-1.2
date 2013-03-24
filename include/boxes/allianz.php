<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/allianz');

$allyAnzahl = $allgAr['Aanz'];
$sqlORDER = ($allgAr['Aart'] == 1) ? '`pos`' : 'RAND()';

$allyAbf = 'SELECT * 
            FROM `prefix_partners` 
			ORDER BY ' . $sqlORDER . ' 
			LIMIT  0,' . $allyAnzahl;
$allyErg = db_query($allyAbf);

if (db_num_rows($allyErg) > 0) {
    $tpl->out('start');
    while ($allyRow = db_fetch_object($allyErg)) {
        $tpl->set('link', $allyRow->link);
        if (empty($allyRow->banner) OR $allyRow->banner == 'http://') {
            $tpl->set('title', $allyRow->name);
        } else {
            $tpl->set('title', $tpl->set_ar_get(array(
                        'banner' => $allyRow->banner,
                        'name' => $allyRow->name
                            ), 'image')
            );
        }
        $tpl->out('link');
    }
    $tpl->out('main');
}
