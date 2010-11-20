<?php

//   Copyright by: T0P0LIN0
//   Support: www.honklords.de

date_default_timezone_set('Europe/Berlin');

include 'captcha.php';
include 'settings.php';
$captcha = new Captcha();


$captcha->setUseRandomColors( $useRandomColors );
$captcha->setImageWidth( $imagewidth );
$captcha->setImageHeight( $imageheight );
$captcha->setFontSize( $fontsize );
$captcha->set_background_intensity( $bgintensity );
$captcha->set_font_type( $bgfonttype );
$captcha->setPassPhraselenght( $passphraselenght );
$captcha->enable_scratches( $scratches );
$captcha->set_scratches_amount( $scratchamount );
$captcha->set_minmax_size( $minsize, $maxsize );
$captcha->set_showgrid( $addagrid );
$captcha->set_angle( $angle );
$captcha->set_showcoloredlines( $addhorizontallines );
$captcha->displayImage();

?>