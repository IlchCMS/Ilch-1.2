<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: User Control Panel';
$hmenu = 'User Control Panel';
$design = new design($title, $hmenu);
$design->header();

$tpl = new tpl("ucp/ucp");

/**
 * gibt alle news seit einem bestimmten zeitpunkt zurück
 * TODO in eigene datei auslagern
 *
 * @param int $time die zeit (timestamp), ab der die news zurück gegeben werden sollen.
 */
function get_news_since($time) {
    $erg = db_query("SELECT
      `a`.`news_title` as `title`,
      `a`.`news_id` as `id`,
      DATE_FORMAT(`a`.`news_time`,'%d. %m. %Y') as `datum`,
      DATE_FORMAT(`a`.`news_time`,'%W') as `dayofweek`,
      `a`.`news_kat` as `kate`,
      `a`.`news_text` as `text`,
      `b`.`name` as `username`
    FROM `prefix_news` as `a`
    LEFT JOIN `prefix_user` as `b` ON `a`.`user_id` = `b`.`id`
    WHERE (" . $_SESSION[ 'authright' ] . " <= `a`.`news_recht`
       			OR `a`.`news_recht` = 0)
       AND `a`.`news_time` > FROM_UNIXTIME(" . $time . ")
    ORDER BY `news_time` DESC
    LIMIT 0,5");
    $news = array();
    while ($row = db_fetch_assoc($erg)) {
        $news[] = $row;
    }
    return $news;
}
// checken, ob der nutzer eingeloggt ist
if (!loggedin()) {
    // Fehlermeldung ausgeben
    $tpl->out("please log in");
    $design->footer(1);
}

$news = get_news_since($_SESSION["lastlogin"]);
// die neuen news holen
$newsout = "";
if (sizeof($news) == 0) {
    $newsout = $tpl->get("no news");
} else {
    foreach($news as $new) {
        $newsout .= $tpl->list_get('news', array($new["id"], $new["title"]));
    }
}
// die neuen topics holen
$hottopics = get_topics_since_last_login();

$topicsout = "";
if (sizeof($hottopics) == 0) {
    $topicsout = $tpl->get("no topics");
} else {
    foreach($hottopics as $hottopic) {
        $listar = array($hottopic["id"], $hottopic["title"], $hottopic["author"]);
        $listar[] = ceil(($hottopic[ 'replies' ] + 1) / $allgAr[ 'Fpanz' ]);
        $listar[] = $hottopic["pid"];
        $topicsout .= $tpl->list_get('topics', $listar);
    }
}
// Module die im UCP angezeigt werden sollen
$modular['gallery'] 		= 'Gallerie';
$modular['kasse'] 			= 'ClanKasse';
$modular['forum-privmsg'] 	= 'Nachrichten';
$modular['user-8'] 			= 'Profil';
$modular['kalender'] 		= 'Termine';
$modular['forum'] 			= 'Forum';
$modular['wars'] 			= 'Wars';
$modular['awaycal'] 		= 'Abwesenheit';

$i = 1; // nicht null weil home-button statisch und somit schon eins vorhanden
$iconout = '<tr>
				<td align="center">
					<a href="index.php" target="_self"><img src="include/images/icons/home.png" title="zur Startseite" border="0" /><br />
					Startseite</a>
				</td>';
foreach ($modular as $key=>$val) {
	
	if (has_right($val)) {
		if ($key != 'forum-privmsg') {
			$msgcnt = '';
		} else {
			$msgcntx = db_result(db_query("SELECT COUNT(id) FROM `prefix_pm` WHERE eid = '".$_SESSION['authid']."'"));
			$msgcnt = ' ('.$msgcntx.')';
		}
		$iconout .= '<td align="center">
						<a href="index.php?'.$key.'" target="_self"><img src="include/images/icons/'.$key.'.png" title="'.$val.'" border="0" /><br />
						'.$val.$msgcnt.'</a>
					</td>';
		$i++;
	}
	if ($i == 3) { // maximal 3 Punkte in einer Zeile
		$i=0;
		$iconout .= '</tr><tr>';
	}
}
$iconout .= '</tr>';
$tpl->set('iconout', $iconout);
$tpl->set('news', $newsout);
$tpl->set('topics', $topicsout);
// unsere templatevariable
$info = $_SESSION;
$info["lastlogin"] = formatdate($_SESSION["lastlogin"]);
$info["title"] = $allgAr["title"];
$tpl->set_ar($info);
// ausgabe
$tpl->out("actions");
$tpl->out("info");

$design->footer();

?>