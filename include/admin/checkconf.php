<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
/**
 * in diesem Array werden "Warnung" und "Fehler" gespeichert und ggfls im UserAdminPanel ausgegeben.
 * einer dieser Bezeichnungen MUSS in der ersten Klammer stehen.
 * die nächsten Klammern beinhalten eine Untergruppierung welche frei gewählt werden kann
 * @EXAMPLE
 * 	$syscheck['Warnung']['chmods'] = $detail_info_zB_Verzeichnis;
 * 	$syscheck['Fehler']['phpversion'] = phpversion() .' ist kleiner als 5.2';
 */
// Ab wie vielen Tagen zählt ein Backup als veraltet ?
$backup_time_diff = 7;
// Syscheck Backups
$backupdir = "./include/backup/";
$verz = opendir($backupdir);
//Anzahl vorhandene Backups
$sqlcount=0;
$newest = 0;
while ($datei = readdir($verz)) {
	if ($datei != "." && $datei != ".." && !is_dir($datei)) {
		if (strstr($datei, ".sql")){
			$sqlcount++;
			$getdate1 = explode('.', $datei); 
			$getdate = explode('_', $getdate1[0]);
			if ($getdate[1] > $newest) {
				$newest = $getdate[1];
			}
  		}
 	}
}
closedir($verz);
if ($sqlcount == 0) {
	$syscheck['Fehler'][] = 'Es ist noch kein Backup vorhanden.
					<a href="admin.php?backup" target="_self">Erstelle ein Backup</a> im Backupverzeichnis um diese Meldung auszublenden';
}
if (strtotime($newest) < (time() - $backup_time_diff * 86400) ) {
	$syscheck['Warnung'][] = 'Dein letztes Backup ist älter als '.$backup_time_diff.' Tage.
					Es wird empfohlen Backups regelmäßig zu erstellen. <a href="admin.php?backup" target="_self">Erstelle ein Backup</a>';
}
// Datum letztes Backup

