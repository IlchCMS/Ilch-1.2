<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
// #
// ##
// ###
// ####  W E I T E R L E I T U N G S   F U N K T I O N
function wd($wdLINK, $wdTEXT, $wdZEIT = 3) {
    global $lang;

    if (!is_array($wdLINK)) {
        $urls = '<a href="' . $wdLINK . '">' . $lang[ 'forward2' ] . '</a>';
        $wdURL = $wdLINK;
    } else {
        $urls = '';
        $i = 0;
        foreach ($wdLINK as $k => $v) {
            if ($i == 0) {
                $wdURL = $v;
            }
            $urls .= '<a href="' . $v . '">' . $k . '</a><br />';
            $i++;
        }
    }
    $tpl = new tpl('weiterleitung.htm');
    $ar = array(
        'LINK' => $urls,
        'URL' => $wdURL,
        'ZEIT' => $wdZEIT,
        'TEXT' => $wdTEXT
        );
    $tpl->set_ar_out($ar, 0);
    unset($tpl);
}
// #
// ##
// ###
// #### g e t   R e c h t
function getrecht($RECHT, $USERRECHT) {
    if (empty($USERRECHT)) {
        return (false);
    } else {
        if ($USERRECHT <= $RECHT) {
            return (true);
        } else {
            return (false);
        }
    }
}
// #
// ##
// ###
// #### g e t   U s e r   N a m e
function get_n($uid) {
    $row = db_fetch_object(db_query("SELECT `name` FROM `prefix_user` WHERE `id` = '" . $uid . "'"));
    return $row->name;
}
// #
// ##
// ###
// #### wochentage sonntag 0 samstag 6
function wtage($tag) {
    $wtage = array(
        'Sonntag',
        'Montag',
        'Dienstag',
        'Mittwoch',
        'Donnerstag',
        'Freitag',
        'Samstag'
        );
    return ($wtage[ $tag ]);
}
// #
// ##
// ###
// #### monate in deutsch
function getDmon($mon) {
    $monate = array(
        'Januar',
        'Februar',
        'M&auml;rz',
        'April',
        'Mai',
        'Juni',
        'Juli',
        'August',
        'September',
        'Oktober',
        'November',
        'Dezember'
        );
    return ($monate[ $mon - 1 ]);
}
// #
// ##
// ###
// #### a l l g e m e i n e s   A r r a y
function getAllgAr() {
    // v1 = schluessel
    // v2 = wert
    // v3 = feldtyp
    // v4 = kurze beschreibung wenn n�tig
    $ar = array();
    $abf = "SELECT `schl`, `wert` FROM `prefix_config`";
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        $ar[ $row[ 'schl' ] ] = $row[ 'wert' ];
    }
    return $ar;
}
// #
// ##
// ###
// #### UserRang ermitteln
function userrang($post, $uid) {
    global $global_user_rang_array;

    if (!isset($global_user_rang_array[ $uid ])) {
        if (!isset($global_user_rang_array)) {
            $global_user_rang_array = array();
        }
        if (empty($uid)) {
            $rRang = 'Gast';
        } else {
            $rRang = @db_result(db_query("SELECT `bez` FROM `prefix_user` LEFT JOIN `prefix_ranks` ON `prefix_ranks`.`id` = `prefix_user`.`spezrank` WHERE `prefix_user`.`id` = " . $uid), 0);
        }
        if (empty($rRang)) {
            $post = ($post == 0 ? 1 : $post);
            $rRang = @db_result(db_query("SELECT `bez` FROM `prefix_ranks` WHERE `spez` = 0 AND `min` <= " . $post . " ORDER BY `min` DESC LIMIT 1"), 0);
        } elseif ($rRang != 'Gast') {
            $rRang = '<i><b>' . $rRang . '</b></i>';
        }
        $global_user_rang_array[ $uid ] = $rRang;
    }

    return ($global_user_rang_array[ $uid ]);
}
// #
// ##
// ###
// #### makiert suchwoerter
function markword($text, $such) {
    $erg = '<span style="background-color: #EBF09B;">';
    $erg .= $such . "</span>";
    $text = str_replace($such, $erg, $text);
    return $text;
}
// #
// ##
// ###
// #### gibt die smiley liste zurueck
function getsmilies($zeilen = 3) {
    global $lang;
    $i = 0;
    $b = '<script language="JavaScript" type="text/javascript">function moreSmilies () { var x = window.open("about:blank", "moreSmilies", "width=250,height=200,status=no,scrollbars=yes,resizable=yes"); ';
    $a = '';
    $erg = db_query('SELECT `emo`, `ent`, `url` FROM `prefix_smilies` ORDER BY `pos` ASC');
    if(@mysql_num_rows($erg)){
      while ($row = db_fetch_object($erg)) {
          $b .= 'x.document.write ("<a href=\"javascript:opener.put(\'' . addslashes(addslashes($row->ent)) . '\')\">");';
          $b .= 'x.document.write ("<img style=\"border: 0px; padding: 5px;\" src=\"include/images/smiles/' . $row->url . '\" title=\"' . $row->emo . '\"></a>");';
  
          if ($i < 12) {
              // float einbauen
              if ($i % $zeilen == 0 AND $i != 0) {
                  $a .= '<br /><br />';
              }
              $a .= '<a href="javascript:put(\'' . addslashes($row->ent) . '\')">';
              $a .= '<img style="margin: 2px;" src="include/images/smiles/' . $row->url . '" border="0" title="' . $row->emo . '"></a>';
          }
          $i++;
      }
    }
    $b .= ' x.document.write("<br /><br /><center><a href=\"javascript:window.close();\">' . $lang[ 'close' ] . '</a></center>"); x.document.close(); }</script>';
    if ($i > 12) {
        $a .= '<br /><br /><center><a href="javascript:moreSmilies();">' . $lang[ 'more' ] . '</a></center>';
    }
    $a = $b . $a;
    return ($a);
}
// #
// ##
// ###
// #### generey key with x length
function genkey($anz) {
    $letterArray = array(
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0'
        );
    $key = '';
    for ($i = 0; $i < $anz; $i++) {
        mt_srand((double) microtime() * 1000000);
        $zufallZahl = mt_rand(0, 62);
        $key .= $letterArray[ $zufallZahl ];
    }
    return ($key);
}

