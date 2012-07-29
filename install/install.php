<?php
define('DEBUG', true);
if (defined('DEBUG')) {
  define('SCRIPT_START_TIME', microtime(true));
}
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id: install.php 245 2011-09-11 13:13:27Z geck0 $
 */
defined('E_DEPRECATED') or define('E_DEPRECATED', 0);
@error_reporting(E_ALL > E_DEPRECATED ? E_ALL : E_ALL ^ E_DEPRECATED);
@date_default_timezone_set('Europe/Berlin');
@ini_set('display_errors', 'On');

function getInstallprofileComment($file)
{
	$content = file($file);
	//First line: $content[0];
	return substr($content[0], 3,strlen($content[0]));
}
?>
<html>

<head>
  <script src="../include/includes/js/global/jquery-1.7.2.min.js" type="text/javascript"></script>
  <script src="../include/includes/js/jquery/jquery.validate.js" type="text/javascript"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>... ::: [ I n s t a l l a t i o n &nbsp; f &uuml; r &nbsp; C l a n s c r i p t &nbsp; v o n &nbsp; i l c h ] ::: ...</title>
	<link rel="stylesheet" href="../include/designs/ilchClan12/style.css" type="text/css">
    <link rel="stylesheet" href="../include/includes/css/jquery/templates/redmond/jquery-ui-1.8.21.custom.css" type="text/css">
	<style type="text/css">.hide { display:none; }</style>

</head>

<body>

<form action="install.php" method="POST" id="installform">

<?php


