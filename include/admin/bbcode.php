<?php
#   Copyright by Thomas Bowe [Funjoy]
#   Support bbcode@phpline.de
#   link www.phpline.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

switch($menu->get(1)) {
	#> Buttons
	case 'buttons':
	
		//> Design ausgeben!
		$design = new design ( 'Ilch Admin-Control-Panel :: BBcode-Buttons', '- Buttons', 2 );
		$design->header();
		
		$tpl = new tpl ( 'bbcode/buttons', 1);
		if(!isset($_POST['BB_SubmitButtons'])) {
			$sql = db_query("SELECT * FROM `prefix_bbcode_buttons` WHERE `fnButtonNr`='1'");

			$bool = db_fetch_assoc($sql);
			$tpl->set('Message',"");

			$Formate = array("fnFormatB" => "Selected_B_",
							 "fnFormatI" => "Selected_I_",
							 "fnFormatU" => "Selected_U_",
							 "fnFormatS" => "Selected_S_",
							 "fnFormatEmph" => "Selected_EMPH_",
							 "fnFormatColor" => "Selected_COLOR_",
							 "fnFormatSize" => "Selected_SIZE_",
							 "fnFormatEmail" => "Selected_MAIL_",
							 "fnFormatUrl" => "Selected_URL_",
							 "fnFormatUrlAuto" => "Selected_AUTO_URL_",
							 "fnFormatLeft" => "Selected_LEFT_",
							 "fnFormatCenter" => "Selected_CENTER_",
							 "fnFormatRight" => "Selected_RIGHT_",
							 "fnFormatSmilies" => "Selected_SMILIES_",
							 "fnFormatList" => "Selected_LIST_",
							 "fnFormatKtext" => "Selected_KTEXT_",
							 "fnFormatImg" => "Selected_IMG_",
							 "fnFormatScreen" => "Selected_SCREEN_",
							 "fnFormatVideo" => "Selected_VIDEO_",
							 "fnFormatCountdown" => "Selected_COUNTDOWN_",
							 "fnFormatPhp" => "Selected_PHP_",
							 "fnFormatCss" => "Selected_CSS_",
							 "fnFormatHtml" => "Selected_HTML_",
							 "fnFormatCode" => "Selected_CODE_",
							 "fnFormatQuote" => "Selected_QUOTE_",
                             "fnFormatFlash" => "Selected_FLASH_");

			foreach($Formate as $Attribut => $Zustand) {
				if($bool[$Attribut] == 1) {
					$tpl->set($Zustand.'On' ,"selected");
					$tpl->set($Zustand.'Off' ,"");
				} else {
					$tpl->set($Zustand.'On' ,"");
					$tpl->set($Zustand.'Off' ,"selected");
				}
			}
		} else {
			db_query("UPDATE
							`prefix_bbcode_buttons`
						SET
							`fnFormatB` = '".$_POST['BBCode_B']."',
							`fnFormatI` = '".$_POST['BBCode_I']."',
							`fnFormatU` = '".$_POST['BBCode_U']."',
							`fnFormatS` = '".$_POST['BBCode_S']."',
							`fnFormatEmph` = '".$_POST['BBCode_EMPH']."',
							`fnFormatColor` = '".$_POST['BBCode_COLOR']."',
							`fnFormatSize` = '".$_POST['BBCode_SIZE']."',
							`fnFormatUrl` = '".$_POST['BBCode_URL']."',
							`fnFormatUrlAuto` = '".$_POST['BBCode_AUTO_URL']."',
							`fnFormatEmail` = '".$_POST['BBCode_MAIL']."',
							`fnFormatLeft` = '".$_POST['BBCode_LEFT']."',
							`fnFormatCenter` = '".$_POST['BBCode_CENTER']."',
							`fnFormatRight` = '".$_POST['BBCode_RIGHT']."',
							`fnFormatSmilies` = '".$_POST['BBCode_SMILIES']."',
							`fnFormatList` = '".$_POST['BBCode_LIST']."',
							`fnFormatKtext` = '".$_POST['BBCode_KTEXT']."',
							`fnFormatImg` = '".$_POST['BBCode_IMG']."',
							`fnFormatScreen` = '".$_POST['BBCode_SCREEN']."',
							`fnFormatVideo` = '".$_POST['BBCode_VIDEO']."',
							`fnFormatCountdown` = '".$_POST['BBCode_COUNTDOWN']."',
							`fnFormatPhp` = '".$_POST['BBCode_PHP']."',
							`fnFormatCss` = '".$_POST['BBCode_CSS']."',
							`fnFormatHtml` = '".$_POST['BBCode_HTML']."',
							`fnFormatCode` = '".$_POST['BBCode_CODE']."',
							`fnFormatQuote` = '".$_POST['BBCode_QUOTE']."',
                            `fnFormatFlash` = '".$_POST['BBCode_FLASH']."'");

			$tpl->set('Message','Zustände wurden erfolgreich gespeichert!');

			$Formate = array("BBCode_B" => "Selected_B_",
							 "BBCode_I" => "Selected_I_",
							 "BBCode_U" => "Selected_U_",
							 "BBCode_S" => "Selected_S_",
							 "BBCode_EMPH" => "Selected_EMPH_",
							 "BBCode_COLOR" => "Selected_COLOR_",
							 "BBCode_SIZE" => "Selected_SIZE_",
							 "BBCode_URL" => "Selected_URL_",
							 "BBCode_AUTO_URL" => "Selected_AUTO_URL_",
							 "BBCode_MAIL" => "Selected_MAIL_",
							 "BBCode_LEFT" => "Selected_LEFT_",
							 "BBCode_CENTER" => "Selected_CENTER_",
							 "BBCode_RIGHT" => "Selected_RIGHT_",
							 "BBCode_SMILIES" => "Selected_SMILIES_",
							 "BBCode_LIST" => "Selected_LIST_",
							 "BBCode_KTEXT" => "Selected_KTEXT_",
							 "BBCode_IMG" => "Selected_IMG_",
							 "BBCode_SCREEN" => "Selected_SCREEN_",
							 "BBCode_VIDEO" => "Selected_VIDEO_",
							 "BBCode_COUNTDOWN" => "Selected_COUNTDOWN_",
							 "BBCode_PHP" => "Selected_PHP_",
							 "BBCode_CSS" => "Selected_CSS_",
							 "BBCode_HTML" => "Selected_HTML_",
							 "BBCode_CODE" => "Selected_CODE_",
							 "BBCode_QUOTE" => "Selected_QUOTE_",
                             "BBCode_FLASH" => "Selected_FLASH_");

			foreach($Formate as $Attribut => $Zustand) {
				if($_POST[$Attribut] == 1) {
					$tpl->set($Zustand.'On' ,"selected");
					$tpl->set($Zustand.'Off' ,"");
				} else {
					$tpl->set($Zustand.'On' ,"");
					$tpl->set($Zustand.'Off' ,"selected");
				}
			}
		}

		$tpl->out(0);
	break;

	#> Design
	case 'design':
	
		//> Design ausgeben!
		$design = new design ( 'Ilch Admin-Control-Panel :: BBcode-Design', '- Design', 2 );
		$design->header();
		
		$tpl = new tpl ( 'bbcode/design', 1);
		if(!isset($_POST['BB_SubmitDesign'])) {
			$sql = db_query("SELECT * FROM `prefix_bbcode_design` WHERE `fnDesignNr`='1'");

			$BB_Design = db_fetch_assoc($sql);
			$tpl->set('Message','');
			$tpl->set('NBSP','');

			//> Zuweisung Quotes/Zitate
			$tpl->set('BBCode_QuoteRandFarbe' ,$BB_Design['fcQuoteRandFarbe']);
			$tpl->set('BBCode_QuoteTabelleBreite' ,$BB_Design['fcQuoteTabelleBreite']);
			$tpl->set('BBCode_QuoteSchriftfarbe' ,$BB_Design['fcQuoteSchriftfarbe']);
			$tpl->set('BBCode_QuoteHintergrundfarbe' ,$BB_Design['fcQuoteHintergrundfarbe']);
			$tpl->set('BBCode_QuoteHintergrundfarbeIT' ,$BB_Design['fcQuoteHintergrundfarbeIT']);
			if(strtolower($BB_Design['fcQuoteSchriftformatIT']) == "bold") {
				$tpl->set('BBCode_Mode' ,"FONT-WEIGHT");
				$tpl->set('SelectedQuoteBold' ,"selected");
				$tpl->set('SelectedQuoteItalic' ,"");
				$tpl->set('SelectedQuoteNormal' ,"");
			} else if(strtolower($BB_Design['fcQuoteSchriftformatIT']) == "italic") {
				$tpl->set('BBCode_Mode' ,"FONT-STYLE");
				$tpl->set('SelectedQuoteBold' ,"");
				$tpl->set('SelectedQuoteItalic' ,"selected");
				$tpl->set('SelectedQuoteNormal' ,"");
			} else {
				$tpl->set('BBCode_Mode' ,"FONT-STYLE");
				$tpl->set('SelectedQuoteBold' ,"");
				$tpl->set('SelectedQuoteItalic' ,"");
				$tpl->set('SelectedQuoteNormal' ,"selected");
			}
			$tpl->set('BBCode_QuoteSchriftformatIT' ,$BB_Design['fcQuoteSchriftformatIT']);
			$tpl->set('BBCode_QuoteSchriftfarbeIT' ,$BB_Design['fcQuoteSchriftfarbeIT']);

			//> Zuweisung Code- Blöcke (PHP,CSS,HTML)
			$tpl->set('BBCode_BlockRandFarbe' ,$BB_Design['fcBlockRandFarbe']);
			$tpl->set('BBCode_BlockTabelleBreite' ,$BB_Design['fcBlockTabelleBreite']);
			$tpl->set('BBCode_BlockSchriftfarbe' ,$BB_Design['fcBlockSchriftfarbe']);
			$tpl->set('BBCode_BlockHintergrundfarbe' ,$BB_Design['fcBlockHintergrundfarbe']);
			$tpl->set('BBCode_BlockHintergrundfarbeIT' ,$BB_Design['fcBlockHintergrundfarbeIT']);
			$tpl->set('BBCode_BlockSchriftfarbeIT' ,$BB_Design['fcBlockSchriftfarbeIT']);

			//> Zuweisung Klapptext
			$tpl->set('BBCode_KtextRandFarbe' ,$BB_Design['fcKtextRandFarbe']);
			if(strtolower($BB_Design['fcKtextRandFormat']) == "dotted") {
				$tpl->set('SelectedKtextDotted' ,"selected");
				$tpl->set('SelectedKtextDashed' ,"");
				$tpl->set('SelectedKtextSolid' ,"");
			} else if(strtolower($BB_Design['fcKtextRandFormat']) == "dashed") {
				$tpl->set('SelectedKtextDotted' ,"");
				$tpl->set('SelectedKtextDashed' ,"selected");
				$tpl->set('SelectedKtextSolid' ,"");
			} else {
				$tpl->set('SelectedKtextDotted' ,"");
				$tpl->set('SelectedKtextDashed' ,"");
				$tpl->set('SelectedKtextSolid' ,"selected");
			}
			$tpl->set('BBCode_KtextTabelleBreite' ,$BB_Design['fcKtextTabelleBreite']);
			$tpl->set('BBCode_KtextRandFormat' ,$BB_Design['fcKtextRandFormat']);

			//> Zuweisung "Text hervorheben"
			$tpl->set('BBCode_EmphHintergrundfarbe' ,$BB_Design['fcEmphHintergrundfarbe']);
			$tpl->set('BBCode_EmphSchriftfarbe' ,$BB_Design['fcEmphSchriftfarbe']);

			//> Zuweisung Countdown
			$tpl->set('BBCode_CountdownRandFarbe' ,$BB_Design['fcCountdownRandFarbe']);
			$tpl->set('BBCode_CountdownTabelleBreite' ,$BB_Design['fcCountdownTabelleBreite']);
			$tpl->set('BBCode_CountdownSchriftfarbe' ,$BB_Design['fcCountdownSchriftfarbe']);
			$tpl->set('BBCode_CountdownSchriftsize' ,$BB_Design['fnCountdownSchriftsize']);
			if(strtolower($BB_Design['fcCountdownSchriftformat']) == "bold") {
				$tpl->set('BBCode_Mode_C' ,"FONT-WEIGHT");
				$tpl->set('SelectedCountdownBold' ,"selected");
				$tpl->set('SelectedCountdownItalic' ,"");
				$tpl->set('SelectedCountdownNormal' ,"");
			} else if(strtolower($BB_Design['fcCountdownSchriftformat']) == "italic") {
				$tpl->set('BBCode_Mode_C' ,"FONT-STYLE");
				$tpl->set('SelectedCountdownBold' ,"");
				$tpl->set('SelectedCountdownItalic' ,"selected");
				$tpl->set('SelectedCountdownNormal' ,"");
			} else {
				$tpl->set('BBCode_Mode_C' ,"FONT-STYLE");
				$tpl->set('SelectedCountdownBold' ,"");
				$tpl->set('SelectedCountdownItalic' ,"");
				$tpl->set('SelectedCountdownNormal' ,"selected");
			}
			$tpl->set('BBCode_CountdownSchriftformat' ,$BB_Design['fcCountdownSchriftformat']);
		} else {
			db_query("UPDATE
							`prefix_bbcode_design`
						SET
							`fcQuoteRandFarbe` = '".$_POST['BBCode_QuoteRandFarbe']."',
							`fcQuoteTabelleBreite` = '".$_POST['BBCode_QuoteTabelleBreite']."',
							`fcQuoteSchriftfarbe` = '".$_POST['BBCode_QuoteSchriftfarbe']."',
							`fcQuoteHintergrundfarbe` = '".$_POST['BBCode_QuoteHintergrundfarbe']."',
							`fcQuoteHintergrundfarbeIT` = '".$_POST['BBCode_QuoteHintergrundfarbeIT']."',
							`fcQuoteSchriftformatIT` = '".$_POST['BBCode_QuoteSchriftformatIT']."',
							`fcQuoteSchriftfarbeIT` = '".$_POST['BBCode_QuoteSchriftfarbeIT']."',
							`fcBlockRandFarbe` = '".$_POST['BBCode_BlockRandFarbe']."',
							`fcBlockTabelleBreite` = '".$_POST['BBCode_BlockTabelleBreite']."',
							`fcBlockSchriftfarbe` = '".$_POST['BBCode_BlockSchriftfarbe']."',
							`fcBlockHintergrundfarbe` = '".$_POST['BBCode_BlockHintergrundfarbe']."',
							`fcBlockHintergrundfarbeIT` = '".$_POST['BBCode_BlockHintergrundfarbeIT']."',
							`fcBlockSchriftfarbeIT` = '".$_POST['BBCode_BlockSchriftfarbeIT']."',
							`fcKtextRandFarbe` = '".$_POST['BBCode_KtextRandFarbe']."',
							`fcKtextTabelleBreite` = '".$_POST['BBCode_KtextTabelleBreite']."',
							`fcKtextRandFormat` = '".$_POST['BBCode_KtextRandFormat']."',
							`fcEmphHintergrundfarbe` = '".$_POST['BBCode_EmphHintergrundfarbe']."',
							`fcEmphSchriftfarbe` = '".$_POST['BBCode_EmphSchriftfarbe']."',
							`fcCountdownRandFarbe` = '".$_POST['BBCode_CountdownRandFarbe']."',
							`fcCountdownTabelleBreite` = '".$_POST['BBCode_CountdownTabelleBreite']."',
							`fcCountdownSchriftfarbe` = '".$_POST['BBCode_CountdownSchriftfarbe']."',
							`fnCountdownSchriftsize` = '".$_POST['BBCode_CountdownSchriftsize']."',
							`fcCountdownSchriftformat` = '".$_POST['BBCode_CountdownSchriftformat']."'");

			$tpl->set('Message','Design wurde erfolgreich gespeichert!');
			$tpl->set('NBSP','&nbsp;');

			//> Zuweisung Quotes/Zitate
			$tpl->set('BBCode_QuoteRandFarbe' ,$_POST['BBCode_QuoteRandFarbe']);
			$tpl->set('BBCode_QuoteTabelleBreite' ,$_POST['BBCode_QuoteTabelleBreite']);
			$tpl->set('BBCode_QuoteSchriftfarbe' ,$_POST['BBCode_QuoteSchriftfarbe']);
			$tpl->set('BBCode_QuoteHintergrundfarbe' ,$_POST['BBCode_QuoteHintergrundfarbe']);
			$tpl->set('BBCode_QuoteHintergrundfarbeIT' ,$_POST['BBCode_QuoteHintergrundfarbeIT']);
			if(strtolower($_POST['BBCode_QuoteSchriftformatIT']) == "bold") {
				$tpl->set('BBCode_Mode' ,"FONT-WEIGHT");
				$tpl->set('SelectedQuoteBold' ,"selected");
				$tpl->set('SelectedQuoteItalic' ,"");
				$tpl->set('SelectedQuoteNormal' ,"");
			} else if(strtolower($_POST['BBCode_QuoteSchriftformatIT']) == "italic") {
				$tpl->set('BBCode_Mode' ,"FONT-STYLE");
				$tpl->set('SelectedQuoteBold' ,"");
				$tpl->set('SelectedQuoteItalic' ,"selected");
				$tpl->set('SelectedQuoteNormal' ,"");
			} else {
				$tpl->set('BBCode_Mode' ,"FONT-STYLE");
				$tpl->set('SelectedQuoteBold' ,"");
				$tpl->set('SelectedQuoteItalic' ,"");
				$tpl->set('SelectedQuoteNormal' ,"selected");
			}
			$tpl->set('BBCode_QuoteSchriftformatIT' ,$_POST['BBCode_QuoteSchriftformatIT']);
			$tpl->set('BBCode_QuoteSchriftfarbeIT' ,$_POST['BBCode_QuoteSchriftfarbeIT']);

			//> Zuweisung Code- Blöcke (PHP,CSS,HTML)
			$tpl->set('BBCode_BlockRandFarbe' ,$_POST['BBCode_BlockRandFarbe']);
			$tpl->set('BBCode_BlockTabelleBreite' ,$_POST['BBCode_BlockTabelleBreite']);
			$tpl->set('BBCode_BlockSchriftfarbe' ,$_POST['BBCode_BlockSchriftfarbe']);
			$tpl->set('BBCode_BlockHintergrundfarbe' ,$_POST['BBCode_BlockHintergrundfarbe']);
			$tpl->set('BBCode_BlockHintergrundfarbeIT' ,$_POST['BBCode_BlockHintergrundfarbeIT']);
			$tpl->set('BBCode_BlockSchriftfarbeIT' ,$_POST['BBCode_BlockSchriftfarbeIT']);

			//> Zuweisung Klapptext
			$tpl->set('BBCode_KtextRandFarbe' ,$_POST['BBCode_KtextRandFarbe']);
			if(strtolower($_POST['BBCode_KtextRandFormat']) == "dotted") {
				$tpl->set('SelectedKtextDotted' ,"selected");
				$tpl->set('SelectedKtextDashed' ,"");
				$tpl->set('SelectedKtextSolid' ,"");
			} else if(strtolower($_POST['BBCode_KtextRandFormat']) == "dashed") {
				$tpl->set('SelectedKtextDotted' ,"");
				$tpl->set('SelectedKtextDashed' ,"selected");
				$tpl->set('SelectedKtextSolid' ,"");
			} else {
				$tpl->set('SelectedKtextDotted' ,"");
				$tpl->set('SelectedKtextDashed' ,"");
				$tpl->set('SelectedKtextSolid' ,"selected");
			}
			$tpl->set('BBCode_KtextTabelleBreite' ,$_POST['BBCode_KtextTabelleBreite']);
			$tpl->set('BBCode_KtextRandFormat' ,$_POST['BBCode_KtextRandFormat']);

			//> Zuweisung "Text hervorheben"
			$tpl->set('BBCode_EmphHintergrundfarbe' ,$_POST['BBCode_EmphHintergrundfarbe']);
			$tpl->set('BBCode_EmphSchriftfarbe' ,$_POST['BBCode_EmphSchriftfarbe']);

			//> Zuweisung Countdown
			$tpl->set('BBCode_CountdownRandFarbe' ,$_POST['BBCode_CountdownRandFarbe']);
			$tpl->set('BBCode_CountdownTabelleBreite' ,$_POST['BBCode_CountdownTabelleBreite']);
			$tpl->set('BBCode_CountdownSchriftfarbe' ,$_POST['BBCode_CountdownSchriftfarbe']);
			$tpl->set('BBCode_CountdownSchriftsize' ,$_POST['BBCode_CountdownSchriftsize']);
			if(strtolower($_POST['BBCode_CountdownSchriftformat']) == "bold") {
				$tpl->set('BBCode_Mode_C' ,"FONT-WEIGHT");
				$tpl->set('SelectedCountdownBold' ,"selected");
				$tpl->set('SelectedCountdownItalic' ,"");
				$tpl->set('SelectedCountdownNormal' ,"");
			} else if(strtolower($_POST['BBCode_CountdownSchriftformat']) == "italic") {
				$tpl->set('BBCode_Mode_C' ,"FONT-STYLE");
				$tpl->set('SelectedCountdownBold' ,"");
				$tpl->set('SelectedCountdownItalic' ,"selected");
				$tpl->set('SelectedCountdownNormal' ,"");
			} else {
				$tpl->set('BBCode_Mode_C' ,"FONT-STYLE");
				$tpl->set('SelectedCountdownBold' ,"");
				$tpl->set('SelectedCountdownItalic' ,"");
				$tpl->set('SelectedCountdownNormal' ,"selected");
			}
			$tpl->set('BBCode_CountdownSchriftformat' ,$_POST['BBCode_CountdownSchriftformat']);
		}

		$tpl->out(0);
	break;

	#> Konfiguration
	case 'config':
	
		//> Design ausgeben!
		$design = new design ( 'Ilch Admin-Control-Panel :: BBcode-Konfiguration', '- Konfiguration', 2 );
		$design->header();
	
		$tpl = new tpl ( 'bbcode/config', 1);
		if(!isset($_POST['BB_SubmitConfig'])) {
			$sql = db_query("SELECT * FROM `prefix_bbcode_config` WHERE `fnConfigNr`='1'");
			$BB_Config = db_fetch_assoc($sql);
			$tpl->set('Message','');
			//> Video "YouTube"
			$tpl->set('BBCode_YoutubeBreite' ,$BB_Config['fnYoutubeBreite']);
			$tpl->set('BBCode_YoutubeHoehe' ,$BB_Config['fnYoutubeHoehe']);
			$tpl->set('BBCode_YoutubeHintergrundfarbe' ,$BB_Config['fcYoutubeHintergrundfarbe']);

			//> Video "Google"
			$tpl->set('BBCode_GoogleBreite' ,$BB_Config['fnGoogleBreite']);
			$tpl->set('BBCode_GoogleHoehe' ,$BB_Config['fnGoogleHoehe']);
			$tpl->set('BBCode_GoogleHintergrundfarbe' ,$BB_Config['fcGoogleHintergrundfarbe']);

			//> Video "MyVideo"
			$tpl->set('BBCode_MyvideoBreite' ,$BB_Config['fnMyvideoBreite']);
			$tpl->set('BBCode_MyvideoHoehe' ,$BB_Config['fnMyvideoHoehe']);
			$tpl->set('BBCode_MyvideoHintergrundfarbe' ,$BB_Config['fcMyvideoHintergrundfarbe']);
			
			//> Flash
			$tpl->set('BBCode_FlashBreite' ,$BB_Config['fnFlashBreite']);
			$tpl->set('BBCode_FlashHoehe' ,$BB_Config['fnFlashHoehe']);
			$tpl->set('BBCode_FlashHintergrundfarbe' ,$BB_Config['fcFlashHintergrundfarbe']);

			//> Zeichenkette
			$tpl->set('BBCode_SizeMax' ,$BB_Config['fnSizeMax']);
			$tpl->set('BBCode_UrlMaxLaenge' ,$BB_Config['fnUrlMaxLaenge']);
			$tpl->set('BBCode_WortMaxLaenge' ,$BB_Config['fnWortMaxLaenge']);

			//> Grafik
			$tpl->set('BBCode_ImgMaxBreite' ,$BB_Config['fnImgMaxBreite']);
			$tpl->set('BBCode_ImgMaxHoehe' ,$BB_Config['fnImgMaxHoehe']);
			$tpl->set('BBCode_ScreenMaxBreite' ,$BB_Config['fnScreenMaxBreite']);
			$tpl->set('BBCode_ScreenMaxHoehe' ,$BB_Config['fnScreenMaxHoehe']);
		} else {
			db_query("UPDATE
							`prefix_bbcode_config`
					  SET
							`fnYoutubeBreite` = '".$_POST['BBCode_YoutubeBreite']."',
							`fnYoutubeHoehe` = '".$_POST['BBCode_YoutubeHoehe']."',
							`fcYoutubeHintergrundfarbe` = '".$_POST['BBCode_YoutubeHintergrundfarbe']."',
							`fnGoogleBreite` = '".$_POST['BBCode_GoogleBreite']."',
							`fnGoogleHoehe` = '".$_POST['BBCode_GoogleHoehe']."',
							`fcGoogleHintergrundfarbe` = '".$_POST['BBCode_GoogleHintergrundfarbe']."',
							`fnMyvideoBreite` = '".$_POST['BBCode_MyvideoBreite']."',
							`fnMyvideoHoehe` = '".$_POST['BBCode_MyvideoHoehe']."',
							`fcMyvideoHintergrundfarbe` = '".$_POST['BBCode_MyvideoHintergrundfarbe']."',
							`fnFlashBreite` = '".$_POST['BBCode_FlashBreite']."',
							`fnFlashHoehe` = '".$_POST['BBCode_FlashHoehe']."',
							`fcFlashHintergrundfarbe` = '".$_POST['BBCode_FlashHintergrundfarbe']."',
							`fnSizeMax` = '".$_POST['BBCode_SizeMax']."',
							`fnUrlMaxLaenge` = '".$_POST['BBCode_UrlMaxLaenge']."',
							`fnWortMaxLaenge` = '".$_POST['BBCode_WortMaxLaenge']."',
							`fnImgMaxBreite` = '".$_POST['BBCode_ImgMaxBreite']."',
							`fnImgMaxHoehe` = '".$_POST['BBCode_ImgMaxHoehe']."',
							`fnScreenMaxBreite` = '".$_POST['BBCode_ScreenMaxBreite']."',
							`fnScreenMaxHoehe` = '".$_POST['BBCode_ScreenMaxHoehe']."'");

			$tpl->set('Message','Konfiguration wurde erfolgreich gespeichert!');
			//> Video "YouTube"
			$tpl->set('BBCode_YoutubeBreite' ,$_POST['BBCode_YoutubeBreite']);
			$tpl->set('BBCode_YoutubeHoehe' ,$_POST['BBCode_YoutubeHoehe']);
			$tpl->set('BBCode_YoutubeHintergrundfarbe' ,$_POST['BBCode_YoutubeHintergrundfarbe']);

			//> Video "Google"
			$tpl->set('BBCode_GoogleBreite' ,$_POST['BBCode_GoogleBreite']);
			$tpl->set('BBCode_GoogleHoehe' ,$_POST['BBCode_GoogleHoehe']);
			$tpl->set('BBCode_GoogleHintergrundfarbe' ,$_POST['BBCode_GoogleHintergrundfarbe']);

			//> Video "MyVideo"
			$tpl->set('BBCode_MyvideoBreite' ,$_POST['BBCode_MyvideoBreite']);
			$tpl->set('BBCode_MyvideoHoehe' ,$_POST['BBCode_MyvideoHoehe']);
			$tpl->set('BBCode_MyvideoHintergrundfarbe' ,$_POST['BBCode_MyvideoHintergrundfarbe']);
			
			//> Flash
			$tpl->set('BBCode_FlashBreite' ,$_POST['BBCode_FlashBreite']);
			$tpl->set('BBCode_FlashHoehe' ,$_POST['BBCode_FlashHoehe']);
			$tpl->set('BBCode_FlashHintergrundfarbe' ,$_POST['BBCode_FlashHintergrundfarbe']);

			//> Zeichenkette
			$tpl->set('BBCode_SizeMax' ,$_POST['BBCode_SizeMax']);
			$tpl->set('BBCode_UrlMaxLaenge' ,$_POST['BBCode_UrlMaxLaenge']);
			$tpl->set('BBCode_WortMaxLaenge' ,$_POST['BBCode_WortMaxLaenge']);

			//> Grafik
			$tpl->set('BBCode_ImgMaxBreite' ,$_POST['BBCode_ImgMaxBreite']);
			$tpl->set('BBCode_ImgMaxHoehe' ,$_POST['BBCode_ImgMaxHoehe']);
			$tpl->set('BBCode_ScreenMaxBreite' ,$_POST['BBCode_ScreenMaxBreite']);
			$tpl->set('BBCode_ScreenMaxHoehe' ,$_POST['BBCode_ScreenMaxHoehe']);
		}
		$tpl->out(0);
	break;

	#> Badwordlist
	case 'badword':

		//> Design ausgeben!
		$design = new design ( 'Ilch Admin-Control-Panel :: BBcode-Badwords', '- Badwords', 2 );
		$design->header();
	
		$tpl = new tpl ( 'bbcode/badword', 1);
		$tpl->set('msgColor','#0033FF');
		$tpl->set('Message','');

		if(isset($_POST['BB_SubmitBadword']) && $_POST['BBCode_BadPatter'] != "" && $_POST['BBCode_BadReplace'] != "") {
			$sql = db_query("SELECT
								fcBadPatter,
								fcBadReplace
							 FROM
							 	prefix_bbcode_badword
							WHERE
								fcBadPatter='".$_POST['BBCode_BadPatter']."'");
			$if = db_fetch_assoc($sql);
			if(isset($if['fcBadPatter'])) {
				$tpl->set('msgColor','#FF0000');
				$tpl->set('Message','Badword existiert schon in der Datenbank!');
			} else {
				db_query("INSERT INTO
							prefix_bbcode_badword
								(fcBadPatter,fcBadReplace)
							VALUES
								('".$_POST['BBCode_BadPatter']."','".$_POST['BBCode_BadReplace']."');");

				$tpl->set('msgColor','#0033FF');
				$tpl->set('Message','Badword wurde erfolgreich gespeichert!');
			}
		}
		//> Badword Löschen!
		if($menu->get(2) == "delete") {
			db_query('DELETE FROM  `prefix_bbcode_badword` WHERE `fnBadwordNr` = "'.$menu->get(3).'"');
		}

		//> Ausgabe der Liste!
		$limit = 15;
  		$page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1 );
  		$MPL = db_make_sites ($page , '' , $limit , "?bbcode-badword" , 'bbcode_badword');
		$anfang = ($page - 1) * $limit;

		//> Seitenzahlen ausgeben!
		$tpl->set_ar_out( array ('MPL' => $MPL ) , 0);

		unset($sql);
		$sql = db_query("SELECT
							`fnBadwordNr`,
							`fcBadPatter`,
							`fcBadReplace`
						 FROM
							 `prefix_bbcode_badword`
						 ORDER BY
						 	`fnBadwordNr` DESC
						 LIMIT ".$anfang.",".$limit);
		$class = '';
		while ($row = db_fetch_object($sql) ) {
    		$class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
			$tpl->set_ar_out( array ( 'dbId' => $row->fnBadwordNr,
									  'CLASS' => $class,
									  'dbBadword' => $row->fcBadPatter,
									  'dbReplace' => $row->fcBadReplace) , 1);
		}

		//> Seitenzahlen ausgeben!
		$tpl->set_ar_out( array ('MPL' => $MPL ) , 2);

	break;
}

if ( !isset($design) ) {
	//> Design ausgeben!
	$design = new design ( 'Ilch Admin-Control-Panel :: BBcode', '- Bitte Menüpunkt wählen!', 2 );
	$design->header();
}

$design->footer();
?>