// Syscheck Backup Ende
if ($menu->get(1) == "phpinfo") {
    phpinfo();
} else {
    $design = new design('Ilch Admin-Control-Panel :: Serverkonfiguration', '', 2);
    $design->header();

    $tpl = new tpl('checkconf', 1);
   
    // # Server conf
    $tpl->set_out('head', $lang[ 'phpserverconf' ], 1);
    $tpl->set_ar_out(array(
            'class' => 'Cmite',
            'opt' => 'version',
            'val' => phpversion()
            ), 3);
    $confstrings = array(
        "safe_mode",
        "display_errors",
        "max_execution_time",
        "memory_limit",
        "register_globals",
        "file_uploads",
        "upload_max_filesize",
        "post_max_size",
        "disable_functions"
        );
    $class = 'Cmite';
    foreach ($confstrings as $str) {
        if ($class == 'Cmite') {
            $class = 'Cnorm';
        } else {
            $class = 'Cmite';
        }
		$iniget = ini_get($str);
		// Systemcheck start
		if ($str == 'file_uploads' and $iniget != 1) {
			$syscheck['Warnung'][] = 'Dein Webspace (php.ini) erlaubt keine file_uploads. 
												Dies wird z.B. für Avatare oder UserDownloads benötigt';
		}
		// Systemcheck ende
        $tpl->set("class", $class);
        $tpl->set("opt", $str);
        $tpl->set("val", $iniget);
        $tpl->out(3);
    }
    // sockets
    if ($class == 'Cmite') {
        $class = 'Cnorm';
    } else {
        $class = 'Cmite';
    }
    $tpl->set("class", $class);
    $tpl->set("opt", 'sockets');
    $tpl->set("val", defined('AF_INET') ? 1 : 0);
    $tpl->out(3);
    $tpl->out(2);
    // chmod
    $tpl->set_out('head', $lang[ 'filesystemrights' ], 1);

    $files = array(
		'.htaccess',
		'include/cache',
        'include/backup',
        'include/images/linkus',
        'include/images/avatars',
        'include/images/gallery',
        'include/images/usergallery',
        'include/downs/downloads',
        'include/downs/downloads/user_upload',
		'include/images/opponents',
        'include/images/wars',
        'include/contents/selfbp/selfp',
        'include/contents/selfbp/selfb',
        'include/images/smiles'
        );
    asort($files);
    $class = 'Cmite';
    foreach ($files as $f) {
        if ($class == 'Cmite') {
            $class = 'Cnorm';
        } else {
            $class = 'Cmite';
        }
        $tpl->set("class", $class);
        $tpl->set("opt", $f);
        if (@is_writeable($f)) {
            $val = $lang[ 'correct' ];
        } else {
            $val = '<span style="background-color: #f00;">' . $lang[ 'incorrect' ] . '</span>';
			$syscheck ['Warnung'][] = 'Bitte gebe Schreibrechte (chmod 777) für <u><i>'.$f.'</i></u>';
        }
        $tpl->set("val", $val);
        $tpl->out(3);
    }
    $tpl->out(2);
    // Server
    $result = db_query("SHOW TABLE STATUS");
    $dbsize = 0;
    while ($row = db_fetch_assoc($result)) {
        $dbsize += $row[ 'Data_length' ];
    }

    $tpl->set_out('head', 'Informationen', 1);
	$sqlversion = db_result(db_query("SELECT VERSION()"));
	$serverzeit = date('Y-m-d H:i:s');
	$sqlzeit = db_result(db_query("SELECT NOW()"));
	
	if ($sqlzeit != $serverzeit) {
		$syscheck ['Fehler'][] = 'Deine Serverzeit läuft nicht Syncron. Dies kann zu Problemen, u.a. in der OnlineBox, führen. Ein Restart des Servers hilft in den meisten Fällen. Ansonsten wende dich deswegen an deinen Hoster.';
	}
    $infos = array(
        'Serversoftware' => $_SERVER[ "SERVER_SOFTWARE" ],
        'Server (PHP) Zeit' => $serverzeit,
        'SQL Zeit' => $sqlzeit,
        'MySQL-Version' => $sqlversion,
        'Datenbankgr&ouml;&szlig;e' => nicebytes($dbsize),
        'Linkusordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/linkus/')),
        'Avatarordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/avatars/')),
        'Galleryordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/gallery/')),
        'Usergalleryordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/usergallery/')),
		'Gegnerordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/opponents/'))
        );
    foreach ($infos as $k => $str) {
        if ($class == 'Cmite') {
            $class = 'Cnorm';
        } else {
            $class = 'Cmite';
        }
        $tpl->set("class", $class);
        $tpl->set("opt", $k);
        $tpl->set("val", $str);
        $tpl->out(3);
    }
	$syscheckoutput = '';
	if (isset($syscheck['Warnung']) and is_array($syscheck['Warnung'])) {
		foreach ($syscheck['Warnung'] as $key => $val) {
			$systemstatus = 'Warnung';
			$syscheckoutput .= '<div style="padding: 0 .7em;" class="ui-state-highlight ui-corner-all">
			 					<p>
									<span style="float: left;" class="ui-icon ui-icon-info"></span> 
									<strong>'.$systemstatus.'</strong><br /> '. $val .'
								</p>
							</div>';
		}
	}	
	if (isset($syscheck['Fehler']) and is_array($syscheck['Fehler'])) {
		foreach ($syscheck['Fehler'] as $key => $val) {
			$systemstatus = 'Fehler';
			$syscheckoutput .= '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">
			 					<p>
									<span style="float: left;" class="ui-icon ui-icon-alert"></span> 
									<strong>'.$systemstatus.'</strong><br /> '. $val .'
								</p>
							</div>';
		}
	}
	if (!isset($syscheck['Fehler']) and !isset($syscheck['Warnung'])) {
		$systemstatus = 'OK';
	}
	
	$tpl->set("sysstatus", ''.$syscheckoutput.'');
	//aktualisiere SysCheck Database
	db_query("UPDATE `prefix_config` SET wert = '".$systemstatus."' WHERE schl = 'syscheckstatus'");
	db_query("UPDATE `prefix_config` SET wert = '".date('Y-m-d')."' WHERE schl = 'syscheckdatum'");
	// Wenn durch Adminpanel angesteuert, reloaden sofern der neue Status nicht dem aktuell angezeigtem Status entspricht

    $tpl->out(0);
    $tpl->out(2);
    $tpl->out(4);
}
if ($menu->get(1) == 1) {
    wd('admin.php?checkconf', 'Untersuche Systemeinstellungen...', 1);
    $design->footer(1);
} else {
    $design->footer();
}
?>