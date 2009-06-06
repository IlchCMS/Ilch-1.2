<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');
// -----------------------------------------------------------|
// Vote Sperre in Stunden
$stunden = 24;

$breite = 50;
$diftime = time() - (60 * 60 * $stunden);

if (has_right(- 1)) {
    $woR = '>= "1"';
} else {
    $woR = '= "1"';
}

$fraErg = db_query('SELECT * FROM `prefix_poll` WHERE recht ' . $woR . ' ORDER BY poll_id DESC LIMIT 1');

if (db_num_rows($fraErg) > 0) {
    $fraRow = db_fetch_object($fraErg);
    if ($fraRow->stat == 1) {
        $maxRow = db_fetch_object(db_query('SELECT MAX(res) as res FROM `prefix_poll_res` WHERE poll_id = "' . $fraRow->poll_id . '"'));
        $gesErg = db_query('SELECT SUM(res) as res FROM `prefix_poll_res` WHERE poll_id = "' . $fraRow->poll_id . '"');
        $gesRow = db_fetch_object($gesErg);

        $max = $maxRow->res;
        $ges = $gesRow->res;
        $textAr = explode('#', $fraRow->text);

        if ($fraRow->recht == 2) {
            $inTextAr = $_SESSION['authid'];
        } elseif ($fraRow->recht == 1) {
            $inTextAr = $_SERVER['REMOTE_ADDR'];
        }

        echo '<b>' . $fraRow->frage . '</b>';
        if (in_array ($inTextAr , $textAr) OR $fraRow->stat == 0) {
            echo '<table width="100%" cellpadding="0">';
            $imPollArrayDrin = true;
        } else {
            echo '<form action="index.php?vote-W' . $fraRow->poll_id . '" method="POST">';
            $imPollArrayDrin = false;
        }
        $i = 0;
        $pollErg = db_query('SELECT antw, res, sort FROM `prefix_poll_res` WHERE poll_id = "' . $fraRow->poll_id . '" ORDER BY sort');
        while ($pollRow = db_fetch_object($pollErg)) {
            if ($imPollArrayDrin) {
                echo '<tr><td>' . $pollRow->antw . '</td><td align="right">' . $pollRow->res . '</td></tr>';
            } else {
                $i++;
                echo '<input type="radio" id="vote' . $i . '" name="radio" value="' . $pollRow->sort . '"><label for="vote' . $i . '"> ' . $pollRow->antw . '</label><br>';
            }
        }
        if ($imPollArrayDrin) {
            echo '<tr><td colspan="2" align="right">' . $lang['whole'] . ': &nbsp; ' . $ges . '</td></tr></table>';
        } else {
            echo '<p align="center"><input type="submit" value="' . $lang['formsub'] . '"></p></form>';
        }
    } else {
        echo $lang['nowvoteavailable'];
    }
} else {
    echo $lang['nowvoteavailable'];
}

?>