function icmail($mail, $bet, $txt, $from = '', $html = false) {
    global $allgAr;
    include_once('include/includes/libs/phpmailer/class.phpmailer.php');
    $mailer = new PHPMailer();
    if (empty($from)) {
        $mailer->From = $allgAr[ 'adminMail' ];
        $mailer->FromName = $allgAr[ 'allg_default_subject' ];
    } elseif (preg_match('%(.*) <([\w\.-]*@[\w\.-]*)>%i', $from, $tmp)) {
        $mailer->From = trim($tmp[ 2 ]);
        $mailer->FromName = trim($tmp[ 1 ]);
    } elseif (preg_match('%([\w\.-]*@[\w\.-]*)%i', $from, $tmp)) {
        $mailer->From = trim($tmp[ 1 ]);
        $mailer->FromName = '';
    }
    if ($allgAr[ 'mail_smtp' ]) { // SMTP Versand
        $smtpser = @db_result(db_query('SELECT `t1` FROM `prefix_allg` WHERE `k` = "smtpconf"'));
        if (empty($smtpser)) {
            echo '<span style="font-size: 2em; color: red;">Mailversand muss konfiguriert werden!</span><br />';
        } else {
            $smtp = unserialize($smtpser);

            $mailer->IsSMTP();
            $mailer->Host = $smtp[ 'smtp_host' ];
            $mailer->SMTPAuth = ($smtp[ 'smtp_auth' ] == 'no' ? false : true);
            if ($smtp[ 'smtp_auth' ] == 'ssl' or $smtp[ 'smtp_auth' ] == 'tls') {
                $mailer->SMTPSecure = $smtp[ 'smtp_auth' ];
            }
            if (!empty($smtp[ 'smtp_port' ])) {
                $mailer->Port = $smtp[ 'smtp_port' ];
            }
            $mailer->AddReplyTo($mailer->From, $mailer->FromName);

            if ($smtp[ 'smtp_changesubject' ] and $mailer->From != $smtp[ 'smtp_email' ]) {
                $bet = '(For ' . $mailer->FromName . ' - ' . $mailer->From . ') ' . $bet;
                $mailer->From = $smtp[ 'smtp_email' ];
            }

            $mailer->Username = $smtp[ 'smtp_login' ];

            require_once('include/includes/libs/AzDGCrypt.class.inc.php');
            $cr64 = new AzDGCrypt(DBDATE . DBUSER . DBPREF);
            $mailer->Password = $cr64->decrypt($smtp[ 'smtp_pass' ]);

            if ($smtp[ 'smtp_pop3beforesmtp' ] == 1) {
                include_once('include/includes/libs/phpmailer/class.pop3.php');
                $pop = new POP3();
                $pop3port = !empty($smpt[ 'smtp_pop3port' ]) ? $smpt[ 'smtp_pop3port' ] : 110;
                $pop->Authorise($smpt[ 'smtp_pop3host' ], $pop3port, 5, $mailer->Username, $mailer->Password, 1);
            }
        }
        // $mailer->SMTPDebug = true;
    }
    if (is_array($mail)) {
        if ($mail[ 0 ] != 'bcc') {
            array_shift($mail);
            foreach ($mail as $m) {
                $mailer->AddBCC(escape_for_email($m));
            }
            $mailer->AddAddress($mailer->From);
        } else {
            foreach ($mail as $m) {
                $mailer->AddAddress(escape_for_email($mail));
            }
        }
    } else {
        $mailer->AddAddress(escape_for_email($mail));
    }
    $mailer->Subject = escape_for_email($bet, true);
    $txt = str_replace("\r", "\n", str_replace("\r\n", "\n", $txt));
    if ($html) {
        $mailer->IsHTML(true);
        $mailer->AltBody = strip_tags($txt);
    }
    $mailer->Body = $txt;

    if ($mailer->Send()) {
        return true;
    } else {
        if (is_coadmin()) {
            echo "<h2 style=\"color:red;\">Mailer Error: " . $mailer->ErrorInfo . '</h2>';
        }
        return false;
    }
}

