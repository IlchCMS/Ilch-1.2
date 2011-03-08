<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

function png_create_transparent($width, $height) 
{
    $res = imagecreatetruecolor($width, $height);
    $transparency = imagecolorallocatealpha($res, 0, 0, 0, 127);
    imagealphablending($res, FALSE);
    imagefilledrectangle($res, 0, 0, $width, $height, $transparency);
    imagealphablending($res, TRUE);
    imagesavealpha($res, TRUE);
    return $res;
}

function gif_create_transparent($neuesBild, $altesBild)
{
    $transparencyIndex = imagecolortransparent($altesBild);
    $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
    if ($transparencyIndex >= 0) { $transparencyColor = imagecolorsforindex($altesBild, $transparencyIndex); }
    $transparencyIndex = imagecolorallocate($neuesBild, $transparencyColor[ 'red' ], $transparencyColor[ 'green' ], $transparencyColor[ 'blue' ]);
    imagefill($neuesBild, 0, 0, $transparencyIndex);
    imagecolortransparent($neuesBild, $transparencyIndex);
}

function create_avatar($imgpath, $thumbpath, $neueBreite, $neueHoehe)
{
    $size = getimagesize($imgpath);
    $breite = $size[ 0 ];
    $hoehe = $size[ 1 ];
    $RatioW = $neueBreite / $breite;
    $RatioH = $neueHoehe / $hoehe;
        
	if ($RatioW < $RatioH)
    {
      $neueBreite = $breite * $RatioW;
      $neueHoehe = $hoehe * $RatioW;
    } 
    else 
    {
      $neueBreite = $breite * $RatioH;
      $neueHoehe = $hoehe * $RatioH;
    }
      
	$neueBreite = round($neueBreite,0);
	$neueHoehe = round($neueHoehe,0);

    if (function_exists('gd_info')) 
	{
      $tmp = gd_info();
      $imgsup = ($tmp[ 'GIF Create Support' ] ? 1 : 2);
      unset($tmp);
    }
	else
    {
      $imgsup = 2;
	}

    if ($size[ 2 ] < $imgsup OR $size[ 2 ] > 3) { return (false); }

    if ($size[ 2 ] == 1)
	{
      $altesBild = imagecreatefromgif($imgpath);
    } 
	elseif ($size[ 2 ] == 2) 
	{
      $altesBild = imagecreatefromjpeg($imgpath);
    }
    elseif ($size[ 2 ] == 3)
	{
      $altesBild = imagecreatefrompng($imgpath);
    }
	
    if (function_exists('imagecreatetruecolor') AND $size[ 2 ] != 1)
	{
      $neuesBild = png_create_transparent($neueBreite, $neueHoehe);
      imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
    } 
	elseif (function_exists('imagecreatetruecolor') AND $size[ 2 ] == 1 )
	{
      $neuesBild = imageCreate($neueBreite, $neueHoehe);
      gif_create_transparent($neuesBild, $altesBild);
      imagecopyresampled($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
	}
	else
	{
      $neuesBild = imageCreate($neueBreite, $neueHoehe);
      imageCopyResized($neuesBild, $altesBild, 0, 0, 0, 0, $neueBreite, $neueHoehe, $breite, $hoehe);
    }
  
	if ($size[ 2 ] == 1)
	{
      ImageGIF($neuesBild, $thumbpath);
    }
	elseif ($size[ 2 ] == 2)
	{
      ImageJPEG($neuesBild, $thumbpath);
    }
	elseif ($size[ 2 ] == 3)
	{
      ImagePNG($neuesBild, $thumbpath);
    }
	
    return (true);
}

?>