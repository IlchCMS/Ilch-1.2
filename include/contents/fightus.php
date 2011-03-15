<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Fightus';
$hmenu = 'Fightus';
$header = Array(
	'jquery/jquery.validate.js',
	'forms/fightus.js'
    );
$design = new design($title, $hmenu);
$design->header($header);

if (0 == db_count_query("SELECT COUNT(*) FROM `prefix_groups` WHERE `show_fightus` = 1")) {
    echo $lang[ 'noteamthere' ];
    $design->footer();
    exit();
}

$far = array(
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
	'game',
    'matchtype',
    'date',
	'stunde',
	'minute'
    );
$x = 0;
$fightusspam = false;
$fehler='';
foreach ($far as $v) {
    if (!empty($_POST[ $v ])) {
        $$v = escape($_POST[ $v ], 'string');
        $x++;
    } else {
        $$v = '';
    }
}
if (isset($_POST['submit']))
{ 
	if (chk_antispam('fightus') != true) 
	{$fehler .= '&middot;&nbsp;'.$lang[ 'incorrectspam' ].'<br/>'; $fightusspam = false;} else { $fightusspam = true; }
}
	
if (count($far) == $x AND $fightusspam == true) {
    $squad = escape($squad, 'integer');
    $abf = "SELECT `mod1`,`mod2`, `mod3`,`name` FROM `prefix_groups` WHERE `id` = " . $squad;
    $erg = db_query($abf);
    $row = db_fetch_assoc($erg);
    $txt = $lang[ 'fightusrequest' ];
	$sekunde = '00';
	$datum = get_datum($date). ' - ' .$stunde. ':' .$minute. ':' .$sekunde;
    $clanpage = get_homepage($clanpage);
    // als upcoming war vormerken (kategorie 1)
    db_query("INSERT INTO `prefix_wars` (`datime`,`status`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt`) VALUES ('" . $datum . "','1','" . $clanname . "','" . $clantag . "','" . $clanpage . "','" . $mailaddy . "','" . $icqnumber . "','" . $meetingplace . "','" . $squad . "','" . $xonx . "','" . $game . "','" . $matchtype . "','" . $clancountry . "','" . $message . "')");
    // pm an den leader
    sendpm($_SESSION[ 'authid' ], $row[ 'mod1' ], 'Fightus Anfrage', $txt, - 1);
    // Wenn Co Leader != Leader
    if ($row[ 'mod1' ] != $row[ 'mod2' ]) {
        sendpm($_SESSION[ 'authid' ], $row[ 'mod2' ], 'Fightus Anfrage', $txt, - 1);
    }
    if ($row[ 'mod3' ] != $row[ 'mod2' ] AND $row[ 'mod1' ] != $row[ 'mod3' ]) {
        sendpm($_SESSION[ 'authid' ], $row[ 'mod3' ], 'Fightus Anfrage', $txt, - 1);
    }
    // informieren
    echo sprintf($lang[ 'leaderofxalert' ], $row[ 'name' ]);
} else {
    $clancountry = '<option></option>';
	$clancountry .= arlistee($clancountry, get_nationality_array());
    $squad = '<option></option>';
    $squad .= dblistee($squad, "SELECT `id`,`name` FROM `prefix_groups` WHERE `show_fightus` = 1 ORDER BY pos");

    $tpl = new tpl('fightus.htm');
    foreach ($far as $v) {
        if ($x > 0 AND empty($_POST[ $v ])) {
			$fehler .= '&middot;&nbsp;'.'Bitte '. $lang[ $v ] . ' angeben!<br />';
        }
        $tpl->set($v, $$v);
    }
	$tpl->set('FEHLER', '<div id="formfehler">'.$fehler.'</div>');
    $tpl->set('ANTISPAM', get_antispam('fightus', 120));
    $tpl->out(0);
}
$design->footer();

?>