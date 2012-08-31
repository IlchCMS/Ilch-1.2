<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
$design = new design('Ilch Admin-Control-Panel :: Nextwars', '', 2);
$design->header();
$show = true;
$tpl = new tpl('wars/next', 1);
if (!empty($_GET[ 'delete' ]))
{
    // aus kalender loeschen fals vorhanden
    db_query("DELETE FROM `prefix_kalender` WHERE `text` LIKE '%more-" . $_GET[ 'delete' ] . "]%'");
    db_query("DELETE FROM `prefix_wars` WHERE `id` = '" . $_GET[ 'delete' ] . "'");
    $msg = '<tr class="Cmite"><td colspan="2">Erfolgreich gel&ouml;scht</td></tr>';
}
if (!empty($_POST[ 'sub' ]) and chk_antispam('adminuser_action', true))
{
    if (!empty($_POST[ 'newmod' ]))
    {
        $_POST[ 'mod' ] = $_POST[ 'newmod' ];
    }
    if (!empty($_POST[ 'newgame' ]))
    {
        $_POST[ 'game' ] = $_POST[ 'newgame' ];
    }
    if (!empty($_POST[ 'newmtyp' ]))
    {
        $_POST[ 'mtyp' ] = $_POST[ 'newmtyp' ];
    }
    if (empty($_POST[ 'tid' ]))
    {
        $_POST[ 'tid' ] = 0;
    }
    $_POST[ 'pkey' ] = escape($_POST[ 'pkey' ], 'integer');
    $_POST[ 'gegner' ] = escape($_POST[ 'gegner' ], 'string');
    $_POST[ 'page' ] = get_homepage(escape($_POST[ 'page' ], 'string'));
    $_POST[ 'tid' ] = escape($_POST[ 'tid' ], 'integer');
    $_POST[ 'mod' ] = escape($_POST[ 'mod' ], 'string');
    $_POST[ 'game' ] = escape($_POST[ 'game' ], 'string');
    $_POST[ 'mtyp' ] = escape($_POST[ 'mtyp' ], 'string');
    $_POST[ 'land' ] = escape($_POST[ 'land' ], 'string');
    $_POST[ 'txt' ] = escape($_POST[ 'txt' ], 'string');
    $_POST[ 'tag' ] = escape($_POST[ 'tag' ], 'string');
    $_POST[ 'email' ] = escape($_POST[ 'email' ], 'string');
    $_POST[ 'icq' ] = escape($_POST[ 'icq' ], 'string');
    $_POST[ 'wo' ] = escape($_POST[ 'wo' ], 'string');
    if (empty($_POST[ 'pkey' ]))
    {
        db_query("INSERT INTO `prefix_wars` (`datime`,`status`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt`) VALUES ('" . get_datime() . "',2,'" . $_POST[ 'gegner' ] . "','" . $_POST[ 'tag' ] . "','" . $_POST[ 'page' ] . "','" . $_POST[ 'email' ] . "','" . $_POST[ 'icq' ] . "','" . $_POST[ 'wo' ] . "','" . $_POST[ 'tid' ] . "','" . $_POST[ 'mod' ] . "','" . $_POST[ 'game' ] . "','" . $_POST[ 'mtyp' ] . "','" . $_POST[ 'land' ] . "','" . $_POST[ 'txt' ] . "')");
        $wid = db_last_id();
        // in den kalender eintragen wenn gewuenscht
        if (isset($_POST[ 'kalender' ]) AND $_POST[ 'kalender' ] == 'yes')
        {
            $timestamp = strtotime(get_datime());
            $page = str_replace('admin.php', 'index.php', $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]);
            db_query("INSERT INTO `prefix_kalender` (`time`, `title`, `text`, `recht`) VALUES (" . $timestamp . ",'Nextwar gegen " . $_POST[ 'gegner' ] . "', '" . $_POST[ 'mtyp' ] . " " . $_POST[ 'mod' ] . " in " . $_POST[ 'game' ] . " gegen [url=" . $_POST[ 'page' ] . "]" . $_POST[ 'gegner' ] . "[/url]\n\n[url=http://" . $page . "?wars-more-" . $wid . "]details des Wars[/url]', 0)");
        }
        $msg = '<tr class="Cmite"><td colspan="2">Erfolgreich eingetragen</td></tr>';
    }
    else
    {
        db_query("UPDATE `prefix_wars` SET `datime` = '" . get_datime() . "', `status` = 2,`gegner` = '" . $_POST[ 'gegner' ] . "',`tag` = '" . $_POST[ 'tag' ] . "',`page` = '" . $_POST[ 'page' ] . "',`mail` = '" . $_POST[ 'email' ] . "',`icq` = '" . $_POST[ 'icq' ] . "',`wo` = '" . $_POST[ 'wo' ] . "',`tid` = '" . $_POST[ 'tid' ] . "',`mod` = '" . $_POST[ 'mod' ] . "',`game` = '" . $_POST[ 'game' ] . "',`mtyp` = '" . $_POST[ 'mtyp' ] . "',`land` = '" . $_POST[ 'land' ] . "',`txt` = '" . $_POST[ 'txt' ] . "' WHERE `id` = '" . $_POST[ 'pkey' ] . "'");
        $wid = $_POST[ 'pkey' ];
        // in den kalender eintragen wenn gewuenscht
        if (isset($_POST[ 'kalender' ]) AND $_POST[ 'kalender' ] == 'yes')
        {
            $timestamp = strtotime(get_datime());
            $page = str_replace('admin.php', 'index.php', $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]);
            if (1 == db_result(db_query("SELECT COUNT(*) FROM `prefix_kalender` WHERE `text` LIKE '%more-" . $wid . "]%'"), 0))
            {
                db_query("UPDATE `prefix_kalender` SET `time` = " . $timestamp . ", `title` = 'Nextwar gegen " . $_POST[ 'gegner' ] . "', `text` = '" . $_POST[ 'mtyp' ] . " " . $_POST[ 'mod' ] . " in " . $_POST[ 'game' ] . " gegen [url=" . $_POST[ 'page' ] . "]" . $_POST[ 'gegner' ] . "[/url]\n\n[url=http://" . $page . "?wars-more-" . $wid . "]details des Wars[/url]' WHERE `text` LIKE '%more-" . $wid . "]%'");
            }
            else
            {
                db_query("INSERT INTO `prefix_kalender` (`time`, `title`, `text`, `recht`) VALUES (" . $timestamp . ",'Nextwar gegen " . $_POST[ 'gegner' ] . "', '" . $_POST[ 'mtyp' ] . " " . $_POST[ 'mod' ] . " in " . $_POST[ 'game' ] . " gegen [url=" . $_POST[ 'page' ] . "]" . $_POST[ 'gegner' ] . "[/url]\n\n[url=http://" . $page . "?wars-more-" . $wid . "]details des Wars[/url]', 0)");
            }
        }
        $msg = '<tr class="Cmite"><td colspan="2">Erfolgreich ver&auml;ndert</td></tr>';
    }
}
if (!empty($_GET[ 'pkey' ]))
{
    $erg = db_query("SELECT DATE_FORMAT(`datime`,'%d.%m.%Y.%H.%i.%s') as `datime`, `id`,`status`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt` FROM `prefix_wars` WHERE `id` = '" . $_GET[ 'pkey' ] . "'");
    $_ilch = db_fetch_assoc($erg);
    list($_ilch[ 'day' ], $_ilch[ 'mon' ], $_ilch[ 'jahr' ], $_ilch[ 'stu' ], $_ilch[ 'min' ], $_ilch[ 'sek' ]) = explode('.', $_ilch[ 'datime' ]);
    $_ilch[ 'kalck' ] = (db_result(db_query("SELECT COUNT(*) FROM `prefix_kalender` WHERE `text` LIKE '%more-" . $_GET[ 'pkey' ] . "]%'"), 0, 0) == 1 ? ' checked' : '');
    $_ilch[ 'pkey' ] = $_GET[ 'pkey' ];
}
else
{
    $_ilch = array(
    'tag' => '',
    'mail' => '',
    'icq' => '',
    'wo' => '',
    'pkey' => '',
    'wlp' => '',
    'erg1' => '',
    'erg2' => '',
    'gegner' => '',
    'page' => 'http://',
    'mtyp' => '',
    'tid' => 0,
    'land' => '',
    'txt' => '',
    'mod' => '',
    'game' => '',
    'day' => date('d'),
    'mon' => date('m'),
    'jahr' => date('Y'),
    'stu' => date('H'),
    'min' => date('i'),
    'sek' => date('s'),
    'kalck' => ''
    );
}
$_ilch[ 'msg' ] = (isset($msg) ? $msg : '');
$_ilch[ 'tid' ] = dblistee($_ilch[ 'tid' ], "SELECT `id`, `name` FROM `prefix_groups` ORDER BY `name`");
$_ilch[ 'mod' ] = dblistee($_ilch[ 'mod' ], "SELECT DISTINCT `mod`,`mod` FROM `prefix_wars` ORDER BY `mod`");
$_ilch[ 'game' ] = dblistee($_ilch[ 'game' ], "SELECT DISTINCT `game`,`game` FROM `prefix_wars` ORDER BY `game`");
$_ilch[ 'mtyp' ] = dblistee($_ilch[ 'mtyp' ], "SELECT DISTINCT `mtyp`,`mtyp` FROM `prefix_wars` ORDER BY `mtyp`");
$_ilch[ 'land' ] = arlistee($_ilch[ 'land' ], get_nationality_array());
$_ilch[ 'ANTISPAM' ] = get_antispam('adminuser_action', 0, true);
$tpl->set_ar_out($_ilch, 0);
$page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1);
$class = '';
if ($page == 1)
{
    $abf = "SELECT `id`,`gegner`,`game` FROM `prefix_wars` WHERE `status` = 1 ORDER BY `id` DESC";
    $erg = db_query($abf);
    while ($r = db_fetch_assoc($erg))
    {
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $r[ 'class' ] = $class;
            $r[ 'game' ] = get_wargameimg($r[ 'game' ]);
            $tpl->set_ar($r);
            $tpl->out(1);
    }
}
$limit = 20;
$MPL = db_make_sites($page, 'WHERE status = 2', $limit, 'admin.php?wars-next', 'wars');
$anfang = ($page - 1) * $limit;
$abf = "SELECT `id`,`gegner`,`game` FROM `prefix_wars` WHERE `status` = 2 ORDER BY `id` DESC LIMIT " . $anfang . "," . $limit;
$erg = db_query($abf);
while ($row = db_fetch_assoc($erg))
{
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row[ 'class' ] = $class;
    $row[ 'game' ] = get_wargameimg($row[ 'game' ]);
    $tpl->set_ar($row);
    $tpl->out(2);
}
$tpl->set('MPL', $MPL);
$tpl->out(3);
$design->footer();