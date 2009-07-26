<?php
#   Copyright by Thomas Bowe [Funjoy]
#   Support bbcode@phpline.de
#   link www.phpline.de

/* Module - Information
* -------------------------------------------------------
* Hier könnt ihr eure Module includieren lassen.
* Wenn Ihr selber Module zum Highlight programmiert
* denkt daran das ihr auch noch das Parsen hier definieren müsst.
* und in der bbcode_config.php Datei müsstet ihr die Option auch noch einstellen.
* um ein Beispiel zu haben schaut euch die Funktion _htmlblock() am besten mal an.
* und in Zeile 308 und Zeile 490 habt ihr ein Beispiel wie ihr die Parsebefehle schreiben könnt.
*/

//> Bitte denkt daran das, dass Modul html.php immer unter dem Modul css.php sein muss.
//> Modul [css.php]
	if(file_exists("include/includes/class/highlight/css.php")) {
		require_once("include/includes/class/highlight/css.php");
	}

//> Modul [html.php]
	if(file_exists("include/includes/class/highlight/html.php")) {
		require_once("include/includes/class/highlight/html.php");
	}


class bbcode {
	//> Tags die geparsed werden dürfen.
	var $permitted = array();

	//> Verschlüsselte codeblocks.
	var $codecblocks = array();

	//> Badwords!
	var $badwords = array();

	//> Informationen für die Klasse!
	var $info = array();

	//> Patter befehle!
	var $pattern = array();

	//> Replace strings!
	var $replace = array();

	//> Smilies die in Grafik umgewandelt werden sollen.
	var $smileys = array();

	//> Cache für Quotes Header!
	var $ayCacheQuoteOpen = array();

	//> Cache fürQuotes Footer!
	var $ayCacheQuoteClose = array();

	//> Cache für Quotes Header!
	var $ayCacheKtextOpen = array();

	//> Cache fürQuotes Footer!
	var $ayCacheKtextClose = array();

	//> Codeblock verschlüsseln und wieder ausgeben.
	function encode_codec($string,$tag,$file=NULL) {
		$file = ($file == NULL) ? "":"=".$file;
		$crypt = md5(count($this->codecblocks));
		$this->codecblocks[$crypt.":".$tag] = $string;
		return "[".$tag.$file."]".$crypt."[/".$tag."]";
	}

	//> Codeblock entschlüsseln und parsen!
	function _codeblock($codecid,$file=NULL,$firstline=1) {
		$string = $this->codecblocks[$codecid.':code'];
		$string = htmlentities($string);

		$string = str_replace("\t", '&nbsp; &nbsp;', $string);
		$string = str_replace('  ', '&nbsp; ', $string);
		$string = str_replace('  ', ' &nbsp;', $string);
		$string = nl2br($string);

        return $this->_addcodecontainer(stripslashes($string), 'Code', $file, $firstline);
	}

	//> htmlblock entschlüsseln und parsen!
	function _htmlblock($codecid,$file=NULL,$firstline=1) {
		$string = $this->codecblocks[$codecid.':html'];
		$string = htmlentities($string);

		//> Highlight Modul Funktion checken ob sie existerit.
		if(function_exists("highlight_html")) {
			$string = highlight_html($string,$this->info['BlockCodeFarbe']);
		}

		$string = str_replace("\t", '&nbsp; &nbsp;', $string);
		$string = str_replace('  ', '&nbsp; ', $string);
		$string = str_replace('  ', ' &nbsp;', $string);
		$string = nl2br($string);

        return $this->_addcodecontainer($string, 'HTML', $file, $firstline);
	}

	//> cssblock entschlüsseln und parsen!
	function _cssblock($codecid,$file=NULL,$firstline=1) {
		$string = $this->codecblocks[$codecid.':css'];
		$string = htmlentities($string);

		//> Highlight Modul Funktion checken ob sie existerit.
		if(function_exists("highlight_css")) {
			$string = highlight_css($string);
		}

		$string = str_replace("\t", '&nbsp; &nbsp;', $string);
		$string = str_replace('  ', '&nbsp; ', $string);
		$string = str_replace('  ', ' &nbsp;', $string);
		$string = nl2br($string);

		return $this->_addcodecontainer($string, 'CSS', $file, $firstline);
	}

	//> phpblock entschlüsseln und parsen!
	function _phpblock($codecid,$file=NULL,$firstline=1) {
		$string = $this->codecblocks[$codecid.':php'];

		ob_start();
   		highlight_string(trim(stripslashes($string)));
    	$php = ob_get_contents();
    	ob_end_clean();

		return $this->_addcodecontainer($php, 'Php', $file, $firstline);
	}

