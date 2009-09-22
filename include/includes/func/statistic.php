<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');
// global die zeit wo ein user als online angezeigt wird definieren.
define ('USERUPTIME', 180);
// #
// ##
// ###
// #### alle online
function ges_online() {
    $dif = date('Y-m-d H:i:s', time() - USERUPTIME);
    $erg = db_query("SELECT COUNT(*) FROM `prefix_online` WHERE `uptime` > '" . $dif . "'");
    $anz = db_result($erg, 0);
    return ($anz);
}
// #
// ##
// ###
// #### nur die user
function ges_user_online() {
    $dif = date('Y-m-d H:i:s', time() - USERUPTIME);
    $erg = db_query("SELECT COUNT(*) FROM `prefix_online` WHERE `uid` > 0 AND `uptime` > '" . $dif . "'");
    $anz = db_result($erg, 0);
    return ($anz);
}
// #
// ##
// ###
// #### nur die gaeste
function ges_gast_online() {
    $dif = date('Y-m-d H:i:s', time() - USERUPTIME);
    $erg = db_query("SELECT COUNT(*) FROM `prefix_online` WHERE `uid` = 0 AND `uptime` > '" . $dif . "'");
    $anz = db_result($erg, 0);
    return ($anz);
}
// #
// ##
// ###
// #### user online liste
function user_online_liste() {
    $OnListe = '';
    $dif = date('Y-m-d H:i:s', time() - USERUPTIME);
    $erg = db_query("SELECT DISTINCT `uid`, `name`, `prefix_ranks`.`bez`, `spezrank` FROM `prefix_online` LEFT JOIN `prefix_user` ON `prefix_user`.`id` = `prefix_online`.`uid` LEFT JOIN `prefix_ranks` ON `prefix_ranks`.`id` = `prefix_user`.`spezrank` WHERE `uid` > 0 AND `uptime` > '" . $dif . "'");
    while ($row = db_fetch_object($erg)) {
        if ($row->spezrank != 0) {
            $OnListe .= '<a title="' . $row->bez . '" href="index.php?user-details-' . $row->uid . '"><b><i>' . $row->name . '</i></b></a> , ';
        } else {
            $OnListe .= '<a href="index.php?user-details-' . $row->uid . '">' . $row->name . '</a> , ';
        }
    }
    $OnListe = substr($OnListe, 0, strlen($OnListe) - 3);
    return ($OnListe);
}

// # user onloine list fuer admin + gaeste
function user_admin_online_liste () {
    $OnListe = '';
    $class = '';
    $dif = date('Y-m-d H:i:s', time() - USERUPTIME);
    $erg = db_query("SELECT DISTINCT `uid`, DATE_FORMAT(`uptime`, '%d.%m.%Y - %H:%i:%s') as `datum`, `ipa`, `name` FROM `prefix_online` LEFT JOIN `prefix_user` on `prefix_user`.`id` = `prefix_online`.`uid` WHERE `uptime` > '" . $dif . "' ORDER BY `uid` DESC");
    while ($row = db_fetch_object($erg)) {
        $name = $row->name;
        if ($row->uid == 0) {
            $name = 'Gast';
        }

        $host_patterns = array(
            '/crawl-[0-9]{1,3}-[0-9]{1,3}-[0-9]{1,3}-[0-9]{1,3}\.googlebot\.com/si',
            '/[a-z]*[0-9]*\.inktomisearch\.com/si',
            '/[a-z]*[0-9]*\.ask\.com/si',
            '/p[0-9A-F]*\.dip[0-9]*\.t-(dialin|ipconnect)\.(net|de)/si',
            '/[0-9A-F]*\.ipt\.aol\.com/si',
            '/dslb-[0-9]{3}-[0-9]{3}-[0-9]{3}-[0-9]{3}.pools.arcor-ip.net/si',
            '/crawl[0-9]*\}exabot\.com/si',
            '/[0-9A-Z]+\.adsl\.highway\.telekom\.at/si'
            );
        $host_names = array(
            'Bot Google',
            'Bot Inktomi/Yahoo',
            'Bot Ask.com',
            'T-Online',
            'AOL',
            'Arcor DSL',
            'Bot Exalead',
            'Telekom Austria DSL'
            );

        $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
        $OnListe .= '<tr class="' . $class . '">';
        $OnListe .= '<td>' . $name . '</td>';
        $OnListe .= '<td>' . $row->datum . '</td>';
        $OnListe .= '<td>' . $row->ipa . '</td>';
        $OnListe .= '<td>' . preg_replace($host_patterns, $host_names, @gethostbyaddr ($row->ipa)) . '</td>';
        $OnListe .= '</tr>';
    }
    // $OnListe = substr($OnListe,0,strlen($OnListe) - 3);
    return ($OnListe);
}

function getip() {
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && isip($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isip($_SERVER["REMOTE_ADDR"])) {
        return $_SERVER["REMOTE_ADDR"];
    } else {
        return '128.0.0.1';
    }
}

