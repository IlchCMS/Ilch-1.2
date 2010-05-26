<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
// -----------------------------------------------------------|
// #
// ##
// ###
// #### ins vote
$um = $menu->get(1);
if ($menu->getA(1) == 'W') {
    $poll_id = escape($menu->getE(1), 'integer');
    $radio = escape($_POST[ 'radio' ], 'integer');

    $fraRow = db_fetch_object(db_query("SELECT * FROM `prefix_poll` WHERE `poll_id` = '" . $poll_id . "'"));
    $textAr = explode('#', $fraRow->text);
    if ($fraRow->recht == 2) {
        $inTextAr = $_SESSION[ 'authid' ];
    } elseif ($fraRow->recht == 1) {
        $inTextAr = $_SERVER[ 'REMOTE_ADDR' ];
    }
    if (!in_array($inTextAr, $textAr)) {
        $textAr[ ] = $inTextAr;
        $textArString = implode('#', $textAr);
        db_query('UPDATE `prefix_poll` SET `text` = "' . $textArString . '" WHERE `poll_id` = "' . $poll_id . '"');
        db_query('UPDATE `prefix_poll_res` SET `res` = `res` + 1 WHERE `poll_id` = "' . $poll_id . '" AND `sort` = "' . $radio . '" LIMIT 1') or die(db_error());
    }
}
// #
// ##
// ###
// #### V o t e    Ü b e r s i c h t
$title = $allgAr[ 'title' ] . ' :: ' . $lang[ 'vote' ];
$hmenu = $lang[ 'vote' ];
$design = new design($title, $hmenu);
$design->header();

?>
<table width="100%" cellpadding="2" cellspacing="1" border="0" class="border">
  <tr class="Chead">
    <td><b><?php
$lang[ 'vote' ];

?></b></td>
  </tr>

<?php

$breite = 200;
if ($_SESSION[ 'authright' ] <= - 1) {
    $woR = '>= "1"';
} else {
    $woR = '= "1"';
}
$limit = 3; // Limit
$page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
$MPL = db_make_sites($page, 'WHERE `recht` ' . $woR, $limit, "?vote", 'poll');
$anfang = ($page - 1) * $limit;
$class = '';
$erg = db_query('SELECT * FROM `prefix_poll` WHERE `recht` ' . $woR . ' ORDER BY `poll_id` DESC LIMIT ' . $anfang . ',' . $limit);
while ($fraRow = db_fetch_object($erg)) {
    $maxRow = db_fetch_object(db_query('SELECT MAX(`res`) as `res` FROM `prefix_poll_res` WHERE `poll_id` = "' . $fraRow->poll_id . '"'));
    $gesRow = db_fetch_object(db_query('SELECT SUM(`res`) as `res` FROM `prefix_poll_res` WHERE `poll_id` = "' . $fraRow->poll_id . '"'));
    $max = $maxRow->res;
    $ges = $gesRow->res;
    $textAr = explode('#', $fraRow->text);

    if ($fraRow->recht == 2) {
        $inTextAr = $_SESSION[ 'authid' ];
    } elseif ($fraRow->recht == 1) {
        $inTextAr = $_SERVER[ 'REMOTE_ADDR' ];
    }
    echo '<tr><td class="Cdark"><b>' . $fraRow->frage . '</b></td></tr>';
    if ($class == 'Cnorm') {
        $class = 'Cmite';
    } else {
        $class = 'Cnorm';
    }
    echo '<tr><td class="' . $class . '">';
    if (in_array($inTextAr, $textAr) OR $fraRow->stat == 0) {
        echo '<table width="100%" cellpadding="0">';
        $imPollArrayDrin = true;
    } else {
        echo '<form action="index.php?vote-W' . $fraRow->poll_id . '" method="POST">';
        $imPollArrayDrin = false;
    }
    $i = 0;
    $pollErg = db_query('SELECT `antw`, `res`, `sort` FROM `prefix_poll_res` WHERE `poll_id` = "' . $fraRow->poll_id . '" ORDER BY `sort`');
    while ($pollRow = db_fetch_object($pollErg)) {
        if ($imPollArrayDrin) {
            if (!empty($pollRow->res)) {
                $weite = ($pollRow->res / $max) * 200;
                $prozent = $pollRow->res * 100 / $ges;
                $prozent = round($prozent, 0);
            } else {
                $weite = 0;
                $prozent = 0;
            }
            $tbweite = $weite + 20;
            echo '<tr><td width="30%">' . $pollRow->antw . '</td>';
            echo '<td width="50%">';
            /*
            '<table width="'.$tbweite.'" border="0" cellpadding="0" cellspacing="0"></td>';
            echo '<tr><td width="10" height="10"></td>';
            echo '<td width="'.$weite.'" background="include/images/vote/voteMitte.jpg" alt=""></td>';
            echo '<td width="10"><img src="include/images/vote/voteRight.jpg" alt=""></td>';
            echo '</tr></table>';*/
            echo '<div style="height: 10px; width: ' . $weite . 'px; background: #3776a5 url(include/images/vote/voteMitte.png) repeat-y top left;">' . '</div>';

            echo '<td width="10%">' . $prozent . '%</td>';
            echo '<td width="20%" align="right">' . $pollRow->res . '</td></tr>';
        } else {
            $i++;
            echo '<input type="radio" id="vote' . $i . '" name="radio" value="' . $pollRow->sort . '"><label for="vote' . $i . '"> ' . $pollRow->antw . '</label><br/>';
        }
    }
    if ($imPollArrayDrin) {
        echo '<tr><td colspan="2" align="right">' . $lang[ 'whole' ] . ': &nbsp; ' . $ges . '</td></tr></table>';
    } else {
        echo '<p align="center"><input type="submit" value="' . $lang[ 'formsub' ] . '"></p></form>';
    }

    echo '</td></tr>';
} // end while
echo '<tr><td class="Cdark" align="center">' . $MPL . '</td></tr></table>';
$design->footer();

?>