function html_enc_substr($text, $start, $length) {
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return (htmlentities(substr(strtr($text, $trans_tbl), $start, $length)));
}

function get_datum($d) {
    if (strpos($d, '.') !== false) {
        $d = str_replace('.', '-', $d);
    }
    if (strpos($d, '/') !== false) {
        $d = str_replace('/', '-', $d);
    }
    if (is_numeric(substr($d, - 4))) {
        list($t, $m, $j) = explode('-', $d);
    } elseif (is_numeric(substr($d, 0, 4))) {
        list($j, $m, $t) = explode('-', $d);
    }
    $d = $j . '-' . $m . '-' . $t;
    return ($d);
}

function get_homepage($h) {
    $h = trim($h);
    if (!empty($h) AND substr($h, 0, 7) != 'http://') {
        $h = 'http://' . $h;
    }
    return ($h);
}

function get_wargameimg($img) {
    if (file_exists('include/images/wargames/' . $img . '.gif')) {
        return ('<img src="include/images/wargames/' . $img . '.gif" alt="' . $img . '" border="0">');
    } elseif (file_exists('include/images/wargames/' . $img . '.jpg')) {
        return ('<img src="include/images/wargames/' . $img . '.jpg" alt="' . $img . '" border="0">');
    } elseif (file_exists('include/images/wargames/' . $img . '.jpeg')) {
        return ('<img src="include/images/wargames/' . $img . '.jpeg" alt="' . $img . '" border="0">');
    } elseif (file_exists('include/images/wargames/' . $img . '.png')) {
        return ('<img src="include/images/wargames/' . $img . '.png" alt="' . $img . '" border="0">');
    }
    return ('');
}

function iurlencode_help($a) {
    if (preg_match("/(http:|https:|ftp:)/", $a[ 0 ])) {
        return ($a[ 0 ]);
    }

    return (rawurlencode($a[ 1 ]) . substr($a[ 0 ], - 1));
}

function iurlencode($s) {
    return (preg_replace_callback("/([^\/]+|\/[^\.])[\.\/]/", 'iurlencode_help', $s));
    /*
    $x = 'false';
    if (preg_match ('/(http:|https:|ftp:)[^:]+:[^@]+@./', $s)) {
    $x = preg_replace('/([^:]+:[^@]+@)./',"\\1",$s);
    $s = str_replace($x,'',$s);
    } elseif (substr($s, 0, 7) == 'http://') {
    $s = substr ($s, 7);
    $x = 'http://';
    } elseif (substr($s, 0, 8) == 'https://') {
    $s = substr ($s, 8);
    $x = 'https://';
    } elseif (substr($s, 0, 6) == 'ftp://') {
    $s = substr ($s, 6);
    $x = 'ftp://';
    }


    $a = explode('/', $s);
    $r = '';
    for ($i=0;$i<count($a);$i++) {
    $r .= rawurlencode($a[$i]).'/';
    }

    if ($x !== 'false') {
    $r = $x.$r;
    }

    $r = substr($r, 0, -1);
    return ($r);
    */
}
// #
// ##
// ###
// #### antispam
function chk_antispam($m) {
    global $allgAr;

    if (is_numeric($allgAr[ 'antispam' ]) AND has_right($allgAr[ 'antispam' ])) {
        return (true);
    }
    $captcha = true;
    if ($captcha) {
        include_once 'include/includes/libs/captcha/captcha.php';
        $controller = new Captcha();
    }
    if ($captcha && !($controller->isValid(htmlentities($_POST[ 'number' ])))) {
        return (false);
    }
    return (true);
}

