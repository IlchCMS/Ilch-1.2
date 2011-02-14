<?php

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Credits', '', 2);
$tpl = new tpl('credits', 1);
$design->header();

# define some vars
$ilchcredits	=	'';
$modcredits		=	'';
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
	$modcredits .= 'Es sind keine Credits zu den installierten Modulen eingetragen';
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

$tpl->set('ilchtablestyle', $ilchtablestyle);
$tpl->set('modtablestyle', $modtablestyle);
$tpl->set('ilchcredits', $ilchcredits);
$tpl->set('modcredits', $modcredits);
$tpl->out(0);
$design->footer();