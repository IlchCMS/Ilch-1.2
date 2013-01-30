<?php
/**
 * Captcha für www.ilch.de
 * @author T0P0LIN0
 * thanks to uwe slick! http://www.deruwe.de/captcha.html - his thoughts
 */
date_default_timezone_set('Europe/Berlin');

include 'captcha.php';
include 'settings.php';
$captcha = new Captcha();

$captcha->setUseRandomColors($useRandomColors);
$captcha->setImageWidth($imagewidth);
$captcha->setImageHeight($imageheight);
$captcha->setFontSize($fontsize);
$captcha->setBackgroundIntensity($bgintensity);
$captcha->setFontType($bgfonttype);
$captcha->setPassPhraselenght($passphraselenght);
$captcha->enableScratches($scratches);
$captcha->setScratchesAmount($scratchamount);
$captcha->setMinMaxSize($minsize, $maxsize);
$captcha->setShowgrid($addagrid);
$captcha->setAngle($angle);
$captcha->setShowColoredLines($addhorizontallines);

$captchaId = isset($_GET['id']) ? $_GET['id'] : 'alwaysWrong';

$captcha->displayImage($captchaId);
