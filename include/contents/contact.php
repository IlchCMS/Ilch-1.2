<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Kontakt';
$hmenu = 'Kontakt';
$header = Array(
	'jquery/jquery.validate.js',
	'forms/contact.js'
    );
$design = new design($title, $hmenu);
$design->header($header);

$erg = db_query("SELECT `v2`,`t1`,`v1` FROM `prefix_allg` WHERE `k` = 'kontakt'");
$row = db_fetch_assoc($erg);
$k = explode('#', $row[ 't1' ]);

$name = '';
$mail = '';
$subject = '';
$wer = '';
$text = '';
$fehler = '';

if(isset($_POST['submit']))
{ 
	// Fehlerabfrage
	if(empty($_POST[ 'wer' ]))
	  {$fehler .= '&middot;&nbsp;'.$lang[ 'emptywer' ].'<br/>';}
	if(empty($_POST[ 'name' ]))
	  {$fehler .= '&middot;&nbsp;'.$lang[ 'emptyname' ].'<br/>';}	
	if(empty($_POST[ 'mail' ]))
	  {$fehler .= '&middot;&nbsp;'.$lang[ 'emptyemail' ].'<br/>';}
	if(empty($_POST[ 'subject' ]))
	  {$fehler .= '&middot;&nbsp;'.$lang[ 'emptysubject' ].'<br/>';}
	if(empty($_POST[ 'txt' ])) 
	  {$fehler .= '&middot;&nbsp;'.$lang[ 'emptymessage' ].'<br/>';}
	if(chk_antispam('contact') != true) 
	  {$fehler .= '&middot;&nbsp;'.$lang[ 'incorrectspam' ].'<br/>';}
	//
 
	if ($fehler == '' ) 
	{
    	$name = escape_for_email($_POST[ 'name' ]);
    	$mail = escape_for_email($_POST[ 'mail' ]);
    	$subject = escape_for_email($_POST[ 'subject' ], true);
		$wer = escape_for_email($_POST[ 'wer' ]);
		$text = $_POST[ 'txt' ];
		$wero = false;
		foreach ($k as $a) 
		{
        $e = explode('|', $a);
        if (md5($e[ 0 ]) == $wer) { $wero = true; $wer = $e[ 0 ]; break; }
		}

		if (strpos($text, 'Content-Type:') === false AND strpos($text, 'MIME-Version:') === false AND strpos($mail, '@') !== false AND $wero === true AND strlen($name) <= 30 AND strlen($mail) <= 30 AND strlen($text) <= 5000 AND $mail != $name AND $name != $text AND $text != $mail)
		{
        $subject = "Kontakt: " . $subject;
			if (icmail($wer, $subject, $text, $name . " <" . $mail . ">"))
			{        
			wd('index.php?contact', $lang[ 'emailsuccessfullsend' ]);
			$design->footer();
			} else {		
			wd('index.php?contact', 'Der Server konnte die Mail nicht versenden, teilen sie dies ggf. einem Administrator mit.');
			$design->footer();
			}
		} else {   
		$name = $_POST['name']; 
		$mail = $_POST['mail']; 
		$subject = $_POST['subject']; 
		$wer  = $_POST['wer']; 
		$text = $_POST['txt'];
        echo $lang[ 'emailcouldnotsend' ];
		}  
	} else {
	$name = $_POST['name']; 
	$mail = $_POST['mail']; 
	$subject = $_POST['subject']; 
	$wer  = $_POST['wer'];
	$text = $_POST['txt'];
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
$tpl->set('FEHLER', '<div id="formfehler">'.$fehler.'</div>');
$tpl->set('ANTISPAM', get_antispam('contact', 100));
$tpl->out(2);

$design->footer();

?>