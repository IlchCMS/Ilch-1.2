<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: Fightus';
$hmenu = 'Fightus';
$design = new design ($title , $hmenu);
$design->header();

if (0 == db_count_query("SELECT COUNT(*) FROM `prefix_groups` WHERE `show_fightus` = 1")) {
    echo $lang['noteamthere'];
    $design->footer();
    exit ();
}

$far = array (
    'clanname',
    'clanpage',
    'clantag',
    'clancountry',
    'mailaddy',
    'icqnumber',
    'squad',
    'meetingplace',
    'message',
    'xonx',
    'matchtype',
    'game',
    'meetingtime',
    );
$x = 0;
foreach ($far as $v) {
    if (!empty($_POST[$v])) {
        $$v = escape($_POST[$v], 'string');
        $x++;
    } else {
        $$v = '';
    }
}
if (count($far) == $x AND chk_antispam('fightus')) {
    $squad = escape($squad, 'integer');
    $abf = "SELECT `mod1`,`mod2`, `mod3`,`name` FROM `prefix_groups` WHERE `id` = " . $squad;
    $erg = db_query($abf);
    $row = db_fetch_assoc($erg);
    $txt = $lang['fightusrequest'];
    list ($datum, $zeit) = explode (' - ', $meetingtime);
    $datum = get_datum ($datum);
    $datum = $datum . " " . $zeit;
    $clanpage = get_homepage ($clanpage);
    // als upcoming war vormerken (kategorie 1)
    db_query("INSERT INTO `prefix_wars` (`datime`,`status`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt`) VALUES ('" . $datum . "','1','" . $clanname . "','" . $clantag . "','" . $clanpage . "','" . $mailaddy . "','" . $icqnumber . "','" . $meetingplace . "','" . $squad . "','" . $xonx . "','" . $game . "','" . $matchtype . "','" . $clancountry . "','" . $message . "')");
    // pm an den leader
    sendpm($_SESSION['authid'], $row['mod1'], 'Fightus Anfrage', $txt, - 1);
    // Wenn Co Leader != Leader
    if ($row['mod1'] != $row['mod2']) {
        sendpm($_SESSION['authid'], $row['mod2'], 'Fightus Anfrage', $txt, - 1);
    }
    if ($row['mod3'] != $row['mod2'] AND $row['mod1'] != $row['mod3']) {
        sendpm($_SESSION['authid'], $row['mod3'], 'Fightus Anfrage', $txt, - 1);
    }
    // informieren
    echo sprintf($lang['leaderofxalert'], $row['name']);
} else {
    $clancountry = arlistee ($clancountry, get_nationality_array());
    $squad = '<option value="0">choose</option>';
    $squad .= dblistee ($squad, "SELECT `id`,`name` FROM `prefix_groups` WHERE `show_fightus` = 1 ORDER BY pos");
    if (empty($meetingtime)) {
        $meetingtime = date ('d.m.Y - H:i:s');
    }
    $tpl = new tpl ('fightus.htm');
    foreach($far as $v) {
        if ($x > 0 AND empty($_POST[$v])) {
            echo 'missing: ' . $lang[$v] . '<br />';
        }
        $tpl->set ($v, $$v);
    }
    $tpl->set ('ANTISPAM', get_antispam ('fightus', 120));
    $tpl->out(0);
}
$design->footer();

?>