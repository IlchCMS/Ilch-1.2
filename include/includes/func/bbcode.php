<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');
function BBcode($s, $maxLength = 40) {
    // $s = unescape($s);
    $coTime = str_replace(' ', '', microtime());
    preg_match_all('/\[code\](.+)\[\/code\]/Uis', $s, $result);

    $s = bbcode_code_start ($s, $coTime, $result);
    // bbcode einheitlicher machen zum bessern pruefen.
    $s = bbcode_simple_prev ($s);
    // $s = preg_replace ("/(\015\012|\015|\012)/", " \\1", $s);
    // autoumbruch nach x zeichen
    // $s = bbcode_autonewline($s, $coTime, $maxLength);
    $s = htmlentities($s);
    // speziell bilder
    $s = bbcode_images ($s);
    // speziell zitate ersetzten.
    $s = bbcode_quote ($s);
    // replace simple
    $s = bbcode_simple ($s);
    // smilies umwandeln
    $s = bbcode_smiles ($s);

    $s = preg_replace ("/\015\012|\015|\012/", "\n<br />", $s);
    // code zurueck ersetzten
    $s = bbcode_code_end ($s, $coTime, $result);

    return ($s);
}
// diese funktion ist etwas komplizierter. und zwar wird hier versucht
// dem problem beizukommen das immer irgendwelche spassvoegel sehr lange
// texte schreiben die dann das design verzerren. dagegen hilft nur der
// automatische umbruch. ich habe mir dafuer ausgedacht es gibt
// bestimmte zeichen ab dennen die kontrolle total aus ist (url, img)
// und es gibt zeichen ab dennen die kontrolle wieder eingeschaltet wird
// ausserdem gibt es zeichen ab dennen wieder von vorn angefangen wird
// zu zahlen, wird der counter erreicht wird ein leerzeichen eingefueght.
function bbcode_autonewline ($s, $coTime, $maxLength) {
    $neu_s = '';

    $ar_start = array (
        '[url=http://',
        '[img]'
        );

    $ar_ende = array (
        ']',
        '[/img]'
        );

    $ar_neu = array (
        ' ',
        );

    $ar_next = array (' ', "\n", "\r", '[/url]', '[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', $coTime, '[list]', '[/list]', '[*]');

    $count = true;
    $countgr = null;
    $counter = - 1;

    $a = strlen ($s);
    for ($i = 0;$i < $a;$i++) {
        // counter raus / rein
        if ($count == true) {
            foreach ($ar_start as $sk => $sv) {
                if ($s {
                        $i} == substr($sv, 0, 1) AND preg_match("/^" . preg_quote($sv, '/') . "/", substr($s, $i))) {
                    $count = false;
                    $countgr = $sk;
                    $counter = 0;
                    // echo '<h1>ON</h1>';
                    break;
                }
            }
        } elseif ($count == false AND $s {
                $i} == substr($ar_ende[$countgr], 0, 1) AND preg_match("/^" . preg_quote($ar_ende[$countgr], '/') . "/", substr($s, $i))) {
            // echo '<h1>||'. $s{$i} .'||<br>||'.substr($s, $i, 10).'||<br>';
            // echo 'OFF</h1>';
            $count = true;
            $counter = - 2;
            $countgr = null;
        }

        if ($count == true) {
            $counter++;
            // ar neu?
            foreach ($ar_neu as $v) {
                if ($count == true AND $s {
                        $i} == substr($v, 0, 1) AND preg_match ("/^" . preg_quote($v) . "/", substr($s, $i))) {
                    $counter = - 3;
                    break;
                }
            }
            // springen
            foreach ($ar_next as $v) {
                if ($s {
                        $i} == substr($v, 0, 1) AND preg_match("/^" . preg_quote($v, '/') . "/", substr($s, $i))) {
                    $i = $i + strlen ($v) - 1;
                    $springen = true;
                    $valSprin = $v;
                    break;
                }
            }
            if (isset($springen) AND $springen === true) {
                $neu_s .= $valSprin;
                $springen = false;
                $valSprin = null;
                continue;
            }

            if ($counter >= $maxLength) {
                $neu_s .= ' ';
                $counter = 0;
            }
        }

        $neu_s .= $s {
            $i} ;
    }

    /*
  $s = str_replace('</a>', ' </a>', $s);
  $lines = explode(' ',$s);

	$ntxt = '';
	foreach ($lines as $v) {
	  if ( strpos($v,$coTime) === FALSE AND strpos ($v, 'src="') === FALSE AND strpos ($v, 'href="') === FALSE AND strpos ($v, '</table>') === FALSE) {
		  $ntxt .= chunk_split($v, $maxLength, ' ').' ';
    } else {
		  $ntxt .= $v.' ';
		}
	}
	$s = $ntxt;
  $s = str_replace(' </a>', '</a>', $s);
  */
    return($neu_s);
}

