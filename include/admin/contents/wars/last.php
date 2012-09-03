<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
// image upload
if ($menu->get(2) == 'upload') 
{
    $tpl = new tpl('wars/upload', 1);
    $msg = '';
    // aktion
    if (isset($_FILES[ 'f' ][ 'name' ]) and chk_antispam('adminuser_action', true))
    {
        $tmp = explode('.', $_FILES[ 'f' ][ 'name' ]);
        if ($tmp[ 1 ] == 'gif' OR $tmp[ 1 ] == 'png' OR $tmp[ 1 ] == 'jpg' OR $tmp[ 1 ] == 'jpeg')
        {
            $nname = $_REQUEST[ 'wid' ] . '_' . $_REQUEST[ 'mid' ] . '.' . $tmp[ 1 ];
            if (move_uploaded_file($_FILES[ 'f' ][ 'tmp_name' ], 'include/images/wars/' . $nname))
            {
                @chmod('include/images/wars/' . $nname, 0777);
                $ar = array(
                'gif' => 'gif',
                'png' => 'png',
                'jpg' => 'jpg',
                'jpeg' => 'jpeg'
                );
                unset($ar[ $tmp[ 1 ] ]);
                foreach ($ar as $v)
                {
                    @unlink('include/images/wars/' . $_REQUEST[ 'wid' ] . '_' . $_REQUEST[ 'mid' ] . '.' . $v);
                }
                $msg = 'Datei (' . $_FILES[ 'f' ][ 'name' ] . ' ) <font color="#00FF00">erfolgreich hochgeladen</font><br />';
            }
            else
            {
                $msg = 'Datei ( ' . $_FILES[ 'f' ][ 'name' ] . ' ) <font color="#FF0000">nicht erfolgreich hochgeladen</font><br />';
            }
        }
        else
        {
            $msg = 'Bitte nur Bilder mit der Endung: .gif, .png, .jpg oder .jpeg!';
        }
    }
    if (isset($_GET[ 'd' ]))
    {   
        if (@unlink('include/images/wars/' . $_GET[ 'd' ]))
        {
            $msg = 'Datei <font color="#00FF00">erfolgreich gel&ouml;scht</font><br />';
        }
        else
        {
            $msg = 'Datei <font color="#FF0000">konnte nicht gel&ouml;scht werden</font><br />';
        }
    }
    // anzeigen
    if (!is_writeable('include/images/wars'))
    {
        $msg = 'Bitte erst dem Ordner "images/wars" Schreibrechte (chmod 777) geben.';
    }
    $mid = $_REQUEST[ 'mid' ];
    $wid = $_REQUEST[ 'wid' ];
    $file = 'Noch kein Bild hochgeladen... ';
    $ar = array(
    'gif',
    'png',
    'jpg',
    'jpeg'
    );
    foreach ($ar as $v)
    {
        if (file_exists('include/images/wars/' . $wid . '_' . $mid . '.' . $v))
        {
            $size = getimagesize('include/images/wars/' . $wid . '_' . $mid . '.' . $v);
            $breite = $size[ 0 ];
            $hoehe = $size[ 1 ];
            $file = '<a href="javascript:openImgWindow(\'' . $v . '\',' . $hoehe . ',' . $breite . ')">include/images/wars/' . $wid . '_' . $mid . '.' . $v . '</a>';
            $file .= '&nbsp; &nbsp; <a href="javascript:deleteMap(\'' . $v . '\')"><img src="include/images/icons/del.gif" border="0" title="l&ouml;schen" /></a>';
            break;
        }
    }
    $tpl->set('wid', $wid);
    $tpl->set('mid', $mid);
    $tpl->set('file', $file);
    $tpl->set('msg', $msg);
    $tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
    $tpl->out(0);
    exit();
}
// manag member for war...
if ($menu->get(2) == 'members')
{
    $tpl = new tpl('wars/last_member', 1);
    $msg = '';
    // aktion
    if (isset($_POST[ 'add_uid' ]) AND !empty($_POST[ 'add_uid' ]) and chk_antispam('adminuser_action', true))
    {
        db_query("INSERT INTO prefix_warmember (wid,uid,aktion) VALUES (" . $_REQUEST[ 'wid' ] . "," . $_POST[ 'add_uid' ] . ",1)");
    }
    if (isset($_GET[ 'delete_uid' ]) AND !empty($_GET[ 'delete_uid' ]))
    {
        db_query("DELETE FROM prefix_warmember WHERE wid = " . $_REQUEST[ 'wid' ] . " AND uid = " . $_GET[ 'delete_uid' ]);
    }
    // anzeigen
    $tpl->set('msg', $msg);
    $tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));
    $tpl->set('wid', $_REQUEST[ 'wid' ]);
    $tpl->set('liste', dblistee(0, "SELECT `prefix_user`.`id`,`name` FROM `prefix_user` LEFT JOIN `prefix_warmember` ON `prefix_warmember`.`uid` = `prefix_user`.`id` AND `prefix_warmember`.`wid` = " . $_REQUEST[ 'wid' ] . " WHERE `prefix_warmember`.`aktion` IS NULL AND `recht` <= -2 ORDER BY `name`"));
    $tpl->out(0);
    $class = '';
    $erg = db_query("SELECT `prefix_user`.`id`, `prefix_user`.`name` FROM `prefix_warmember` LEFT JOIN `prefix_user` ON `prefix_user`.`id` = `prefix_warmember`.`uid` WHERE `wid` = " . $_REQUEST[ 'wid' ] . " ORDER BY `prefix_user`.`name` ASC");
    while ($r = db_fetch_assoc($erg))
    {
        $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
        $r[ 'class' ] = $class;
        $tpl->set_ar_out($r, 1);
    }
    $tpl->out(2);
    exit();
}
// last wars
$design = new design('Ilch Admin-Control-Panel :: Lastwars', '', 2);
$design->header();
$show = true;
$tpl = new tpl('wars/last', 1);
if (!empty($_GET[ 'delete' ]))
{
    // aus kalender loeschen fals vorhanden
    db_query("DELETE FROM `prefix_kalender` WHERE `text` LIKE '%more-" . $_GET[ 'delete' ] . "]%'");
    db_query("DELETE FROM `prefix_wars` WHERE `id` = '" . $_GET[ 'delete' ] . "'");
    $wid = $_GET[ 'delete' ];
    $imgar = array(
    'gif',
    'png',
    'jpg',
    'jpeg'
    );
    for ($i = 1; $i <= 5; $i++)
    {
        db_query("DELETE FROM `prefix_warmaps` WHERE `wid` = " . $wid . " AND `mnr` = " . $i);
        foreach ($imgar as $v)
        {
            if (file_exists('include/images/wars/' . $wid . '_' . $i . '.' . $v))
            {
                unlink('include/images/wars/' . $wid . '_' . $i . '.' . $v);
            }
        }
    }
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
    // Neuen Datensatz anlegen
    if (empty($_POST[ 'pkey' ]))
    {
        db_query("INSERT INTO `prefix_wars` (`datime`,`status`,wlp,`owp`,`opp`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt`) VALUES ('" . get_datime() . "',3,'" . $_POST[ 'wlp' ] . "','" . $_POST[ 'sumowp' ] . "','" . $_POST[ 'sumopp' ] . "','" . $_POST[ 'gegner' ] . "','" . $_POST[ 'tag' ] . "','" . $_POST[ 'page' ] . "','" . $_POST[ 'email' ] . "','" . $_POST[ 'icq' ] . "','" . $_POST[ 'wo' ] . "','" . $_POST[ 'tid' ] . "','" . $_POST[ 'mod' ] . "','" . $_POST[ 'game' ] . "','" . $_POST[ 'mtyp' ] . "','" . $_POST[ 'land' ] . "','" . $_POST[ 'txt' ] . "')");
        $wid = db_last_id();
        for ($i = 1; $i <= 5; $i++)
        {
            db_query("INSERT INTO `prefix_wars` (`datime`,`status`,wlp,`owp`,`opp`,`gegner`,`tag`,`page`,`mail`,`icq`,`wo`,`tid`,`mod`,`game`,`mtyp`,`land`,`txt`) VALUES ('" . get_datime() . "',3,'" . $_POST[ 'wlp' ] . "','" . $_POST[ 'sumowp' ] . "','" . $_POST[ 'sumopp' ] . "','" . $_POST[ 'gegner' ] . "','" . $_POST[ 'tag' ] . "','" . $_POST[ 'page' ] . "','" . $_POST[ 'email' ] . "','" . $_POST[ 'icq' ] . "','" . $_POST[ 'wo' ] . "','" . $_POST[ 'tid' ] . "','" . $_POST[ 'mod' ] . "','" . $_POST[ 'game' ] . "','" . $_POST[ 'mtyp' ] . "','" . $_POST[ 'land' ] . "','" . $_POST[ 'txt' ] . "')");
            $wid = db_last_id();
            if ($_POST[ 'map' ][ $i ] != '' AND $_POST[ 'opp' ][ $i ] != '' AND $_POST[ 'owp' ][ $i ] != '')
            {
                db_query("INSERT INTO `prefix_warmaps` (`wid`,`mnr`,`map`,`opp`,`owp`) VALUES (" . $wid . "," . $i . ",'" . escape($_POST[ 'map' ][ $i ], 'string') . "'," . escape($_POST[ 'opp' ][ $i ], 'string') . "," . escape($_POST[ 'owp' ][ $i ], 'string') . ")");
            }
        }
        // in den kalender eintragen wenn gewuenscht
        if (isset($_POST[ 'kalender' ]) AND $_POST[ 'kalender' ] == 'yes')
        {
            $timestamp = strtotime(get_datime());
            $page = str_replace('admin.php', 'index.php', $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]);
            db_query("INSERT INTO `prefix_kalender` (`time`, `title`, `text`, `recht`) VALUES (" . $timestamp . ",'Lastwar gegen " . $_POST[ 'gegner' ] . "', '" . $_POST[ 'mtyp' ] . " " . $_POST[ 'mod' ] . " in " . $_POST[ 'game' ] . " gegen [url=" . $_POST[ 'page' ] . "]" . $_POST[ 'gegner' ] . "[/url]\n\n[url=http://" . $page . "?wars-more-" . $wid . "]details des Wars[/url]', 0)");
        }
        $msg = '<tr class="Cmite"><td colspan="2">Erfolgreich eingetragen</td></tr>';
    }
    else
    { //
        db_query("UPDATE `prefix_wars` SET `datime` = '" . get_datime() . "', `status` = 3,`wlp` = '" . $_POST[ 'wlp' ] . "',`owp` = '" . $_POST[ 'sumowp' ] . "',`opp` = '" . $_POST[ 'sumopp' ] . "',`gegner` = '" . $_POST[ 'gegner' ] . "',`tag` = '" . $_POST[ 'tag' ] . "',`page` = '" . $_POST[ 'page' ] . "',`mail` = '" . $_POST[ 'email' ] . "',`icq` = '" . $_POST[ 'icq' ] . "',`wo` = '" . $_POST[ 'wo' ] . "',`tid` = '" . $_POST[ 'tid' ] . "',`mod` = '" . $_POST[ 'mod' ] . "',`game` = '" . $_POST[ 'game' ] . "',`mtyp` = '" . $_POST[ 'mtyp' ] . "',`land` = '" . $_POST[ 'land' ] . "',`txt` = '" . $_POST[ 'txt' ] . "' WHERE `id` = '" . $_POST[ 'pkey' ] . "'");
        $wid = $_POST[ 'pkey' ];
        for ($i = 1; $i <= 5; $i++)
        {
            $a = db_count_query("SELECT COUNT(*) FROM `prefix_warmaps` WHERE `mnr` = " . $i . " AND `wid` = " . $wid);
            if ($a == 0 AND $_POST[ 'map' ][ $i ] != '' AND $_POST[ 'opp' ][ $i ] != '' AND $_POST[ 'owp' ][ $i ] != '')
            {
                db_query("INSERT INTO `prefix_warmaps` (`wid`,`mnr`,`map`,`opp`,`owp`) VALUES (" . $wid . "," . $i . ",'" . $_POST[ 'map' ][ $i ] . "'," . $_POST[ 'opp' ][ $i ] . "," . $_POST[ 'owp' ][ $i ] . ")");
            }
            elseif ($a == 1 AND ($_POST[ 'map' ][ $i ] == '' OR $_POST[ 'opp' ][ $i ] == '' AND $_POST[ 'owp' ][ $i ] == ''))
            {
                db_query("DELETE FROM `prefix_warmaps` WHERE `wid` = " . $wid . " AND `mnr` = " . $i);
                if (file_exists('include/images/wars/' . $wid . '_' . $i . '.gif'))
                {
                    unlink('include/images/wars/' . $wid . '_' . $i . '.gif');
                }
                if (file_exists('include/images/wars/' . $wid . '_' . $i . '.png'))
                {
                    unlink('include/images/wars/' . $wid . '_' . $i . '.png');
                }
                if (file_exists('include/images/wars/' . $wid . '_' . $i . '.jpg'))
                {
                    unlink('include/images/wars/' . $wid . '_' . $i . '.jpg');
                }
                if (file_exists('include/images/wars/' . $wid . '_' . $i . '.jpeg'))
                {
                    unlink('include/images/wars/' . $wid . '_' . $i . '.jpeg');
                }
            }
            elseif ($a == 1 AND $_POST[ 'map' ][ $i ] != '' AND $_POST[ 'opp' ][ $i ] != '' AND $_POST[ 'owp' ][ $i ] != '')
            {
                db_query("UPDATE `prefix_warmaps` SET `map` = '" . escape($_POST[ 'map' ][ $i ], 'string') . "', `opp` = " . escape($_POST[ 'opp' ][ $i ], 'string') . ", `owp` = " . escape($_POST[ 'owp' ][ $i ], 'string') . " WHERE `wid` = " . $wid . " AND `mnr` = " . $i);
            }   
        }
        // in den kalender eintragen wenn gewuenscht
        if (isset($_POST[ 'kalender' ]) AND $_POST[ 'kalender' ] == 'yes')
        {
            $timestamp = strtotime(get_datime());
            $page = str_replace('admin.php', 'index.php', $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ]);
            if (1 == db_result(db_query("SELECT COUNT(*) FROM `prefix_kalender` WHERE `text` LIKE '%more-" . $wid . "]%'"), 0))
            {
                db_query("UPDATE `prefix_kalender` SET `time` = " . $timestamp . ", `title` = 'Lastwar gegen " . $_POST[ 'gegner' ] . "', `text` = '" . $_POST[ 'mtyp' ] . " " . $_POST[ 'mod' ] . " in " . $_POST[ 'game' ] . " gegen [url=" . $_POST[ 'page' ] . "]" . $_POST[ 'gegner' ] . "[/url]\n\n[url=http://" . $page . "?wars-more-" . $wid . "]details des Wars[/url]' WHERE `text` LIKE '%more-" . $wid . "]%'");
            }
            else
            {
                db_query("INSERT INTO `prefix_kalender` (`time`, `title`, `text`, `recht`) VALUES (" . $timestamp . ",'Lastwar gegen " . $_POST[ 'gegner' ] . "', '" . $_POST[ 'mtyp' ] . " " . $_POST[ 'mod' ] . " in " . $_POST[ 'game' ] . " gegen [url=" . $_POST[ 'page' ] . "]" . $_POST[ 'gegner' ] . "[/url]\n\n[url=http://" . $page . "?wars-more-" . $wid . "]details des Wars[/url]', 0)");
            }
        }
        $msg = '<tr class="Cmite"><td colspan="2">Erfolgreich ver&auml;ndert</td></tr>';
    }
}
if (!empty($_GET[ 'pkey' ]))
{
    $erg = db_query("SELECT DATE_FORMAT(`datime`,'%d.%m.%Y.%H.%i.%s') as `datime`, `id`,`status`,`wlp`,`owp`,`opp`,`gegner`,`tag`,`page`,`mail`,`icq`,`tid`,`wo`,`mod`,`game`,`mtyp`,`land`,`txt` FROM `prefix_wars` WHERE `id` = '" . $_GET[ 'pkey' ] . "'");
    $_ilch = db_fetch_assoc($erg);
    $_ilch[ 'pkey' ] = $_GET[ 'pkey' ];
    list($_ilch[ 'day' ], $_ilch[ 'mon' ], $_ilch[ 'jahr' ], $_ilch[ 'stu' ], $_ilch[ 'min' ], $_ilch[ 'sek' ]) = explode('.', $_ilch[ 'datime' ]);
    $_ilch[ 'kalck' ] = (db_result(db_query("SELECT COUNT(*) FROM `prefix_kalender` WHERE `text` LIKE '%more-" . $_GET[ 'pkey' ] . "]%'"), 0, 0) == 1 ? ' checked' : '');
    $wid = $_GET[ 'pkey' ];
    for ($i = 1; $i <= 5; $i++)
    {
        $erg = db_query("SELECT `map`,`opp`,`owp` FROM `prefix_warmaps` WHERE `mnr` = " . $i . " AND `wid` = " . $wid);
        if (db_num_rows($erg) == 0)
        {
            $_ilch[ 'map' . $i ] = '';
            $_ilch[ 'opp' . $i ] = '';
            $_ilch[ 'owp' . $i ] = '';
        }
        else
        {
            $mpr = db_fetch_assoc($erg);
            $_ilch[ 'map' . $i ] = $mpr[ 'map' ];
            $_ilch[ 'opp' . $i ] = $mpr[ 'opp' ];
            $_ilch[ 'owp' . $i ] = $mpr[ 'owp' ];
        }
    }
}
else
{
    $_ilch = array(
    'tag' => '',
    'mail' => '',
    'icq' => '',
    'wo' => '',
    'pkey' => 0,
    'wlp' => '',
    'opp' => '',
    'owp' => '',
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
    for ($i = 1; $i <= 5; $i++)
    {
        $_ilch[ 'map' . $i ] = '';
        $_ilch[ 'opp' . $i ] = '';
        $_ilch[ 'owp' . $i ] = '';
    }
}
$_ilch[ 'msg' ] = (isset($msg) ? $msg : '');
$_ilch[ 'tid' ] = dblistee($_ilch[ 'tid' ], "SELECT `id`, `name` FROM `prefix_groups` ORDER BY `name`");
$_ilch[ 'mod' ] = dblistee($_ilch[ 'mod' ], "SELECT DISTINCT `mod`,`mod` FROM `prefix_wars` ORDER BY `mod`");
$_ilch[ 'game' ] = dblistee($_ilch[ 'game' ], "SELECT DISTINCT `game`,`game` FROM `prefix_wars` ORDER BY `game`");
$_ilch[ 'mtyp' ] = dblistee($_ilch[ 'mtyp' ], "SELECT DISTINCT `mtyp`,`mtyp` FROM `prefix_wars` ORDER BY `mtyp`");
$_ilch[ 'land' ] = arlistee($_ilch[ 'land' ], get_nationality_array());
$_ilch[ 'wlp' ] = arlistee($_ilch[ 'wlp' ], get_wlp_array());
$_ilch[ 'ANTISPAM' ] = get_antispam('adminuser_action', 0, true);
$tpl->set_ar_out($_ilch, 0);
$page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1);
$limit = 20;
$class = '';
$MPL = db_make_sites($page, 'WHERE `status` = 3', $limit, 'admin.php?wars-last', 'wars');
$anfang = ($page - 1) * $limit;
$abf = "SELECT `id`,`gegner`,`game` FROM `prefix_wars` WHERE `status` = 3 ORDER BY `id` DESC LIMIT " . $anfang . "," . $limit;
$erg = db_query($abf);
while ($row = db_fetch_assoc($erg))
{
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row[ 'class' ] = $class;
    $row[ 'game' ] = get_wargameimg($row[ 'game' ]);
    $tpl->set_ar($row);
    $tpl->out(1);
}
$tpl->set('MPL', $MPL);
$tpl->out(2);
$design->footer();
?>