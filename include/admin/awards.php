<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Awards', '', 2);
$design->header();
// #### F u n k t i o n e n
function getTeams() {
    $squads = '';
    $erg1 = db_query("SELECT `name`, `id` FROM `prefix_groups` ORDER BY `pos`");
    while ($row = db_fetch_assoc($erg1)) {
        $squads .= '<option>' . $row[ 'name' ] . '</option>';
    }
    return ($squads);
}
// #### A k t i o n e n
// Löschen
if ($menu->getA(1) == 'd' AND is_numeric($menu->getE(1))) {
    db_query('DELETE FROM `prefix_awards` WHERE `id` = "' . $menu->getE(1) . '" LIMIT 1');
}
// Eintragen
if (isset($_POST[ 'ins' ]) and chk_antispam('adminuser_action', true)) {
    $datum = get_datum($_POST[ 'datum' ]);
    $wofur = escape($_POST[ 'wofur' ], 'string');
    $text = escape($_POST[ 'text' ], 'string');
    $platz = escape($_POST[ 'platz' ], 'string');
    $bild = get_homepage(escape($_POST[ 'bild' ], 'string'));
    if ($_POST[ 'atype' ] == 'user') {
        $team = escape($_POST[ 'name' ], 'string');
    } else {
        $team = escape($_POST[ 'team' ], 'string');
    }

    if ($menu->getA(1) == 'e' AND is_numeric($menu->getE(1))) {
        $id = $menu->getE(1);
        db_query("UPDATE `prefix_awards` SET `time` = '" . $datum . "', `platz` = '" . $platz . "',
              `team` = '" . $team . "', `wofur` = '" . $wofur . "', `bild` = '" . $bild . "', `text` = '" . $text . "' WHERE `id` = " . $id);
        echo mysql_error();
        $menu->set_url(1, '');
    } else {
        db_query("INSERT INTO `prefix_awards` (`time`, `platz`, `team`, `wofur`, `bild`, `text`) VALUES
    ('" . $datum . "', '" . $platz . "', '" . $team . "', '" . $wofur . "', '" . $bild . "', '" . $text . "')");
    }
}
// Ändern/Ausgabearray füllen
if ($menu->getA(1) == 'e' AND is_numeric($menu->getE(1))) {
    $r = db_fetch_assoc(db_query("SELECT * FROM `prefix_awards` WHERE `id` = " . $menu->getE(1)));
    $r[ 'id' ] = '-e' . $r[ 'id' ];
    $t = explode('-', $r[ 'time' ]);
    $r[ 'datum' ] = $t[ 2 ] . '.' . $t[ 1 ] . '.' . $t[ 0 ];
} else {
    $r = array(
        'id' => '',
        'datum' => date('d.m.Y'),
        'platz' => '',
        'wofur' => '',
        'bild' => '',
        'text' => '',
		'ANTISPAM' => get_antispam('adminuser_action', 0, true),
        'teams' => getTeams()
        );
}
// Ausgabe
$tpl = new tpl('awards', 1);
$tpl->set_ar_out($r, 0);
if (empty($r[ 'team' ]))
    $tpl->set_ar_out($r, 1);
else
    $tpl->set_ar_out($r, 2);
$tpl->set_ar_out($r, 3);

$erg = db_query('SELECT * FROM `prefix_awards` ORDER BY `time` DESC');
while ($row = db_fetch_assoc($erg)) {
    $t = explode('-', $row[ 'time' ]);
    $row[ 'datum' ] = $t[ 2 ] . '.' . $t[ 1 ] . '.' . $t[ 0 ];
    $tpl->set_ar_out($row, 4);
}
$tpl->out(5);

$design->footer();

?>