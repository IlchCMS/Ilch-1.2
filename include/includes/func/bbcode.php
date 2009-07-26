<?php
#   Copyright by Manuel Staechele
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );
//Klasse laden

$ILCH_HEADER_ADDITIONS .= "<script type=\"text/javascript\" src=\"include/includes/js/BBCodeGlobal.js\"></script>\n<script type=\"text/javascript\">\nvar bbcodemaximagewidth = {$info['ImgMaxBreite']};\nvar bbcodemaximageheight = {$info['ImgMaxHoehe']};\n</script>";

//Farbliste erstellen
function colorliste ( $ar ) {
  $l = '';
  foreach($ar as $k => $v) {
   $l .= '<td width="10" style="background-color: '.$k.';"><a href="#" onClick="javascript:bbcode_code_insert(\'color\',\''.$k.'\'); hide_color();"><img src="include/images/icons/bbcode/transparent.gif" border="0" height="10" width="10" alt="'.$v.'" title="'.$v.'"></td>';
  }
  return ($l);
}

function getBBCodeButtons(){
		//> Buttons Informationen.
		$ButtonSql = db_query("SELECT *	FROM prefix_bbcode_buttons WHERE fnButtonNr='1'");
		$boolButton = db_fetch_assoc($ButtonSql);

		$cfgBBCsql = db_query("SELECT * FROM prefix_bbcode_config WHERE fnConfigNr='1'");
		$cfgInfo = db_fetch_assoc($cfgBBCsql);
		
        $BBCodeButtons = '<script type="text/javascript" src="include/includes/js/interface.js"></script>';

		//> Fett Button!
		if($boolButton['fnFormatB'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('b','Gib hier den Text an der formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_bold.png\" alt=\"Fett formatieren\" title=\"Fett formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}


		//> Kursiv Button!
		if($boolButton['fnFormatI'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('i','Gib hier den Text an der formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_italic.png\" alt=\"Kursiv formatieren\" title=\"Kursiv formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Unterschrieben Button!
		if($boolButton['fnFormatU'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('u','Gib hier den Text an der formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_underline.png\" alt=\"Unterstrichen formatieren\" title=\"Unterstrichen formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Durchgestrichener Button!
		if($boolButton['fnFormatS'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('s','Gib hier den Text an der formatiert werden soll..')\"><img src=\"include/images/icons/bbcode/bbcode_strike.png\" alt=\"Durchgestrichen formatieren\" title=\"Durchgestrichen formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Leerzeichen?
		if($boolButton['fnFormatI'] == 1 || $boolButton['fnFormatU'] == 1 || $boolButton['fnFormatS'] == 1) {
			$BBCodeButtons .= "&nbsp;";
		}

		//> Links Button!
		if($boolButton['fnFormatLeft'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('left','0')\"><img src=\"include/images/icons/bbcode/bbcode_left.png\" alt=\"Links ausrichten\" title=\"Links ausrichten\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Zentriert Button!
		if($boolButton['fnFormatCenter'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('center','0')\"><img src=\"include/images/icons/bbcode/bbcode_center.png\" alt=\"Mittig ausrichten\" title=\"Mittig ausrichten\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Rechts Button!
		if($boolButton['fnFormatRight'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('right','0')\"><img src=\"include/images/icons/bbcode/bbcode_right.png\" alt=\"Rechts ausrichten\" title=\"Rechts ausrichten\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Leerzeichen?
		if($boolButton['fnFormatLeft'] == 1 || $boolButton['fnFormatCenter'] == 1 || $boolButton['fnFormatRight'] == 1) {
			$BBCodeButtons .= "&nbsp;";
		}

		//> Listen Button!
		if($boolButton['fnFormatList'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('list','Gib hier den Text ein der aufgelistet werden soll \\n Um die liste zu beenden einfach auf Abbrechen klicken.')\"><img src=\"include/images/icons/bbcode/bbcode_list.png\" alt=\"Liste erzeugen\" title=\"Liste erzeugen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Hervorheben Button!
		if($boolButton['fnFormatEmph'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('emph','0')\"><img src=\"include/images/icons/bbcode/bbcode_emph.png\" alt=\"Text hervorheben\" title=\"Text hervorheben\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Schriftfarbe Button!
        if($boolButton['fnFormatColor'] == 1) {
          $colorar = array('#FF0000' => 'red','#FFFF00' => 'yellow','#008000' => 'green','#00FF00' => 'lime','#008080' => 'teal','#808000' => 'olive','#0000FF' => 'blue','#00FFFF' => 'aqua', '#000080' => 'navy','#800080' => 'purple','#FF00FF' => 'fuchsia','#800000' => 'maroon','#C0C0C0' => 'grey','#808080' => 'silver','#000000' => 'black','#FFFFFF' => 'white',);
          $BBCodeButtons .= "<a href=\"javascript:hide_color();\"><img id=\"bbcode_color_button\" src=\"include/images/icons/bbcode/bbcode_color.png\" alt=\"Text f&auml;rben\" title=\"Text f&auml;rben\" width=\"23\" height=\"22\" border=\"0\"></a> ";
          $BBCodeButtons .= '<div style="display:none; position:absolute; top:0px; left:0px; width:200px; z-index:100;" id="colorinput">
          <table width="100%" class="border" border="0" cellspacing="1" cellpadding="0">
            <tr class="Chead" onclick="javascript:hide_color();"><td colspan="16"><b>Farbe wählen</b></td></tr>
            <tr class="Cmite" height="15">'.colorliste($colorar).'</tr></table>
          </div>';
        }

		//> Schriftgröße Button!
		if($boolButton['fnFormatSize'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('size','Gib hier den Text an der formatiert werden soll.','Gib hier die Gr&ouml;&szlig;e des textes in Pixel an. \\n Pixellimit liegt bei ".$cfgInfo['fnSizeMax']."px !!!')\"><img src=\"include/images/icons/bbcode/bbcode_size.png\" alt=\"Textgr&ouml;&szlig;e ver&auml;ndern\" title=\"Textgr&ouml;&szlig;e ver&auml;ndern\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Leerzeichen?
		if($boolButton['fnFormatList'] == 1 || $boolButton['fnFormatEmph'] == 1 || $boolButton['fnFormatColor'] == 1 || $boolButton['fnFormatSize'] == 1) {
			$BBCodeButtons .= "&nbsp;";
		}

		//> Url Button!
		if($boolButton['fnFormatUrl'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('url','Gib hier den namen des links an.','Gib hier die Adresse zu welcher verlinkt werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_url.png\" alt=\"Hyperlink einf&uuml;gen\" title=\"Hyperlink einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> E-Mail Button!
		if($boolButton['fnFormatEmail'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('mail','Gib hier den namen des links an.','Gib hier die eMail - Adresse an.')\"><img src=\"include/images/icons/bbcode/bbcode_email.png\" alt=\"eMail hinzuf&uuml;gen\" title=\"eMail hinzuf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Leerzeichen?
		if($boolButton['fnFormatUrl'] == 1 || $boolButton['fnFormatEmail'] == 1) {
			$BBCodeButtons .= "&nbsp;";
		}

		//> Bild Button!
		if($boolButton['fnFormatImg'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('img','Gib hier die Adresse des Bildes an.  \\n Die Breite und H&ouml;he des Bildes ist auf ".$cfgInfo['fnImgMaxBreite']."x".$cfgInfo['fnImgMaxHoehe']." eingeschränkt und würde verkleinert dargstellt werden.')\"><img src=\"include/images/icons/bbcode/bbcode_image.png\" alt=\"Bild einf&uuml;gen\" title=\"Bild einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Screenshot Button!
		if($boolButton['fnFormatScreen'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('shot','Gib hier die Adresse des Screens an.  \\n Die Breite und H&ouml;he des Bildes ist auf ".$cfgInfo['fnScreenMaxBreite']."x".$cfgInfo['fnScreenMaxHoehe']." eingeschränkt und wird verkleinert dargstellt.')\"><img src=\"include/images/icons/bbcode/bbcode_screenshot.png\" alt=\"Bild einf&uuml;gen\" title=\"Screen einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Leerzeichen?
		if($boolButton['fnFormatImg'] == 1 || $boolButton['fnFormatScreen'] == 1) {
			$BBCodeButtons .= "&nbsp;";
		}

		//> Quote Button!
		if($boolButton['fnFormatQuote'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('quote','0')\"><img src=\"include/images/icons/bbcode/bbcode_quote.png\" alt=\"Zitat einf&uuml;gen\" title=\"Zitat einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Klapptext Button!
		if($boolButton['fnFormatKtext'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('ktext','Gib hier den zu verbergenden Text ein.','Gib hier einen Titel f&uuml;r den Klapptext an.')\"><img src=\"include/images/icons/bbcode/bbcode_ktext.png\" alt=\"Klappfunktion hinzuf&uuml;gen\" title=\"Klappfunktion hinzuf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Video Button!
		if($boolButton['fnFormatVideo'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value_2('video','Gib hier die Video ID vom Anbieter an.','Bitte Anbieter ausw&auml;hlen.\\nAkzeptiert werden: Google, YouTube, MyVideo und GameTrailers')\"><img src=\"include/images/icons/bbcode/bbcode_video.png\" alt=\"Video einf&uuml;gen\" title=\"Video einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}
		
		//> Flash Button!
		if($boolButton['fnFormatFlash'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert('flash','Gib hier den Link zur Flashdatei an')\"><img src=\"include/images/icons/bbcode/bbcode_flash.png\" alt=\"Flash einf&uuml;gen\" title=\"Flash einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Countdown Button!
		if($boolButton['fnFormatCountdown'] == 1) {
			$BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('countdown','Gib hier das Datum an wann das Ereignis beginnt.\\n Format: TT.MM.JJJJ Bsp: 24.12.".date("Y")."','Gib hier eine Zeit an, wann das Ergeinis am Ereignis- Tag beginnt.\\nFormat: Std:Min:Sek Bsp: 20:15:00')\"><img src=\"include/images/icons/bbcode/bbcode_countdown.png\" alt=\"Countdown festlegen\" title=\"Countdown festlegen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
		}

		//> Leerzeichen?
		if($boolButton['fnFormatQuote'] == 1|| $boolButton['fnFormatKtext'] == 1 || $boolButton['fnFormatVideo'] == 1) {
			$BBCodeButtons .= "&nbsp;";
		}

		//> Code Dropdown!
    if($boolButton['fnFormatCode'] == 1 || $boolButton['fnFormatPhp'] == 1 || $boolButton['fnFormatHtml'] == 1 || $boolButton['fnFormatCss'] == 1) {
      $BBCodeButtons .= "<select onChange=\"javascript:bbcode_code_insert_codes(this.value); javascript:this.value='0';\" style=\"font-family:Verdana;font-size:10px; margin-bottom:6px; z-index:0;\" name=\"code\"><option value=\"0\">Code einf&uuml;gen</option>";
    }


    if($boolButton['fnFormatPhp'] == 1) {
      $BBCodeButtons .= "<option value=\"php\">PHP</option>";
    }

    if($boolButton['fnFormatHtml'] == 1) {
      $BBCodeButtons .= "<option value=\"html\">HTML</option>";
    }

    if($boolButton['fnFormatCss'] == 1) {
      $BBCodeButtons .= "<option value=\"css\">CSS</option>";
    }

    if($boolButton['fnFormatCode'] == 1) {
      $BBCodeButtons .= "<option value=\"code\">Sonstiger Code</option>";
    }

		if($boolButton['fnFormatCode'] == 1 || $boolButton['fnFormatPhp'] == 1 || $boolButton['fnFormatHtml'] == 1 || $boolButton['fnFormatCss'] == 1) {
			$BBCodeButtons .= "</select>";
		}
    
    return $BBCodeButtons;
}

function BBcode($s,$maxLength=0,$maxImgWidth=0,$maxImgHeight=0) {
  global $permitted,$info,$global_smiles_array;
  
  //> Smilies in array abspeichern.
	if(!isset($global_smiles_array)) {
		$erg = db_query("SELECT ent, url, emo FROM `prefix_smilies`");
		while ($row = db_fetch_object($erg) ) {
			$global_smiles_array[$row->ent] = $row->emo.'#@#-_-_-#@#'.$row->url;
		}
	}

	$bbcode = new bbcode();
	$bbcode->smileys = $global_smiles_array;
	$bbcode->permitted = $permitted;
	$bbcode->info = $info;

  if ($maxLength != 0) {
    $bbcode->info['fnWortMaxLaenge'] = $maxLength;
  }
  if ($maxImgWidth != 0) {
    $bbcode->info['fnImgMaxBreite'] = $maxImgWidth;
  }
  if ($maxImgHeight != 0) {
    $bbcode->info['fnImgMaxBreite'] = $maxImgHeight;
  }

	return $bbcode->parse($s);
}
?>