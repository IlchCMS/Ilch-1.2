<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: Sitemap';
$hmenu = '<a href="?sitemap">Sitemap</a>';
$design = new design ($title , $hmenu);
$design->header();
// function show_sitemap shows sitemap for a given query
// and lv is the link bevor id (first field of 2 in query)
function show_sitemap ($q, $lv, $table, $menu, $where, $was) {
    $limit = 200;
    $page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1);
    $MPL = db_make_sites ($page , $where , $limit , '?sitemap-' . $menu->get(1) , $table);
    $anfang = ($page - 1) * $limit;
    $q = db_query($q . " LIMIT " . $anfang . "," . $limit);
    $tpl = new tpl ('sitemap');
    $l = '';
    while ($r = db_fetch_row($q)) {
        $l .= $tpl->list_get ('links', array(str_replace('{id}', $r[0], $lv), $r[1]));
    }
    $tpl->set_ar_out (array('MPL' => $MPL, 'site' => $was, 'links' => $l), 1);
}
// sitemap fuer module
// - fourm
// - news
// - wars
// - downloads
// - links (cats)
// - faqs
switch ($menu->get(1)) {
    default :
        $tpl = new tpl ('sitemap');
        $tpl->out(0);
        break;
    case 'forum' :
        show_sitemap ("SELECT id,name FROM prefix_topics ORDER BY id ASC", '?forum-showposts-{id}', 'topics', $menu, '', 'Forum');
        break;
    case 'downloads' :
        show_sitemap ("SELECT id, concat(name,' ',version) as x FROM prefix_downloads WHERE cat >= 0 ORDER BY id ASC", '?downloads-show-{id}', 'downloads', $menu, '', 'Downloads');
        break;
    case 'links' :
        show_sitemap ("SELECT id,name FROM prefix_linkcats ORDER BY id ASC", '?links-{id}', 'links', $menu, '', 'Links');
        break;
    case 'news' :
        show_sitemap ("SELECT news_id,news_title FROM prefix_news ORDER BY news_id ASC", '?news-{id}', 'news', $menu, "WHERE news_recht >= " . $_SESSION['authright'], 'News');
        break;
}

$design->footer();

?>