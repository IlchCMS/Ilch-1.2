<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr[ 'title' ] . ' :: Impressum';
$hmenu = 'Impressum';
$design = new design($title, $hmenu);
$design->header();

$erg = db_query("SELECT * FROM `prefix_allg` WHERE `k` = 'impressum' LIMIT 1");
$row = db_fetch_assoc($erg);

echo $row[ 'v1' ]; // eigentuemer oder sowas
echo '<br /><br />';
echo $row[ 'v2' ]; // voller name
echo '<br />';
echo $row[ 'v3' ]; // strasse nr
echo '<br /><br />';
echo $row[ 'v4' ]; // plz, ort
echo '<br/><br />';
echo 'Kontakt: <a href="index.php?contact">Formular</a><br /><br />';
echo unescape($row[ 't1' ]); // disclaimer

// Credits-System von GeCk0 - Start
# define some vars
$ilchcredits	=	'';
$modcredits		=	'';
$gfxcredits		=   '';
$ilchtablestyle = 	'';
$modtablestyle	= 	'';

# die ilch-credits auslesen
$ilchcountqry	= 	db_query("SELECT * FROM `prefix_credits` WHERE sys = 'ilch'");
$ilchcount		=	db_num_rows($ilchcountqry);

# Prüfen ob ilch-credits eingetragen sind
if ($ilchcount == 0 or $ilchcount === FALSE) {
	$ilchcredits .= 'Es sind keine Credits f&uuml;r das ilch-Script eingetragen oder es ist ein Fehler aufgetreten';
	$ilchtablestyle = 	'display:none;';

} else {
	# liste erstellen
	while ($ilchrow = db_fetch_assoc($ilchcountqry)) {
		$ilchcredits .= '
			<tr>
				<td width="40%">'.$ilchrow['name'].'</td>
   				<td width="20%">'.$ilchrow['version'].'</td>
    			<td width="20%"><a href="'.$ilchrow['url'].'">Link</a></td>
    			<td width="20%"><a href="'.$ilchrow['lizenzurl'].'">'.$ilchrow['lizenzname'].'</a></td>
			</tr>';
	}
}

# die modul-credits auslesen
$modcountqry	= 	db_query("SELECT * FROM `prefix_credits` WHERE sys = 'modul'");
$modcount		=	db_num_rows($modcountqry);

# prüfen ob modul-credits eingetragen sind und ggfl Liste erstellen
if ($modcount == 0 or $modcount === FALSE) {
	$modcredits .= 'Es sind keine Credits zu den installierten Modulen eingetragen';
	$modtablestyle	= 	'display:none;';
} else {
	# liste erstellen
	while ($modrow = db_fetch_assoc($modcountqry)) {
		$modcredits .= '
			<tr>
				<td width="40%">'.$modrow['name'].'</td>
   				<td width="20%">'.$modrow['version'].'</td>
    			<td width="20%"><a href="'.$modrow['url'].'">Link</a></td>
    			<td width="20%"><a href="'.$modrow['lizenzurl'].'">'.$modrow['lizenzname'].'</a></td>
			</tr>';
	}
}

# die gfx-credits auslesen
$gfxcountqry	= 	db_query("SELECT * FROM `prefix_credits` WHERE sys = 'gfx'");
$gfxcount		=	db_num_rows($gfxcountqry);

# Prüfen ob gfx-credits eingetragen sind
if ($gfxcount == 0 or $gfxcount === FALSE) {
	$gfxcredits .= 'Es sind keine Credits f&uuml;r Grafiken eingetragen';
	$gfxtablestyle = 	'display:none;';

} else {
	# liste erstellen
	while ($gfxrow = db_fetch_assoc($gfxcountqry)) {
		$gfxcredits .= '
			<tr>
				<td width="40%">'.$gfxrow['name'].'</td>
   				<td width="20%">'.$gfxrow['version'].'</td>
    			<td width="20%"><a href="'.$gfxrow['url'].'">Link</a></td>
    			<td width="20%"><a href="'.$gfxrow['lizenzurl'].'">'.$gfxrow['lizenzname'].'</a></td>
			</tr>';
	}
}
echo <<<HTML
			<p>
				<hr />
				<h4>Script-Credits:</h4>
			</p>
		<table width="100%" align="center">
			$ilchcredits
		</table>
			<p>
				 <h4>Modul-Credits:</h4>
			</p>
		<table width="100%" align="center">
			$modcredits
		</table>
		<p>
			<h4>Bild- und Grafikverzeichnis:</h4>
		</p>
		<table width="100%" align="center">
			$gfxcredits
		</table>
		<p>
		</p>
HTML;
// Credits-System von GeCk0 - Ende

$design->footer();

?>