if ( empty ($_POST['step']) ) {

?>
		<input type="hidden" name="step" value="3" />

		<table width="700" class="border" border="0" cellspacing="1" cellpadding="3" align="center">
      <tr class="ui-state-default ui-corner-all">
          <td colspan="3"><b>Lizenz</b></td>
        </tr>
      <tr class="Cmite">
          <td colspan="3" align="center" class="ui-widget-content ui-corner-all"><p><a href="http://www.gnu.de/documents/gpl.de.html" target="_blank"><img src="ilch_logo.png" width="192" height="96" border="0"><br>
              <img src="http://www.gnu.org/graphics/gplv3-127x51.png" width="79" height="31" border="0"><br>
              Komplette Lizenz lesen</a><br>
              <br>
          </p></td>
          </tr>
      <tr class="ui-state-default ui-corner-all">
          <td colspan="3"><b>Installationsprofil</b></td>
          </tr>
      <tr  class="ui-widget-content ui-corner-all">
          <td align="center" width="40%"><span class="Cmite">Wähle hier ein Installationsprofil aus. <br>
Die Standard-Installation wird durch die &quot;ilchClan.sql&quot; ausgewählt. <br>
Mehr Infos dazu findest du in den <a href="http://ilch.de" target="_blank">FAQ</a></span></td>
          <?php
			# Auslesen der beigelegten install.sql - Files und deren Kommentare in der ersten Zeile
		$sqllist = '';
		$sqlextent = '';
		if ($sqlhandle = opendir('../install/')) {
		$comment = array();
		$_SESSION['installProfile'] = '';
			while (FALSE !== ($sqlfilex = readdir($sqlhandle))) {

				# nur .sql listen
				$sqlextent = @explode('.', $sqlfilex);
				if (isset($sqlextent[1]) and $sqlextent[1] == 'sql') {
					$comment[$sqlextent[0]] = getInstallprofileComment(implode('.',$sqlextent));
					$selected = ($sqlextent[0] == 'ilchClan') ? 'selected="selected"' : '';
					$sqllist .=  '<option value="' . $sqlextent[0] . '" ' . $selected . '>' . $sqlextent[0] . '</option>';
				}
			}
			closedir($sqlhandle);
		}
		?>
          <td width="20%" align="center"  class="ui-widget-content ui-corner-all"><select name="selectinstallsql" id="selectinstallsql">
              <?php echo $sqllist; ?>
          </select></td>
				<td width="40%" align="center">
			<p>
				<?php
				  foreach ($comment as $key => $value) {
					echo '<div class="hide" id="' . $key . '_profileText">' . $value . '</div>';
				  }
				?>
               <p id="sqlComment">{1. Kommentarzeile aus der gewählten SQL}</p>
      <tr class="Cdark">
          <td colspan="3" align="center"><input type="submit" id="submitstep1" value="Lizenz gelesen und akzeptiert" class="ui-button ui-state-default ui-corner-all"></td>
          </tr>
    </table>
<?php

} elseif ($_POST['step'] == 3) {

$servercheck = array();
  ?>
<input type="hidden" name="step" value="4" />
<table width="700" class="border" border="0" cellspacing="1" cellpadding="3" align="center">
      <tr class="Chead">
        <td colspan="2" class="ui-state-default ui-corner-all"><b>Voraussetzungen Pr&uuml;fen</b> <?php echo $_POST['selectinstallsql']; ?></td>
		</tr>
        <input type="hidden" name="selectinstallsql" id="selectinstallsql" value="<?php echo $_POST['selectinstallsql']; ?>" />
<?php

// PHP Compare
$servercheck['php_compare']['msg'] = 'PHP-Version 5.2.0 oder besser';
if ( @version_compare(@phpversion(), '5.2.0') != -1) {
 	$servercheck['php_compare']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['php_compare']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}

// Windows-PHP-Bug
// https://github.com/IlchCMS/Ilch-1.2/wiki/Doc-pcre.recursion_limit-auf-Windows-Servern
if (defined('PHP_OS') && function_exists('ini_get')) {
	if ( (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' or PHP_OS == 'WINNT') && ini_get('pcre.recursion_limit') > 600) {
		$servercheck['php_overflow']['msg'] = '(Windows-Server) pcre.recursion_limit';
		$servercheck['php_overflow']['erg'] = '<font color="#FF0000"><b>ACHTUNG</b> <a href="https://github.com/IlchCMS/Ilch-1.2/wiki/Doc-pcre.recursion_limit-auf-Windows-Servern">weitere Info</a></font>';
		// Lösung ( für ./install/install.php und index.php
		#if (function_exists('ini_set')) { ini_set("pcre.recursion_limit", "524"); }
	}
}
// mySQL Server/Client-Check
$servercheck['sql_compare']['msg'] = 'SQL 5.0.0 oder besser';

if (function_exists('mysql_get_server_info')) {
	$sqlinfo = @mysql_get_server_info();
	@preg_match('/[1-9]\.[0-9]\.\d{1,2}/', $sqlinfo, $sqlmatch);
	if (isset($sqlmatch[0])) $sqlserver = $sqlmatch[0];
}
if (!isset($sqlmatch[0])) {
	ob_start();
	phpinfo(INFO_MODULES);
	$sqlinfo = ob_get_contents();
	ob_end_clean();
	$sqlinfo = stristr($sqlinfo, 'Client API version');
	preg_match('/[1-9]\.[0-9]\.\d{1,2}/', $sqlinfo, $sqlmatch);
	if (isset($sqlmatch[0])) $sqlserver = $sqlmatch[0];
}
if ( @version_compare($sqlserver, '5.0.0') != -1) {
		$servercheck['sql_compare']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
		$servercheck['sql_compare']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}

// config.php
	$servercheck['configphp']['msg'] = '"../include/includes/config.php" (CHMOD 666)';
if ( file_exists( '../include/includes/config.php' ) and is_writeable ( '../include/includes/config.php' ) ) {
	$servercheck['configphp']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else
if ( file_exists( 'include/includes/config.php' ) and !is_writeable ( '../include/includes/config.php' ) ){
	$servercheck['configphp']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
} else
if ( !file_exists( '../include/includes/config.php' ) and is_writeable('../include/includes/')) {
	$servercheck['configphp']['erg'] = '<font color="#40aa00"><b>wird angelegt</b></font>';
} else {
	$servercheck['configphp']['erg'] = '<font color="#FF0000"><b>FEHLER</b></font>';
}
// smarty-cache
$servercheck['smarty/cache']['msg'] = '"../include/cache" (CHMOD 777)';
if ( @is_writeable ( '../include/cache' ) ) {
	$servercheck['smarty/cache']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['smarty/cache']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// backup-verzeichnis
$servercheck['backup_dir']['msg'] = '"../include/backup/" (CHMOD 777)';
if ( @is_writeable ( '../include/backup' ) ) {
	$servercheck['backup_dir']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['backup_dir']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// selfpb
$servercheck['selfbp/selfp']['msg'] = '"../include/contents/selfbp/selfp" (CHMOD 777)';
if ( @is_writeable ( '../include/contents/selfbp/selfp' ) ) {
	$servercheck['selfbp/selfp']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['selfbp/selfp']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// selfpb
$servercheck['selfbp/selfb']['msg'] = '"../include/contents/selfbp/selfb" (CHMOD 777)';
if ( @is_writeable ( '../include/contents/selfbp/selfb' ) ) {
	$servercheck['selfbp/selfb']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['selfbp/selfb']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}

// images/linkus
$servercheck['images/linkus']['msg'] = '"../include/images/linkus" (CHMOD 777)';
if ( @is_writeable ( '../include/images/linkus' ) ) {
	$servercheck['images/linkus']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/linkus']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// images/avatars
$servercheck['images/avatar']['msg'] = '"../include/images/avatars" (CHMOD 777)';
if ( @is_writeable ( '../include/images/avatars' ) ) {
	$servercheck['images/avatar']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/avatar']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// images/opponents
$servercheck['images/opponents']['msg'] = '"../include/images/opponents" (CHMOD 777)';
if ( @is_writeable ( '../include/images/opponents' ) ) {
	$servercheck['images/opponents']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/opponents']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// images/gallery
$servercheck['images/gallery']['msg'] = '"../include/images/gallery" (CHMOD 777)';
if ( @is_writeable ( '../include/images/gallery' ) ) {
	$servercheck['images/gallery']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/gallery']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// images/smilies
$servercheck['images/smiles']['msg'] = '"../include/images/smiles" (CHMOD 777)';
if ( @is_writeable ( '../include/images/smiles' ) ) {
	$servercheck['images/smiles']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/smiles']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}

// images/usergallery
$servercheck['images/usergallery']['msg'] = '"../include/images/usergallery" (CHMOD 777)';
if ( @is_writeable ( '../include/images/usergallery' ) ) {
	$servercheck['images/usergallery']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/usergallery']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// images/wars
$servercheck['images/wars']['msg'] = '"../include/images/wars" (CHMOD 777)';
if ( @is_writeable ( '../include/images/wars' ) ) {
	$servercheck['images/wars']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['images/wars']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}

// downs/downloads
$servercheck['downs/downloads']['msg'] = '"../include/downs/downloads" (CHMOD 777)';
if ( @is_writeable ( '../include/downs/downloads' ) ) {
	$servercheck['downs/downloads']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['downs/downloads']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}

// downs/downloads/user_upload
$servercheck['downs/downloads/user_upload']['msg'] = '"../include/downs/downloads/user_upload" (CHMOD 777)';
if ( @is_writeable ( '../include/downs/downloads/user_upload' ) ) {
	$servercheck['downs/downloads/user_upload']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	$servercheck['downs/downloads/user_upload']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
}
// .htaccess
$servercheck['htaccess']['msg'] = '"../.htaccess" (CHMOD 777)';
if ( @is_writeable ( '../.htaccess' ) ) {
	$servercheck['htaccess']['erg'] = '<font color="#40aa00"><b>RICHTIG</b></font>';
} else {
	if (is_file('../.htaccess')) {
		$servercheck['htaccess']['erg'] = '<font color="#FF0000"><b>FALSCH</b></font>';
	} else {
		if (FALSE === @file_put_contents('../.htaccess', '')) {
			$servercheck['htaccess']['erg'] = '<font color="#CC9933"><b>bitte eine leere .htaccess anlegen</b></font>';
		} else {
			$servercheck['htaccess']['erg'] = '<font color="#40aa00"><b>Datei angelegt</b></font>';
		}
	}
}
###############################
##############################
############################ DONT TOUCH ANYTHING HERE
###########################
##########################

foreach ($servercheck as $key => $val) {
	?>
    	<tr>
        	<td class="Cmite">
            	<?php echo $val['msg'] ?>
            </td>
    		<td class="Cnorm">
            	<?php echo $val['erg'] ?>
            </td>
  		</tr>
    <?php
}

?>
<tr class="Cdark">
		    <td></td>
				<td><input type="submit" value="Weiter ->" class="ui-button ui-state-default ui-corner-all"></td>
		</tr>
 	</table>

<?php
} elseif ( $_POST['step'] == 4 ) {
  ?>
	<input type="hidden" name="step" value="5">
	<input type="hidden" name="selectinstallsql" id="selectinstallsql" value="<?php echo $_POST['selectinstallsql']; ?>">
	<table width="700" class="border" border="0" cellspacing="1" cellpadding="3" align="center">
      <tr class="Chead">
        <td colspan="3" class="ui-state-default ui-corner-all"><b>Installation </b>- <?php echo $_POST['selectinstallsql']; ?></td>
			</tr><tr class="Cdark">
 		    <td colspan="3"><b>MySQL Einstellungen</b><br />Wenn Sie mit den MySQL Daten nicht zurecht kommen, also nicht wissen was Sie im folgenden eingeben sollen, lesen Sie bitte erst die Beschreibung hinter dem Feld und bei weiterer Unklarheit wenden Sie sich an Ihren Webspace Anbieter oder Ihren Systemadministrator um die n&ouml;tigen Daten zu erfahren.</td>
		  </tr><tr>
        <td class="Cmite" width="100">Hostname</td>
    		<td class="Cnorm"><input type="text" value="localhost" name="mysql_hostname" class="required"></td>
				<td class="Cnorm">i.d.R. localhost oder 127.0.0.1 ansonsten ein Server-Name oder eine Server-IP.</td>
  		</tr><tr>
    		<td class="Cmite">Username</td>
    		<td class="Cnorm"><input type="text" name="mysql_username" class="required"></td>
				<td class="Cnorm">Der Username der auf die Datenbank zugreiffen soll.</td>
  		</tr><tr>
    		<td class="Cmite">Password</td>
    		<td class="Cnorm"><input type="password" name="mysql_password" id="mysql_password" class=""></td>
				<td class="Cnorm">Das Password f&uuml;r den Username damit er sich an der Datenbank anmelden kann.</td>
  		</tr><tr>
    		<td class="Cmite">Password</td>
    		<td class="Cnorm"><input type="password" name="confirm_mysql_password" class=""></td>
				<td class="Cnorm">Bitte wiederholen Sie das Passwort von oben.</td>
  		</tr><tr>
    		<td class="Cmite">Datenbank</td>
    		<td class="Cnorm"><input type="text" name="mysql_datenbank" class="required"></td>
				<td class="Cnorm">Die Datenbank in der die Tabellen f&uuml;r das Clanscript angelegt werden sollen.</td>
  		</tr><tr>
    		<td class="Cmite">Installation Nr.</td>
    		<td class="Cnorm"><select name="mysql_prefix"><?php
           for($i=1;$i<=10;$i++) {
             echo '<option value="ic'.$i.'_">'.$i.'</option>';
           }
        ?></select></td>
				<td class="Cnorm">Kann i.d.R. unver&auml;ndert bleiben, ausser das Script wird mehr als einmal in die selbe Datenbank installiert.</td>
  		</tr><tr class="Cdark">
    		<td colspan="3"><b>Admin anlegen</b></td>
  		</tr><tr>
    		<td class="Cmite">Usernamen</td>
    		<td class="Cnorm"><input type="text" name="admin_name" maxlength="15" class="required"></td>
				<td class="Cnorm">Der Nickname des Administrator Users mit dem Sie sich nach dieser Installation anmelden k&ouml;nnen.</td>
  		</tr><tr>
    		<td class="Cmite">Passwort</td>
    		<td class="Cnorm"><input type="password" name="admin_pwd" maxlength="20" class="required"></td>
				<td class="Cnorm">Das Password mit dem Sie sich nach der Installation zusammen mit dem Username anmelden k&ouml;nnen.</td>
  		</tr><tr>
    		<td class="Cmite">Passwort</td>
    		<td class="Cnorm"><input type="password" name="confirm_admin_pwd" id="confirm_admin_pwd" maxlength="20" class="required"></td>
				<td class="Cnorm">Bitte wiederholen Sie das Passwort von oben.</td>
  		</tr>
  		<tr>
  		    <td class="Cmite">Admin eMail</td>
  		    <td class="Cnorm"><input type="text" name="admin_email" class="required {validate:{required:true,email:true}}"></td>
  		    <td class="Cnorm">Die eMail-Adresse des Administrator Users (also vermutlich Ihre eMail-Adresse).</td>
  		    </tr>
  		<tr class="Cdark">
				<td colspan="3" align="center"><p>&nbsp;</p>				    <button onClick="javascript:submitForm();" class="ui-button ui-state-default ui-corner-all">Daten Speichern und Installieren</button></td>
 			</tr>
 	</table>
</form>
<?php
} elseif ( $_POST['step'] == 5 ) {

  if (
      	empty($_POST['admin_name'] ) or
	    empty($_POST['admin_email'] ) or
	    empty($_POST['mysql_hostname']) or
	    empty($_POST['mysql_username']) or
	    empty($_POST['mysql_datenbank']) or
	    empty($_POST['mysql_prefix'])
    )
  {
    echo '<table width="50%" class="border" border="0" cellspacing="1" cellpadding="3" align="center"><tr><td class="Cnorm">Folgende Angaben sind unbedingt erforderlich:<br />&nbsp;&nbsp;- Hostname<br />&nbsp;&nbsp;- Username<br />&nbsp;&nbsp;- Installations Nr.<br />&nbsp;&nbsp;- Datenbank<br />&nbsp;&nbsp;- AdminPassword<br />&nbsp;&nbsp;- AdminE-Mail<br />&nbsp;&nbsp;- AdminName<br />&nbsp;<a href="javascript:history.back(-1)">zur&uuml;ck</a></td></tr></table>';
  } else {
    $config = <<< config
<?php
define ( 'DBHOST', '{$_POST['mysql_hostname']}' );   # sql host
define ( 'DBUSER', '{$_POST['mysql_username']}');  # sql user
define ( 'DBPASS', '{$_POST['mysql_password']}');  # sql pass
define ( 'DBDATE', '{$_POST['mysql_datenbank']}');  # sql datenbank
define ( 'DBPREF', '{$_POST['mysql_prefix']}'); # sql prefix
config;
    $config .= "\n".'?>';
    $open = @fopen('../include/includes/config.php' , 'w' );
    if ($open) {
    	fwrite ( $open , trim($config) );
		  fclose ( $open );
      require_once('../include/includes/config.php');
    } else {
      define ( 'DBHOST', $_POST['mysql_hostname'] );   # sql host
      define ( 'DBUSER', $_POST['mysql_username']);  # sql user
      define ( 'DBPASS', $_POST['mysql_password']);  # sql pass
      define ( 'DBDATE', $_POST['mysql_datenbank']);  # sql datenbank
      define ( 'DBPREF', $_POST['mysql_prefix']); # sql prefix
    }



define ( 'main' , TRUE );
require_once('../include/includes/func/db/mysql.php');

db_connect();


# checken ob die config tabelle + prefix schon da ist.
# wenn ja wird hier abgebrochen, keine 2 mal installation.
# zumal sonst evtl. eintraege doppelt vorkommen koennten
if (DBPREF.'allg' == @db_result(@db_query("SHOW TABLES LIKE 'prefix_allg'"),0)) {
  ?>
		<table width="70%" class="border" border="0" cellspacing="0" cellpadding="25" align="center">
      <tr>
        <td class="ui-state-error ui-corner-all">
        	<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>Die Installation wurde vermutlich schon ausgef&uuml;hrt.
          <br />
          Auf jeden Fall ist die allgemeine Tabelle schon vorhanden...
          <br />Bitte ersteinmal den Status der <a href="index.php">Seite</a> checken.
          <br />- Wenn es die Version 1.1 ist, dann bitte das Update ausf&uuml;hren.
          <br />- Ansonsten wurde die Version 1.2 offenbar schon installiert.
          <br /><br />Bei Fragen bitte auf <a href="http://www.ilch.de/">ilch.de</a> nachfragen.
          </p>

   	      <h1 style="color: #FF0000;">FEHLER: Es ist ein <u>Fehler</u> aufgetreten!</h2>

        </td>
      </tr>
    </table>
  <?php

  exit ();
}

$filename = $_POST['selectinstallsql'].'.sql';

db_import_sql_file($filename);

db_query ("INSERT INTO `prefix_user` (
										name,
										name_clean,
										pass,
										regist,
										email,
										recht,
										llogin,
										status,
										opt_mail,
										opt_pm
									)
										VALUES
									(
										'".$_POST['admin_name']."',
										'".strtolower($_POST['admin_name'])."',
										'".md5($_POST['admin_pwd'])."',
										'".time()."',
										'".strtolower($_POST['admin_email'])."',
										'-9',
										'".time()."',
										1,
										1,
										1
									)");
db_query ("UPDATE prefix_allg SET t1 = '".$_POST['admin_email']."|Webmaster' WHERE k = 'kontakt'");
db_query ("UPDATE prefix_config SET wert = '".$_POST['admin_email']."' WHERE schl = 'adminMail'");
?>
		<table width="70%" class="border" border="0" cellspacing="0" cellpadding="25" align="center">
      <tr>
        <td class="ui-state-highlight ui-corner-all">
    	    <h2><b>Installation abgeschlossen</b></h2>
					Sofern keine Fehler aufgetreten sind ist die Installation abgeschlossen.

          <?php if (!$open) { ?>
          <br /><br /><br />Weil die Datei include/includes/config.php nicht die n&ouml;tigen
          Rechte hatte bitte einfach den Inhalt des folgenden Eingabefeldes kopieren
          und komplett in diese Datei auf dem Server schreiben. Die Datei dazu einfach
          mit einem Editor &ouml;ffnen und den Inhalt einf&uuml;gen.
          <br /><b>Inhalt include/includes/config.php</b><br />
          <textarea cols="60" rows="10"><?php echo trim($config); ?></textarea>
          <br /><br />
          <?php  } ?>
					<br />
          Die Seite kann jetzt <a href="../index.php">Aufgerufen</a> werden.
					<br /><br />
					<strong>Bitte unbedingt den install-Ordner l&ouml;schen!
          </strong></td>
      </tr>
    </table>
<?php
  }
}

?>
<script type="text/javascript" charset="utf-8">

  $(document).ready(function() {
	$('#selectinstallsql').change(function() {
	  $("#selectinstallsql option:selected").each(function () {
			$('#sqlComment').html($('#' + $(this).text() + '_profileText').text());
	  });
	});

	$("#selectinstallsql option:selected").each(function () {
		$('#sqlComment').html($('#' + $(this).text() + '_profileText').text());
	});

    $('#installform').validate({
      rules: {
			confirm_mysql_password: {
				equalTo: "#mysql_password"
			},
			admin_pwd: {
				required: true,
				minlength: 8
			},
			confirm_admin_pwd: {
				required: true,
				minlength: 8,
				equalTo: "#confirm_admin_pwd"
			},
			admin_email: {
				required: true,
				email: true
			}
		},
		messages: {
			admin_pwd: {
				required: "Bitte wählen Sie ein Passwort",
				minlength: "Ihr Passwort muss mindestens 8 Zeichen lang sein.",
			},
			confirm_admin_pwd: {
				required: "Bitte wählen Sie ein Passwort",
				minlength: "Ihr Passwort muss mindestens 8 Zeichen lang sein.",
				equalTo: "Bitte das selbe Passwort wie oben angeben."
			},
			confirm_mysql_password: {
				required: "Bitte wählen Sie ein Passwort",
				equalTo: "Bitte das selbe Passwort wie oben angeben."
			},
			admin_email: "Bitte gib eine gültige E-Mail adresse an."
		}
});
  });
</script>
</body>
</html>