function bbcode_images ($s) {
    global $allgAr;

    preg_match_all('/\[img\](http|https):\/\/([^\ \?&=\#\"\n\r\t!=]+)\.(gif|jpeg|jpg|png)\[\/img\]/Ui', $s, $imgRs);

    $max_breite = 0;
    if (isset($allgAr['allg_bbcode_max_img_width'])) {
        $max_breite = $allgAr['allg_bbcode_max_img_width'];
    }
    $endung = array (1 => 'gif', 2 => 'jpg', 3 => 'png');

    if (isset ($imgRs[0][0])) {
        for($i = 0;$i < count($imgRs[0]);$i++) {
            $imgstr = $imgRs[1][$i] . '://' . $imgRs[2][$i] . '.' . $imgRs[3][$i];
            $size = @getimagesize($imgstr);
            $breite = $neueBreite = $size[0];
            $hoehe = $neueHoehe = $size[1];
            $er = '';
            if (isset($endung[$size[2]]) OR !is_array($size)) {
                $er = '<img style="border: none;" src="' . $imgstr . '" />';
                if ($breite > $max_breite) {
                    $neueHoehe = intval($hoehe * $max_breite / $breite);
                    $neueBreite = $max_breite;
                    $er = '<a href="' . $imgstr . '" target="_blank"><img height="' . $neueHoehe . '" width="' . $neueBreite . '" style="border: none;" src="' . $imgstr . '" /></a>';
                }
            }
            $s = str_replace($imgRs[0][$i], $er, $s);
        }
    }
    return($s);
}

function bbcode_quote ($s) {
    $tpl = new tpl ('zitatreplace.htm');
    $header1_quote = $tpl->get(0);
    $header2_quote = $tpl->get(1);
    $footer1_quote = $tpl->get(2);
    unset($tpl);
    $i = 0;
    while (strpos($s, "[/quote]") !== false AND $i < 5) {
        $i++;
        $s = preg_replace("#\[quote\=([^\]]*)\](.*)\[\/quote\]#Uis", $header1_quote . "geschrieben von \\1" . $header2_quote . "\\2" . $footer1_quote, $s);
        $s = preg_replace("/\[quote\](.*)\[\/quote\]/Usi", $header1_quote . $header2_quote . "\\1" . $footer1_quote, $s);
    }
    return ($s);
}

function bbcode_simple_prev ($s) {
    $search = array (
        "/(^|[^=\]\>\"])http:\/\/(www\.)?([^\s\"\<\[]*)/i",
        "/\[url\]http:\/\/(www\.)?(.*?)\[\/url\]/si",
        );

    $replace = array (
        "\\1[url]http://\\2\\3[/url]",
        "[url=http://\\1\\2]\\2[/url]",
        );

    $s = preg_replace($search, $replace, $s);
    return ($s);
}

function bbcode_simple ($s) {
    $page = preg_quote(dirname(str_replace('www.', '', $_SERVER["HTTP_HOST"]) . $_SERVER["SCRIPT_NAME"]), '/');
    $search = array (
        "/\[b\](.*?)\[\/b\]/si",
        "/\[i\](.*?)\[\/i\]/si",
        "/\[u\](.*?)\[\/u\]/si",
        "/\[url=http:\/\/(www\.)?(" . $page . ")(.*?)](.*?)\[\/url\]/si",
        "/\[url=http:\/\/(www\.)?(.*?)\](.*?)\[\/url\]/si",
        "/\[list(=1)?\](.+)\[\/list\]/Usie",
        "/(script|about|applet|activex|chrome):/is",
        );

    $replace = array (
        "<b>\\1</b>",
        "<i>\\1</i>",
        "<u>\\1</u>",
        "<a href=\"http://\\1\\2\\3\">\\4</a>",
        "<a href=\"http://\\1\\2\" target=\"_blank\">\\3</a>",
        "bbcode_simple_list ('\\1', '\\2')",
        "\\1&#058;",
        );

    $s = preg_replace($search, $replace, $s);
    return ($s);
}

function bbcode_simple_list ($w, $s) {
    // $s = preg_replace("\015\012
    $s = preg_replace("/\[\*\]([^\[]+)/ies", "'<li>'.trim('\\1').'</li>'", trim($s));
    if ($w == '=1') {
        return ('<ol>' . trim($s) . '</ol>');
    }

    return ('<ul>' . trim($s) . '</ul>');
}

function bbcode_smiles ($s) {
    global $global_smiles_array;
    if (!isset($global_smiles_array)) {
        $global_smiles_array = array();
        $erg = db_query("SELECT ent, url, emo FROM `prefix_smilies`");
        while ($row = db_fetch_object($erg)) {
            $global_smiles_array[$row->ent] = $row->emo . '#@#-_-_-#@#' . $row->url;
        }
    }
    foreach ($global_smiles_array as $k => $v) {
        list($emo, $url) = explode('#@#-_-_-#@#', $v);
        $s = str_replace($k, '<img src="include/images/smiles/' . $url . '" border="0" alt="' . $emo . '" title="' . $emo . '" />', $s);
    }
    return ($s);
}

function bbcode_code_start ($s, $coTime, $result) {
    for ($i = 0;$i < count($result[1]);$i++) {
        if ($result[0][$i]) {
            $s = str_replace ($result[0][$i], '#' . $coTime . '#' . $i . '#' . $coTime . '#', $s);
        }
    }
    return ($s);
}

function bbcode_code_end ($s, $coTime, $result) {
    $tpl = new tpl ('codereplace.htm');
    for ($i = 0;$i < count($result[1]);$i++) {
        if ($result[1][$i]) {
            ob_start();
            // $result[1][$i] = str_replace ('&lt;', '<', str_replace('&gt;', '>', $result[1][$i]));
            // $codereplace = highlight_string(trim($result[1][$i]), 1);
            highlight_string(trim($result[1][$i]));
            $codereplace = ob_get_contents();
            ob_end_clean();
            $newstring = $tpl->set_get ('CODEREPLACE', $codereplace, 0);
            $s = str_replace('#' . $coTime . '#' . $i . '#' . $coTime . '#', $newstring, $s);
        }
    }
    unset($tpl);
    return ($s);
}

?>