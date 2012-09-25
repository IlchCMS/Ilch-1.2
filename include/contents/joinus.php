<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
// -----------------------------------------------------------|
$title = $allgAr[ 'title' ] . ' :: Joinus';
$hmenu = 'Joinus';
$header = Array(
	'jquery/jquery.validate.js',
	'forms/joinus.js'
    );
$design = new design($title, $hmenu);
$design->header($header);

if (0 == db_count_query("SELECT COUNT(*) FROM `prefix_groups` WHERE `show_joinus` = 1")) {
    echo $lang[ 'noteamthere' ];
    $design->footer();
    exit();
}

$skill_ar = array(1 => $lang[ 'verybad' ],
    2 => $lang[ 'bad' ],
    3 => $lang[ 'middle' ],
    4 => $lang[ 'good' ],
    5 => $lang[ 'verygood' ]
    );

$far = array(
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
    if (!empty($_POST[ $v ])) {
        $$v = escape($_POST[ $v ], 'string');
        $x++;
    } else {
        $$v = '';
    }
}

$xname = escape_nickname($name);
$ch_name = false; $joinusspam = true;
if (loggedin()) {
    $ch_name = true;
} elseif (isset($_POST[ 'sub' ]) AND $name == $xname AND !empty($name) AND 0 == db_result(db_query("SELECT COUNT(*) FROM `prefix_user` WHERE `name_clean` = BINARY '" . get_lower($name) . "'"), 0)) {
    $ch_name = true;
}
if (isset($_POST['sub'])){ 
	if(chk_antispam('joinus') != true) {$fehler .= '&middot;&nbsp;'.$lang[ 'incorrectspam' ].'<br/>'; $joinusspam = false;}
	}

if (count($far) != $x OR $ch_name == false OR $joinusspam == false) {
    $tpl = new tpl('joinus.htm');
	$skill = '<option></option>';
    $skill .= arlistee($skill, $skill_ar);
    $squad = '<option></option>';
    $squad .= dblistee($squad, "SELECT `id`,`name` FROM `prefix_groups` WHERE `show_joinus` = 1 ORDER BY `pos`");
    if (loggedin()) {
        $name = $_SESSION[ 'authname' ];
    }
    foreach ($far as $v) {
        if ($x > 0 AND empty($_POST[ $v ])) {
            $fehler .= '&middot;&nbsp;'.'Bitte '. $lang[ $v ] . ' angeben!<br />';
        }
        $tpl->set($v, $$v);
    }
    if ($x > 0 AND $name != $xname) {
	  $fehler .= '&middot;&nbsp;'.$lang[ 'wrongnickname' ] . '<br />';
    } elseif ($x > 0 AND $ch_name == false) {
	  $fehler .= '&middot;&nbsp;'.$lang[ 'namealreadyinuse' ] . '<br />';
    }
    $name = $xname;
    $tpl->set('readonly', (loggedin() ? ' readonly' : ''));
	$tpl->set('FEHLER', '<div id="formfehler">'.$fehler.'</div>');
    $tpl->out(0);
    if ($allgAr[ 'joinus_rules' ] != 1) {
        $tpl->out(1);
    } else {
        $rules = '<h2>' . $lang[ 'rules' ] . '</h2>';
        $rerg = db_query('SELECT `zahl`,`titel`,`text` FROM `prefix_rules` ORDER BY `zahl`');
        while ($rrow = db_fetch_row($rerg)) {
            $rules .= '<table width="100%" border="0" cellpadding="5" cellspacing="1" class="border">';
            $rules .= '<tr class="Cmite"><td><b>&sect;' . $rrow[ 0 ] . '. &nbsp; ' . $rrow[ 1 ] . '</b></td></tr>';
            $rules .= '<tr class="Cnorm"><td>' . bbcode($rrow[ 2 ]) . '</td></tr>';
            $rules .= '</table><br />';
        }
        $rules .= '<input type="checkbox" name="rules" value="' . $lang[ 'yes' ] . '" />' . str_replace(array(
                '<a target="_blank" href="index.php?rules">',
                '</a>'
                ), '', $lang[ 'rulzreaded' ]) . '<br />';
        $tpl->set_out('RULES', $rules, 2);
    }
    $tpl->set('ANTISPAM', get_antispam('joinus', 100));
    $tpl->out(3);
} else { // eintragen
    $name = $xname;
    $userreg = $lang[ 'no' ];
    if (!loggedin() AND $allgAr[ 'forum_regist' ] != 0) {
        $x = user_regist($name, $mail, PwCrypt::getRndString(8));
        $userreg = $lang[ 'yes' ];
    }

    db_query("INSERT INTO `prefix_usercheck` (`check`,`name`,`datime`,`ak`,`groupid`) VALUES ('" . PwCrypt::getRndString(8) . "','" . $name . "',NOW(),4," . $squad . ")");

    $squad = escape($squad, 'integer');
    $abf = "SELECT `mod1`, `mod2`, `mod4`, `name` FROM `prefix_groups` WHERE `id` = " . $squad;
    $erg = db_query($abf);
    $row = db_fetch_assoc($erg);
    $rulz = (isset($_POST[ 'rules' ]) ? $_POST[ 'rules' ] : $lang[ 'no' ]);
    $skill = $skill_ar[ $skill ];
    // bitte in der richtigen reihenfolge angeben, sonst das nicht gehen tun, kann.
    $mailtxt = sprintf($lang[ 'joinusprivmsg' ], $name, $row[ 'name' ], $skill, $mail, $hometown, $age, $icqnumber, $favmap, $ground, $rulz, $userreg);
    // pm an den leader
    sendpm($_SESSION[ 'authid' ], $row[ 'mod1' ], 'Joinus Anfrage', $mailtxt, - 1);
    // Wenn Co Leader != Leader
    if ($row[ 'mod2' ] != $row[ 'mod1' ]) {
        sendpm($_SESSION[ 'authid' ], $row[ 'mod2' ], 'Joinus Anfrage', $mailtxt, - 1);
    }
    if ($row[ 'mod4' ] != $row[ 'mod1' ] AND $row[ 'mod2' ] != $row[ 'mod4' ]) {
        sendpm($_SESSION[ 'authid' ], $row[ 'mod4' ], 'Joinus Anfrage', $mailtxt, - 1);
    }

    if (!loggedin() AND $allgAr[ 'forum_regist' ] != 0) {
        echo $lang[ 'amailhasbeensenttoyouwithmailandpass' ] . '<br /><br />';
    }
    echo sprintf($lang[ 'leaderofxalert' ], $row[ 'name' ]);
}
$design->footer();

?>