<?php
// Copyright by: Manuel
// Support: www.ilch.de
// Captcha by: T0P0LIN0
// Support: www.honklords.de
defined('main') or die('no direct access');
defined('admin') or die('administrative access only - nur administrativer Zugang');

if (isset($_POST[ 'sub' ])) {
    extract($_POST);
    $ximagewidth = intval($ximagewidth);
    $ximageheight = intval($ximageheight);
    $xfontsize = intval($xfontsize);
    $xbgintensity = intval($xbgintensity);
    $xbgfonttype = intval($xbgfonttype);
    $xscratchamount = intval($xscratchamount);
    $xscratches = ($xscratches == 1) ? $xscratches : 0;
    $xaddagrid = ($xaddagrid == 1) ? $xaddagrid : 0;
    $xaddhorizontallines = ($xaddhorizontallines == 1) ? $xaddhorizontallines : 0;
    $xuseRandomColors = ($xuseRandomColors == 1) ? $xuseRandomColors : 0;
    $xangle = intval($xangle);
    $xminsize = intval($xminsize);
    $xmaxsize = intval($xmaxsize);
    $xpassphraselenght = (intval($xpassphraselenght) > 0) ? intval($xpassphraselenght) : 5;
    @chmod("include/includes/func/captcha/settings.php", 0666);
    $open = @fopen("include/includes/func/captcha/settings.php", "w");
    $content = "<?php\n";
    $content .= "#########################################################################\n";
    $content .= "# Author: T0P0LIN0                                                      #\n";
    $content .= "# thanks to uwe slick! http://www.deruwe.de/captcha.html - his thoughts #\n";
    $content .= "#########################################################################\n\n";
    $content .= "\$imagewidth = " . $ximagewidth . ";\n";
    $content .= "\$imageheight = " . $ximageheight . ";\n";
    $content .= "\$fontsize = " . $xfontsize . ";\n";
    $content .= "\$bgintensity = " . $xbgintensity . ";\n";
    $content .= "\$bgfonttype = " . $xbgfonttype . ";\n";
    $content .= "\$scratchamount = " . $xscratchamount . ";\n";
    $content .= "\$scratches = " . $xscratches . ";\n";
    $content .= "\$passphraselenght = " . $xpassphraselenght . ";\n";
    $content .= "\$addagrid = " . $xaddagrid . ";\n";
    $content .= "\$addhorizontallines = " . $xaddhorizontallines . ";\n";
    $content .= "\$useRandomColors = " . $xuseRandomColors . ";\n";
    $content .= "\$minsize = " . $xminsize . ";\n";
    $content .= "\$maxsize = " . $xmaxsize . ";\n";
    $content .= "\$angle = " . $xangle . ";\n";
    $content .= "\n";
    $content .= "?>";
    fwrite($open, $content);
    fclose($open);
    @chmod("include/includes/func/captcha/settings.php", 0644);
}

$design = new design('Ilch Admin-Control-Panel :: Captcha', '', 2);
$design->header();

if (@!include("include/includes/func/captcha/settings.php")) {
    $imagewidth = 170;
    $imageheight = 50;
    $fontsize = 24;
    $bgintensity = 100;
    $bgfonttype = 3;
    $scratchamount = 100;
    $scratches = 0;
    $passphraselenght = 4;
    $addagrid = 1;
    $addhorizontallines = 0;
    $useRandomColors = 1;
    $minsize = 20;
    $maxsize = 30;
    $angle = 45;
}

$tpl = new tpl('captcha', 1);

$useRandomColorsja = ($useRandomColors == 1 ? 'checked' : '');
$useRandomColorsno = ($useRandomColors == 1 ? '' : 'checked');
$addagridja = ($addagrid == 1 ? 'checked' : '');
$addagridno = ($addagrid == 1 ? '' : 'checked');
$addhorizontallinesja = ($addhorizontallines == 1 ? 'checked' : '');
$addhorizontallinesno = ($addhorizontallines == 1 ? '' : 'checked');
$scratchesja = ($scratches == 1 ? 'checked' : '');
$scratchesno = ($scratches == 1 ? '' : 'checked');

$r = array(
    'imagewidth' => $imagewidth,
    'imageheight' => $imageheight,
    'fontsize' => $fontsize,
    'bgintensity' => $bgintensity,
    'bgfonttype' => $bgfonttype,
    'scratchamount' => $scratchamount,
    'scratches' => $scratches,
    'passphraselenght' => $passphraselenght,
    'minsize' => $minsize,
    'maxsize' => $maxsize,
    'angle' => $angle,
    'useRandomColorsja' => $useRandomColorsja,
    'useRandomColorsno' => $useRandomColorsno,
    'addagridja' => $addagridja,
    'addagridno' => $addagridno,
    'addhorizontallinesja' => $addhorizontallinesja,
    'addhorizontallinesno' => $addhorizontallinesno,
    'scratchesja' => $scratchesja,
    'scratchesno' => $scratchesno
    );
$tpl->set_ar_out($r, 0);
$design->footer();

?>