function get_antispam($m, $t) {
    global $allgAr;

    if (is_numeric($allgAr[ 'antispam' ]) AND has_right($allgAr[ 'antispam' ])) {
        return ('');
    }

    $rs = '<img class="Custom" src="include/includes/libs/captcha/captchaimg.php" alt="captchaimg" title="::Geben Sie diese Zeichen in das direkt darunter stehende Feld ein.">&nbsp;<input name="number" type="text" maxlength="5" size="8">';
    if ($t == 0) {
        return ('<img class="Custom" src="include/includes/libs/captcha/captchaimg.php" alt="captchaimg" title="::Geben Sie diese Zeichen in das direkt darunter stehende Feld ein."><br/><input name="number" type="text" maxlength="5" size="8">');
    } elseif ($t == 1) {
        return ('<tr><td class="Cmite" v><b>Antispam</b></td><td class="Cnorm">' . $rs . '</td></tr>');
    } elseif ($t > 10) {
        return ('<label style="float:left; width: ' . $t . 'px; ">Antispam</label>' . $rs . '<br/>');
    } else {
        return ('');
    }
}
// Funktion, die die Größe aller Dateien im Ordner zusammenrechnet
function dirsize($dir) {
    if (!is_dir($dir)) {
        return - 1;
    }
    $size = 0;
    $files = array_slice(scandir($dir), 2);
    foreach ($files as $filenr => $file) {
        if (is_dir($dir . $file)) {
            $size += dirsize($dir . $file . '/');
        } else {
            $size += @filesize($dir . $file);
        }
    }
    return $size;
}
// Rechnet bytes in KB oder MB um
function nicebytes($bytes) {
    if ($bytes < 1000000) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return round($bytes / (1024 * 1024), 2) . ' MB';
    }
}
// Alle Buchstaben in kleine Buchstaben umwandeln
function get_lower($value) {
    if (is_array($value)) {
        foreach ($value as $key => $wert) {
            $array[ $key ] = get_lower($wert);
        }
        return $array;
    } else {
        return strtolower($value);
    }
}

/**
 * Gib alle Dateien des angegeben Ordners mit der gegebenen Endung uas
 *
 * @param  $dir der Ordner (z.B. "include/admin")
 * @param  $ext die Extension (z.B. "php" oder array("php","html","htm"))
 * @param  $sExt gibt die Dateierweiterung mit aus
 * @param  $sDir gibt das Verzeichnis mit aus
 */
function read_ext($dir, $ext = '', $sExt = 1, $sDir = 0) {
    $buffer = Array();
    if (!is_array($ext)) {
        $ext = Array($ext
            );
    }
    $open = opendir($dir);
    while ($file = readdir($open)) {
        $file_info = pathinfo($file);
        if (substr($file, 0, 1) != "." AND !is_dir($dir . '/' . $file) AND (in_array($file_info[ "extension" ], $ext) OR empty($ext))) {
            if ($sExt == 0) {
                $file = basename($dir . '/' . $file, '.' . $file_info[ "extension" ]);
            }
            if ($sDir == 1) {
                $file = $dir . '/' . $file;
            }
            $buffer[ ] = $file;
        }
    }
    closedir($open);
    return ($buffer);
}

/**
 * Alle Keys, die in $array2 vorhanden sind, aber nicht in $array1,
 * werden in $array1 gesetzt.
 * TODO: genauer recherchieren, ob es nicht schon eine entsprechende funktion gibt
 *
 * @param  $array1 the array to set the missing keys
 * @param  $array2 array zum auffüllen von array1
 */
function array_set_missing_keys($array1, $array2) {
    foreach($array2 as $key => $value) {
        if (!isset($array1[$key])) {
            $array1[$key] = $value;
        }
    }
    return $array1;
}

/**
 * getSiteURL()
 * Gibt die URL der Seite zurück, um z.B. Links zu erstellen
 *
 * @param boolean $endslash URL mit abschließendem Slash
 * @return string URL der Seite
 */
function getSiteURL($endslash = true)
{
    $site = 'http://' . $_SERVER['HTTP_HOST'];
    $dir = dirname($_SERVER['SCRIPT_NAME']);
    if (strlen($dir) == 1) {
        if ($endslash) {
            $site .= '/';
        }
    } else {
        $site .= $dir . ($endslash ? '/' : '');
    }
    return $site;
}
?>