    function _addcodecontainer($code, $type, $file=null, $firstline=1) {
        //> Datei pfad mit angegeben?
		$file = ($file == NULL) ? "":" von Datei <em>".$this->_shortwords($file)."</em>";

		//> Zeilen zählen.
		$lines = explode("\n",$code);
		$linescount = count($lines) + $firstline;
        if ($type == 'Php') {
            $linescount--;
        }
		for($no=$firstline;$no < $linescount;$no++) {
			$line .= "".$no.":<br />";
		}
		
		//> Hier könnt ihr den Header und Footer für HTML editieren.
		$breite = trim($this->info['BlockTabelleBreite']);
		$breite = (strpos($breite, '%') !== false) ? '450px' : $breite.'px';
		$header = "<div style=\"overflow: auto; width: {$breite};\">"
				 ."<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"BORDER: 1px SOLID ".$this->info['BlockRandFarbe'].";\" width=\"100%\">"
				 ."<tr><td colspan=\"3\" style=\"font-family:Arial, Helvetica, sans-serif;font-size:12px; font-weight:bold; color:".$this->info['BlockSchriftfarbe'].";background-color:".$this->info['BlockHintergrundfarbe'].";\">&nbsp;".$type.$file."</td></tr>"
				 ."<tr bgcolor=\"".$this->info['BlockHintergrundfarbeIT']."\"><td style=\"width:20px; color:".$this->info['BlockSchriftfarbe'].";padding-left:2px;padding-right:2px;border-right:1px solid ".$this->info['BlockHintergrundfarbe'].";font-family:Arial, Helvetica, sans-serif;\" align=\"right\" valign=\"top\"><code style=\"width:20px;\">"
				 .$line
				 ."</code></td><td width=\"5\">&nbsp;</td><td valign=\"top\" style=\"background-color:".$this->info['block_contentbgcolor']."; color:".$this->info['BlockSchriftfarbe'].";\" nowrap width=\"95%\"><code>";
		$footer = "</code></td></tr></table></div>";
		
		return $header.$code.$footer;
    }

	//> Smilies aus dem Array auslesen.
	function _smileys($string) {
		if(!is_null($this->smileys) && is_array($this->smileys)) {
			if($this->permitted['smileys'] == true) {
				foreach ($this->smileys as $icon => $info) {
					list($emo, $url) = explode('#@#-_-_-#@#', $info);
					$string = str_replace($icon,'<img src="include/images/smiles/'.$url.'" border="0" alt="'.$emo.'" title="'.$emo.'" />',$string);
				}
			}
			return $string;
		} else {
			return $string;
		}
	}

