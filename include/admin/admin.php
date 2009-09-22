<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Admins Area', 'Admins Area', 2);
$design->header();
// script version
$scriptVersion = 12;
$scriptUpdate = 'A';
// statistik wird bereinigt.
$mon = date('n');
$lastmon = $mon - 1;
$jahr = date('Y');
$lastjahr = $jahr;
if ($lastmon <= 0) {
    $lastmon = 12;
    $lastjahr = $jahr - 1;
}

db_query("DELETE FROM `prefix_stats` WHERE NOT ((`mon` = ".$mon." OR `mon` = ".$lastmon.") AND (`yar` = ".$jahr." OR `yar` = ".$lastjahr."))");
db_query("OPTIMIZE TABLE `prefix_stats`");

$um = $menu->get(1);
switch ($um) {
    default : { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="rand">
  <tr class="Chead">
    <td><b>Willkommen bei ilchClan - Administration!</b></td>
  </tr>

  <tr>
    <td class="Cnorm">



           <table width="100%"><tr><td valign="top" width="100%">
           <!--
           Hallo, hier k&ouml;nnen Sie alle m&ouml;glichen Einstellungen vornehmen.
           <br /><br />
           Wenn Sie Probleme haben sollten, oder irgendwie nicht weiter wissen, bitte
           kommen Sie uns doch auf <a href="http://www.ilch.de" target="_blank">www.ilch.de</a>
           besuchen, damit wir Ihnen helfen k&ouml;nnen.
           <br /><br />
           Auch wenn Sie Verbesserungsw&uuml;nsche oder eine geniale Idee haben,
           freuen wir uns &uuml;ber jeden Vorschlag.
           <br /><br />
           und jetzt <b>viel Spass mit dem Script!</b>
           -->
          <!-- </td><td valign="top" width="60%"> -->
           <br />
           <h3 style="display:inline;">Ein &Uuml;berblick &uuml;ber alle Inhalte</h3>

           <script type="text/javascript">
           function toggle_mimg()
           {
               class_name = document.getElementById('cpm').className;
               if(class_name == "admix")
               {
                   class_name = "admix_n";
                   link_text = "Symbol-Ansicht";
               }
               else
               {
                   class_name = "admix";
                   link_text = "Listen-Ansicht";

               }
               document.getElementById('cpm').className = class_name;
               document.getElementById('list_toggle').innerHTML = link_text;
           }
           </script>

           <ul id="cpm" class="admix">
             <li class="admix_box">Admin<br />
               <ul>
                 <li><a href="admin.php?allg"><img src="include/images/icons/admin/konfiguration.png" alt="">Konfiguration</a></li>
                 <?php if ($allgAr['mail_smtp']) { ?>
                 <li><a href="admin.php?smtpconf"><img src="include/images/icons/admin/smtpconf.png" alt="">SMTP konfigurieren</a></li>
                 <?php } ?>
				 <li><a href="admin.php?menu"><img src="include/images/icons/admin/navigation.png" alt="">Navigation</a></li>
                 <li><a href="admin.php?backup"><img src="include/images/icons/admin/backup.png" alt="">Backup</a></li>
                 <li><a href="admin.php?range"><img src="include/images/icons/admin/ranks.png" alt="">Ranks</a></li>
                 <li><a href="admin.php?smilies"><img src="include/images/icons/admin/smilies.png" alt="">Smiles</a></li>
                 <li><a href="admin.php?newsletter"><img src="include/images/icons/admin/newsletter.png" alt="">Newsletter</a></li>
                 <li><a href="admin.php?admin-versionsKontrolle"><img src="include/images/icons/admin/version_check.png" alt="">Versions Kontrolle</a></li>
                 <li><a href="admin.php?checkconf"><img src="include/images/icons/admin/version_check.png" alt="">Server Konfiguration</a></li>
                 <br class="admix_last"/>
               </ul>
             </li>
	     <li class="admix_box">Statistik<br />
                   <ul>
                     <li><a href="admin.php?admin-besucherStatistik"><img src="include/images/icons/admin/stats_visitor.png" alt="">Besucher</a></li>
                     <li><a href="admin.php?admin-siteStatistik"><img src="include/images/icons/admin/stats_site.png" alt="">Seite</a></li>
                     <li><a href="admin.php?admin-userOnline"><img src="include/images/icons/admin/stats_online.png" alt="">Online</a></li>
                     <br class="admix_last"/>
                   </ul>
                 </li>
             <li class="admix_box">Clanbox<br />
               <ul>
                 <li><a href="admin.php?wars-last"><img src="include/images/icons/admin/wars_last.png" alt="" />Lastwars</a></li>
                 <li><a href="admin.php?wars-next"><img src="include/images/icons/admin/wars_next.png" alt="" />Nextwars</a></li>
                 <li><a href="admin.php?awards"><img src="include/images/icons/admin/awards.png" alt="" />Awards</a></li>
                 <li><a href="admin.php?kasse"><img src="include/images/icons/admin/kasse.png" alt="" />Kasse</a></li>
                 <li><a href="admin.php?rules"><img src="include/images/icons/admin/rules.png" alt="" />Rules</a></li>
                 <li><a href="admin.php?history"><img src="include/images/icons/admin/history.png" alt="" />History</a></li>
                 <li><a href="admin.php?groups"><img src="include/images/icons/admin/teams.png" alt="" />Teams</a></li>
                 <li><a href="admin.php?trains"><img src="include/images/icons/admin/training_times.png" alt="" />Trainzeiten</a></li>
                 <br class="admix_last"/>
               </ul>
             </li>
             <li class="admix_box">User<br />
               <ul>
                 <li><a href="admin.php?user"><img src="include/images/icons/admin/user.png" alt="">Verwalten</a></li>
                 <li><a href="admin.php?grundrechte"><img src="include/images/icons/admin/user_rights.png" alt="">Grundrechte</a></li>
                 <li><a href="admin.php?profilefields"><img src="include/images/icons/admin/user_profile_fields.png" alt="">Profilefelder</a></li>
                 <li><a href="javascript: createNewUser();"><img src="include/images/icons/admin/user_add.png" alt="">neuen User</a></li>
                 <br class="admix_last"/>
               </ul>
             </li>
             <li class="admix_box">Content<br />
               <ul>
                 <li><a href="admin.php?news"><img src="include/images/icons/admin/news.png" alt="">News</a></li>
                 <li><a href="admin.php?forum"><img src="include/images/icons/admin/forum.png" alt="">Forum</a></li>
                 <li><a href="admin.php?archiv-downloads"><img src="include/images/icons/admin/downloads.png" alt="">Downloads</a></li>
                 <li><a href="admin.php?archiv-links"><img src="include/images/icons/admin/links.png" alt="">Links</a></li>
                 <li><a href="admin.php?gallery"><img src="include/images/icons/admin/gallery.png" alt="">Gallery</a></li>
                 <li><a href="admin.php?vote"><img src="include/images/icons/admin/vote.png" alt="">Umfrage</a></li>
                 <li><a href="admin.php?kalender"><img src="include/images/icons/admin/calendar.png" alt="">Kalender</a></li>
                 <li><a href="admin.php?contact"><img src="include/images/icons/admin/contact.png" alt="">Kontakt</a></li>
                 <li><a href="admin.php?impressum"><img src="include/images/icons/admin/imprint.png" alt="">Impressum</a></li>
                 <li><a href="admin.php?selfbp"><img src="include/images/icons/admin/self_page_box.png" alt="">Eigene Box/Page</a></li>
                 <li><a href="admin.php?gbook"><img src="include/images/icons/admin/guestbook.png" alt="">G&auml;stebuch</a></li>
                 <br class="admix_last"/>
               </ul>
             </li>
             <li class="admix_box">Boxen<br />
               <ul>
                 <li><a href="admin.php?picofx"><img src="include/images/icons/admin/picofx.png" alt="">PicOfX</a></li>
                 <li><a href="admin.php?archiv-partners"><img src="include/images/icons/admin/partners.png" alt="">Partner</a></li>
                 <br class="admix_last"/>
               </ul>
             </li>
             <li class="admix_box">Module
             <?php
            $modabf = db_query("SELECT * FROM `prefix_modules` WHERE `ashow` = 1");
            if (db_num_rows($modabf) > 0) {
                echo '<br /><ul>';
                while ($modrow = db_fetch_object($modabf)) {
                    if (file_exists('include/images/icons/admin/' . $modrow->url . '.png')) {
                        $bild = 'include/images/icons/admin/' . $modrow->url . '.png';
                    } else {
                        $bild = 'include/images/icons/admin/na.png';
                    }
                    echo '<li><a href="admin.php?' . $modrow->url . '"><img src="' . $bild . '" alt="">' . $modrow->name . '</a></li>' . "\n";
                }
                echo '<br class="admix_last"/></ul>';
            }

            ?>
             </li>
           </ul>

           </td></td></table>

		</td>
  </tr>
</table>


           <?php
            break;
        }

    case 'versionsKontrolle' : {
            // ICON Anzeige...
            echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/version_check.png" /></td><td width="30"></td><td valign="bottom"><h1>Versionskontrolle</h1></td></tr></table>';

            echo 'Scripte Version: ' . $scriptVersion . '<br />Update Version: ' . $scriptUpdate . '<br /><br />';
            echo '<script language="JavaScript" type="text/javascript" src="http://www.ilch.de/down/ilchClan/update.php?version=' . $scriptVersion . '&update=' . $scriptUpdate . '"></script>';
            // echo '<iframe width="100%" height="60" src="http://www.ilch.de/down/ilchClan/update.php?version='.$scriptVersion.'&update='.$scriptUpdate.'"></iframe>';
            break;
        }
        // ####################################
    case 'besucherStatistik' : {
            function echo_admin_site_statistik ($title, $col, $smon, $ges, $orderQuery) {
                $sql = db_query("SELECT COUNT(*) AS `wert`, ".$col." as `schl` FROM  `prefix_stats` WHERE `mon` = " . $smon . " GROUP BY `schl` ORDER BY " . $orderQuery);
                $max = @db_result(db_query("SELECT COUNT(*) as `wert`, ".$col." as `schl` FROM `prefix_stats` WHERE `mon` = " . $smon . " GROUP BY `schl` ORDER BY `wert` DESC LIMIT 1"), 0, 0);
                if (empty($max)) {
                    $max = 1;
                }
                if (empty($ges)) {
                    $ges = 1;
                }
                echo '<tr><th align="left" colspan="4">' . $title . '</th></tr>';
                while ($r = db_fetch_assoc($sql)) {
                    $wert = (empty($r['wert']) ? 1 : $r['wert']);
                    $weite = ($wert / $max) * 200;
                    $prozent = ($wert * 100) / $ges;
                    $prozent = number_format(round($prozent, 2), 2, ',', '');
                    $name = $r['schl'];
                    if (strlen ($name) >= 50) {
                        $name = substr($name, 0, 50) . '<b>...</b>';
                    }
                    echo '<tr class="norm"><td width="150" title="' . $r['schl'] . '">' . $name . '</td><td width="250">';
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

            echo_admin_site_statistik ('Besucher nach Tagen', 'day', $smon, $ges, "`schl` DESC LIMIT 50");
            echo_admin_site_statistik ('Besucher nach Wochentagen', 'DAYNAME(FROM_UNIXTIME((wtag+3)*86400))', $smon, $ges, "`wtag` DESC LIMIT 50");
            echo_admin_site_statistik ('Besucher nach Uhrzeit', 'stunde', $smon, $ges, "`schl` ASC LIMIT 50");
            echo_admin_site_statistik ('Besucher nach Browsern', 'browser', $smon, $ges, "`schl` DESC LIMIT 50");
            echo_admin_site_statistik ('Besucher nach Betriebssytemen', 'os', $smon, $ges, "`schl` DESC LIMIT 50");
            echo_admin_site_statistik ('Besucher nach Herkunft', 'ref', $smon, $ges, "`wert` DESC LIMIT 50");

            echo '</table>';

            break;
        }

    case 'userOnline' : { ?>
          <table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_online.png" /></td><td width="30"></td><td valign="bottom"><h1>Online Statistik</h1></td></tr></table>
          <table border="0" cellpadding="2" cellspacing="1" class="border">
          <tr class="Chead">
            <th>Username</th>
            <th>Letzte aktivitaet</th>
            <th>IP-Adresse</th>
            <th>Anbieter</th>
          </tr>
          <?php
            echo user_admin_online_liste();

            ?>
          </table>
          <?php

            break;
        }
    case 'besucherUebersicht' : {
            function get_max_from_x ($q) {
                $q = db_query($q);
                $m = 0;
                while ($r = db_fetch_row($q)) {
                    if ($r[0] > $m) {
                        $m = $r[0];
                    }
                }
                return ($m);
            }

            function echo_admin_site_uebersicht ($schl, $wert, $max, $ges) {
                $wert = (empty($wert) ? 1 : $wert);
                $weite = ($wert / $max) * 100;
                $prozent = ($wert * 100) / $ges;
                $prozent = number_format(round($prozent, 2), 2, ',', '');
                $name = $schl;
                if (strlen ($name) >= 50) {
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
            echo '<tr><td valign="top" width="33%"><b>Nach Tagen (letzten 5 Monate):</b><br />';

            echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
            $max = db_result(db_query("SELECT MAX(`count`) FROM `prefix_counter`"), 0);
            $ges = db_result(db_query("SELECT SUM(`count`) FROM `prefix_counter`"), 0);
            $erg = db_query("SELECT `count` as `sum`, DATE_FORMAT(`date`, '%d.%m.%Y') as `datum` FROM `prefix_counter` ORDER BY `date` DESC");
            while ($r = db_fetch_assoc($erg)) {
                echo_admin_site_uebersicht ($r['datum'], $r['sum'], $max, $ges);
            }
            echo '</table>';

            echo '</td><td valign="top" width="33%"><b>Nach Monaten:</b><br />';

            echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
            $max = get_max_from_x("SELECT SUM(`count`) FROM `prefix_counter` GROUP BY MONTH(`date`), YEAR(`date`)");
            $erg = db_query("SELECT SUM(`count`) as `sum`, MONTH(`date`) as `monat`, YEAR(`date`) as `jahr` FROM `prefix_counter` GROUP BY `monat`, `jahr` ORDER BY `jahr` DESC, `monat` DESC");
            while ($r = db_fetch_assoc($erg)) {
                echo_admin_site_uebersicht ((strlen($r['monat']) == 1?'0':'') . $r['monat'] . '.' . $r['jahr'], $r['sum'], $max, $ges);
            }
            echo '</table>';

            echo '</td><td valign="top" width="33%"><b>Nach Jahren:</b><br />';

            echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
            $max = get_max_from_x("SELECT SUM(`count`) FROM `prefix_counter` GROUP BY YEAR(`date`)");
            $erg = db_query("SELECT SUM(`count`) as `sum`, YEAR(`date`) as `jahr` FROM `prefix_counter` GROUP BY `jahr` ORDER BY `jahr` DESC");
            while ($r = db_fetch_assoc($erg)) {
                echo_admin_site_uebersicht ($r['jahr'], $r['sum'], $max, $ges);
            }
            echo '</table>';

            echo '</td></tr></table>';
            break;
        }
    case 'siteStatistik' : {
            // #########################################
            function forum_statistic_show ($sql, $ges) {
                $erg = db_query($sql);
                echo '<table border="0" cellpadding="0" cellspacing="0">';
                while ($r = db_fetch_row($erg)) {
                    // str_repeat('|',abs($row['regs'] / 2))
                    echo '<tr><td>' . $r[1] . '</td><td>' . str_repeat('|', $r[0]) . ' ' . $r[0] . '</td></tr>';
                }
                echo '</table>';
            }
            // ICON Anzeige...
            echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_site.png" /></td><td width="30"></td><td valign="bottom"><h1>Seiten Statistik</h1></td></tr></table>';

            echo '<table><tr><td valign="top">';
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

            echo '</td><td valign="top">';
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

            echo '<h1>Es ist ganz ehrlich noch mehr geplant :P</h1>';
            // #########################################
            break;
        }
}

$design->footer();

?>