function isip($ip) {
    // es hat so ein komischen regex fuer IPV6 :-) und evtl /24 etc...
    return preg_match("/^[0-9a-zA-Z\/.:]{7,}$/", $ip);
}

function site_statistic () {
    if (!array_key_exists('ilchCMSstati', $_SESSION)) {
        $_SESSION['ilchCMSstati'] = 'geloggt';
        $d = date('j');
        $m = date('n');
        $y = date('Y');
        $ip = getip();
        $ergResul = db_result(db_query("SELECT COUNT(`ip`) FROM `prefix_stats` WHERE `ip` = '" . $ip . "' AND `day` = " . $d . " AND `mon` = " . $m . " AND `yar` = " . $y), 0);
        debug ($ergResul . '#statistic res');
        if ($ergResul == 0) {
            $os = escape(site_statistic_get_os($_SERVER['HTTP_USER_AGENT']), 'string');
            $br = escape(site_statistic_get_browser($_SERVER['HTTP_USER_AGENT']), 'string');
            $wt = date('w');
            $st = date('G');
            $ur = (isset ($_SERVER['HTTP_REFERER']) ? escape(site_statistic_get_referer($_SERVER['HTTP_REFERER']), 'string') : '');
            db_query("INSERT INTO `prefix_stats` (`wtag`,`stunde`,`day`,`mon`,`yar`,`os`,`browser`,`ip`,`ref`)
			VALUES(" . $wt . "," . $st . "," . $d . "," . $m . "," . $y . ",'" . $os . "','" . $br . "','" . $ip . "','" . $ur . "')");

            $dc = (strlen ($d) == 1 ? '0' . $d : $d);
            $mc = (strlen ($m) == 1 ? '0' . $m : $m);
            $cdate = $y . '-' . $mc . '-' . $dc;
            $query = "SELECT COUNT(`date`) FROM `prefix_counter` WHERE `date` = '" . $cdate . "'";
            if (db_result(db_query($query), 0) == 0) {
                db_query('INSERT INTO `prefix_counter` (`date`,`count`) VALUES ( "' . $cdate . '" , "1" ) ');
            } else {
                db_query('UPDATE `prefix_counter` SET `count` = `count` +1 WHERE `date` = "' . $cdate . '"');
            }
        }
    }
}

function site_statistic_get_browser($useragent) {
	if (preg_match("=Firefox/([\.a-zA-Z0-9]*)=", $useragent, $browser)) {
		return ("Firefox " . $browser[1]);
	} elseif (preg_match("=MSIE ([0-9]{1,2})\.[0-9]{1,2}=", $useragent, $browser)) {
		return "Internet Explorer " . $browser[1];
	} elseif (preg_match("=Opera[/ ]([0-9\.]+)=", $useragent, $browser)) {
		return "Opera " . $browser[1];
	} elseif (preg_match("=Chrome/([0-9\.]*)=", $useragent, $browser)) {
		$tmp = explode('.', $browser[1]);
		if (count($tmp) > 2) {
			$browser[1] = $tmp[0].'.'.$tmp[1];
		}
		return "Chrome " . $browser[1];
	} elseif (preg_match('=Safari/=', $useragent)){
		if (preg_match('=Version/([\.0-9]*)=', $useragent, $browser)) {
			$version = ' ' . $browser[1];
		} else {
			$version = '';
		}
		return "Safari" . $version;
	} elseif (preg_match("=Konqueror=", $useragent)) {
		return "Konqueror";
	} elseif (preg_match("=Netscape|Navigator=", $useragent)) {
		return "Netscape";
	} else {
		return 0;
	}
}

function site_statistic_get_os($useragent) {
	$osArray = array(
		'Windows XP'   => '=Windows NT 5\.1|Windows XP=',
		'Windows Vista' => '=Windows NT 6\.0|Windows Vista=',
		'Windows 7' => '=Windows NT 6\.1|Windows 7=',
		'Windows 2000' => '=Windows NT 5\.0|Windows 2000=',
		'Windows Server 2003\\Windows XP x64' => '=Windows NT 5\.2|Windows Server 2003|Windows XP x64=',
		'Windows NT' => '=Windows NT 4\.0|Windows NT|WinNT4\.0=',
		'Windows 98' => '=Windows 98=',
		'Windows 95' => '=Windows 95=',
		'Macintosh' => '=Mac_PowerPC|Macintosh=',
		'Linux' => '=Linux=',
		'SunOs' => '=SunOS='
	);

	foreach ($osArray as $os => $regex){
		if (preg_match($regex, $useragent)) {
			return $os;
		}
	}
	return 0;
}

function site_statistic_get_referer ($referer) {
    if (! empty ($referer)) {
        $refzar = parse_url($referer);
        $refspa = $refzar['scheme'] . '://' . $refzar['host'] . $refzar['path'];
        return preg_replace('=[^a-z0-9öäüß_/:\-\.\\\\]=i', '', $refspa);
    } else {
        return 0;
    }
}

?>