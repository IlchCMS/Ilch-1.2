<?php

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Credits', '', 2);
$tpl = new tpl('credits', 1);
$design->header();

# define some vars
$ilchcredits	=	'';
$modcredits		=	'';
$gfxcredits		=	'';
$ilchtablestyle = 	'';
$modtablestyle	= 	'';

# die ilch-credits auslesen
$ilchcountqry	= 	db_query("SELECT * FROM `prefix_credits` WHERE sys = 'ilch'");
$ilchcount		=	db_num_rows($ilchcountqry);

# Prüfen ob ilch-credits eingetragen sind
if ($ilchcount == 0 or $ilchcount === FALSE) {
	$ilchcredits .= '<tr><td>Es sind keine Credits f&uuml;r das ilch-Script eingetragen oder es ist ein Fehler aufgetreten</td></tr>';
	$ilchtablestyle = 	'display:none;';

} else {
	# liste erstellen
	while ($ilchrow = db_fetch_assoc($ilchcountqry)) {
		$ilchcredits .= '
			<tr>
				<td>'.$ilchrow['name'].'</td>
   				<td>'.$ilchrow['version'].'</td>
    			<td><a href="'.$ilchrow['url'].'">Link</a></td>
    			<td><a href="'.$ilchrow['lizenzurl'].'">'.$ilchrow['lizenzname'].'</a></td>
			</tr>';
	}
}

# die modul-credits auslesen
$modcountqry	= 	db_query("SELECT * FROM `prefix_credits` WHERE sys = 'modul'");
$modcount		=	db_num_rows($modcountqry);

# prüfen ob modul-credits eingetragen sind und ggfl Liste erstellen
if ($modcount == 0 or $modcount === FALSE) {
	$modcredits .= '<tr><td>Es sind keine Credits zu den installierten Modulen eingetragen</td></tr>';
	$modtablestyle	= 	'display:none;';
} else {
	# liste erstellen
	while ($modrow = db_fetch_assoc($modcountqry)) {
		$modcredits .= '
			<tr>
				<td>'.$modrow['name'].'</td>
   				<td>'.$modrow['version'].'</td>
    			<td><a href="'.$modrow['url'].'">Link</a></td>
    			<td><a href="'.$modrow['lizenzurl'].'">'.$modrow['lizenzname'].'</a></td>
			</tr>';
	}
}
# die gfx-credits auslesen
$gfxcountqry	= 	db_query("SELECT * FROM `prefix_credits` WHERE sys = 'gfx'");
$gfxcount		=	db_num_rows($gfxcountqry);

# prüfen ob modul-credits eingetragen sind und ggfl Liste erstellen
if ($gfxcount == 0 or $gfxcount === FALSE) {
	$gfxcredits .= '<tr><td>Es sind keine Credits zu Grafiken/Designs eingetragen</td></tr>';
	$gfxtablestyle	= 	'display:none;';
} else {
	# liste erstellen
	while ($gfxrow = db_fetch_assoc($gfxcountqry)) {
		$gfxcredits .= '
			<tr>
				<td>'.$gfxrow['name'].'</td>
   				<td>'.$gfxrow['version'].'</td>
    			<td><a href="'.$gfxrow['url'].'">Link</a></td>
    			<td><a href="'.$gfxrow['lizenzurl'].'">'.$gfxrow['lizenzname'].'</a></td>
			</tr>';
	}
}

$tpl->set('ilchtablestyle', $ilchtablestyle);
$tpl->set('modtablestyle', $modtablestyle);
$tpl->set('ilchcredits', $ilchcredits);
$tpl->set('modcredits', $modcredits);
$tpl->set('gfxcredits', $gfxcredits);
$tpl->out(0);
$design->footer();