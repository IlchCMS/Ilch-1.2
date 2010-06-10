<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

function show_members($gid, $tpl) {
    global $allgAr;
    // icq team bild, hier die zahl aendern.
    $teams_show_icq_pic = 7;

    $q = "SELECT `b`.`uid`, `a`.`icq`, `a`.`avatar`, `a`.`status`, `a`.`name`, `c`.`name` as `posi`, `staat` FROM `prefix_groupusers` `b` LEFT JOIN `prefix_user` `a` ON `a`.`id` = `b`.`uid` LEFT JOIN `prefix_groupfuncs` `c` ON `b`.`fid` = `c`.`id` WHERE `b`.`gid` = " . $gid . " ORDER BY `c`.`pos` ASC, a.name ASC";
    $erg = db_query($q);
    if (db_num_rows($erg) < 1) {
        return false;
    }
    $tpl->out(1);
    $class = 'Cnorm';
    while ($row = db_fetch_assoc($erg)) {
        $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
        $row[ 'class' ] = $class;
        if ($row[ 'staat' ] != '') {
            $row[ 'staat' ] = '<img src="include/images/flags/' . $row[ 'staat' ] . '" alt="" border="0">';
        } else {
            $row[ 'staat' ] = 'n/a';
        }
        $row[ 'status' ] = ($row[ 'status' ] ? 'aktiv' : 'inaktiv');
        if (!empty($row[ 'icq' ])) {
            $row[ 'icq' ] = '<a href="http://www.icq.com/whitepages/cmd.php?uin=' . $row[ 'icq' ] . '&action=add"><img src="http://wwp.icq.com/scripts/online.dll?icq=' . $row[ 'icq' ] . '&img=' . $teams_show_icq_pic . '" valign="bottom"  border="0"></a>';
        } else {
            $row[ 'icq' ] = 'n/a';
        }
        if ($allgAr[ 'teams_show_list' ] == 1) {
            if (empty($row[ 'avatar' ])) {
                $row[ 'avatar' ] = 'n/a';
            } else {
                $row[ 'avatar' ] = '<img src="' . $row[ 'avatar' ] . '" alt="Avatar von ' . $row[ 'name' ] . '" border="0" >';
            }
            $tpl->set_ar_out($row, 2);
        } else {
            $tpl->set_ar_out($row, 3);
        }
    }
    $tpl->out(4);
}

if ($menu->get(1) == 'show') {
    $groups = array(); //collect given teams
    $i = 2;
    while ($gid = escape($menu->get($i), 'integer')) {
        $groups[ ] = $gid;
        $i++;
    }
    // $gid = escape($menu->get(2), 'integer');
    if (count($groups) == 1) { // only 1 group to show
        $gid = $groups[ 0 ];
        $name = @db_result(db_query("SELECT `name` FROM `prefix_groups` WHERE `id` = " . $gid));
        $bild = @db_result(db_query("SELECT `img` FROM `prefix_groups` WHERE `id` = " . $gid));
        $title = $allgAr[ 'title' ] . ' :: Teams :: ' . $name;
        $hmenu = '<a class="smalfont" href="?teams">Teams</a> &raquo; ' . $name;
        $design = new design($title, $hmenu);
        $design->header();
        $tpl = new tpl('teams');
        if (!empty($bild)) {
            $show = '<img src="' . $bild . '" title="' . $name . '" alt="' . $name . '" border="0"></a>';
        } else {
            $show = '<b>' . $name . '</b>';
        }
        $tpl->set_out('show', $show, 0);
        show_members($gid, $tpl);
    } else { // more groups to show
        $title = $allgAr[ 'title' ] . ' :: Teams :: ';
        $hmenu = '<a class="smalfont" href="?teams">Teams</a>';
        $design = new design($title, $hmenu);
        $design->header();
        $tpl = new tpl('teams');

        foreach ($groups as $gid) {
            $sql = db_query("SELECT `name`, `img` FROM `prefix_groups` WHERE `id` =" . $gid);
            if (db_num_rows($sql) < 1) {
                continue;
            }
            $row = db_fetch_assoc($sql);
            if (!empty($row[ 'img' ])) {
                $show = '<img src="' . $row[ 'img' ] . '" title="' . $row[ 'name' ] . '" alt="' . $row[ 'name' ] . '" border="0"></a>';
            } else {
                $show = '<b>' . $row[ 'name' ] . '</b>';
            }
            $tpl->set_out('show', $show, 0);
            show_members($gid, $tpl);
        }
    }
} else {
    $title = $allgAr[ 'title' ] . ' :: Teams';
    $hmenu = 'Teams';
    $design = new design($title, $hmenu);
    $design->header();
    $tpl = new tpl('teams');
    $erg1 = db_query("SELECT `name`,`img`,`id` as `gid` FROM `prefix_groups` WHERE `zeigen` = 1 ORDER BY `pos`");
    while ($row = db_fetch_assoc($erg1)) {
        if (!empty($row[ 'img' ])) {
            $row[ 'show' ] = '<a href="index.php?teams-show-' . $row[ 'gid' ] . '"><img src="' . $row[ 'img' ] . '" title="' . $row[ 'name' ] . '" alt="' . $row[ 'name' ] . '" border="0"></a>';
        } else {
            $row[ 'show' ] = '<a href="index.php?teams-show-' . $row[ 'gid' ] . '"><b>' . $row[ 'name' ] . '</b></a>';
        }
        $tpl->set_ar_out($row, 0);
        if ($allgAr[ 'teams_show_cat' ] == 0) {
            show_members($row[ 'gid' ], $tpl);
        }
    }
}

$design->footer(0);

?>