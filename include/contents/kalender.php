<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 * Kalender Script © by Nickel
 */
defined('main') or die('no direct access');
// -----------------------------------------------------------|
$title = $allgAr[ 'title' ] . ' :: Kalender';
$hmenu = 'Kalender';
$design = new design($title, $hmenu);
$design->header();
$tooltips = '';
$tpl = new tpl('kalender.htm');
// -----------------------------------------------------------|
// Daten
$month = date('n');
$year = date('Y');
$gday = 0;
$view = 1;
$eid = 0;
if ($menu->getA(1) == 'v' AND is_numeric($menu->getE(1))) {
    $view = $menu->getE(1);
}
if ($menu->getA(2) == 'm' AND is_numeric($menu->getE(2)) AND $menu->getE(2) > 0 AND $menu->getE(2) < 13) {
    $month = $menu->getE(2);
}
if ($menu->getA(4) == 'd' AND is_numeric($menu->getE(4)) AND $menu->getE(4) > 0 AND $menu->getE(4) < 32) {
    $gday = $menu->getE(4);
}
if ($menu->getA(3) == 'y' AND is_numeric($menu->getE(3)) AND $menu->getE(3) > 2000 AND $menu->getE(3) < date('Y', (time() + pow(8, 9)))) {
    $year = $menu->getE(3);
}
if ($menu->getA(2) == 'e' AND is_numeric($menu->getE(1))) {
    $eid = $menu->getE(2);
}

$arr_month = array(
    '1' => 'Januar',
    'Februar',
    'M�rz',
    'April',
    'Mai',
    'Juni',
    'Juli',
    'August',
    'September',
    'Oktober',
    'November',
    'Dezember'
    );
$arr_day = array(
    'So',
    'Mo',
    'Di',
    'Mi',
    'Do',
    'Fr',
    'Sa'
    );

$days = date('t', mktime(0, 0, 0, $month, 1, $year));
$start_col = date('w', mktime(0, 0, 0, $month, 1, $year)) - 1;
$rows = ceil($days / 7);
$day = 1;
$data = array();
$data_id = array();
$aus = array();
// Daten aus der MySQL
$where1 = mktime(0, 0, 0, $month, 1, $year);
$where2 = mktime(24, 0, 0, $month, date('t', $where1), $year);

