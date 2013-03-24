<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$title = $allgAr['title'] . ' :: Impressum';
$hmenu = 'Impressum';
$design = new design($title, $hmenu);
$design->header();
$tpl = new tpl('impressum');

$erg = db_query("SELECT * FROM `prefix_allg` WHERE `k` = 'impressum' LIMIT 1");
$row = db_fetch_assoc($erg);
$tpl->set('owner', $row['v1']);
$tpl->set('name', $row['v2']);
$tpl->set('street', $row['v3']);
$tpl->set('city', $row['v4']);
$tpl->set('disclaimer', bbcode(unescape($row['t1'])));
$tpl->out(0);

// Credits-System von GeCk0 - Start
# define some vars
$ilchcredits = '';
$modcredits = '';
$gfxcredits = '';
$ilchtablestyle = '';
$modtablestyle = '';

# die ilch-credits auslesen
$ilchcountqry = db_query("SELECT * FROM `prefix_credits` WHERE sys = 'ilch'");
$ilchcount = db_num_rows($ilchcountqry);
$tpl->out('ilch_credits_start');

# Prüfen ob ilch-credits eingetragen sind
if ($ilchcount == 0 or $ilchcount === FALSE) {
    $tpl->out('no_ilch_credits');
} else {
    # liste erstellen
    while ($ilchrow = db_fetch_assoc($ilchcountqry)) {
        $tpl->set('name', $ilchrow['name']);
        $tpl->set('version', $ilchrow['version']);
        $tpl->set('url', $ilchrow['url']);
        $tpl->set('lizenzurl', $ilchrow['lizenzurl']);
        $tpl->set('lizenzname', $ilchrow['lizenzname']);
        $tpl->out('ilch_credits');
    }
}
$tpl->out('ilch_credits_end');

# die modul-credits auslesen
$modcountqry = db_query("SELECT * FROM `prefix_credits` WHERE sys = 'modul'");
$modcount = db_num_rows($modcountqry);
$tpl->out('ilch_modul_start');

# prüfen ob modul-credits eingetragen sind und ggfl Liste erstellen
if ($modcount == 0 or $modcount === FALSE) {
    $tpl->out('no_modul_credits');
} else {
    # liste erstellen
    while ($modrow = db_fetch_assoc($modcountqry)) {
        $tpl->set('name', $modrow['name']);
        $tpl->set('version', $modrow['version']);
        $tpl->set('url', $modrow['url']);
        $tpl->set('lizenzurl', $modrow['lizenzurl']);
        $tpl->set('lizenzname', $modrow['lizenzname']);
        $tpl->out('modul_credits');
    }
}
$tpl->out('ilch_modul_end');

# die gfx-credits auslesen
$gfxcountqry = db_query("SELECT * FROM `prefix_credits` WHERE sys = 'gfx'");
$gfxcount = db_num_rows($gfxcountqry);
$tpl->out('ilch_gfx_start');

# Prüfen ob gfx-credits eingetragen sind
if ($gfxcount == 0 or $gfxcount === FALSE) {
    $tpl->out('no_gfx_credits');
} else {
    # liste erstellen
    while ($gfxrow = db_fetch_assoc($gfxcountqry)) {
        $tpl->set('name', $gfxrow['name']);
        $tpl->set('version', $gfxrow['version']);
        $tpl->set('url', $gfxrow['url']);
        $tpl->set('lizenzurl', $gfxrow['lizenzurl']);
        $tpl->set('lizenzname', $gfxrow['lizenzname']);
        $tpl->out('gfx_credits');
    }
}
$tpl->out('ilch_gfx_end');
// Credits-System von GeCk0 - Ende

$design->footer();
