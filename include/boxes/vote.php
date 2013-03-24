<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// Einstellungen
$stunden = 24;     // Sperre in Stunden
//

$tpl = new tpl('boxes/vote');

$diftime = time() - (60 * 60 * $stunden);
if (has_right(- 1)) {
    $woR = '>= "1"';
} else {
    $woR = '= "1"';
}
$fraErg = db_query('SELECT * FROM `prefix_poll` WHERE `recht` ' . $woR . ' ORDER BY `poll_id` DESC LIMIT 1');

if (db_num_rows($fraErg) > 0) {
    $fraRow = db_fetch_object($fraErg);
    if ($fraRow->stat == 1) {
        $maxRow = db_fetch_object(db_query('SELECT MAX(`res`) as `res` FROM `prefix_poll_res` WHERE `poll_id` = "' . $fraRow->poll_id . '"'));
        $gesErg = db_query('SELECT SUM(`res`) as `res` FROM `prefix_poll_res` WHERE `poll_id` = "' . $fraRow->poll_id . '"');
        $gesRow = db_fetch_object($gesErg);

        $max = $maxRow->res;
        $ges = $gesRow->res;
        $textAr = explode('#', $fraRow->text);

        if ($fraRow->recht == 2) {
            $inTextAr = $_SESSION['authid'];
        } elseif ($fraRow->recht == 1) {
            $inTextAr = $_SERVER['REMOTE_ADDR'];
        }

        $tpl->set('question', $fraRow->frage);
        $tpl->out('start');

        if (in_array($inTextAr, $textAr) OR $fraRow->stat == 0) {
            $imPollArrayDrin = true;
        } else {
            $tpl->set('pollid', $fraRow->poll_id);
            $tpl->out('selection_start');
            $imPollArrayDrin = false;
        }
        $i = 0;
        $pollErg = db_query('SELECT `antw`, `res`, `sort` FROM `prefix_poll_res` WHERE `poll_id` = "' . $fraRow->poll_id . '" ORDER BY `sort`');
        while ($pollRow = db_fetch_object($pollErg)) {
            if ($imPollArrayDrin) {
                $tpl->set('answer', $pollRow->antw);
                $tpl->set('result', $pollRow->res);
                $tpl->out('voted_points');
            } else {
                $i++;
                $tpl->set('answer', $pollRow->antw);
                $tpl->set('sort', $pollRow->sort);
                $tpl->set('number', $i);
                $tpl->out('selection_points');
            }
        }
        if ($imPollArrayDrin) {
            $tpl->set('whole', $ges);
            $tpl->out('voted_end');
        } else {
            $tpl->out('selection_end');
        }
    } else {
        $tpl->out('novote');
    }
} else {
    $tpl->out('novote');
}
