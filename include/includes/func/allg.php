<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

/**
 * Funktion die eine Meldung und verschiedene Links ausgibt und zu einem nach einer bestimmten Zeit weiterleitet
 * 
 * @global array $lang
 * @param string|array $wdLINK Link oder Array von Links, bei array(beschreibung => url)
 * @param string $wdTEXT Textmeldung für Weiterleitung
 * @param integer $wdZEIT Zeit in Sekunden nach der automatisch auf ersten Link weitergeleitet wird
 */
function wd($wdLINK, $wdTEXT, $wdZEIT = 3)
{
    global $lang;

    if (!is_array($wdLINK)) {
        $urls = '<a href="' . $wdLINK . '">' . $lang['forward2'] . '</a>';
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

/**
 * Vergleicht erforderliches Recht, und übergebenes Recht, wenn übergebenes Recht ausreicht gibt die Funktion true zurück
 * 
 * @param integer $RECHT erforderliches Recht
 * @param integer $USERRECHT Recht des angemeldeten Benutzers
 * @return boolean
 */
function getrecht($RECHT, $USERRECHT)
{
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

/**
 * Benutzernamen anhand der UserId ermitteln, sollte nur für einzelne Abfragen verwendet werden
 * 
 * @param integer $uid
 * @return string Benuztername oder leerer String, wenn nicht gefunden
 */
function get_n($uid)
{
    $qry = db_query('SELECT `name` FROM `prefix_user` WHERE `id` = ' . intval($uid));
    if (db_num_rows($qry)) {
        return db_result($qry, 0);
    }
    return '';
}

/**
 * Gibt den deutschen Wochentag zurück
 * 
 * @param integer $tag Tag der Woche von 0 Sonntag bis 6 Samstags
 * @return string
 */
function wtage($tag)
{
    $wtage = array(
            'Sonntag',
            'Montag',
            'Dienstag',
            'Mittwoch',
            'Donnerstag',
            'Freitag',
            'Samstag'
    );
    return ($wtage[$tag]);
}

/**
 * Gibt den deutschen Monatsnamen zurück
 * 
 * @param integer $mon 1 Januar bis 12 Dezember
 * @return string
 */
function getDmon($mon)
{
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
    return ($monate[$mon - 1]);
}

/**
 * Gibt relativen Tag (Heute, Gestern) oder Datum an
 * 
 * @param integer $posttime Unix-Timestamp
 * @param boolean $sec obsolete
 * @return string
 */
function post_date($posttime, $sec = false)
{
    if (!empty($posttime)) {
        $akttime = time();
        $jahr_jetzt = date("y", $akttime);
        $jahr_post = date("y", $posttime);
        $tag_jetzt = date("z", $akttime);
        $tag_post = date("z", $posttime);
        if ($sec == true) {
            if ($tag_post == $tag_jetzt and $jahr_post == $jahr_jetzt) {
                return( "Heute um " . date("G:i:s", $posttime) . " Uhr" );
            } elseif ($tag_post == $tag_jetzt - 1 and $jahr_post == $jahr_jetzt) {
                return( "Gestern um " . date("G:i:s", $posttime) . " Uhr" );
            } else {
                return( "Am " . date("j.n.Y \u\m G:i:s", $posttime) . " Uhr" );
            }
        } else {
            if ($tag_post == $tag_jetzt and $jahr_post == $jahr_jetzt) {
                return( "Heute um " . date("G:i", $posttime) . " Uhr" );
            } elseif ($tag_post == $tag_jetzt - 1 and $jahr_post == $jahr_jetzt) {
                return( "Gestern um " . date("G:i", $posttime) . " Uhr" );
            } else {
                return( "Am " . date("j.n.Y \u\m G:i", $posttime) . " Uhr" );
            }
        }
    } else {
        return( '' );
    }
}

/**
 * Gibt Konfigurationsoptionen (prefix_config) als Array in Form schl => wert zurück
 * 
 * @return array
 */
function getAllgAr()
{
    $ar = array();
    $abf = "SELECT `schl`, `wert` FROM `prefix_config`";
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        $ar[$row['schl']] = $row['wert'];
    }
    return $ar;
}

/**
 * Ermittelt den Rang des Users (Spezialrang oder anhand der Anzahl von Posts)
 * 
 * @param integer $post
 * @param integer $uid
 * @return string
 */
function userrang($post, $uid)
{
    static $user_rang_array = array();

    if (!isset($user_rang_array[$uid])) {
        if (!isset($user_rang_array)) {
            $user_rang_array = array();
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
        $user_rang_array[$uid] = $rRang;
    }

    return ($user_rang_array[$uid]);
}

/**
 * Markiert Suchwort in einem Text (mittels span)
 * 
 * @param string $text Text der durchsucht wird
 * @param string $such gesuchtes Wort
 * @return string Text mit markierten Suchwort
 */
function markword($text, $such)
{
    $erg = '<span style="background-color: #EBF09B;">';
    $erg .= $such . "</span>";
    $text = str_replace($such, $erg, $text);
    return $text;
}

/**
 * Erzeugt Smileyauswahlliste
 * 
 * @global array $lang
 * @param integer $zeilen Anzahl Smileys pro Zeile
 * @return string
 */
function getsmilies($zeilen = 3)
{
    global $lang;
    $i = 0;
    $b = '<script language="JavaScript" type="text/javascript">function moreSmilies () { var x = window.open("about:blank", "moreSmilies", "width=250,height=200,status=no,scrollbars=yes,resizable=yes"); ';
    $a = '';
    $erg = db_query('SELECT `emo`, `ent`, `url` FROM `prefix_smilies` ORDER BY `pos` ASC');
    if (@mysql_num_rows($erg)) {
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
    $b .= ' x.document.write("<br /><br /><center><a href=\"javascript:window.close();\">' . $lang['close'] . '</a></center>"); x.document.close(); }</script>';
    if ($i > 12) {
        $a .= '<br /><br /><center><a href="javascript:moreSmilies();">' . $lang['more'] . '</a></center>';
    }
    $a = $b . $a;
    return ($a);
}

/**
 * Versenden einer E-Mail
 * 
 * @global array $allgAr
 * @param string $mail Ziel-E-Mail-Adresse
 * @param string $bet Betreff
 * @param string $txt Nachrichtentext
 * @param string $from Sender nur Adresse oder Name <Adresse>
 * @param boolean $html Gibt an, ob Email im HTML Format verschickt wird ($txt muss vollständiges HTML Dokument sein)
 * @return boolean
 */
function icmail($mail, $bet, $txt, $from = '', $html = false)
{
    global $allgAr;
    include_once('include/includes/libs/phpmailer/class.phpmailer.php');
    $mailer = new PHPMailer();
    if (empty($from)) {
        $mailer->From = $allgAr['adminMail'];
        $mailer->FromName = $allgAr['allg_default_subject'];
    } elseif (preg_match('%(.*) <([\w\.-]*@[\w\.-]*)>%i', $from, $tmp)) {
        $mailer->From = trim($tmp[2]);
        $mailer->FromName = trim($tmp[1]);
    } elseif (preg_match('%([\w\.-]*@[\w\.-]*)%i', $from, $tmp)) {
        $mailer->From = trim($tmp[1]);
        $mailer->FromName = '';
    }
    if ($allgAr['mail_smtp']) { // SMTP Versand
        $smtpser = @db_result(db_query('SELECT `t1` FROM `prefix_allg` WHERE `k` = "smtpconf"'));
        if (empty($smtpser)) {
            echo '<span style="font-size: 2em; color: red;">Mailversand muss konfiguriert werden!</span><br />';
        } else {
            $smtp = unserialize($smtpser);

            $mailer->IsSMTP();
            $mailer->Host = $smtp['smtp_host'];
            $mailer->SMTPAuth = ($smtp['smtp_auth'] == 'no' ? false : true);
            if ($smtp['smtp_auth'] == 'ssl' or $smtp['smtp_auth'] == 'tls') {
                $mailer->SMTPSecure = $smtp['smtp_auth'];
            }
            if (!empty($smtp['smtp_port'])) {
                $mailer->Port = $smtp['smtp_port'];
            }
            $mailer->AddReplyTo($mailer->From, $mailer->FromName);

            if ($smtp['smtp_changesubject'] and $mailer->From != $smtp['smtp_email']) {
                $bet = '(For ' . $mailer->FromName . ' - ' . $mailer->From . ') ' . $bet;
                $mailer->From = $smtp['smtp_email'];
            }

            $mailer->Username = $smtp['smtp_login'];

            require_once('include/includes/libs/AzDGCrypt.class.inc.php');
            $cr64 = new AzDGCrypt(DBDATE . DBUSER . DBPREF);
            $mailer->Password = $cr64->decrypt($smtp['smtp_pass']);

            if ($smtp['smtp_pop3beforesmtp'] == 1) {
                include_once('include/includes/libs/phpmailer/class.pop3.php');
                $pop = new POP3();
                $pop3port = !empty($smpt['smtp_pop3port']) ? $smpt['smtp_pop3port'] : 110;
                $pop->Authorise($smpt['smtp_pop3host'], $pop3port, 5, $mailer->Username, $mailer->Password, 1);
            }
        }
        // $mailer->SMTPDebug = true;
    }
    if (is_array($mail)) {
        if ($mail[0] == 'bcc') {
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

/**
 * Ersetzt HTML Entities in einer Zeichenkette durch deren "normales" Zeichen
 * 
 * @param string $text Zeichenkette
 * @param integer $start Startposition (wie substr)
 * @param integer $length Endposition (wie substr)
 * @return string
 */
function html_enc_substr($text, $start = 0, $length = -1)
{
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return (substr(strtr($text, $trans_tbl), $start, $length));
}

/**
 * Wandelt Datum (deutsch oder englisch) in DATE (yyyy-mm-dd) Format um
 * 
 * @param string $d Datumsstring (dd.mm.yyyy, dd/mm/yyyy, yyyy/mm/dd, yyyy-mm-dd)
 * @return string
 */
function get_datum($d)
{
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

/**
 * Fügt http:// vor eine Webadresse, wenn dies fehlt
 * 
 * @param string $h
 * @return string
 */
function get_homepage($h)
{
    $h = trim($h);
    if (!empty($h) && preg_match('%^https?\://') === false) {
        $h = 'http://' . $h;
    }
    return $h;
}

/**
 * Gibt den img HTML Code eines Warbildes zurück, wenn es existiert
 * 
 * @param string $img Dateiname ohne Dateierweiterung des Bildes
 * @return string
 */
function get_wargameimg($img)
{
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

/**
 * Hilfsfunktion für iurlencode
 * 
 * @param string $a
 * @return string
 */
function iurlencode_help($a)
{
    if (preg_match("/(http:|https:|ftp:)/", $a[0])) {
        return ($a[0]);
    }

    return (rawurlencode($a[1]) . substr($a[0], - 1));
}

/**
 * Wendet rawurlencode auf eine Webadresse an, ohne das Protokoll zu verändern
 * 
 * @param string $s
 * @return string
 */
function iurlencode($s)
{
    return (preg_replace_callback("/([^\/]+|\/[^\.])[\.\/]/", 'iurlencode_help', $s));
}

/**
 * Prüft, ob der Antispamcode richtig eingegeben wurde
 * Der NoPictureMode fügt ein Hidden Feld ein, um Cross Site Request Forgery Attacken zu unterbinden, der NoPictureMode
 * wird automatisch genutzt, wenn kein Bildabfrage statt findet, kann aber auch erzwungen werden
 * 
 * @global array $allgAr
 * @param string $m Modulname, um unterschiedliche Antispamfelder auf einer Seite zu ermöglichen
 * @param boolean $nopictures NoPictureMode erzwingen
 * @return boolean
 */
function chk_antispam($m, $nopictures = false)
{
    global $allgAr;

    if (!$nopictures && is_numeric($allgAr['antispam']) && has_right($allgAr['antispam'])) {
        $nopictures = true;
    }

    $valid = false;

    if ($nopictures && isset($_POST['antispam_id'])) {
        $antispamId = $_POST['antispam_id'];
        if (isset($_SESSION['antispam'][$antispamId]) && $_SESSION['antispam'][$antispamId]) {
            $valid = true;
            unset($_SESSION['antispam'][$antispamId]);
        }
    } elseif (isset($_POST['captcha_code']) && isset($_POST['captcha_id'])) {
        require_once 'include/includes/libs/captcha/captcha.php';
        $controller = new Captcha();
        $captchaCode = strtoupper($_POST['captcha_code']);
        $valid = $controller->isValid($captchaCode, $_POST['captcha_id']);
    }
    return $valid;
}

/**
 * Erzeugt HTML Code für ein Formularfeld, welches für einen Antibot-Schutz dienen oder vor CSFR Attacken schützen soll
 * Beschreibung zum NoPictureMode bitte der chk_antispam Funktion entnehmen
 * 
 * @global array $allgAr
 * @param string $m Modulname
 * @param integer $t Type, der angibt wie das Formularfeld formatiert wird (0, 1 oder > 10 als Breite für das label) siehe Code :P
 * @param boolean $nopictures Erzwing NoPictureMode
 * @return string
 */
function get_antispam($m, $t, $nopictures = false)
{
    global $allgAr;

    if (!$nopictures && $t < 0 || (is_numeric($allgAr['antispam']) && has_right($allgAr['antispam']))) {
        $nopictures = true;
    }

    $id = uniqid($m, true);
    
    if ($nopictures) {
        $_SESSION['antispam'][$id] = true;
        return '<input type="hidden" name="antispam_id" value="' . $id . '" />';
    }

    include 'include/includes/libs/captcha/settings.php';

    $helpText = 'Geben Sie diese Zeichen in das direkt daneben stehende Feld ein.';
    $seperator = ' ';

    if ($t == 0) {
        $seperator = '<br />';
        $helpText = 'Geben Sie diese Zeichen in das direkt darunter stehende Feld ein.';
    }
    $img = '<img width="' . $imagewidth . '" height="' . $imageheight . '" src="include/includes/libs/captcha/captchaimg.php?id='
        . $id . '&nocache=' . time() . '" alt="captchaimg" title="' . $helpText . '">'
        . $seperator . '<input class="captcha_code" name="captcha_code" type="text" maxlength="5" size="8">'
        . '<input type="hidden" name="captcha_id" value="' . $id .  '" />';
        ;

    if ($t == 1) {
        $img = '<tr><td class="Cmite"><b>Antispam</b></td><td class="Cnorm">' . $img . '</td></tr>';
    } elseif ($t > 10) {
        $img = '<label style="float:left; width: ' . $t . 'px; ">Antispam</label>' . $img . '<br/>';
    }
    return $img;
}

/**
 * Ermittelt die Größe eines Verzeichnis (mit allen Dateien und Unterverzeichnissen)
 * 
 * @param string $dir Verzeichnis
 * @return integer
 */
function dirsize($dir)
{
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

/**
 * Gibt Bytes als KB oder MB aus
 * 
 * @param integer $bytes
 * @return string
 */
function nicebytes($bytes)
{
    if ($bytes < 1000000) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return round($bytes / (1024 * 1024), 2) . ' MB';
    }
}

/**
 * Wandelt in einem Array (rekursiv) oder einer Zeichenkette, alle Buchstaben zu Kleinbuchstaben um
 * 
 * @param array | string $value
 * @return array | string
 */
function get_lower($value)
{
    if (is_array($value)) {
        foreach ($value as $key => $wert) {
            $array[$key] = get_lower($wert);
        }
        return $array;
    } else {
        return strtolower($value);
    }
}

/**
 * Gib alle Dateien des angegeben Ordners mit der gegebenen Endung aus
 *
 * @param  $dir der Ordner (z.B. "include/admin")
 * @param  $ext die Extension (z.B. "php" oder array("php","html","htm"))
 * @param  $sExt gibt die Dateierweiterung mit aus
 * @param  $sDir gibt das Verzeichnis mit aus
 */
function read_ext($dir, $ext = '', $sExt = 1, $sDir = 0)
{
    $buffer = Array();
    if (!is_array($ext)) {
        $ext = Array($ext
        );
    }
    $open = opendir($dir);
    while ($file = readdir($open)) {
        $file_info = pathinfo($file);
        if (substr($file, 0, 1) != "." AND !is_dir($dir . '/' . $file) AND (in_array($file_info["extension"], $ext) OR empty($ext))) {
            if ($sExt == 0) {
                $file = basename($dir . '/' . $file, '.' . $file_info["extension"]);
            }
            if ($sDir == 1) {
                $file = $dir . '/' . $file;
            }
            $buffer[] = $file;
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
function array_set_missing_keys($array1, $array2)
{
    foreach ($array2 as $key => $value) {
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
