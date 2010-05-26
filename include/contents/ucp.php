<?php
// Copyright by: Manuel
// Support: www.ilch.de
// User Control Panel
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
    while ($row = mysql_fetch_assoc($erg)) {
        $news[] = $row;
    }
    return $news;
}
// checken, ob der nutzer eingeloggt ist
if (!loggedin()) {
    // Fehlermeldung ausgeben
    $tpl->out("please log in");
    $design->footer();
    exit();
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