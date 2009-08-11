<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
// -----------------------------------------------------------|
$title = $allgAr['title'] . ' :: Joinus';
$hmenu = 'Joinus';
$design = new design ($title , $hmenu);
$design->header();

if (0 == db_count_query("SELECT COUNT(*) FROM `prefix_groups` WHERE `show_joinus` = 1")) {
    echo $lang['noteamthere'];
    $design->footer();
    exit ();
}

$skill_ar = array (1 => $lang['verybad'],
    2 => $lang['bad'],
    3 => $lang['middle'],
    4 => $lang['good'],
    5 => $lang['verygood'],
    );

$far = array (
    'name',
    'skill',
    'icqnumber',
    'favmap',
    'mail',
    'age',
    'hometown',
    'squad',
    'ground',
    'rules'
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

$xname = escape_nickname($name);
$ch_name = false;
if (loggedin()) {
    $ch_name = true;
} elseif (isset($_POST['sub']) AND $name == $xname AND !empty($name)
        AND 0 == db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `name_clean` = BINARY '" . get_lower ($name) . "'"), 0)) {
    $ch_name = true;
}

if (count($far) != $x OR $ch_name == false OR !chk_antispam('joinus')) {
    $tpl = new tpl ('joinus.htm');
    $skill = arlistee ($skill, $skill_ar);
    $squad = '<option value="0">choose</option>';
    $squad .= dblistee ($squad, "SELECT `id`,`name` FROM `prefix_groups` WHERE `show_joinus` = 1 ORDER BY `pos`");
    if (loggedin()) {
        $name = $_SESSION['authname'];
    }
    foreach($far as $v) {
        if ($x > 0 AND empty($_POST[$v])) {
            echo 'missing: ' . $lang[$v] . '<br />';
        }
        $tpl->set ($v, $$v);
    }
    if ($x > 0 AND $name != $xname) {
        echo $lang['wrongnickname'] . '<br />';
    } elseif ($x > 0 AND $ch_name == false) {
        echo $lang['namealreadyinuse'] . '<br />';
    }
    $name = $xname;
    $tpl->set('readonly', (loggedin()?' readonly': ''));
    $tpl->out(0);
    if ($allgAr['joinus_rules'] != 1) {
        $tpl->out(1);
    } else {
        $rules = '<h2>' . $lang['rules'] . '</h2>';
        $rerg = db_query('SELECT `zahl`,`titel`,`text` FROM `prefix_rules` ORDER BY `zahl`');
        while ($rrow = db_fetch_row($rerg)) {
            $rules .= '<table width="100%" border="0" cellpadding="5" cellspacing="1" class="border">';
            $rules .= '<tr class="Cmite"><td><b>&sect;' . $rrow[0] . '. &nbsp; ' . $rrow[1] . '</b></td></tr>';
            $rules .= '<tr class="Cnorm"><td>' . bbcode($rrow[2]) . '</td></tr>';
            $rules .= '</table><br />';
        }
        $rules .= '<input type="checkbox" name="rules" value="' . $lang['yes'] . '" />' . str_replace(array('<a target="_blank" href="index.php?rules">', '</a>'), '', $lang['rulzreaded']) . '<br />';
        $tpl->set_out('RULES', $rules, 2);
    }
    $tpl->set('ANTISPAM', get_antispam('joinus', 100));
    $tpl->out(3);
} else { // eintragen
    $name = $xname;
    $userreg = $lang['no'];
    if (!loggedin() AND $allgAr['forum_regist'] != 0) {
        $x = user_regist ($name, $mail, genkey(8));
        $userreg = $lang['yes'];
    }

    db_query("INSERT INTO `prefix_usercheck` (`check`,`name`,`datime`,`ak`,`groupid`) VALUES ('" . genkey(8) . "','" . $name . "',NOW(),4,".$squad.")");

    $squad = escape($squad, 'integer');
    $abf = "SELECT `mod1`, `mod2`, `mod4`, `name` FROM `prefix_groups` WHERE `id` = " . $squad;
    $erg = db_query($abf);
    $row = db_fetch_assoc($erg);
    $rulz = (isset($_POST['rules'])?$_POST['rules']:$lang['no']);
    $skill = $skill_ar[$skill];
    // bitte in der richtigen reihenfolge angeben, sonst das nicht gehen tun, kann.
    $mailtxt = sprintf ($lang['joinusprivmsg'],
        $name,
        $row['name'],
        $skill,
        $mail,
        $hometown,
        $age,
        $icqnumber,
        $favmap,
        $ground,
        $rulz,
        $userreg
        );
    // pm an den leader
    sendpm ($_SESSION['authid'], $row['mod1'], 'Joinus Anfrage', $mailtxt, - 1);
    // Wenn Co Leader != Leader
    if ($row['mod2'] != $row['mod1']) {
        sendpm ($_SESSION['authid'], $row['mod2'], 'Joinus Anfrage', $mailtxt, - 1);
    }
    if ($row['mod4'] != $row['mod1'] AND $row['mod2'] != $row['mod4']) {
        sendpm ($_SESSION['authid'], $row['mod4'], 'Joinus Anfrage', $mailtxt, - 1);
    }

    if (!loggedin() AND $allgAr['forum_regist'] != 0) {
        echo $lang['amailhasbeensenttoyouwithmailandpass'] . '<br /><br />';
    }
    echo sprintf($lang['leaderofxalert'], $row['name']);
}
$design->footer();

?>