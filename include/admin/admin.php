<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Willkommen', '', 2);
$design->header();
// script version
$scriptVersion = 1.2;
$scriptUpdate = 1;
// statistik wird bereinigt.
$mon = date('n');
$lastmon = $mon - 1;
$jahr = date('Y');
$lastjahr = $jahr;
if ($lastmon <= 0) {
    $lastmon = 12;
    $lastjahr = $jahr - 1;
}

db_query("DELETE FROM `prefix_stats` WHERE NOT ((`mon` = " . $mon . " OR `mon` = " . $lastmon . ") AND (`yar` = " . $jahr . " OR `yar` = " . $lastjahr . "))");
db_query("OPTIMIZE TABLE `prefix_stats`");

$um = $menu->get(1);
switch ($um) {
    default:
        // Funktionen
        // Menue im Template ausgeben
        function make_menu_list($erg, $katname = '') {
            global $tpl;

            while ($row = db_fetch_assoc($erg)) {
                if ($katname != $row[ 'menu' ]) {
                    if ($katname != '') {
                        $tpl->out(3);
                    }
                    $tpl->set_ar_out(Array(
                            'kat' => $row[ 'menu' ],
                            'url' => $row[ 'url' ]
                            ), 1);
                    $katname = $row[ 'menu' ];
                }

                $exturl = str_replace('-', '_', $row[ 'url' ]);
                $expurl = explode('_', $exturl);

                if (file_exists('include/images/icons/admin/' . $exturl . '.png')) {
                    $bild = 'include/images/icons/admin/' . $exturl . '.png';
                } else if (file_exists('include/images/icons/admin/' . $expurl[ 0 ] . '.png')) {
                    $bild = 'include/images/icons/admin/' . $expurl[ 0 ] . '.png';
                } else {
                    $bild = 'include/images/icons/admin/na.png';
                }

                $tpl->set_ar_out(Array(
                        'url' => $row[ 'url' ],
                        'pic' => $bild,
                        'name' => $row[ 'name' ]
                        ), 2);
            }

            if ($katname != '' AND $katname != 'Admin') {
                $tpl->out(3);
            }
        }
        // Kategorie-Name
        $katname = '';
        // Template laden
        $tpl = new tpl('admin', 1);
        // Template-Header
        $tpl->out(0);
        // Module abfragen und Ausgeben
        $first_erg = db_query("SELECT * FROM `prefix_modules` WHERE `menu` = 'admin' ORDER BY  `pos` ASC");
        $second_erg = db_query("SELECT * FROM `prefix_modules` WHERE `menu` != '' AND `menu` != 'admin' ORDER BY `menu`, `pos` ASC");
        // Admin gesondert ausgeben
        make_menu_list($first_erg);
        // Restliche Module
        make_menu_list($second_erg, 'Admin');
        // Template-Footer
        $tpl->out(4);

        break;

    case 'versionsKontrolle':
        // ICON Anzeige...
        echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/version_check.png" /></td><td width="30"></td><td valign="bottom"><h1>Versionskontrolle</h1></td></tr></table>';

        echo 'Scripte Version: ' . $scriptVersion . '<br />Update Version: ' . $scriptUpdate . '<br /><br />';
        echo '<script language="JavaScript" type="text/javascript" src="http://www.ilch.de/down/ilchClan/update.php?version=' . $scriptVersion . '&update=' . $scriptUpdate . '"></script>';
        break;
    // ####################################
    case 'besucherStatistik':
        function echo_admin_site_statistik($title, $col, $smon, $ges, $orderQuery) {
            $sql = db_query("SELECT COUNT(*) AS `wert`, " . $col . " as `schl` FROM  `prefix_stats` WHERE `mon` = " . $smon . " GROUP BY `schl` ORDER BY " . $orderQuery);
            $max = @db_result(db_query("SELECT COUNT(*) as `wert`, " . $col . " as `schl` FROM `prefix_stats` WHERE `mon` = " . $smon . " GROUP BY `schl` ORDER BY `wert` DESC LIMIT 1"), 0, 0);
            if (empty($max)) {
                $max = 1;
            }
            if (empty($ges)) {
                $ges = 1;
            }
            echo '<tr><th align="left" colspan="4">' . $title . '</th></tr>';
            while ($r = db_fetch_assoc($sql)) {
                $wert = (empty($r[ 'wert' ]) ? 1 : $r[ 'wert' ]);
                $weite = ($wert / $max) * 200;
                $prozent = ($wert * 100) / $ges;
                $prozent = number_format(round($prozent, 2), 2, ',', '');
                $name = $r[ 'schl' ];
                if (strlen($name) >= 50) {
                    $name = substr($name, 0, 50) . '<b>...</b>';
                }
                echo '<tr class="norm"><td width="150" title="' . $r[ 'schl' ] . '">' . $name . '</td><td width="250">';
                echo '<hr width="' . $weite . '" align="left" /></td>';
                echo '<td width="50" align="right">' . $prozent . '%</td>';
                echo '<td  width="50" align="right">' . $wert . '</td></tr>';
            }
        }
        // ICON Anzeige...
        echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_visitor.png" /></td><td width="30"></td><td valign="bottom"><h1>Besucher Statistik</h1></td></tr></table>';

        echo '<a href="admin.php?admin-besucherUebersicht">&Uuml;bersicht</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $lastmon . '" title="' . $lastmon . '. ' . $lastjahr . '">letzter Monat</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $mon . '" title="' . $mon . '. ' . $jahr . '">dieser Monat</a>';
        $smon = $menu->get(2);
        if (empty($smon)) {
            $smon = $mon;
        }

        $ges = db_result(db_query("SELECT COUNT(*) FROM `prefix_stats` WHERE `mon` = " . $smon), 0, 0);
        echo '<br /><br /><b>Gesamt diesen Monat: ' . $ges . '</b>';
        echo '<table cellpadding="2" border="0" cellspacing="0">';

        echo_admin_site_statistik('Besucher nach Tagen', 'day', $smon, $ges, "`schl` DESC LIMIT 50");
        echo_admin_site_statistik('Besucher nach Wochentagen', 'DAYNAME(FROM_UNIXTIME((wtag+3)*86400))', $smon, $ges, "`wtag` DESC LIMIT 50");
        echo_admin_site_statistik('Besucher nach Uhrzeit', 'stunde', $smon, $ges, "`schl` ASC LIMIT 50");
        echo_admin_site_statistik('Besucher nach Browsern', 'browser', $smon, $ges, "`schl` DESC LIMIT 50");
        echo_admin_site_statistik('Besucher nach Betriebssytemen', 'os', $smon, $ges, "`schl` DESC LIMIT 50");
        echo_admin_site_statistik('Besucher nach Herkunft', 'ref', $smon, $ges, "`wert` DESC LIMIT 50");

        echo '</table>';

        break;

    case 'userOnline':
        ?>
          <table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_online.png" /></td><td width="30"></td><td valign="bottom"><h1>Online Statistik</h1></td></tr></table>
          <table border="0" cellpadding="2" cellspacing="1" class="border">
          <tr class="Chead">
            <th>Username</th>
            <th>Letzte aktivitaet</th>
            <th>IP-Adresse</th>
            <th>Anbieter</th>
            <th>Aufenthalt</th>
          </tr>
          <?php
        echo user_admin_online_liste();

        ?>
          </table>
          <?php

        break;

    case 'besucherUebersicht':
        function get_max_from_x($q) {
            $q = db_query($q);
            $m = 0;
            while ($r = db_fetch_row($q)) {
                if ($r[ 0 ] > $m) {
                    $m = $r[ 0 ];
                }
            }
            return ($m);
        }

        function echo_admin_site_uebersicht($schl, $wert, $max, $ges) {
            $wert = (empty($wert) ? 1 : $wert);
            $weite = ($wert / $max) * 100;
            $prozent = ($wert * 100) / $ges;
            $prozent = number_format(round($prozent, 2), 2, ',', '');
            $name = $schl;
            if (strlen($name) >= 50) {
                $name = substr($name, 0, 50) . '<b>...</b>';
            }
            echo '<tr class="norm"><td width="150" title="' . $schl . '">' . $name . '</td><td width="250">';
            echo '<hr width="' . $weite . '" align="left" /></td>';
            echo '<td width="50" align="right">' . $prozent . '%</td>';
            echo '<td  width="50" align="right">' . $wert . '</td></tr>';
        }
        // ICON Anzeige...
        echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_visitor.png" /></td><td width="30"></td><td valign="bottom"><h1>Besucher Statistik</h1></td></tr></table>';

        echo '<a href="admin.php?admin-besucherUebersicht">&Uuml;bersicht</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $lastmon . '" title="' . $lastmon . '. ' . $lastjahr . '">letzter Monat</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $mon . '" title="' . $mon . '. ' . $jahr . '">dieser Monat</a>';

        echo '<br /><br /><table cellpadding="0" border="0" cellspacing="0" width="100%">';
        echo '<tr><td v width="33%"><b>Nach Tagen (letzten 5 Monate):</b><br />';

        echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
        $max = db_result(db_query("SELECT MAX(`count`) FROM `prefix_counter`"), 0);
        $ges = db_result(db_query("SELECT SUM(`count`) FROM `prefix_counter`"), 0);
        $erg = db_query("SELECT `count` as `sum`, DATE_FORMAT(`date`, '%d.%m.%Y') as `datum` FROM `prefix_counter` ORDER BY `date` DESC");
        while ($r = db_fetch_assoc($erg)) {
            echo_admin_site_uebersicht($r[ 'datum' ], $r[ 'sum' ], $max, $ges);
        }
        echo '</table>';

        echo '</td><td v width="33%"><b>Nach Monaten:</b><br />';

        echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
        $max = get_max_from_x("SELECT SUM(`count`) FROM `prefix_counter` GROUP BY MONTH(`date`), YEAR(`date`)");
        $erg = db_query("SELECT SUM(`count`) as `sum`, MONTH(`date`) as `monat`, YEAR(`date`) as `jahr` FROM `prefix_counter` GROUP BY `monat`, `jahr` ORDER BY `jahr` DESC, `monat` DESC");
        while ($r = db_fetch_assoc($erg)) {
            echo_admin_site_uebersicht((strlen($r[ 'monat' ]) == 1 ? '0' : '') . $r[ 'monat' ] . '.' . $r[ 'jahr' ], $r[ 'sum' ], $max, $ges);
        }
        echo '</table>';

        echo '</td><td v width="33%"><b>Nach Jahren:</b><br />';

        echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
        $max = get_max_from_x("SELECT SUM(`count`) FROM `prefix_counter` GROUP BY YEAR(`date`)");
        $erg = db_query("SELECT SUM(`count`) as `sum`, YEAR(`date`) as `jahr` FROM `prefix_counter` GROUP BY `jahr` ORDER BY `jahr` DESC");
        while ($r = db_fetch_assoc($erg)) {
            echo_admin_site_uebersicht($r[ 'jahr' ], $r[ 'sum' ], $max, $ges);
        }
        echo '</table>';

        echo '</td></tr></table>';
        break;

    case 'siteStatistik':
        // #########################################
        function forum_statistic_show($sql, $ges) {
            $erg = db_query($sql);
            echo '<table border="0" cellpadding="0" cellspacing="0">';
            while ($r = db_fetch_row($erg)) {
                // str_repeat('|',abs($row['regs'] / 2))
				$sum = db_result(db_query("SELECT SUM(counter) FROM `prefix_stats_content`"));
				
				$proz = $r[ 0 ] / $sum * 100;
                echo '<tr><td>' . $r[ 1 ] . '</td><td>' . str_repeat('|', $proz) . ' ' . $r[ 0 ] . ' (' . number_format($proz, 2).' %)</td></tr>';
            }
            echo '</table>';
        }
        // ICON Anzeige...
        echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_site.png" /></td><td width="30"></td><td valign="bottom"><h1>Seiten Statistik</h1></td></tr></table>';

        echo '<table><tr><td v>';
        $heute = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $anzheute = db_result(db_query("SELECT COUNT(*) FROM `prefix_posts` WHERE `time` >= " . $heute), 0, 0);
        echo 'Gesamt Posts heute: ' . $anzheute . '<br /><hr>';
        // aktivsten user
        $sql = "SELECT COUNT(*) as `kk` , `erst` as `vv` FROM `prefix_posts` WHERE `time` >= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<b>Aktivsten User heute</b><br />';
        forum_statistic_show($sql, $anzheute);
        // aktivsten themen
        $sql = "SELECT COUNT(*) as `kk` , `name` as `vv` FROM `prefix_topics` LEFT JOIN `prefix_posts` ON `prefix_posts`.`tid` = `prefix_topics`.`id` WHERE `time` >= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<hr><b>Aktivsten Themen heute</b><br />';
        forum_statistic_show($sql, $anzheute);
        // aktivsten foren
        $sql = "SELECT COUNT(*) as `kk` , `prefix_forums`.`name` as `vv` FROM `prefix_topics` LEFT JOIN `prefix_forums` ON `prefix_forums`.`id` = `prefix_topics`.`fid` LEFT JOIN `prefix_posts` ON `prefix_posts`.`tid` = `prefix_topics`.`id` WHERE `time` >= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<hr><b>Aktivsten Foren heute</b><br />';
        forum_statistic_show($sql, $anzheute);
        // neue user heute
        $gsh = db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `regist` >= " . $heute), 0, 0);
        $sql = "SELECT COUNT(*) as `kk` , `name` as `vv` FROM `prefix_user` WHERE `regist` >= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<hr><b>Neue User heute</b><br />';
        forum_statistic_show($sql, $gsh);

        echo '</td><td v>';
        $heute1 = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $anzheute = db_result(db_query("SELECT COUNT(*) FROM `prefix_posts` WHERE `time` >= " . $heute1 . " AND `time` <= " . $heute), 0, 0);
        echo 'Gesamt Posts gestern: ' . $anzheute . '<br /><hr>';
        // aktivsten user
        $sql = "SELECT COUNT(*) as `kk` , `erst` as `vv` FROM `prefix_posts` WHERE `time` >= " . $heute1 . " AND `time` <= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<b>Aktivsten User gestern</b><br />';
        forum_statistic_show($sql, $anzheute);
        // aktivsten themen
        $sql = "SELECT COUNT(*) as `kk` , `name` as `vv` FROM `prefix_topics` LEFT JOIN `prefix_posts` ON `prefix_posts`.`tid` = `prefix_topics`.`id` WHERE `time` >= " . $heute1 . " AND `time` <= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<hr><b>Aktivsten Themen gestern</b><br />';
        forum_statistic_show($sql, $anzheute);
        // aktivsten foren
        $sql = "SELECT COUNT(*) as `kk` , `prefix_forums`.`name` as `vv` FROM `prefix_topics` LEFT JOIN `prefix_forums` ON `prefix_forums`.`id` = `prefix_topics`.`fid` LEFT JOIN `prefix_posts` ON `prefix_posts`.`tid` = `prefix_topics`.`id` WHERE `time` >= " . $heute1 . " AND `time` <= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<hr><b>Aktivsten Foren gestern</b><br />';
        forum_statistic_show($sql, $anzheute);
        // neue user heute
        $gsh = db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `regist` >= " . $heute1 . " AND `regist` <= " . $heute), 0, 0);
        $sql = "SELECT COUNT(*) as `kk` , `name` as `vv` FROM `prefix_user` WHERE `regist` >= " . $heute1 . " AND `regist` <= " . $heute . " GROUP BY `vv` ORDER BY `kk` DESC LIMIT 10";
        echo '<hr><b>Neue User gestern</b><br />';
        forum_statistic_show($sql, $gsh);

        echo '</td></tr></table>';

		// meist besuchte Seiten
        $gsh = db_result(db_query("SELECT COUNT(*) FROM `prefix_stats_content` ORDER BY counter DESC LIMIT 25"),0 ,0);
        $sql = "SELECT counter, content FROM `prefix_stats_content` ORDER BY counter DESC LIMIT 25";
        echo '<hr><b>meist besuchter Content</b><br />';
        forum_statistic_show($sql, $anzheute);

        // #########################################
        break;
}

$design->footer();

?>