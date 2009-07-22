<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: News';
$hmenu = 'News';
$design = new design ($title , $hmenu);
$design->addheader('<link rel="alternate" type="application/atom+xml" title="News (Atom)" href="index.php?news-atom" />
<link rel="alternate" type="application/rss+xml" title="News (RSS)" href="index.php?news-rss" />');

function news_find_kat ($kat) {
    $katpfad = 'include/images/news/';
    $katjpg = $katpfad . $kat . '.jpg';
    $katgif = $katpfad . $kat . '.gif';
    $katpng = $katpfad . $kat . '.png';

    if (file_exists($katjpg)) {
        $pfadzumBild = $katjpg;
    } elseif (file_exists ($katgif)) {
        $pfadzumBild = $katgif;
    } elseif (file_exists ($katpng)) {
        $pfadzumBild = $katpng;
    }

    if (!empty($pfadzumBild)) {
        $kategorie = '<img style="" src="' . $pfadzumBild . '" alt="' . $kat . '" />';
    } else {
        $kategorie = '<b>' . $kat . '</b><br /><br />';
    }

    return ($kategorie);
}

if (!is_numeric($menu->get(1))) {
    if ($menu->get(1) == 'rss' || $menu->get(1) == 'atom') {
        // ob_clean();
        $feed_type = $menu->get(1);

        $abf = "SELECT MAX(news_time) AS last_update FROM prefix_news";
        $erg = db_query($abf);
        $row = db_fetch_assoc($erg);
        $last_update = str_replace(' ', 'T', $row['last_update']) . 'Z';

        $abf = "SELECT
      a.news_title as title,
      a.news_id as id,";
        $abf .= ($feed_type == 'atom') ? 'a.news_time as datum,' : "DATE_FORMAT(a.news_time,'%a, %e %b %y %H:%i:%s') as datum,";
        $abf .=
        "a.news_kat as kate,
      a.news_text as text,
      b.name as username
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    WHERE a.news_recht = 0
    ORDER BY news_time DESC LIMIT 15";
        $erg = db_query($abf);
        $tpl = new tpl('news_' . $menu->get(1) . '.htm');

        header('Content-type: application/' . $menu->get(1) . '+xml');

        $tpl->set_ar_out(array('FEEDTITLE' => $allgAr['title'],
                'UPDATED' => $last_update,
                'SITEURL' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])), 0);
        while ($row = db_fetch_assoc($erg)) {
            if ($feed_type == 'atom') {
                $Z = (date('Z') > 0 ? '+' : '') . date('H:i:s', date('Z') + 23 * 3600);
                $row['datum'] = str_replace(' ', 'T', $row['datum']) . $Z;
            }

            $a = explode('[PREVIEWENDE]', $row['text']);
            $tpl->set_ar_out(array('TITLE' => $row['title'],
                    'TXT' => bbcode($a[0]),
                    'LINK' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php?news-' . $row['id'],
                    'AUTHOR' => $row['username'],
                    'DATE' => $row['datum']
                    ), 1);
        }
        $tpl->out(2);
        exit;
    }else {
        $design->header();
        $limit = $allgAr['Nlimit'];
        $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
        $MPL = db_make_sites ($page , "WHERE news_recht >= " . $_SESSION['authright'] , $limit , '?news' , 'news');
        $anfang = ($page - 1) * $limit;

        $tpl = new tpl ('news.htm');

        $abf = "SELECT
      a.news_title as title,
      a.news_id as id,
      DATE_FORMAT(a.news_time,'%d. %m. %Y') as datum,
      DATE_FORMAT(a.news_time,'%W') as dayofweek,
      a.news_kat as kate,
      a.news_text as text,
      b.name as username
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    WHERE " . $_SESSION['authright'] . " <= a.news_recht
       OR a.news_recht = 0
    ORDER BY news_time DESC
    LIMIT " . $anfang . "," . $limit;
        // echo '<pre>'.$abf.'</pre>';
        $erg = db_query($abf);
        while ($row = db_fetch_assoc($erg)) {
            $k0m = db_query("SELECT COUNT(ID) FROM `prefix_koms` WHERE uid = " . $row['id'] . " AND cat = 'NEWS'");
            $row['kom'] = db_result($k0m, 0);

            $row['kate'] = news_find_kat($row['kate']);
            $row['datum'] = $lang[$row['dayofweek']] . ' ' . $row['datum'];
            if (strpos ($row['text'] , '[PREVIEWENDE]') !== false) {
                $a = explode('[PREVIEWENDE]' , $row['text']);
                $row['text'] = $a[0];
                $row['readwholenews'] = '&raquo; <a href="index.php?news-' . $row['id'] . '">' . $lang['readwholenews'] . '</a>  &laquo;';
            } else {
                $row['readwholenews'] = '';
            }
            $row['text'] = bbcode($row['text']);
            $tpl->set_ar_out($row, 0);
        }
        $tpl->set_out('SITELINK', $MPL, 1);
        unset($tpl);
    }
} else {
    $design->header();
    $nid = escape($menu->get(1), 'integer');
    $row = db_fetch_object(db_query("SELECT * FROM `prefix_news` WHERE news_id = '" . $nid . "'"));

    if (has_right(array($row->news_recht))) {
        $komsOK = true;
        if ($allgAr['Ngkoms'] == 0) {
            if (loggedin()) {
                $komsOK = true;
            } else {
                $komsOK = false;
            }
        }
        if ($allgAr['Nukoms'] == 0) {
            $komsOK = false;
        }
        // kommentar add
        if ((loggedin() OR chk_antispam ('newskom')) AND $komsOK AND !empty($_POST['name']) AND !empty($_POST['txt'])) {
            $_POST['txt'] = escape($_POST['txt'], 'string');
            $_POST['name'] = escape($_POST['name'], 'string');
            db_query("INSERT INTO `prefix_koms` (`uid`,`cat`,`name`,`text`) VALUES (" . $nid . ",'NEWS','" . $_POST['name'] . "','" . $_POST['txt'] . "')");
        }
        // kommentar add
        // kommentar loeschen
        if ($menu->getA(2) == 'd' AND is_numeric($menu->getE(2)) AND has_right(- 7, 'news')) {
            $kommentar_id = escape($menu->getE(2), 'integer');
            db_query("DELETE FROM prefix_koms WHERE uid = " . $nid . " AND cat = 'NEWS' AND id = " . $kommentar_id);
        }
        // kommentar loeschen
        $kategorie = news_find_kat($row->news_kat);

        $textToShow = bbcode($row->news_text);
        $textToShow = str_replace('[PREVIEWENDE]', '', $textToShow);
        if (!empty($such)) {
            $textToShow = markword($textToShow, $such);
        }

        $tpl = new tpl ('news.htm');
        $ar = array (
            'TEXT' => $textToShow,
            'KATE' => $kategorie,
            'NID' => $nid,
            'uname' => $_SESSION['authname'],
            'ANTISPAM' => (loggedin()?'':get_antispam ('newskom', 100)),
            'NAME' => $row->news_title
            );
        $tpl->set_ar_out($ar, 2);

        if ($komsOK) {
            $tpl->set_ar_out (array ('NAME' => $row->news_title , 'NID' => $nid), 3);
        }
        $erg1 = db_query("SELECT text, name, id FROM `prefix_koms` WHERE uid = " . $nid . " AND cat = 'NEWS' ORDER BY id DESC");
        $ergAnz1 = db_num_rows($erg1);
        if ($ergAnz1 == 0) {
            echo '<b>' . $lang['nocomments'] . '</b>';
        } else {
            $zahl = $ergAnz1;
            while ($row1 = db_fetch_assoc($erg1)) {
                $row1['text'] = bbcode(trim($row1['text']));
                if (has_right(- 7, 'news')) {
                    $row1['text'] .= '<a href="?news-' . $nid . '-d' . $row1['id'] . '"><img src="include/images/icons/del.gif" alt="l&ouml;schen" border="0" title="l&ouml;schen" /></a>';
                }
                $tpl->set_ar_out(array('NAME' => $row1['name'], 'TEXT' => $row1['text'], 'ZAHL' => $zahl) , 4);
                $zahl--;
            }
        }
    }
    $tpl->out(5);
}

$design->footer();

?>