<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Konfiguration', '', 2);
$design->header();

if (!is_admin()) {
    echo 'Dieser Bereich ist nicht f&uuml;r dich...';
    $design->footer();
    exit();
}
// Load needed functions
$funcs = read_ext('include/admin/inc/allg', 'php');
foreach ($funcs as $file) {
    require_once('include/admin/inc/allg/' . $file);
}

if (empty($_POST[ 'submit' ])) {
    // Template laden
    $tpl = new tpl('allg', 1);
    // Template-Header ausgeben
    $tpl->out(0);
    // Kategorien-ID und NAME
    $katid = 0;
    $katname = '';
    // Abfrage für Menü und admin/allg.php starten
    $abf = 'SELECT * FROM `prefix_config` WHERE hide = 0 ORDER BY `kat`,`pos`,`typ` ASC';
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        // Werte in Array speichern
        $cache[] = $row;
        // Kategorie ausgeben, falls neu
        if ($katname != $row[ 'kat' ]) {
            $katid++;
            $katname = $row[ 'kat' ];
            $tpl->set_ar_out(Array(
                    'katid' => $katid,
                    'katname' => $katname
                    ), 1);
        }
    }
    // Navigation-Ende
    $tpl->out(2);
    // Kategorien-ID und NAME Resett
    $katid = 0;
    $katname = '';
    // Fragen Abrufen
    foreach ($cache AS $row) {
        // Kategorie ausgeben, falls neu
        if ($katname != $row[ 'kat' ]) {
            // Kategorien-Ende ausgeben, falls nötig
            if ($katid != 0) {
                $tpl->out(5);
            }

            $katid++;
            $katname = $row[ 'kat' ];
            $tpl->set_ar_out(Array(
                    'katid' => $katid,
                    'kat' => $katname
                    ), 3);
        }
        if ($row[ 'typ' ] == 'input') {
            $input = '<input size="50" type="text" name="' . $row[ 'schl' ] . '" value="' . $row[ 'wert' ] . '">';
        } elseif ($row[ 'typ' ] == 'r2') {
            $checkedj = '';
            $checkedn = '';
            if ($allgAr[ $row[ 'schl' ] ] == 1) {
                $checkedj = 'checked';
                $checkedn = '';
            } else {
                $checkedn = 'checked';
                $checkedj = '';
            }
            $input = '<input type="radio" name="' . $row[ 'schl' ] . '" value="1" ' . $checkedj . ' > ja' . '&nbsp;&nbsp;' . '<input type="radio" name="' . $row[ 'schl' ] . '" value="0" ' . $checkedn . ' > nein';
        } elseif ($row[ 'typ' ] == 's') {
				if ($row['schl'] == 'jqueryui') {
					$jqv = './include/includes/css/jquery/templates/';
					$teAr = scandir($jqv);
					
					foreach ($teAr as $verz) {
						if ($verz != '.' && $verz != '..' && is_dir($jqv.$verz)) {
							if ($verz == $allgAr['jqueryui']) {
								@$grml .= '<option selected="selected">'.$verz.'</option>';
							} else {
								@$grml .= '<option>'.$verz.'</option>';
							}
						}
					}
					$input = '<select name="' . $row[ 'schl' ] . '">' . $grml . '</select>';
				} else {
					$vname = $row['schl'];
					$input = '<select name="' . $row[ 'schl' ] . '">' . $$vname . '</select>';
				}
        } 
		elseif ($row[ 'typ' ] == 'textarea') {
            $input = '<textarea cols="55" rows="3" name="' . $row[ 'schl' ] . '">' . $row[ 'wert' ] . '</textarea>';
        } elseif ($row[ 'typ' ] == 'grecht') {
            $grl = dblistee($allgAr[ $row[ 'schl' ] ], "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC");
            $input = '<select name="' . $row[ 'schl' ] . '">' . $grl . '</select>';
        } elseif ($row[ 'typ' ] == 'grecht2') {
            $grl = dblistee($allgAr[ $row[ 'schl' ] ], "SELECT id,name FROM prefix_grundrechte WHERE id >= -2 ORDER BY id ASC");
            $input = '<select name="' . $row[ 'schl' ] . '">' . $grl . '</select>';
        } elseif ($row[ 'typ' ] == 'password') {
            $input = '<input size="50" type="password" name="' . $row[ 'schl' ] . '" value="***" />';
        } elseif ($row['typ'] == 'select' and !is_null($row['typextra'])) {
            $typextra = json_decode($row['typextra'], true);
            if (isset($typextra['values'])) {
                if (isset($typextra['keys'])) {
                    $teAr = array_combine($typextra['keys'], $typextra['values']);
                } else {
                    $teAr = array_combine($typextra['values'], $typextra['values']);
                }
                $grl = arlistee($allgAr[$row['schl']], $teAr);
                $input = '<select name="' . $row[ 'schl' ] . '">' . $grl . '</select>';
            } else {
                $input = 'Fehler in Datenbank!';
            }
        }


        $tpl->set_ar_out(array(
                'frage' => $row[ 'frage' ],
                'input' => $input,
                'schl' => $row['schl'],
                'help' => is_null($row['helptext']) ? 0 : 1,
                'helptext' => $row['helptext']
                ), 4);
    }
    // Kategorien-Ende ausgeben, falls nötig
    if ($katid != 0) {
        $tpl->out(5);
    }
    // Template-Footer ausgeben
    $tpl->set('antispam', get_antispam('admin_allg', 1, true));
    $tpl->out(6);
} elseif (chk_antispam('admin_allg', true)) {
    $abf = 'SELECT * FROM `prefix_config` WHERE hide = 0 ORDER BY `kat` ';
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        if ($row[ 'typ' ] == 'password' AND $_POST[ $row[ 'schl' ] ] == '***') {
            continue;
        } elseif ($row[ 'typ' ] == 'password') {
            require_once('include/includes/libs/AzDGCrypt.class.inc.php');
            $cr64 = new AzDGCrypt(DBDATE . DBUSER . DBPREF);
            $_POST[ $row[ 'schl' ] ] = $cr64->crypt($_POST[ $row[ 'schl' ] ]);
        }
        db_query('UPDATE `prefix_config` SET wert = "' . escape($_POST[ $row[ 'schl' ] ], 'textarea') . '" WHERE schl = "' . $row[ 'schl' ] . '"');
    }
    wd('admin.php?allg', 'Erfolgreich ge&auml;ndert', 2);
}
// -----------------------------------------------------------|
$design->footer();

?>