	//> Badwords Filtern.
	function _badwords($string) {
		//> Badwords aus der Datenbank laden!
		$cfgBBCodeSql = db_query("SELECT
									fcBadPatter,
									fcBadReplace
								  FROM
									prefix_bbcode_badword");

		while ($row = db_fetch_object($cfgBBCodeSql) ) {
			$pattern[] = "%".addcslashes($row->fcBadPatter,"[]{}%/$^()+.*\"\\")."%iU";
			$replace[] = $row->fcBadReplace;
		}
		if(isset($pattern)) {
			$string = preg_replace($pattern,$replace,$string);
		}

		return $string;
	}

	//> Liste formatieren.
	function _list($codecid) {
		$string = $this->codecblocks[$codecid.':list'];
		$array = explode("[*]",$string);
		for($no=1;$no<=(count($array)-1);$no++) {
			$li .= "<li>".$this->parse($array[$no])."</li>";
		}

		return "<ul>".$li."</ul>";
	}

	//> Auf Maximale Schriftgröße überprüfen.
	function _size($size,$string) {
		$max = $this->info['SizeMax'];
		if($size <= $max) {
			$fontsize = "<span style=\"font-size:".$size."px\">$string</span>";
		} else {
			$fontsize = "<span style=\"font-size:".$max."px\">$string</span>";
		}

		return $fontsize;
	}

  //> Bilder auf Verkleinern via Javascript überprüfen + Lightbox im 2. Forum
  function _img($string,$float='') {
    global $menu;
    $lightbox = $menu->get(0) == 'forum2' ? 'rel="lightbox"' : '';
    if ($float == 'none' OR $float == 'left' OR $float == 'right') {
      $float = 'style="float:'.$float.'; margin: 5px;" ';
    } else {
      $float = '';
    }
    $image = '<img src="'.$string.'" alt="" title="" border="0" class="bbcode_image" '.$lightbox.' '.$float.'/>';
    return $image;
  }

	//> Screenshots darstellen.
	function _screenshot($string,$float='none') {
	  if ($float == 'none' OR $float == 'left' OR $float == 'right') {
      $float = 'style="float:'.$float.'; margin: 5px;" ';
    } else {
      $float = '';
    }
    $image = '<a href="'.$string.'" target="_blank"><img src="'.$string.'" alt="" title="" border="0" width="'.$this->info['ScreenMaxBreite'].'" height="'.$this->info['ScreenMaxHoehe'].'" '.$float.'/></a>';
    return $image;
	}

	//> Urls Filtern um XSS vorzubeugen
	function _filterurl($url) {
    str_replace(
      array('<','>','(',')','#'),
      array('&lt;','&gt;','&#40;','&#41;','&#35'),
      $url
    );
    return $url;
  }

	//> Links darstellen und ggf. kürzen
	function _shorturl($string,$caption=null) {
		if ($caption == null) { $caption = $string; }
    $string = trim($this->_filterurl($string));
    $caption = trim($this->_filterurl($caption));
    $server = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
    if (preg_match('%^((http|ftp|https)://)|^/%i',$string) == 0) { $string = 'http://'.$string; }
    if (substr($string,0,1) == '/' OR strpos($string,$server) !== false) {
      $target = '_self';
    } else {
      $target = '_blank';
    }

		$count = strlen($caption);
    if($count >= $this->info['UrlMaxLaenge']) {
			$string = "<a href=\"".$string."\" target=\"".$target."\">".$this->_shortcaptions($caption)."</a>";
		} else {
			$string = "<a href=\"".$string."\" target=\"".$target."\">".$caption."</a>";
		}
		return $string;
	}

	//> Linkbeschreibung kürzen
	function _shortcaptions($string) {
		$words = explode(" ",$string);
		foreach($words as $word)
      if(strlen($word) > $this->info['WortMaxLaenge'] && !preg_match('%(\[img\](.*)\[/img\])%i',$word)) {
				$maxd2 = sprintf("%00d",($this->info['WortMaxLaenge']/2));
				$string = str_replace($word,substr($word,0,$maxd2)."...".substr($word,-$maxd2),$string);
			}
		return $string;
	}

	//> Hilfsfunktion für _shortwords
	function _checkpatterns($patterns, $word) {
        if (!is_array($patterns)) {
            return true;
        }
        foreach ($patterns as $p) {
            if (preg_match($p, $word) == 1) {
                return false;
            }
        }
        return true;
    }

	//> Zu lange Wörter kürzen.
	function _shortwords($string) {
		//> Zeichenkette in einzelne Array elemente zerlegen.
		$lines = explode("\n",$string);

		//> Patter Befehle die nicht gekürzt werden dürfen !!!
		$pattern = array("%^(www)(.[-a-zA-Z0-9@:;\%_\+.~#?&//=]+?)%i",
						 "%^(http|https|ftp)://{1}[-a-zA-Z0-9@:;\%_\+.~#?&//=]+?%i",
						 "%(\[(url|img(=(left|right))?|shot(=(left|right))?)\](.*)\[/(url|img|shot)\])|(\[url=(.*)\])%i",
                         "%\[(code|html|css|php|countdown)(=[^]]+)].*\[/(code|html|css|php|countdown)]%i",
                         "%\[flash]((http|https|ftp)://[a-z-0-9@:\%_\+.~#\?&/=,;]+)\[/flash]%i",
                         "%\[list].*\[/list]%");

		foreach($lines as $line) {
			$words = explode(" ",$line);
			foreach($words as $word)
        if(strlen($word) > $this->info['WortMaxLaenge'] && $this->_checkpatterns($pattern, $word)) {
					$maxd2 = sprintf("%00d",($this->info['WortMaxLaenge']/2));
					$string = str_replace($word,substr($word,0,$maxd2)."...".substr($word,-$maxd2),$string);
				}
    }
		return $string;
	}

	//> Geöffnete Ktext- Tags Nummerieren.
	function _addKtextOpen($Titel=Null) {
		$this->ayCacheKtextOpen[count($this->ayCacheKtextOpen)+1] = true;
		$intCountKtext = count($this->ayCacheKtextOpen);

		$string = "[ktext:".$intCountKtext."=".$Titel."]";

		return $string;
	}

	//> Geschlossene Ktext- Tags Nummerieren.
	function _addKtextClose() {
		$this->ayCacheKtextClose[count($this->ayCacheKtextClose)+1] = true;
		$intCountKtext = count($this->ayCacheKtextClose);

		return "[/ktext:".$intCountKtext."]";
	}

	//> Ktext- Tags umwandeln..
	function _ktext($string) {
		$Random = rand(1,10000000);

		//> Html- Muster für geöffnete Tags mit Titel.
		$HeaderTitel = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$this->info['KtextTabelleBreite']."\" align=\"center\">"
					  ."<tr><td><a href=\"javascript:Klapptext('__ID__')\">"
					  ."<img src=\"include/images/icons/plus.gif\" id=\"image___ID__\" border=0 alt=\"Aus/Ein-klappen\" title=\"Aus/Ein-klappen\"> ";

		$FooterTitel = "</a></td></tr>"
					  ."<tr><td><div id=\"layer___ID__\" style=\"display:none;border:1px ".$this->info['KtextRandFormat']." ".$this->info['KtextRandFarbe'].";\">";

		//> Html- Muster für geschlossene Tags.
		$KtextClose = "</div></td></tr></table>\n";

		//> Geöffnete Tags umwandeln.
		for($c=1;$c <= count($this->ayCacheKtextOpen);$c++) {
			if(count($this->ayCacheKtextClose) == count($this->ayCacheKtextOpen)) {
				//> Format: [ktext=xxx]
				$this->ktext_pattern[] = "%\[ktext:".$c."=([^]]*)\]%siU";
				$this->ktext_replace[] = str_replace("__ID__",$c."@".$Random,$HeaderTitel)."\$1".str_replace("__ID__",$c."@".$Random,$FooterTitel);
				//> Format: [/ktext]
				$this->ktext_pattern[] = "%\[/ktext:".$c."\]%siU";
				$this->ktext_replace[] = $KtextClose;
			} else {
				//> Format: [ktext=xxx]xxx[/ktext]
				$this->ktext_pattern[] = "%\[ktext:([0-9]*)=([^]](.*)\[/ktext:([0-9]*)\]%siU";
				$this->ktext_replace[] = str_replace("__ID__","\$1@".$Random,$HeaderTitel)."\$2".str_replace("__ID__","\$1@".$Random,$FooterTitel)."\$3".$KtextClose;
			}
		}

		//> Nicht gefundene Paare wieder darstellen.
		//> Format: [ktext=xxx]
		$this->ktext_pattern[] = "%\[ktext:([0-9]*)=([^[/]*)\]%siU";
		$this->ktext_replace[] = "[ktext=\$1]";

		//> Format: [/ktext]
		$this->ktext_pattern[] = "%\[/ktext:([0-9]*)\]%siU";
		$this->ktext_replace[] = "[/ktext]";

		//> String parsen
		$string = preg_replace($this->ktext_pattern,$this->ktext_replace,$string);


		return $string;
	}

	//> Geöffnete Quote- Tags Nummerieren.
	function _addQuoteOpen($User=Null) {
		$this->ayCacheQuoteOpen[count($this->ayCacheQuoteOpen)+1] = $User;
		$intCountQuote = count($this->ayCacheQuoteOpen);

		if($User != NULL) {
			$string = "[quote:".$intCountQuote."=".$User."]";
		} else {
			$string = "[quote:".$intCountQuote."]";
		}

		return $string;
	}

	//> Geschlossene Quote- Tags Nummerieren.
	function _addQuoteClose() {
		$this->ayCacheQuoteClose[count($this->ayCacheQuoteClose)+1] = true;
		$intCountQuote = count($this->ayCacheQuoteClose);

		return "[/quote:".$intCountQuote."]";
	}


	//> Quote- Tags umwandeln.
	function _quote($string) {
		//> überprüfen ob Bod gesetzt ist.
		if(strtolower($this->info['QuoteSchriftformatIT']) == "bold") {
			$Schriftformat = "font-weight:bold;";
		} else {
			$Schriftformat = "font-style:".$this->info['QuoteSchriftformatIT'].";";
		}

		//> Html- Muster für geöffnete Quote- Tags.
		$Header = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"BORDER: 1px SOLID ".$this->info['QuoteRandFarbe'].";\" width=\"".$this->info['QuoteTabelleBreite']."\" align=\"center\">"
				 ."<tr><td style=\"font-family:Arial, Helvetica, sans-serif;FONT-SIZE:13px;FONT-WEIGHT:BOLD;COLOR:".$this->info['QuoteSchriftfarbe'].";BACKGROUND-COLOR:".$this->info['QuoteHintergrundfarbe'].";\">&nbsp;Zitat</td></tr>"
				 ."<tr bgcolor=\"".$this->info['QuoteHintergrundfarbeIT']."\"><td><table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\"><tr><td style=\"".$Schriftformat."FONT-SIZE:10px;COLOR:".$this->info['QuoteSchriftfarbeIT'].";\">";

		//> Html- Muster für geöffnete Quote- Tags mit User.
		$HeaderUser = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"BORDER: 1px SOLID ".$this->info['QuoteRandFarbe'].";\" width=\"".$this->info['QuoteTabelleBreite']."\" align=\"center\">"
					  	 ."<tr><td style=\"font-family:Arial, Helvetica, sans-serif;FONT-SIZE:13px;FONT-WEIGHT:BOLD;COLOR:".$this->info['QuoteSchriftfarbe'].";BACKGROUND-COLOR:".$this->info['QuoteHintergrundfarbe'].";\">&nbsp;Zitat von ";

		$FooterUser = "</td></tr><tr bgcolor=\"".$this->info['QuoteHintergrundfarbeIT']."\"><td><table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\"><tr><td style=\"".$Schriftformat."FONT-SIZE:10px;COLOR:".$this->info['QuoteSchriftfarbeIT'].";\">";

		//> Html- Muster für geschlossene Quote- Tags.
		$QuoteClose = "</td></tr></table></td></tr></table>";

		$string = stripslashes($string);

		//> Geöffnete Tags umwandeln.
		for($c=1;$c <= count($this->ayCacheQuoteOpen);$c++) {
			if(count($this->ayCacheQuoteClose) == count($this->ayCacheQuoteOpen)) {
				//> Format: [quote=xxx]
				$this->quote_pattern[] = "%\[quote:".$c."=([^[/]*)\]%siU";
				$this->quote_replace[] = $HeaderUser."\$1".$FooterUser;

				//> Format: [quote]
				$this->quote_pattern[] = "%\[quote:".$c."\]%siU";
				$this->quote_replace[] = $Header;

				//> Format: [/quote]
				$this->quote_pattern[] = "%\[/quote:".$c."\]%siU";
				$this->quote_replace[] = $QuoteClose;
			} else {
				//> Format: [quote=xxx]xxx[/quote]
				$this->quote_pattern[] = "%\[quote:([0-9]*)=([^[/]*)\[/quote:([0-9]*)\]%siU";
				$this->quote_replace[] = $HeaderUser."\$2".$FooterUser."\$3".$QuoteClose;

				//> Format: [quote]xxx[/quote]
				$this->quote_pattern[] = "%\[quote:([0-9]*)\](.*)\[/quote:\\1\]%siU";
				$this->quote_replace[] = $Header."\$2".$QuoteClose;
			}
		}

		//> Nicht gefundene Paare wieder darstellen.
		//> Format: [quote=xxx]
		$this->quote_pattern[] = "%\[quote:([0-9]*)=([^[/]*)\]%siU";
		$this->quote_replace[] = "[quote=\$2]";

		//> Format: [quote]
		$this->quote_pattern[] = "%\[quote:([0-9])\]%siU";
		$this->quote_replace[] = "[quote]";

		//> Format: [/quote]
		$this->quote_pattern[] = "%\[/quote:([0-9])\]%siU";
		$this->quote_replace[] = "[/quote]";

		//> String parsen
		$string = preg_replace($this->quote_pattern,$this->quote_replace,$string);


		return $string;
	}

	//> Video intergration.
	function _video($typ,$id) {
		$typ = strtolower($typ);

		if($typ == "google") {
			$str = "<embed style=\"width:".$this->info['GoogleBreite']."px; height:".$this->info['GoogleHoehe']."px;\" id=\"VideoPlayback\" align=\"middle\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=".$id."\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"".$this->info['GoogleHintergrundfarbe']."\" scale=\"noScale\" salign=\"TL\" FlashVars=\"playerMode=embedded\"/>";
		}

		if($typ == "youtube") {
			$str = "<object width=\"".$this->info['YoutubeBreite']."\" height=\"".$this->info['YoutubeHoehe']."\"><param name=\"movie\" value=\"http://www.youtube.com/v/".$id."\"></param><embed src=\"http://www.youtube.com/v/".$id."\" type=\"application/x-shockwave-flash\"  width=\"".$this->info['YoutubeBreite']."\" height=\"".$this->info['YoutubeHoehe']."\" bgcolor=\"".$this->info['YoutubeHintergrundfarbe']."\"></embed></object>";
		}

		if($typ == "myvideo") {
			$str = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"".$this->info['MyvideoBreite']."\" height=\"".$this->info['MyvideoHoehe']."\"><param name=\"movie\" value=\"http://www.myvideo.de/movie/".$id."\"></param><embed src=\"http://www.myvideo.de/movie/".$id."\" width=\"".$this->info['MyvideoBreite']."\" height=\"".$this->info['MyvideoHoehe']."\" type=\"application/x-shockwave-flash\"></embed></object>";
		}

		if($typ == "gametrailers") {
      $str = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"  codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="gtembed" width="'.$this->info['YoutubeBreite'].'" height="'.$this->info['YoutubeHoehe'].'">	<param name="allowScriptAccess" value="sameDomain" /> 	<param name="allowFullScreen" value="true" /> <param name="movie" value="http://www.gametrailers.com/remote_wrap.php?mid='.$id.'"/> <param name="quality" value="high" /> <embed src="http://www.gametrailers.com/remote_wrap.php?mid='.$id.'" swLiveConnect="true" name="gtembed" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$this->info['YoutubeBreite'].'" height="'.$this->info['YoutubeHoehe'].'"></embed> </object>';
    }

		return $str;
	}

	//> Countdown berechnen.
	function _countdown($date,$time=NULL) {
		$date = explode(".",$date);

		if ($time != NULL) {
      $timechk = explode(':',$time);
      if ($timechk[0] <= 23 && $timechk[1] <= 59 && $timechk[2] <= 59) $timechk = TRUE;
      else $timechk = FALSE;
      }
    else $timechk = TRUE;

		//> Html Design.
			$Header =  "<div style=\"width:".$this->info['CountdownTabelleBreite'].";padding:5px;font-family:Verdana;font-size:".$this->info['CountdownSchriftsize']."px;".$Font."color:".$this->info['CountdownSchriftfarbe'].";border:2px dotted ".$this->info['CountdownRandFarbe'].";text-align:center\">";
			$Footer = "</div>";

		//> Überprüfen ob die angaben stimmen.
		if($date[0] <= 31 && $date[1] <= 12 && $date[2] /*>= date("Y")*/ && checkdate($date[1],$date[0],$date[2]) && $timechk) {
			if(isset($time)) {
				$time = explode(":",$time);
				$intStd = $time[0];
				$intMin = $time[1];
				$intSek = $time[2];
			} else {
				$intStd = 0;
				$intMin = 0;
				$intSek = 0;
			}

			$Timestamp = @mktime($intStd, $intMin, $intSek, $date[1], $date[0], $date[2]);
			$Diff = $Timestamp - time();

			$Font =($this->info['CountdownSchriftformat'] == "bold") ? "font-wight:bold;":"font-style:".$this->info['CountdownSchriftformat'].";";

			if($Diff > 1) {
				$Tage = sprintf("%00d",($Diff / 86400));
				$Stunden = sprintf("%00d",(($Diff - ($Tage * 86400)) / 3600));
				$Minuten = sprintf("%00d",(($Diff - (($Tage * 86400)+($Stunden*3600))) / 60));
				$Sekunden = ($Diff - (($Tage * 86400)+($Stunden*3600)+($Minuten*60)));

				//> Bei höheren Wert wie 1 als Mehrzahl ausgeben.
				$mzTg = ($Tage == 1) ? "":"e";
				$mzStd = ($Stunden == 1) ? "":"n";
				$mzMin = ($Minuten == 1) ? "":"n";
				$mzSek = ($Sekunden == 1) ? "":"n";

				//> Datum zusamstellen.
				$str = $Header.$Tage." Tag".$mzTg.", ".$Stunden." Stunde".$mzStd.", ".$Minuten." Minute".$mzMin." und ".$Sekunden." Sekunde".$mzSek.$Footer;
			} else {
				//> Datum zusamstellen wenn Datum unmittelbar bevor steht.
				$str = $Header.(is_array($time) ? implode(':',$time) : $time).' '.implode('.',$date)." !!!".$Footer;
			}
		} else {
			/*if($time == NULL) {
				$str = "[countdown]".implode('.',$date)."[/countdown]";
			} else {
				$str = "[countdown=".$time."]".implode('.',$date)."[/countdown]";
			}*/
			$str =  $Header."Der Countdown ist falsch definiert".$Footer;

		}

		return $str;
	}

	function _ws($ws) {
    return $ws;
  }

	function parse($string) {
		//> Die Blocks werden codiert um sie vor dem restlichen parsen zu schützen.
		if($this->permitted['php'] == true) {
			$string = preg_replace("%\[php\](.+)\[\/php\]%esiU","\$this->encode_codec('\$1','php')",$string);
			$string = preg_replace("%\[php=(.*)\](.+)\[\/php\]%esiU","\$this->encode_codec('\$2','php','\$1')",$string);
		}

		if($this->permitted['html'] == true) {
			$string = preg_replace("%\[html\](.+)\[\/html\]%esiU","\$this->encode_codec('\$1','html')",$string);
			$string = preg_replace("%\[html=(.*)\](.+)\[\/html\]%esiU","\$this->encode_codec('\$2','html','\$1')",$string);
		}

		if($this->permitted['css'] == true) {
			$string = preg_replace("%\[css\](.+)\[\/css\]%esiU","\$this->encode_codec('\$1','css')",$string);
			$string = preg_replace("%\[css=(.*)\](.+)\[\/css\]%esiU","\$this->encode_codec('\$2','css','\$1')",$string);
		}

		if($this->permitted['code'] == true) {
			$string = preg_replace("%\[code\](.+)\[\/code\]%esiU","\$this->encode_codec('\$1','code')",$string);
			$string = preg_replace("%\[code=(.*)\](.+)\[\/code\]%esiU","\$this->encode_codec('\$2','code','\$1')",$string);
		}

		if($this->permitted['list'] == true) {
			$string = preg_replace("%\[list\](.+)\[\/list\]%esiU","\$this->encode_codec('\$1','list')",$string);
		}

		//> BB Code der den Codeblock nicht betrifft.
		//> Überprüfen ob die wörter nicht die maximal länge überschrieten.
		$string = $this->_shortwords($string);
		$string = htmlentities($string);
		$string = nl2br($string);

		if($this->permitted['url'] == true) {
			if($this->permitted['autourl'] == true) {
				//> Format: www.xxx.de
				$this->pattern[] = "%(( |\n|^)(www.[a-zA-Z\-0-9@:\%_\+.~#?&//=,;]+?))%eUi";
				$this->replace[] = "\$this->_ws('\$2').\$this->_shorturl('\$3')";

				//> Format: http://www.xxx.de
				$this->pattern[] = "%(( |\n|^)((http|https|ftp)://{1}[a-zA-Z\-0-9@:\%_\+.~#?&//=,;]+?))%eUi";
				$this->replace[] = "\$this->_ws('\$2').\$this->_shorturl('\$3')";

				//> Format xxx@xxx.de
				$this->pattern[] = "%(\s|^)([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})%i";
				$this->replace[] = "<a href=\"mailto:$2\">$2</a>";
			}

			//> Format: [url=xxx]xxx[/url]
			$this->pattern[] = "%\[url=([^\]]*)\](.+)\[\/url\]%eUis";
			$this->replace[] = "\$this->_shorturl('\$1','\$2')";

			//> Format: [url]xxx[/url]
			$this->pattern[] = "%\[url\](.+)\[\/url\]%esiU";
			$this->replace[] = "\$this->_shorturl('\$1')";
		}

		//> Darf BB Code [MAIL] dekodiert werden?
		if($this->permitted['email'] == true) {
			//> Format: [mail]xxx@xxx.de[/mail]
			$this->pattern[] = "%\[mail\]([_\.0-9a-z-]+\@([0-9a-z\-]+)\.[a-z]{2,3})\[\/mail\]%Uis";
			$this->replace[] = "<a href=\"mailto:$1\">$1</a>";

			//> Format: [mail=xxx@xxx.de]xxx[/mail]
			$this->pattern[] = "%\[mail=([_\.0-9a-z-]+\@([0-9a-z\-]+)\.[a-z]{2,3})\](.+)\[\/mail\]%Uis";
			$this->replace[] = "<a href=\"mailto:$1\">$3</a>";
		}



		//> Darf BB Code [B] dekodiert werden?
		if($this->permitted['b'] == true) {
			//> Format: [b]xxx[/b]
			$this->pattern[] = "%\[b\](.+)\[\/b\]%Uis";
			$this->replace[] = "<b>\$1</b>";
		}

		//> Darf BB Code [I] dekodiert werden?
		if($this->permitted['i'] == true) {
			//> Format: [i]xxx[/i]
			$this->pattern[] = "%\[i\](.+)\[\/i\]%Uis";
			$this->replace[] = "<i>\$1</i>";
		}

		//> Darf BB Code [U] dekodiert werden?
		if($this->permitted['u'] == true) {
			//> Format: [u]xxx[/u]
			$this->pattern[] = "%\[u\](.+)\[\/u\]%Uis";
			$this->replace[] = "<u>\$1</u>";
		}

		//> Darf BB Code [S] dekodiert werden?
		if($this->permitted['s'] == true) {
			//> Format: [s]xxx[/s]
			$this->pattern[] = "%\[s\](.+)\[\/s\]%Uis";
			$this->replace[] = "<strike>\$1</strike>";
		}


		###############################################


		//> Darf BB Code [LEFT] dekodiert werden?
		if($this->permitted['left'] == true) {
			//> Format: [left]xxx[/left]
			$this->pattern[] = "%\[left\](.+)\[\/left\]%Uis";
			$this->replace[] = "<div align=\"left\">\$1</div>";
		}

		//> Darf BB Code [CENTER] dekodiert werden?
		if($this->permitted['center'] == true) {
			//> Format: [center]xxx[/center]
			$this->pattern[] = "%\[center\](.+)\[\/center\]%Uis";
			$this->replace[] = "<div align=\"center\">\$1</div>";
		}

		//> Darf BB Code [RIGHT] dekodiert werden?
		if($this->permitted['right'] == true) {
			//> Format: [right]xxx[/right]
			$this->pattern[] = "%\[right\](.+)\[\/right\]%Uis";
			$this->replace[] = "<div align=\"right\">\$1</div>";
		}


		###############################################

		//> Darf BB Code [EMPH] dekodiert werden?
		if($this->permitted['emph'] == true) {
			//> Format: [emph]xxx[/emph]
			$this->pattern[] = "%\[emph\](.+)\[\/emph\]%Uis";
			$this->replace[] = "<span style=\"background-color:".$this->info['EmphHintergrundfarbe'].";color:".$this->info['EmphSchriftfarbe'].";\">$1</span>";
		}

		//> Darf BB Code [COLOR] dekodiert werden?
		if($this->permitted['color'] == true) {
			//> Format: [color=#xxxxxx]xxx[/color]
			$this->pattern[] = "%\[color=(#{1}[0-9a-zA-Z]+?)\](.+)\[\/color\]%Uis";
			$this->replace[] = "<font color=\"$1\">$2</font>";
		}

		//> Darf BB Code [SIZE] dekodiert werden?
		if($this->permitted['size'] == true) {
			//> Format: [size=xx]xxx[/size]
			$this->pattern[] = "%\[size=([0-9]+?)\](.+)\[\/size\]%eUi";
			$this->replace[] = "\$this->_size('\$1','\$2')";
		}

		//> Darf BB Code [KTEXT] decodiert werden?
		if($this->permitted['ktext'] == true) {
			//> Format: [ktext=xxx]
			$this->pattern[] = "%\[ktext=([^[/]*)\]%esiU";
			$this->replace[] = "\$this->_addKtextOpen('\\1')";

			//> Format: [/ktext]
			$this->pattern[] = "%\[/ktext\]%esiU";
			$this->replace[] = "\$this->_addKtextClose()";
		}

		//> Darf BB Code [IMG] dekodiert werden?
		if($this->permitted['img'] == true) {
			//> Format: [img]xxx.de[/img]
			$this->pattern[] = "%\[img\]([-a-zA-Z0-9@:\%_\+,.~#?&//=]+?)\[\/img\]%eUi";
			$this->replace[] = "\$this->_img('\$1')";
      //> Format: [img=left|right]xxx.de[/img]
      $this->pattern[] = "%\[img=(left|right)\]([-a-zA-Z0-9@:\%_\+,.~#?&//=]+?)\[\/img\]%eUi";
		  $this->replace[] = "\$this->_img('\$2','\$1')";
    }

		//> Darf BB Code [SCREENSHOT] dekodiert werden?
		if($this->permitted['screenshot'] == true) {
			//> Format: [shot]xxx.de[/screenshot]
			$this->pattern[] = "%\[shot\]([-a-zA-Z0-9@:\%_\+.~#?&//=]+?)\[\/shot\]%eUi";
			$this->replace[] = "\$this->_screenshot('\$1')";
      //> Format: [shot=left|right]xxx.de[/screenshot]
			$this->pattern[] = "%\[shot=(left|right)\]([-a-zA-Z0-9@:\%_\+.~#?&//=]+?)\[\/shot\]%eUi";
			$this->replace[] = "\$this->_screenshot('\$2','\$1')";

		}

		//> Farf BB Code [VIDEO] dekodiert werden?
        if($this->permitted['video'] == true) {
            //> Format: [video=xxx]xxx[/video]
            $this->pattern[] = "%\[video=(google|youtube|myvideo|gametrailers)\](.+)\[\/video\]%eUis";
            $this->replace[] = "\$this->_video('\$1','\$2')";
        }

		//> Darf BB Code [COUNTDOWN] dekodiert werden?
		if($this->permitted['countdown'] == true) {
			//> Format: [countdown=Std:Min:Sek]TT.MM.JJJJ[/countdown]
			$this->pattern[] = "%\[countdown=(([0-9]{2}):([0-9]{2}):([0-9]{2}))\](([0-9]{2})\.([0-9]{2})\.([0-9]{4}))\[\/countdown\]%eUis";
			$this->replace[] = "\$this->_countdown('\$5','\$1')";

			//> Format: [countdown]TT.MM.JJJJ[/countdown]
			$this->pattern[] = "%\[countdown\](([0-9]{2})\.([0-9]{2})\.([0-9]{4}))\[\/countdown\]%eUis";
			$this->replace[] = "\$this->_countdown('\$1')";
		}

		###############################################

		//> Darf BB Code [QUOTE] dekodiert werden?
		if($this->permitted['quote'] == true) {

			//> Format: [quote]
			$this->pattern[] = "%\[quote\]%esiU";
			$this->replace[] = "\$this->_addQuoteOpen()";

			//> Format: [quote=xxx]
			$this->pattern[] = "%\[quote=([^[/]*)\]%esiU";
			$this->replace[] = "\$this->_addQuoteOpen('\\1')";

			//> Format: [/quote]
			$this->pattern[] = "%\[/quote\]%esiU";
			$this->replace[] = "\$this->_addQuoteClose()";
		}

		//> Darf BB Code [FLASH] dekodiert werden?
		if($this->permitted['flash'] == true) {
		  
		    //> Format: [flash]*[/flash]
		    $this->pattern[] = "%\[flash]((http|https|ftp)://[a-z-0-9@:\%_\+.~#\?&/=,;]+)\[/flash]%i";
		    $this->replace[] =  '<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$this->info['FlashBreite'].'" height="'.$this->info['FlashHoehe'].'"'.
                                'codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=7,0,0,0">'.
                                '<param name="movie" value="$1">'.
                                '<param name="quality" value="high">'.
                                '<param name="scale" value="exactfit">'.
                                '<param name="menu" value="true">'.
                                '<param name="bgcolor" value="'.$this->info['FlashHintergrundfarbe'].'">'.
                                '<embed src="$1" quality="high" scale="exactfit" menu="false"'.
                                'bgcolor="'.$this->info['FlashHintergrundfarbe'].'" width="'.$this->info['FlashBreite'].'" height="'.$this->info['FlashHoehe'].'" swLiveConnect="false"'.
                                'type="application/x-shockwave-flash"'.
                                'pluginspage="http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">'.
                                '</embed>'.
                                '</object>';
        } 

		//> String parsen
		$string = preg_replace($this->pattern,$this->replace,$string);

		//> Darf BB Code [QUOTE] dekodiert werden?
		if($this->permitted['quote'] == true) {
			$string = $this->_quote($string);
		}

		//> Darf BB Code [KTEXT] decodiert werden?
		if($this->permitted['ktext'] == true) {
			$string = $this->_ktext($string);
		}

		//> Smilies Filtern.
		$string = $this->_smileys($string);

		//> Zum schluss die blöcke die verschlüsselt wurden wieder entschlüsseln und Parsen.
		if($this->permitted['php'] == true) {
			$string = preg_replace("%\[php\](.+)\[\/php\]%esiU","\$this->_phpblock('\$1')",$string);
			$string = preg_replace("%\[php=([^;]*);(\d+)\](.+)\[\/php\]%esiU","\$this->_phpblock('\$3','\$1','\$2')",$string);
      $string = preg_replace("%\[php=(.*)\](.+)\[\/php\]%esiU","\$this->_phpblock('\$2','\$1')",$string);
		}

		if($this->permitted['html'] == true) {
			$string = preg_replace("%\[html\](.+)\[\/html\]%esiU","\$this->_htmlblock('\$1')",$string);
			$string = preg_replace("%\[html=([^;]*);(\d+)\](.+)\[\/html\]%esiU","\$this->_htmlblock('\$3','\$1','\$2')",$string);
      $string = preg_replace("%\[html=(.*)\](.+)\[\/html\]%esiU","\$this->_htmlblock('\$2','\$1')",$string);
		}

		if($this->permitted['css'] == true) {
			$string = preg_replace("%\[css\](.+)\[\/css\]%esiU","\$this->_cssblock('\$1')",$string);
			$string = preg_replace("%\[css=([^;]*);(\d+)\](.+)\[\/css\]%esiU","\$this->_cssblock('\$3','\$1','\$2')",$string);
      $string = preg_replace("%\[css=(.*)\](.+)\[\/css\]%esiU","\$this->_cssblock('\$2','\$1')",$string);
		}

		if($this->permitted['code'] == true) {
			$string = preg_replace("%\[code\](.+)\[\/code\]%esiU","\$this->_codeblock('\$1')",$string);
			$string = preg_replace("%\[code=([^;]*);(\d+)\](.+)\[\/code\]%esiU","\$this->_codeblock('\$3','\$1','\$2')",$string);
      $string = preg_replace("%\[code=(.*)\](.+)\[\/code\]%esiU","\$this->_codeblock('\$2','\$1')",$string);
		}

		if($this->permitted['list'] == true) {
			$string = preg_replace("%\[list\](.+)\[\/list\]%esiU","\$this->_list('\$1')",$string);
		}

		//> Badwors Filtern.
		$string = $this->_badwords($string);

		unset($this->pattern);
		unset($this->replace);

		unset($this->ayCacheQuoteOpen);
		unset($this->ayCacheQuoteClose);

		unset($this->ayCacheKtextOpen);
		unset($this->ayCacheKtextClose);

		return $string;
	}
}
?>