<?php
/**
*
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
* @copyright (C) 2000-2010 ilch.de originally from Thomas Bowe [Funjoy]
* @version $Id$
*/
/* Module - Information
* -------------------------------------------------------
* Hier könnt ihr eure Module includieren lassen.
* Wenn Ihr selber Module zum Highlight programmiert
* denkt daran das ihr auch noch das Parsen hier definieren müsst.
* und in der bbcode_config.php Datei müsstet ihr die Option auch noch einstellen.
* um ein Beispiel zu haben schaut euch die Funktion _htmlblock() am besten mal an.
* und in Zeile 308 und Zeile 490 habt ihr ein Beispiel wie ihr die Parsebefehle schreiben könnt.
*/
// > Bitte denkt daran das, dass Modul html.php immer unter dem Modul css.php sein muss.
// > Modul [css.php]
if (file_exists("include/includes/class/highlight/css.php")) {
    require_once("include/includes/class/highlight/css.php");
}
// > Modul [html.php]
if (file_exists("include/includes/class/highlight/html.php")) {
    require_once("include/includes/class/highlight/html.php");
}

class bbcode {
    // Instance - Singleton
    public static $instance = null;
    // Speichern der BBCodeButtons
    protected static $BBCodeButtons = '';
    // > Tags die geparsed werden dürfen.
    protected $permitted = array();
    // > Verschlüsselte codeblocks.
    protected $codecblocks = array();
    // > Badwords!
    protected $badwords = array();
    // > Informationen für die Klasse!
    protected $info = array();
    // > Patter befehle!
    protected $pattern = array();
    // > Replace strings!
    protected $replace = array();
    // > Smilies die in Grafik umgewandelt werden sollen.
    protected $smileys = array();
    // > Cache für Quotes Header!
    protected $ayCacheQuoteOpen = array();
    // > Cache fürQuotes Footer!
    protected $ayCacheQuoteClose = array();
    // > Cache für Quotes Header!
    protected $ayCacheKtextOpen = array();
    // > Cache fürQuotes Footer!
    protected $ayCacheKtextClose = array();
    // > Singleton Funktionen
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new bbcode();
        }
        return self::$instance;
    }
    private function __clone()
    {
    }
    private function __construct()
    {
        global $global_smiles_array, $ILCH_BODYEND_ADDITIONS;
        require 'include/includes/func/bbcode_config.php';
        if (!isset($global_smiles_array)) {
            $erg = db_query("SELECT ent, url, emo FROM `prefix_smilies`");
            while ($row = db_fetch_object($erg)) {
                $global_smiles_array[$row->ent] = $row->emo . '#@#-_-_-#@#' . $row->url;
            }
        }

        $this->smileys = $global_smiles_array;
        $this->permitted = $permitted;
        $this->info = $info;
        $ILCH_BODYEND_ADDITIONS .= "<script type=\"text/javascript\">\nvar bbcodemaximagewidth = {$info['ImgMaxBreite']};\nvar bbcodemaximageheight = {$info['ImgMaxHoehe']};\n</script>";
    }
    public function setConfig($name, $value)
    {
        $this->info[$name] = $value;
    }

    public static function getBBCodeButtons()
    {
        if (empty(self::$BBCodeButtons)) {
            // > Buttons Informationen.
            $ButtonSql = db_query("SELECT *	FROM prefix_bbcode_buttons WHERE fnButtonNr='1'");
            $boolButton = db_fetch_assoc($ButtonSql);

            $cfgBBCsql = db_query("SELECT * FROM prefix_bbcode_config WHERE fnConfigNr='1'");
            $cfgInfo = db_fetch_assoc($cfgBBCsql);

            $BBCodeButtons = '<script type="text/javascript" src="include/includes/js/interface.js"></script>';
            // > Fett Button!
            if ($boolButton['fnFormatB'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('b','Gib hier den Text an der fett formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_bold.png\" alt=\"Fett formatieren\" title=\"Fett formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Kursiv Button!
            if ($boolButton['fnFormatI'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('i','Gib hier den Text an der kursiv formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_italic.png\" alt=\"Kursiv formatieren\" title=\"Kursiv formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Unterschrieben Button!
            if ($boolButton['fnFormatU'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('u','Gib hier den Text an der unterstrichen formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_underline.png\" alt=\"Unterstrichen formatieren\" title=\"Unterstrichen formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Durchgestrichener Button!
            if ($boolButton['fnFormatS'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('s','Gib hier den Text an der durchgestrichen formatiert werden soll..')\"><img src=\"include/images/icons/bbcode/bbcode_strike.png\" alt=\"Durchgestrichen formatieren\" title=\"Durchgestrichen formatieren\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Leerzeichen?
            if ($boolButton['fnFormatB'] == 1 || $boolButton['fnFormatI'] == 1 || $boolButton['fnFormatU'] == 1 || $boolButton['fnFormatS'] == 1) {
                $BBCodeButtons .= "&nbsp;";
            }
            // > Links Button!
            if ($boolButton['fnFormatLeft'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('left','0')\"><img src=\"include/images/icons/bbcode/bbcode_left.png\" alt=\"Links ausrichten\" title=\"Links ausrichten\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Zentriert Button!
            if ($boolButton['fnFormatCenter'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('center','0')\"><img src=\"include/images/icons/bbcode/bbcode_center.png\" alt=\"Mittig ausrichten\" title=\"Mittig ausrichten\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Rechts Button!
            if ($boolButton['fnFormatRight'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('right','0')\"><img src=\"include/images/icons/bbcode/bbcode_right.png\" alt=\"Rechts ausrichten\" title=\"Rechts ausrichten\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Leerzeichen?
            if ($boolButton['fnFormatLeft'] == 1 || $boolButton['fnFormatCenter'] == 1 || $boolButton['fnFormatRight'] == 1) {
                $BBCodeButtons .= "&nbsp;";
            }
            // > Listen Button!
            if ($boolButton['fnFormatList'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('list','Gib hier den Text ein der aufgelistet werden soll.\\nUm die liste zu beenden einfach auf Abbrechen klicken.')\"><img src=\"include/images/icons/bbcode/bbcode_list.png\" alt=\"Liste erzeugen\" title=\"Liste erzeugen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Hervorheben Button!
            if ($boolButton['fnFormatEmph'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('emph','0')\"><img src=\"include/images/icons/bbcode/bbcode_emph.png\" alt=\"Text hervorheben\" title=\"Text hervorheben\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Schriftfarbeauswahlcontainer
            if ($boolButton['fnFormatColor'] == 1) {
                $colorar = array('#FF0000' => 'red', '#FFFF00' => 'yellow', '#008000' => 'green', '#00FF00' => 'lime', '#008080' => 'teal', '#808000' => 'olive', '#0000FF' => 'blue', '#00FFFF' => 'aqua', '#000080' => 'navy', '#800080' => 'purple', '#FF00FF' => 'fuchsia', '#800000' => 'maroon', '#C0C0C0' => 'grey', '#808080' => 'silver', '#000000' => 'black', '#FFFFFF' => 'white');
                $BBCodeButtons .= "<a href=\"javascript:hide_color();\"><img id=\"bbcode_color_button\" src=\"include/images/icons/bbcode/bbcode_color.png\" alt=\"Text f&auml;rben\" title=\"Text f&auml;rben\" width=\"23\" height=\"22\" border=\"0\"></a> ";
                $BBCodeButtons .= '<div style="position:absolute;"><div style="display:none; position:relative; top:-30px; left:100px; width:200px; z-index:100;" id="colorinput">
                <table width="100%" class="border" border="0" cellspacing="1" cellpadding="0">
                	<tr class="Chead" onclick="javascript:hide_color();"><td colspan="16"><b>Farbe wählen</b></td></tr>
                	<tr class="Cmite" height="15">' . colorliste($colorar) . '</tr></table>
                </div></div>';
            }
            // > Schriftgröße Button!
            if ($boolButton['fnFormatSize'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('size','Gib hier den Text an, der in einer anderen Schriftgr&ouml;ße formatiert werden soll.','Gib hier die Gr&ouml;&szlig;e des textes in Pixel an. \\n Pixellimit liegt bei " . $cfgInfo['fnSizeMax'] . "px !!!')\"><img src=\"include/images/icons/bbcode/bbcode_size.png\" alt=\"Textgr&ouml;&szlig;e ver&auml;ndern\" title=\"Textgr&ouml;&szlig;e ver&auml;ndern\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Leerzeichen?
            if ($boolButton['fnFormatList'] == 1 || $boolButton['fnFormatEmph'] == 1 || $boolButton['fnFormatColor'] == 1 || $boolButton['fnFormatSize'] == 1) {
                $BBCodeButtons .= "&nbsp;";
            }
            // > Url Button!
            if ($boolButton['fnFormatUrl'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('url','Gib hier die Beschreibung für den Link an.','Gib hier die Adresse zu welcher verlinkt werden soll an.')\"><img src=\"include/images/icons/bbcode/bbcode_url.png\" alt=\"Hyperlink einf&uuml;gen\" title=\"Hyperlink einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > E-Mail Button!
            if ($boolButton['fnFormatEmail'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('mail','Gib hier den namen des links an.','Gib hier die eMail - Adresse an.')\"><img src=\"include/images/icons/bbcode/bbcode_email.png\" alt=\"eMail hinzuf&uuml;gen\" title=\"eMail hinzuf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Leerzeichen?
            if ($boolButton['fnFormatUrl'] == 1 || $boolButton['fnFormatEmail'] == 1) {
                $BBCodeButtons .= "&nbsp;";
            }
            // > Bild Button!
            if ($boolButton['fnFormatImg'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('img','Gib hier die Adresse des Bildes an.\\nHinweise: Die Breite und H&ouml;he des Bildes ist auf " . $cfgInfo['fnImgMaxBreite'] . "x" . $cfgInfo['fnImgMaxHoehe'] . " eingeschr&auml;nkt und w&uuml;rde verkleinert dargstellt werden.\\nEs ist möglich ein Bild rechts oder links von anderen Elementen darzustellen, indem man [img=left] oder [img=right] benutzt.')\"><img src=\"include/images/icons/bbcode/bbcode_image.png\" alt=\"Bild einf&uuml;gen\" title=\"Bild einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Screenshot Button!
            if ($boolButton['fnFormatScreen'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('shot','Gib hier die Adresse des Screens an.\\nDie Breite und H&ouml;he des Bildes ist auf " . $cfgInfo['fnScreenMaxBreite'] . "x" . $cfgInfo['fnScreenMaxHoehe'] . " eingeschränkt und wird verkleinert dargstellt.\\nEs ist möglich ein Screenshot rechts oder links von anderen Elementen darzustellen, indem man [shot=left] oder [shot=right] benutzt.')\"><img src=\"include/images/icons/bbcode/bbcode_screenshot.png\" alt=\"Bild einf&uuml;gen\" title=\"Screen einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Leerzeichen?
            if ($boolButton['fnFormatImg'] == 1 || $boolButton['fnFormatScreen'] == 1) {
                $BBCodeButtons .= "&nbsp;";
            }
            // > Quote Button!
            if ($boolButton['fnFormatQuote'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('quote','0')\"><img src=\"include/images/icons/bbcode/bbcode_quote.png\" alt=\"Zitat einf&uuml;gen\" title=\"Zitat einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Klapptext Button!
            if ($boolButton['fnFormatKtext'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('ktext','Gib hier den zu verbergenden Text ein.','Gib hier einen Titel f&uuml;r den Klapptext an.')\"><img src=\"include/images/icons/bbcode/bbcode_ktext.png\" alt=\"Klappfunktion hinzuf&uuml;gen\" title=\"Klappfunktion hinzuf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Video Button!
            if ($boolButton['fnFormatVideo'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value_2('video','Gib hier die Video ID vom Anbieter an.','Bitte Anbieter ausw&auml;hlen.\\nAkzeptiert werden: Google, YouTube, MyVideo und GameTrailers')\"><img src=\"include/images/icons/bbcode/bbcode_video.png\" alt=\"Video einf&uuml;gen\" title=\"Video einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Flash Button!
            if ($boolButton['fnFormatFlash'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_multiple_values('flash',{tag:['Gib hier den Link zur Flashdatei an',''],width:['Gib hier die Breite für die Flashdatei an','400'],height:['Gib hier die Höhe für die Flashdatei an','300']})\"><img src=\"include/images/icons/bbcode/bbcode_flash.png\" alt=\"Flash einf&uuml;gen\" title=\"Flash einf&uuml;gen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Countdown Button!
            if ($boolButton['fnFormatCountdown'] == 1) {
                $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('countdown','Gib hier das Datum an wann das Ereignis beginnt.\\n Format: TT.MM.JJJJ Bsp: 24.12." . date("Y") . "','Gib hier eine Zeit an, wann das Ergeinis am Ereignis- Tag beginnt.\\nFormat: Std:Min:Sek Bsp: 20:15:00')\"><img src=\"include/images/icons/bbcode/bbcode_countdown.png\" alt=\"Countdown festlegen\" title=\"Countdown festlegen\" width=\"23\" height=\"22\" border=\"0\"></a> ";
            }
            // > Leerzeichen?
            if ($boolButton['fnFormatQuote'] == 1 || $boolButton['fnFormatKtext'] == 1 || $boolButton['fnFormatVideo'] == 1 || $boolButton['fnFormatFlash'] == 1 || $boolButton['fnFormatCountdown'] == 1) {
                $BBCodeButtons .= "&nbsp;";
            }
            // > Code Dropdown!
            if ($boolButton['fnFormatCode'] == 1 || $boolButton['fnFormatPhp'] == 1 || $boolButton['fnFormatHtml'] == 1 || $boolButton['fnFormatCss'] == 1) {
                $BBCodeButtons .= "<select onChange=\"javascript:bbcode_code_insert_codes(this.value); javascript:this.value='0';\" style=\"font-family:Verdana;font-size:10px; margin-bottom:6px; z-index:0;\" name=\"code\"><option value=\"0\">Code einf&uuml;gen</option>";
            }

            if ($boolButton['fnFormatPhp'] == 1) {
                $BBCodeButtons .= "<option value=\"php\">PHP</option>";
            }

            if ($boolButton['fnFormatHtml'] == 1) {
                $BBCodeButtons .= "<option value=\"html\">HTML</option>";
            }

            if ($boolButton['fnFormatCss'] == 1) {
                $BBCodeButtons .= "<option value=\"css\">CSS</option>";
            }

            if ($boolButton['fnFormatCode'] == 1) {
                $BBCodeButtons .= "<option value=\"code\">Sonstiger Code</option>";
            }

            if ($boolButton['fnFormatCode'] == 1 || $boolButton['fnFormatPhp'] == 1 || $boolButton['fnFormatHtml'] == 1 || $boolButton['fnFormatCss'] == 1) {
                $BBCodeButtons .= "</select>";
            }

            self::$BBCodeButtons = $BBCodeButtons;
        }
        return self::$BBCodeButtons;
    }
    // > Codeblock verschlüsseln und wieder ausgeben.
    protected function encode_codec($string, $tag, $file = null)
    {
        $string = str_replace('\"', '"', $string);
        $file = ($file == null) ? "":"=" . $file;
        $crypt = md5(count($this->codecblocks));
        $this->codecblocks[$crypt . ":" . $tag] = $string;
        return "[" . $tag . $file . "]" . $crypt . "[/" . $tag . "]";
    }
    // > Codeblock entschlüsseln und parsen!
    protected function _codeblock($codecid, $file = null, $firstline = 1)
    {
        $string = $this->codecblocks[$codecid . ':code'];
        // $string = htmlentities($string);
        $string = str_replace("\t", '&nbsp; &nbsp;', $string);
        $string = str_replace('  ', '&nbsp; ', $string);
        $string = str_replace('  ', ' &nbsp;', $string);
        $string = nl2br($string);

        return $this->_addcodecontainer($string, 'Code', $file, $firstline);
    }
    // > htmlblock entschlüsseln und parsen!
    protected function _htmlblock($codecid, $file = null, $firstline = 1)
    {
        $string = $this->codecblocks[$codecid . ':html'];
        // $string = htmlentities($string);
        // > Highlight Modul Funktion checken ob sie existerit.
        if (function_exists("highlight_html")) {
            $string = highlight_html($string, $this->info['BlockCodeFarbe']);
        }

        $string = str_replace("\t", '&nbsp; &nbsp;', $string);
        $string = str_replace('  ', '&nbsp; ', $string);
        $string = str_replace('  ', ' &nbsp;', $string);
        $string = nl2br($string);

        return $this->_addcodecontainer($string, 'HTML', $file, $firstline);
    }
    // > cssblock entschlüsseln und parsen!
    protected function _cssblock($codecid, $file = null, $firstline = 1)
    {
        $string = $this->codecblocks[$codecid . ':css'];
        // $string = htmlentities($string);
        // > Highlight Modul Funktion checken ob sie existerit.
        if (function_exists("highlight_css")) {
            $string = highlight_css($string);
        }

        $string = str_replace("\t", '&nbsp; &nbsp;', $string);
        $string = str_replace('  ', '&nbsp; ', $string);
        $string = str_replace('  ', ' &nbsp;', $string);
        $string = nl2br($string);

        return $this->_addcodecontainer($string, 'CSS', $file, $firstline);
    }
    // > phpblock entschlüsseln und parsen!
    protected function _phpblock($codecid, $file = null, $firstline = 1)
    {
        $string = $this->codecblocks[$codecid . ':php'];
        if (strpos($string, '<?php') === false) {
            $string = "<?php\n{$string}\n?>";
            $remove = true;
        } else {
            $remove = false;
        }
        ob_start();
        highlight_string($string);
        $php = ob_get_contents();
        ob_end_clean();
        if ($remove) {
            $php = str_replace(array('&lt;?php<br />', '<br /></span><span style="color: #0000BB">?&gt;</span>'), '', $php);
        }
        return $this->_addcodecontainer($php, 'Php', $file, $firstline);
    }

    protected function _addcodecontainer($code, $type, $file = null, $firstline = 1)
    {
        // > Datei pfad mit angegeben?
        $file = ($file == null) ? "":" von Datei <em>" . $this->_shortwords($file) . "</em>";
        // > Zeilen zählen.
        $linescount = substr_count($code, "\n") + $firstline + 1;
        if ($type == 'Php') {
            $linescount = substr_count($code, "\r") + $firstline + 1;
        }
        $line = '';
        for($no = $firstline;$no < $linescount;$no++) {
            $line .= $no . ":<br />";
        }
        // > Hier könnt ihr den Header und Footer für HTML editieren.
        $breite = trim($this->info['BlockTabelleBreite']);
        $breite = (strpos($breite, '%') !== false) ? '450px' : $breite . 'px';
        $header = "<div style=\"overflow: auto; width: {$breite};\">"
         . "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"BORDER: 1px SOLID " . $this->info['BlockRandFarbe'] . ";\" width=\"100%\">"
         . "<tr><td colspan=\"3\" style=\"font-family:Arial, Helvetica, sans-serif;font-size:12px; font-weight:bold; color:" . $this->info['BlockSchriftfarbe'] . ";background-color:" . $this->info['BlockHintergrundfarbe'] . ";\">&nbsp;" . $type . $file . "</td></tr>"
         . "<tr bgcolor=\"" . $this->info['BlockHintergrundfarbeIT'] . "\"><td style=\"width:20px; color:" . $this->info['BlockSchriftfarbe'] . ";padding-left:2px;padding-right:2px;border-right:1px solid " . $this->info['BlockHintergrundfarbe'] . ";font-family:Arial, Helvetica, sans-serif;\" align=\"right\" valign=\"top\"><code style=\"width:20px;\">"
         . $line
         . "</code></td><td width=\"5\">&nbsp;</td><td valign=\"top\" style=\"background-color:" . $this->info['BlockHintergrundfarbe'] . "; color:" . $this->info['BlockSchriftfarbe'] . ";\" nowrap width=\"95%\"><code>";
        $footer = "</code></td></tr></table></div>";

        return $header . $code . $footer;
    }
    // > Smilies aus dem Array auslesen.
    protected function _smileys($string)
    {
        if (!is_null($this->smileys) && is_array($this->smileys)) {
            if ($this->permitted['smileys'] == true) {
                $smileystart = '#@' . uniqid('') . '@#';
                $smileymid = '|#@|' . uniqid('') . '|@#|';
                $smileyend = '#@' . uniqid('') . '@#';
                foreach ($this->smileys as $icon => $info) {
                    list($emo, $url) = explode('#@#-_-_-#@#', $info);
                    $string = str_replace($icon, $smileystart . $icon . $smileyend, $string);
                }
                $string = str_replace($smileyend . $smileystart, $smileymid, $string);
                $string = preg_replace('%(\S)' . $smileystart . '(.*)' . $smileyend . '%iU', '$1$2', $string);
                $string = preg_replace('%(^|\s)(' . $smileystart . ')(.*)(' . $smileyend . ')%iUe', '\'$1\'.\'$2\'.str_replace(\'' . $smileymid . '\',\'' . $smileyend . $smileystart . '\',\'$3\').\'$4\'', $string);

                $string = str_replace($smileymid, '', $string);
                foreach ($this->smileys as $icon => $info) {
                    list($emo, $url) = explode('#@#-_-_-#@#', $info);
                    $string = str_replace($smileystart . $icon . $smileyend, '<img src="include/images/smiles/' . $url . '" border="0" alt="' . $icon . '" title="' . $emo . '" />', $string);
                }
                $string = str_replace(array($smileyend, $smileystart), '', $string);
            }
            return $string;
        } else {
            return $string;
        }
    }
    // //> Smilies aus dem Array auslesen.
    // function _smileys($string) {
    // if(!is_null($this->smileys) && is_array($this->smileys)) {
    // if($this->permitted['smileys'] == true) {
    // foreach ($this->smileys as $icon => $info) {
    // list($emo, $url) = explode('#@#-_-_-#@#', $info);
    // $string = str_replace($icon,'<img src="include/images/smiles/'.$url.'" border="0" alt="'.$emo.'" title="'.$emo.'" />',$string);
    // }
    // }
    // return $string;
    // } else {
    // return $string;
    // }
    // }
    // > Badwords Filtern.
    protected function _badwords($string)
    {
        // > Badwords aus der Datenbank laden!
        $cfgBBCodeSql = db_query("SELECT fcBadPatter, fcBadReplace FROM prefix_bbcode_badword");
        while ($row = db_fetch_object($cfgBBCodeSql)) {
            $pattern[] = '%' . preg_quote($row->fcBadPatter, '%') . '%iU';
            $replace[] = $row->fcBadReplace;
        }
        if (isset($pattern)) {
            $string = preg_replace($pattern, $replace, $string);
        }

        return $string;
    }
    // > Liste formatieren.
    protected function _list($codecid)
    {
        $string = $this->codecblocks[$codecid . ':list'];
        $array = explode("[*]", $string);
        for($no = 1;$no <= (count($array) - 1);$no++) {
            $li .= "<li>" . $this->parse($array[$no]) . "</li>";
        }

        return "<ul>" . $li . "</ul>";
    }
    // > Auf Maximale Schriftgröße überprüfen.
    protected function _size($size, $string)
    {
        $max = $this->info['SizeMax'];
        // return '<span style="font-size:' . ($size > $max ? $max : $size) . 'px">' . $string . '</span>';
        return '<span style="font-size:' . ($size > $max ? $max : $size) . 'px">' . stripcslashes($string) . '</span>';
    }
    // > Bilder auf Verkleinern via Javascript überprüfen.
    protected function _img($string, $float = '')
    {
        if ($float == 'none' OR $float == 'left' OR $float == 'right') {
            $float = 'style="float:' . $float . '; margin: 5px;" ';
        } else {
            $float = '';
        }
        $image = '<img src="' . $string . '" alt="" title="" border="0" class="bbcode_image" ' . $float . '/>';
        return $image;
    }
    // > Screenshots darstellen.
    protected function _screenshot($string, $float = 'none')
    {
        if ($float == 'none' OR $float == 'left' OR $float == 'right') {
            $float = 'style="float:' . $float . '; margin: 5px;" ';
        } else {
            $float = '';
        }
        $image = '<a href="' . $string . '" target="_blank"><img src="' . $string . '" alt="" title="" border="0" width="' . $this->info['ScreenMaxBreite'] . '" height="' . $this->info['ScreenMaxHoehe'] . '" ' . $float . '/></a>';
        return $image;
    }
    // > Urls Filtern um XSS vorzubeugen
    protected function _filterurl($url)
    {
        return str_replace(
            array('<', '>', '(', ')', '#'),
            array('&lt;', '&gt;', '&#40;', '&#41;', '&#35;'),
            $url
            );
    }
    // > Links darstellen und ggf. kürzen
    protected function _shorturl($string, $caption = null)
    {
        if ($caption == null) {
            $caption = $string;
        }
        $string = trim($string);
        $caption = trim($caption);
        $server = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        //Wenn kein Protokoll angegeben ist
        if (preg_match('%^((http|ftp|https)://)|^/%i', $string) == 0) {
            //Schauen ob Link mit www. beginnt, ansonsten wird angenommen, dass der Link relativ sein soll
            if (preg_match('/^www\./i', $string) == 1) {
                $string = 'http://' . $string;
            }
        }
        if (substr($string, 0, 1) == '/' OR strpos($string, $server) !== false) {
            $target = '_self';
        } else {
            $target = '_blank';
        }

        if (strlen($caption) >= $this->info['UrlMaxLaenge']) {
            $caption = $this->_shortcaptions($caption);
        }
        return '<a href="' . $string . '" target="' . $target . '">' . $caption . '</a>';
    }
    // > Linkbeschreibung kürzen
    protected function _shortcaptions($string)
    {
        $words = explode(" ", $string);
        foreach($words as $word)
        if (strlen($word) > $this->info['WortMaxLaenge'] && !preg_match('%(\[(img|shot)\](.*)\[/(img|shot)\])%i', $word)) {
            $maxd2 = sprintf("%00d", ($this->info['WortMaxLaenge'] / 2));
            $string = str_replace($word, substr($word, 0, $maxd2) . "..." . substr($word, - $maxd2), $string);
        }
        return $string;
    }
    // > Hilfsfunktion für _shortwords
    protected function _checkpatterns($patterns, $word)
    {
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
    // > Zu lange Wörter kürzen.
    protected function _shortwords($string)
    {
        // > Zeichenkette in einzelne Array elemente zerlegen.
        $lines = explode("\n", $string);
        // > Patter Befehle die nicht gekürzt werden dürfen !!!
        $pattern = array("%^(www)(.[-a-zA-Z0-9@:;\%_\+.~#?&//=]+?)%i",
            "%^(http|https|ftp)://{1}[-a-zA-Z0-9@:;\%_\+.~#?&//=]+?%i",
            "%(\[(url|img(=(left|right))?|shot(=(left|right))?)\](.*)\[/(url|img|shot)\])|(\[url=(.*)\])%i",
            "%\[(code|html|css|php|countdown)(=[^]]+)].*\[/(code|html|css|php|countdown)]%i",
            "%(\[flash)?]((http|https|ftp)://[a-z-0-9@:\%_\+.~#\?&/=,;]+)\[/flash]%i",
            "%\[list].*\[/list]%");

        foreach($lines as $line) {
            $words = explode(" ", $line);
            foreach($words as $word)
            if (strlen($word) > $this->info['WortMaxLaenge'] && $this->_checkpatterns($pattern, $word)) {
                // Auskommentiert also Variante mit 'zulanges...Wort' zu gunsten von 'zulanges allesdazwischen Wort' (ohne ...)
                // $maxd2 = sprintf("%00d",($this->info['WortMaxLaenge']/2));
                $string = wordwrap($string, $this->info['WortMaxLaenge'], ' ', true);
            }
        }
        return $string;
    }
    // > Geöffnete Ktext- Tags Nummerieren.
    protected function _addKtextOpen($Titel = null)
    {
        $this->ayCacheKtextOpen[count($this->ayCacheKtextOpen) + 1] = true;
        $intCountKtext = count($this->ayCacheKtextOpen);

        $string = "[ktext:" . $intCountKtext . "=" . $Titel . "]";

        return $string;
    }
    // > Geschlossene Ktext- Tags Nummerieren.
    protected function _addKtextClose()
    {
        $this->ayCacheKtextClose[count($this->ayCacheKtextClose) + 1] = true;
        $intCountKtext = count($this->ayCacheKtextClose);

        return "[/ktext:" . $intCountKtext . "]";
    }
    // > Ktext- Tags umwandeln..
    protected function _ktext($string)
    {
        $Random = rand(1, 10000000);
        // > Html- Muster für geöffnete Tags mit Titel.
        $HeaderTitel = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"" . $this->info['KtextTabelleBreite'] . "\" align=\"center\">"
         . "<tr><td><a href=\"javascript:Klapptext('__ID__')\">"
         . "<img src=\"include/images/icons/plus.gif\" id=\"image___ID__\" border=0 alt=\"Aus/Ein-klappen\" title=\"Aus/Ein-klappen\"> ";

        $FooterTitel = "</a></td></tr>"
         . "<tr><td><div id=\"layer___ID__\" style=\"display:none;border:1px " . $this->info['KtextRandFormat'] . " " . $this->info['KtextRandFarbe'] . ";\">";
        // > Html- Muster für geschlossene Tags.
        $KtextClose = "</div></td></tr></table>\n";
        // > Geöffnete Tags umwandeln.
        for($c = 1;$c <= count($this->ayCacheKtextOpen);$c++) {
            if (count($this->ayCacheKtextClose) == count($this->ayCacheKtextOpen)) {
                // > Format: [ktext=xxx]
                $this->ktext_pattern[] = "%\[ktext:" . $c . "=([^]]*)\]%siU";
                $this->ktext_replace[] = str_replace("__ID__", $c . "@" . $Random, $HeaderTitel) . "\$1" . str_replace("__ID__", $c . "@" . $Random, $FooterTitel);
                // > Format: [/ktext]
                $this->ktext_pattern[] = "%\[/ktext:" . $c . "\]%siU";
                $this->ktext_replace[] = $KtextClose;
            } else {
                // > Format: [ktext=xxx]xxx[/ktext]
                $this->ktext_pattern[] = "%\[ktext:([0-9]*)=([^]](.*)\[/ktext:([0-9]*)\]%siU";
                $this->ktext_replace[] = str_replace("__ID__", "\$1@" . $Random, $HeaderTitel) . "\$2" . str_replace("__ID__", "\$1@" . $Random, $FooterTitel) . "\$3" . $KtextClose;
            }
        }
        // > Nicht gefundene Paare wieder darstellen.
        // > Format: [ktext=xxx]
        $this->ktext_pattern[] = "%\[ktext:([0-9]*)=([^[/]*)\]%siU";
        $this->ktext_replace[] = "[ktext=\$1]";
        // > Format: [/ktext]
        $this->ktext_pattern[] = "%\[/ktext:([0-9]*)\]%siU";
        $this->ktext_replace[] = "[/ktext]";
        // > String parsen
        $string = preg_replace($this->ktext_pattern, $this->ktext_replace, $string);

        return $string;
    }
    // > Geöffnete Quote- Tags Nummerieren.
    protected function _addQuoteOpen($User = null)
    {
        $this->ayCacheQuoteOpen[count($this->ayCacheQuoteOpen) + 1] = $User;
        $intCountQuote = count($this->ayCacheQuoteOpen);

        if ($User != null) {
            $string = "[quote:" . $intCountQuote . "=" . $User . "]";
        } else {
            $string = "[quote:" . $intCountQuote . "]";
        }

        return $string;
    }
    // > Geschlossene Quote- Tags Nummerieren.
    protected function _addQuoteClose()
    {
        $this->ayCacheQuoteClose[count($this->ayCacheQuoteClose) + 1] = true;
        $intCountQuote = count($this->ayCacheQuoteClose);

        return "[/quote:" . $intCountQuote . "]";
    }
    // > Quote- Tags umwandeln.
    protected function _quote($string)
    {
        // > überprüfen ob Bod gesetzt ist.
        if (strtolower($this->info['QuoteSchriftformatIT']) == "bold") {
            $Schriftformat = "font-weight:bold;";
        } else {
            $Schriftformat = "font-style:" . $this->info['QuoteSchriftformatIT'] . ";";
        }
        // > Html- Muster für geöffnete Quote- Tags.
        $Header = "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" style=\"BORDER: 1px SOLID " . $this->info['QuoteRandFarbe'] . ";\" width=\"" . $this->info['QuoteTabelleBreite'] . "\" align=\"center\">"
         . "<tr><td style=\"font-family:Arial, Helvetica, sans-serif;FONT-SIZE:13px;FONT-WEIGHT:BOLD;COLOR:" . $this->info['QuoteSchriftfarbe'] . ";BACKGROUND-COLOR:" . $this->info['QuoteHintergrundfarbe'] . ";\">&nbsp;Zitat</td></tr>"
         . "<tr bgcolor=\"" . $this->info['QuoteHintergrundfarbeIT'] . "\"><td><table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\"><tr><td style=\"" . $Schriftformat . "FONT-SIZE:10px;COLOR:" . $this->info['QuoteSchriftfarbeIT'] . ";\">";
        // > Html- Muster für geöffnete Quote- Tags mit User.
        $HeaderUser = "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" style=\"BORDER: 1px SOLID " . $this->info['QuoteRandFarbe'] . ";\" width=\"" . $this->info['QuoteTabelleBreite'] . "\" align=\"center\">"
         . "<tr><td style=\"font-family:Arial, Helvetica, sans-serif;FONT-SIZE:13px;FONT-WEIGHT:BOLD;COLOR:" . $this->info['QuoteSchriftfarbe'] . ";BACKGROUND-COLOR:" . $this->info['QuoteHintergrundfarbe'] . ";\">&nbsp;Zitat von ";

        $FooterUser = "</td></tr><tr bgcolor=\"" . $this->info['QuoteHintergrundfarbeIT'] . "\"><td><table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\"><tr><td style=\"" . $Schriftformat . "FONT-SIZE:10px;COLOR:" . $this->info['QuoteSchriftfarbeIT'] . ";\">";
        // > Html- Muster für geschlossene Quote- Tags.
        $QuoteClose = "</td></tr></table></td></tr></table>";
        // > Geöffnete Tags umwandeln.
        for($c = 1;$c <= count($this->ayCacheQuoteOpen);$c++) {
            if (count($this->ayCacheQuoteClose) == count($this->ayCacheQuoteOpen)) {
                // > Format: [quote=xxx]
                $this->quote_pattern[] = "%\[quote:" . $c . "=([^[/]*)\]%siU";
                $this->quote_replace[] = $HeaderUser . "\$1" . $FooterUser;
                // > Format: [quote]
                $this->quote_pattern[] = "%\[quote:" . $c . "\]%siU";
                $this->quote_replace[] = $Header;
                // > Format: [/quote]
                $this->quote_pattern[] = "%\[/quote:" . $c . "\]%siU";
                $this->quote_replace[] = $QuoteClose;
            } else {
                // > Format: [quote=xxx]xxx[/quote]
                $this->quote_pattern[] = "%\[quote:([0-9]*)=([^[/]*)\[/quote:([0-9]*)\]%siU";
                $this->quote_replace[] = $HeaderUser . "\$2" . $FooterUser . "\$3" . $QuoteClose;
                // > Format: [quote]xxx[/quote]
                $this->quote_pattern[] = "%\[quote:([0-9]*)\](.*)\[/quote:\\1\]%siU";
                $this->quote_replace[] = $Header . "\$2" . $QuoteClose;
            }
        }
        // > Nicht gefundene Paare wieder darstellen.
        // > Format: [quote=xxx]
        $this->quote_pattern[] = "%\[quote:([0-9]*)=([^[/]*)\]%siU";
        $this->quote_replace[] = "[quote=\$2]";
        // > Format: [quote]
        $this->quote_pattern[] = "%\[quote:([0-9])\]%siU";
        $this->quote_replace[] = "[quote]";
        // > Format: [/quote]
        $this->quote_pattern[] = "%\[/quote:([0-9])\]%siU";
        $this->quote_replace[] = "[/quote]";
        // > String parsen
        $string = preg_replace($this->quote_pattern, $this->quote_replace, $string);

        return $string;
    }
    // > Video intergration.
    protected function _video($typ, $id)
    {
        $typ = strtolower($typ);

        if ($typ == "google") {
            $str = "<embed style=\"width:" . $this->info['GoogleBreite'] . "px; height:" . $this->info['GoogleHoehe'] . "px;\" id=\"VideoPlayback\" align=\"middle\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=" . $id . "\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"" . $this->info['GoogleHintergrundfarbe'] . "\" scale=\"noScale\" salign=\"TL\" FlashVars=\"playerMode=embedded\"/>";
        }

        if ($typ == "youtube") {
            $str = "<object width=\"" . $this->info['YoutubeBreite'] . "\" height=\"" . $this->info['YoutubeHoehe'] . "\"><param name=\"movie\" value=\"http://www.youtube.com/v/" . $id . "\"></param><embed src=\"http://www.youtube.com/v/" . $id . "\" type=\"application/x-shockwave-flash\"  width=\"" . $this->info['YoutubeBreite'] . "\" height=\"" . $this->info['YoutubeHoehe'] . "\" bgcolor=\"" . $this->info['YoutubeHintergrundfarbe'] . "\"></embed></object>";
        }

        if ($typ == "myvideo") {
            $str = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"" . $this->info['MyvideoBreite'] . "\" height=\"" . $this->info['MyvideoHoehe'] . "\"><param name=\"movie\" value=\"http://www.myvideo.de/movie/" . $id . "\"></param><embed src=\"http://www.myvideo.de/movie/" . $id . "\" width=\"" . $this->info['MyvideoBreite'] . "\" height=\"" . $this->info['MyvideoHoehe'] . "\" type=\"application/x-shockwave-flash\"></embed></object>";
        }

        if ($typ == "gametrailers") {
            $str = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"  codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="gtembed" width="' . $this->info['YoutubeBreite'] . '" height="' . $this->info['YoutubeHoehe'] . '">    <param name="allowScriptAccess" value="sameDomain" />     <param name="allowFullScreen" value="true" /> <param name="movie" value="http://www.gametrailers.com/remote_wrap.php?mid=' . $id . '"/> <param name="quality" value="high" /> <embed src="http://www.gametrailers.com/remote_wrap.php?mid=' . $id . '" swLiveConnect="true" name="gtembed" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' . $this->info['YoutubeBreite'] . '" height="' . $this->info['YoutubeHoehe'] . '"></embed> </object>';
        }

        return $str;
    }
    // > Countdown berechnen.
    protected function _countdown($date, $time = null)
    {
        $date = explode(".", $date);

        if ($time != null) {
            $timechk = explode(':', $time);
            if ($timechk[0] <= 23 && $timechk[1] <= 59 && $timechk[2] <= 59) $timechk = true;
            else $timechk = false;
        } else $timechk = true;
        // > Html Design.
        $Header = "<div style=\"width:" . $this->info['CountdownTabelleBreite'] . ";padding:5px;font-family:Verdana;font-size:" . $this->info['CountdownSchriftsize'] . "px;" . $Font . "color:" . $this->info['CountdownSchriftfarbe'] . ";border:2px dotted " . $this->info['CountdownRandFarbe'] . ";text-align:center\">";
        $Footer = "</div>";
        // > Überprüfen ob die angaben stimmen.
        if ($date[0] <= 31 && $date[1] <= 12 && $date[2]/*>= date("Y")*/ && checkdate($date[1], $date[0], $date[2]) && $timechk) {
            if (isset($time)) {
                $time = explode(":", $time);
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

            $Font = ($this->info['CountdownSchriftformat'] == "bold") ? "font-wight:bold;":"font-style:" . $this->info['CountdownSchriftformat'] . ";";

            if ($Diff > 1) {
                $Tage = sprintf("%00d", ($Diff / 86400));
                $Stunden = sprintf("%00d", (($Diff - ($Tage * 86400)) / 3600));
                $Minuten = sprintf("%00d", (($Diff - (($Tage * 86400) + ($Stunden * 3600))) / 60));
                $Sekunden = ($Diff - (($Tage * 86400) + ($Stunden * 3600) + ($Minuten * 60)));
                // > Bei höheren Wert wie 1 als Mehrzahl ausgeben.
                $mzTg = ($Tage == 1) ? "":"e";
                $mzStd = ($Stunden == 1) ? "":"n";
                $mzMin = ($Minuten == 1) ? "":"n";
                $mzSek = ($Sekunden == 1) ? "":"n";
                // > Datum zusamstellen.
                $str = $Header . $Tage . " Tag" . $mzTg . ", " . $Stunden . " Stunde" . $mzStd . ", " . $Minuten . " Minute" . $mzMin . " und " . $Sekunden . " Sekunde" . $mzSek . $Footer;
            } else {
                // > Datum zusamstellen wenn Datum unmittelbar bevor steht.
                $str = $Header . (is_array($time) ? implode(':', $time) : $time) . ' ' . implode('.', $date) . " !!!" . $Footer;
            }
        } else {
            /*if($time == NULL) {
                $str = "[countdown]".implode('.',$date)."[/countdown]";
            } else {
                $str = "[countdown=".$time."]".implode('.',$date)."[/countdown]";
            }*/
            $str = $Header . "Der Countdown ist falsch definiert" . $Footer;
        }

        return $str;
    }

    protected function _ws($ws)
    {
        return $ws;
    }
    // > Flash verwerten
    protected function _flash($url, $options)
    {
        $width = $this->info['FlashBreite'];
        $height = $this->info['FlashHoehe'];
        if (!empty($options)) {
            $options = explode(' ', $options);
            foreach ($options as $option) {
                $tmp = 0;
                list($name, $value) = explode('=', $option);
                if ($name == 'width') {
                    $tmp = substr($value, 2, - 2);
                    if ($tmp < $width) {
                        $width = $tmp;
                    }
                } elseif ($name == 'height') {
                    $tmp = substr($value, 2, - 2);
                    if ($tmp < $height) {
                        $height = $tmp;
                    }
                }
            }
        }
        return '<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' . $width . '" height="' . $height . '"' .
        'codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=7,0,0,0" class="bbcode_flash">' .
        '<param name="movie" value="' . $url . '">' .
        '<param name="quality" value="high">' .
        '<param name="scale" value="exactfit">' .
        '<param name="menu" value="true">' .
        '<param name="bgcolor" value="' . $this->info['FlashHintergrundfarbe'] . '"> ' .
        '<embed src="' . $url . '" quality="high" scale="exactfit" menu="false" ' .
        'bgcolor="' . $this->info['FlashHintergrundfarbe'] . '" width="' . $width . '" height="' . $height . '" swLiveConnect="false" ' .
        'type="application/x-shockwave-flash" ' .
        'pluginspage="http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">' .
        '</embed>' .
        '</object>';
    }

    public function onlySmileys($string, $maxLength)
    {
        // Optionen setzen
        if ($maxLength != 0) {
            $resetMaxLength = $this->info['fnWortMaxLaenge'];
            $this->info['fnWortMaxLaenge'] = $maxLength;
        }

        $string = $this->_shortwords($string);
        $string = nl2br($string);
        $string = $this->_smileys($string);
        // Optionen rückgängig machen
        if (isset($resetMaxLength)) {
            $this->info['fnWortMaxLaenge'] = $resetMaxLength;
        }

        return $string;
    }

    public function parse($string, $maxLength = 0, $maxImgWidth = 0, $maxImgHeight = 0)
    {
        // Optionen setzen
        if ($maxLength != 0) {
            $resetMaxLength = $this->info['fnWortMaxLaenge'];
            $this->info['fnWortMaxLaenge'] = $maxLength;
        }
        if ($maxImgWidth != 0) {
            $resetMaxImgWidth = $this->info['fnImgMaxBreite'];
            $this->info['fnImgMaxBreite'] = $maxImgWidth;
        }
        if ($maxImgHeight != 0) {
            $resetMaxImgHeight = $this->info['fnImgMaxBreite'];
            $this->info['fnImgMaxBreite'] = $maxImgHeight;
        }
        // > Die Blocks werden codiert um sie vor dem restlichen parsen zu schützen.
        if ($this->permitted['php'] == true) {
            $string = preg_replace("%\[php\](.+)\[\/php\]%esiU", "\$this->encode_codec('\$1','php')", $string);
            $string = preg_replace("%\[php=(.*)\](.+)\[\/php\]%esiU", "\$this->encode_codec('\$2','php','\$1')", $string);
        }

        if ($this->permitted['html'] == true) {
            $string = preg_replace("%\[html\](.+)\[\/html\]%esiU", "\$this->encode_codec('\$1','html')", $string);
            $string = preg_replace("%\[html=(.*)\](.+)\[\/html\]%esiU", "\$this->encode_codec('\$2','html','\$1')", $string);
        }

        if ($this->permitted['css'] == true) {
            $string = preg_replace("%\[css\](.+)\[\/css\]%esiU", "\$this->encode_codec('\$1','css')", $string);
            $string = preg_replace("%\[css=(.*)\](.+)\[\/css\]%esiU", "\$this->encode_codec('\$2','css','\$1')", $string);
        }

        if ($this->permitted['code'] == true) {
            $string = preg_replace("%\[code\](.+)\[\/code\]%esiU", "\$this->encode_codec('\$1','code')", $string);
            $string = preg_replace("%\[code=(.*)\](.+)\[\/code\]%esiU", "\$this->encode_codec('\$2','code','\$1')", $string);
        }

        if ($this->permitted['list'] == true) {
            $string = preg_replace("%\[list\](.+)\[\/list\]%esiU", "\$this->encode_codec('\$1','list')", $string);
        }
        // > Badwors Filtern.
        $string = $this->_badwords($string);
        // > BB Code der den Codeblock nicht betrifft.
        // > Überprüfen ob die wörter nicht die maximal länge überschrieten.
        $string = $this->_shortwords($string);
        // $string = htmlentities($string);
        $string = nl2br($string);

        if ($this->permitted['url'] == true) {
            if ($this->permitted['autourl'] == true) {
                // > Format: www.xxx.de
                $this->pattern[] = "%(( |\n|^)(www.[a-zA-Z\-0-9@:\%_\+.~#?&//=,;]+?))%eUi";
                $this->replace[] = "\$this->_ws('\$2').\$this->_shorturl('\$3')";
                // > Format: http://www.xxx.de
                $this->pattern[] = "%(( |\n|^)((http|https|ftp)://{1}[a-zA-Z\-0-9@:\%_\+.~#?&//=,;]+?))%eUi";
                $this->replace[] = "\$this->_ws('\$2').\$this->_shorturl('\$3')";
                // > Format xxx@xxx.de
                $this->pattern[] = "%(\s|^)([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})%i";
                $this->replace[] = "<a href=\"mailto:$2\">$2</a>";
            }
            // > Format: [url=xxx]xxx[/url]
            $this->pattern[] = "%\[url=([^\]]*)\](.+)\[\/url\]%eUis";
            $this->replace[] = "\$this->_shorturl('\$1','\$2')";
            // > Format: [url]xxx[/url]
            $this->pattern[] = "%\[url\](.+)\[\/url\]%esiU";
            $this->replace[] = "\$this->_shorturl('\$1')";
        }
        // > Darf BB Code [MAIL] dekodiert werden?
        if ($this->permitted['email'] == true) {
            // > Format: [mail]xxx@xxx.de[/mail]
            $this->pattern[] = "%\[mail\]([_\.0-9a-z-]+\@([0-9a-z\-]+)\.[a-z]{2,3})\[\/mail\]%Uis";
            $this->replace[] = "<a href=\"mailto:$1\">$1</a>";
            // > Format: [mail=xxx@xxx.de]xxx[/mail]
            $this->pattern[] = "%\[mail=([_\.0-9a-z-]+\@([0-9a-z\-]+)\.[a-z]{2,3})\](.+)\[\/mail\]%Uis";
            $this->replace[] = "<a href=\"mailto:$1\">$3</a>";
        }
        // > Darf BB Code [B] dekodiert werden?
        if ($this->permitted['b'] == true) {
            // > Format: [b]xxx[/b]
            $this->pattern[] = "%\[b\](.+)\[\/b\]%Uis";
            $this->replace[] = "<b>\$1</b>";
        }
        // > Darf BB Code [I] dekodiert werden?
        if ($this->permitted['i'] == true) {
            // > Format: [i]xxx[/i]
            $this->pattern[] = "%\[i\](.+)\[\/i\]%Uis";
            $this->replace[] = "<i>\$1</i>";
        }
        // > Darf BB Code [U] dekodiert werden?
        if ($this->permitted['u'] == true) {
            // > Format: [u]xxx[/u]
            $this->pattern[] = "%\[u\](.+)\[\/u\]%Uis";
            $this->replace[] = "<u>\$1</u>";
        }
        // > Darf BB Code [S] dekodiert werden?
        if ($this->permitted['s'] == true) {
            // > Format: [s]xxx[/s]
            $this->pattern[] = "%\[s\](.+)\[\/s\]%Uis";
            $this->replace[] = "<strike>\$1</strike>";
        }
        // ##############################################
        // > Darf BB Code [LEFT] dekodiert werden?
        if ($this->permitted['left'] == true) {
            // > Format: [left]xxx[/left]
            $this->pattern[] = "%\[left\](.+)\[\/left\]%Uis";
            $this->replace[] = "<div align=\"left\">\$1</div>";
        }
        // > Darf BB Code [CENTER] dekodiert werden?
        if ($this->permitted['center'] == true) {
            // > Format: [center]xxx[/center]
            $this->pattern[] = "%\[center\](.+)\[\/center\]%Uis";
            $this->replace[] = "<div align=\"center\">\$1</div>";
        }
        // > Darf BB Code [RIGHT] dekodiert werden?
        if ($this->permitted['right'] == true) {
            // > Format: [right]xxx[/right]
            $this->pattern[] = "%\[right\](.+)\[\/right\]%Uis";
            $this->replace[] = "<div align=\"right\">\$1</div>";
        }
        // ##############################################
        // > Darf BB Code [EMPH] dekodiert werden?
        if ($this->permitted['emph'] == true) {
            // > Format: [emph]xxx[/emph]
            $this->pattern[] = "%\[emph\](.+)\[\/emph\]%Uis";
            $this->replace[] = "<span style=\"background-color:" . $this->info['EmphHintergrundfarbe'] . ";color:" . $this->info['EmphSchriftfarbe'] . ";\">$1</span>";
        }
        // > Darf BB Code [COLOR] dekodiert werden?
        if ($this->permitted['color'] == true) {
            // > Format: [color=#xxxxxx]xxx[/color]
            $this->pattern[] = "%\[color=(#{1}[0-9a-zA-Z]+?)\](.+)\[\/color\]%Uis";
            $this->replace[] = "<font color=\"$1\">$2</font>";
        }
        // > Darf BB Code [SIZE] dekodiert werden?
        if ($this->permitted['size'] == true) {
            // > Format: [size=xx]xxx[/size]
            $this->pattern[] = "%\[size=([0-9]+?)\](.+)\[\/size\]%eUis";
            $this->replace[] = "\$this->_size('\$1','\$2')";
        }
        // > Darf BB Code [KTEXT] decodiert werden?
        if ($this->permitted['ktext'] == true) {
            // > Format: [ktext=xxx]
            $this->pattern[] = "%\[ktext=([^[/]*)\]%esiU";
            $this->replace[] = "\$this->_addKtextOpen('\\1')";
            // > Format: [/ktext]
            $this->pattern[] = "%\[/ktext\]%esiU";
            $this->replace[] = "\$this->_addKtextClose()";
        }
        // > Darf BB Code [IMG] dekodiert werden?
        if ($this->permitted['img'] == true) {
            // > Format: [img]xxx.de[/img]
            $this->pattern[] = "%\[img\]([-a-zA-Z0-9@:\%_\+,.~#?&//=]+?)\[\/img\]%eUi";
            $this->replace[] = "\$this->_img('\$1')";
            // > Format: [img=left|right]xxx.de[/img]
            $this->pattern[] = "%\[img=(left|right)\]([-a-zA-Z0-9@:\%_\+,.~#?&//=]+?)\[\/img\]%eUi";
            $this->replace[] = "\$this->_img('\$2','\$1')";
        }
        // > Darf BB Code [SCREENSHOT] dekodiert werden?
        if ($this->permitted['screenshot'] == true) {
            // > Format: [shot]xxx.de[/screenshot]
            $this->pattern[] = "%\[shot\]([-a-zA-Z0-9@:\%_\+.~#?&//=]+?)\[\/shot\]%eUi";
            $this->replace[] = "\$this->_screenshot('\$1')";
            // > Format: [shot=left|right]xxx.de[/screenshot]
            $this->pattern[] = "%\[shot=(left|right)\]([-a-zA-Z0-9@:\%_\+.~#?&//=]+?)\[\/shot\]%eUi";
            $this->replace[] = "\$this->_screenshot('\$2','\$1')";
        }
        // > Farf BB Code [VIDEO] dekodiert werden?
        if ($this->permitted['video'] == true) {
            // > Format: [video=xxx]xxx[/video]
            $this->pattern[] = "%\[video=(google|youtube|myvideo|gametrailers)\](.+)\[\/video\]%eUis";
            $this->replace[] = "\$this->_video('\$1','\$2')";
        }
        // > Darf BB Code [COUNTDOWN] dekodiert werden?
        if ($this->permitted['countdown'] == true) {
            // > Format: [countdown=Std:Min:Sek]TT.MM.JJJJ[/countdown]
            $this->pattern[] = "%\[countdown=(([0-9]{2}):([0-9]{2}):([0-9]{2}))\](([0-9]{2})\.([0-9]{2})\.([0-9]{4}))\[\/countdown\]%eUis";
            $this->replace[] = "\$this->_countdown('\$5','\$1')";
            // > Format: [countdown]TT.MM.JJJJ[/countdown]
            $this->pattern[] = "%\[countdown\](([0-9]{2})\.([0-9]{2})\.([0-9]{4}))\[\/countdown\]%eUis";
            $this->replace[] = "\$this->_countdown('\$1')";
        }
        // ##############################################
        // > Darf BB Code [QUOTE] dekodiert werden?
        if ($this->permitted['quote'] == true) {
            // > Format: [quote]
            $this->pattern[] = "%\[quote\]%esiU";
            $this->replace[] = "\$this->_addQuoteOpen()";
            // > Format: [quote=xxx]
            $this->pattern[] = "%\[quote=([^[/]*)\]%esiU";
            $this->replace[] = "\$this->_addQuoteOpen('\\1')";
            // > Format: [/quote]
            $this->pattern[] = "%\[/quote\]%esiU";
            $this->replace[] = "\$this->_addQuoteClose()";
        }
        // > Darf BB Code [FLASH] dekodiert werden?
        if ($this->permitted['flash'] == true) {
            // > Format: [flash]*[/flash]
            $this->pattern[] = "%\[flash(( \w+=\'\d+\')*)]((http|https|ftp)://[a-z-0-9@:\%_\+.~#\?&/=,;]+)\[/flash]%ie";
            $this->replace[] = '$this->_flash("$3", trim("$1"));';
        }
        // > String parsen
        $string = preg_replace($this->pattern, $this->replace, $string);
        // > Darf BB Code [QUOTE] dekodiert werden?
        if ($this->permitted['quote'] == true) {
            $string = $this->_quote($string);
        }
        // > Darf BB Code [KTEXT] decodiert werden?
        if ($this->permitted['ktext'] == true) {
            $string = $this->_ktext($string);
        }
        // > Smilies Filtern.
        $string = $this->_smileys($string);
        // > Zum schluss die blöcke die verschlüsselt wurden wieder entschlüsseln und Parsen.
        if ($this->permitted['php'] == true) {
            $string = preg_replace("%\[php\](.+)\[\/php\]%esiU", '$this->_phpblock("$1")', $string);
            $string = preg_replace("%\[php=([^;]*);(\d+)\](.+)\[\/php\]%esiU", 'this->_phpblock("$3","$1","$2")', $string);
            $string = preg_replace("%\[php=(.*)\](.+)\[\/php\]%esiU", '$this->_phpblock("$2","$1")', $string);
        }

        if ($this->permitted['html'] == true) {
            $string = preg_replace("%\[html\](.+)\[\/html\]%esiU", "\$this->_htmlblock('\$1')", $string);
            $string = preg_replace("%\[html=([^;]*);(\d+)\](.+)\[\/html\]%esiU", "\$this->_htmlblock('\$3','\$1','\$2')", $string);
            $string = preg_replace("%\[html=(.*)\](.+)\[\/html\]%esiU", "\$this->_htmlblock('\$2','\$1')", $string);
        }

        if ($this->permitted['css'] == true) {
            $string = preg_replace("%\[css\](.+)\[\/css\]%esiU", "\$this->_cssblock('\$1')", $string);
            $string = preg_replace("%\[css=([^;]*);(\d+)\](.+)\[\/css\]%esiU", "\$this->_cssblock('\$3','\$1','\$2')", $string);
            $string = preg_replace("%\[css=(.*)\](.+)\[\/css\]%esiU", "\$this->_cssblock('\$2','\$1')", $string);
        }

        if ($this->permitted['code'] == true) {
            $string = preg_replace("%\[code\](.+)\[\/code\]%esiU", "\$this->_codeblock('\$1')", $string);
            $string = preg_replace("%\[code=([^;]*);(\d+)\](.+)\[\/code\]%esiU", "\$this->_codeblock('\$3','\$1','\$2')", $string);
            $string = preg_replace("%\[code=(.*)\](.+)\[\/code\]%esiU", "\$this->_codeblock('\$2','\$1')", $string);
        }

        if ($this->permitted['list'] == true) {
            $string = preg_replace("%\[list\](.+)\[\/list\]%esiU", "\$this->_list('\$1')", $string);
        }
        // Reset Arrays
        $this->pattern = array();
        $this->replace = array();
        $this->ayCacheQuoteOpen = array();
        $this->ayCacheQuoteClose = array();
        $this->ayCacheKtextOpen = array();
        $this->ayCacheKtextClose = array();
        // Optionen rückgängig machen
        if (isset($resetMaxLength)) {
            $this->info['fnWortMaxLaenge'] = $resetMaxLength;
        }
        if (isset($resetMaxImgWidth)) {
            $this->info['fnImgMaxBreite'] = $resetMaxImgWidth;
        }
        if (isset($resetMaxImgHeight)) {
            $this->info['fnImgMaxBreite'] = $resetMaxImgHeight;
        }

        return $string;
    }
}

?>