$result = db_query("SELECT *
	FROM `prefix_kalender`
	WHERE ((`time` >= " . $where1 . " AND `time` < " . $where2 . ") OR `id` = " . $eid . ")
		AND " . $_SESSION[ 'authright' ] . " <= `recht`
	ORDER BY `time` LIMIT 200");
while ($row = db_fetch_assoc($result)) {
    $t_id = $row[ 'id' ];
    $t_d = date('j', $row[ 'time' ]);
    $t_m = date('n', $row[ 'time' ]);
    $t_y = date('Y', $row[ 'time' ]);
    $date = mktime(0, 0, 0, $t_m, $t_d, $t_y);
    $data_id[ $t_id ] = $row;
    $data[ $date ][ ] = $row;
}
$ueid = 0;
if (substr($eid, 0, 3) == 999) {
    $ueid = substr($eid, 3);
}
$result = db_query("SELECT
  `name`, `gebdatum`, `id`
  FROM `prefix_user`
  WHERE MONTH(`gebdatum`) = " . $month . "
	  OR `id` = " . $ueid . "
	ORDER BY MONTH(`gebdatum`), DAYOFMONTH(`gebdatum`) LIMIT 200");
while ($r = db_fetch_assoc($result)) {
    list($y, $m, $d) = explode('-', $r[ 'gebdatum' ]);
    $date = mktime(0, 0, 0, $m, $d);
    $date2 = mktime(0, 0, 0, $m, $d, $year);
    $alter = date('Y') - $y;
    $row = array(
        'title' => $alter . '. Geburtstag von ' . $r[ 'name' ],
        'text' => 'Der ' . $alter . '. Geburtstag von [url=http://' . ($_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]) . '?user-details-' . $r[ 'id' ] . '][b]' . $r[ 'name' ] . '[/b][/url]',
        'time' => $date + 99,
        'id' => '999' . $r[ 'id' ]
        );
    $data_id[ '999' . $r[ 'id' ] ] = $row;
    $data[ $date2 ][ ] = $row;
}

if ($view == 0) {
    $title_liste = $arr_month[ $month ] . ' ' . $year;
} elseif ($view == 1 && !empty($gday)) {
    $title_liste = 'Nur am ' . $gday . ' ' . $arr_month[ $month ] . ' ' . $year;
} elseif ($view == 1) {
    $title_liste = 'Liste ab ' . $arr_month[ $month ] . ' ' . $year;
}
// Template Ausgabe
$aus[ 'MONTH' ] = $arr_month[ $month ];
$aus[ 'YEAR' ] = $year;
$aus[ 'TITLE' ] = ($eid) ? $data_id[ $eid ][ 'title' ] : $title_liste;
$aus[ 'TITLE_ALIGN' ] = ($eid) ? '' : ' align="center"';
$tpl->set_ar_out($aus, 0);
unset($aus);
// Detailansicht
if ($eid) {
    $aus[ 'DEATIL_DATE' ] = date('d.m.Y', $data_id[ $eid ][ 'time' ]);
    $aus[ 'DEATIL_TIME' ] = date('H:i', $data_id[ $eid ][ 'time' ]);
    $aus[ 'DEATIL_TEXT' ] = BBcode($data_id[ $eid ][ 'text' ]);
    $tpl->set_ar_out($aus, 2);
    unset($aus);
}
// Liste der Tage (Monats-Ansicht)
elseif ($view == 0) {
    for ($i = 0; $i < $days; $i++) {
        $date = mktime(0, 0, 0, $month, $i + 1, $year);
        $text = '';
        if (isset($data[ $date ])) {
            foreach ($data[ $date ] as $eventinfo) {
                $text .= eventlink($tpl, $view, $eventinfo);
                // bbcode anwenden
                $eventinfo["text"] = BBCode($eventinfo["text"]);
                $tooltips .= $tpl->set_ar_get($eventinfo, "tooltip");
            }
        }

        $aus[ 'LIST_I' ] = $i + 1;
        $aus[ 'LIST_D' ] = $arr_day[ date('w', mktime(0, 0, 0, $month, $i + 1, $year)) ];
        $aus[ 'LIST_T' ] = $text;
        $class = ($i % 2) ? 'Cnorm' : 'Cmite';
        $aus[ 'LIST_CLASS' ] = ($i + 1 == date('j') && $month == date('n') && $year == date('Y')) ? 'Cdark' : $class;
        $tpl->set_ar_out($aus, 1);

        unset($aus);
    }
    showTooltips($tpl, $tooltips);
}
// Liste der Tage (Listenansicht)
elseif ($view == 1) {
    // Nur ein Tag
    if (isset($data) && !empty($gday)) {
        $date = mktime(0, 0, 0, $month, $gday, $year);
        $i = 1;
        if (isset($data[ $date ])) {
            foreach ($data[ $date ] as $eventinfo) {
                $text = '';
                $text .= eventlink($tpl, $view, $eventinfo);
                $aus[ 'LIST_I' ] = $arr_day[ date('w', $date) ];
                $aus[ 'LIST_D' ] = date('H:i', $eventinfo[ 'time' ]);
                $aus[ 'LIST_T' ] = $text;
                $class = ($i % 2) ? 'Cnorm' : 'Cmite';
                $aus[ 'LIST_CLASS' ] = ($i + 1 == date('j') && $month == date('n') && $year == date('Y')) ? 'Cdark' : $class;
                $tpl->set_ar_out($aus, 1);
                unset($aus);
                $i++;
                // bbcode anwenden
                $eventinfo["text"] = BBCode($eventinfo["text"]);
                $tooltips .= $tpl->set_ar_get($eventinfo, "tooltip");
            }
        }
        showTooltips($tpl, $tooltips);
        // Ganze Liste
    } elseif (isset($data)) {
        $i = 1;
        foreach ($data as $date => $data1) {
            $text = '';
            foreach ($data1 as $eventinfo) {
                $text .= eventlink($tpl, $view, $eventinfo);
            }
            $aus[ 'LIST_I' ] = date('d.m.Y', $date);
            $aus[ 'LIST_D' ] = $arr_day[ date('w', $date) ];
            $aus[ 'LIST_T' ] = $text;
            $class = ($i % 2) ? 'Cnorm' : 'Cmite';
            $aus[ 'LIST_CLASS' ] = ($i + 1 == date('j') && $month == date('n') && $year == date('Y')) ? 'Cdark' : $class;
            $tpl->set_ar_out($aus, 1);
            unset($aus);
            $i++;
            // bbcode anwenden
            $eventinfo["text"] = BBCode($eventinfo["text"]);
            $tooltips .= $tpl->set_ar_get($eventinfo, "tooltip");
        }
        showTooltips($tpl, $tooltips);
    } else {
        $aus[ 'LIST_I' ] = '-';
        $aus[ 'LIST_D' ] = '-';
        $aus[ 'LIST_T' ] = '-';
        $aus[ 'LIST_CLASS' ] = 'Cnorm';
        $tpl->set_ar_out($aus, 1);
        unset($aus);
    }
}
// kalender
$tpl->set('calender', getCalendar($month, $year, 'index.php?kalender-v1-m{mon}-y{jahr}-d{tag}', 'index.php?kalender-v' . $view . '-m{mon}-y{jahr}', $data));
$tpl->out(3);

$design->footer();

?>