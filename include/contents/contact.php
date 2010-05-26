<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Kontakt';
$hmenu = 'Kontakt';
$design = new design($title, $hmenu);
$design->header();

$erg = db_query("SELECT `v2`,`t1`,`v1` FROM `prefix_allg` WHERE `k` = 'kontakt'");
$row = db_fetch_assoc($erg);
$k = explode('#', $row[ 't1' ]);

$name = '';
$mail = '';
$subject = '';
$wer = '';
$text = '';
if (!empty($_POST[ 'wer' ]) AND !empty($_POST[ 'mail' ]) AND !empty($_POST[ 'txt' ]) AND !empty($_POST[ 'name' ]) AND !empty($_POST[ 'subject' ]) AND chk_antispam('contact')) {
    $name = escape_for_email($_POST[ 'name' ]);
    $mail = escape_for_email($_POST[ 'mail' ]);
    $subject = escape_for_email($_POST[ 'subject' ], true);
    $wer = escape_for_email($_POST[ 'wer' ]);
    $text = $_POST[ 'txt' ];
    $wero = false;
    foreach ($k as $a) {
        $e = explode('|', $a);
        if (md5($e[ 0 ]) == $wer) {
            $wero = true;
            $wer = $e[ 0 ];
            break;
        }
    }

    if (strpos($text, 'Content-Type:') === false AND strpos($text, 'MIME-Version:') === false AND strpos($mail, '@') !== false AND $wero === true AND strlen($name) <= 30 AND strlen($mail) <= 30 AND strlen($text) <= 5000 AND $mail != $name AND $name != $text AND $text != $mail) {
        $subject = "Kontakt: " . $subject;
        if (icmail($wer, $subject, $text, $name . " <" . $mail . ">")) {
            echo $lang[ 'emailsuccessfullsend' ];
        } else {
            echo 'Der Server konnte die Mail nicht versenden, teilen sie dies ggf. einem Administrator mit.';
        }
        $name = '';
        $mail = '';
        $subject = '';
        $wer = '';
        $text = '';
    } else {
        echo $lang[ 'emailcouldnotsend' ];
    }
}

$tpl = new tpl('contact.htm');
$tpl->out(0);

$i = 1;
foreach ($k as $a) {
    $e = explode('|', $a);
    if ($e[ 0 ] == '' OR $e[ 1 ] == '') {
        continue;
    }
    if ($i == 1) {
        $c = 'checked';
    } else {
        $c = '';
    }
    $tpl->set_ar_out(array(
            'KEY' => md5($e[ 0 ]),
            'VAL' => $e[ 1 ],
            'c' => $c
            ), 1);
    $i++;
}

$tpl->set('name', $name);
$tpl->set('mail', $mail);
$tpl->set('subject', $subject);
$tpl->set('text', $text);
$tpl->set('ANTISPAM', get_antispam('contact', 100));
$tpl->out(2);

$design->footer();

?>