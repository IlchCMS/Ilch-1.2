<?php

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$ILCH_HEADER_ADDITIONS .= '<link rel="stylesheet" type="text/css" href="include/includes/css/contentslider/style.css" />' . "\n";
$ILCH_HEADER_ADDITIONS .= '<script type="text/javascript" src="include/includes/js/contentslider/slider.js"></script>' . "\n";

function genkey($anz) {
    $letterArray = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
    $key = '';
    for ($i = 0; $i < $anz; $i++) {
        mt_srand((double) microtime() * 1000000);
        $zufallZahl = mt_rand(0, 62);
        $key .= $letterArray[$zufallZahl];
    }
    return ($key);
}

// verschieben
if ($menu->getA(1) == 'o' or $menu->getA(1) == 'u') {
    $pos = $menu->get(2);
    $id = $menu->getE(1);
    $nps = ($menu->getA(1) == 'u' ? $pos + 1 : $pos - 1);
    $anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_contentslider`"), 0);
    if ($nps < 0) {
        db_query("UPDATE `prefix_contentslider` SET pos = " . $anz . " WHERE id = " . $id);
        db_query("UPDATE `prefix_contentslider` SET pos = pos -1");
    }
    if ($nps >= $anz) {
        db_query("UPDATE `prefix_contentslider` SET pos = -1 WHERE id = " . $id);
        db_query("UPDATE `prefix_contentslider` SET pos = pos +1");
    }
    if ($nps < $anz and $nps >= 0) {
        db_query("UPDATE `prefix_contentslider` SET pos = " . $pos . " WHERE pos = " . $nps);
        db_query("UPDATE `prefix_contentslider` SET pos = " . $nps . " WHERE id = " . $id);
    }
}

$design = new design('Ilch Admin-Control-Panel :: Contentslider ', '', 2);
$tpl = new tpl('contentslider.htm', 1);
$design->header();

$um = $menu->get(1);
switch ($um) {

    default :

        $tpl->out(1);
        $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
        $limit = 15;
        $class = 'Cnorm';
        $MPL = db_make_sites($page, '', $limit, '?contentslider', 'contentslider');
        $anfang = ($page - 1) * $limit;
        $abf = sprintf("SELECT `id`,`name`,`link`,`pos`,`status`,`target` FROM `prefix_contentslider` ORDER BY `pos` ASC LIMIT %d,%d", $anfang, $limit);
        $erg = db_query($abf);
        while ($row = db_fetch_assoc($erg)) {
            $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
            $status = $row['status'] == 1 ? '1' : '0';
            $row['class'] = $class;
            $row['status'] = $status;
            $tpl->set_ar($row);
            $tpl->out(2);
        }
        $tpl->set('MPL', $MPL);
        $tpl->out(3);
        break;

    case 'post' :

        // aendern / eintragen
        if (isset($_POST['sub']) AND chk_antispam('adminuser_action', true)) {
            if (!empty($_POST['name'])) {
                $_POST['name'] = escape($_POST['name'], 'string');
                $_POST['link'] = get_homepage(escape($_POST['link'], 'string'));
                $_POST['target'] = escape($_POST['target'], 'string');
                $_POST['status'] = (isset($_POST['status'])) ? escape($_POST['status'], 'integer') : 0;
                $_POST['pkey'] = escape($_POST['pkey'], 'integer');
                $imgbig_update = "";
                $imgbig_in = "";
                $imgupdate = false;
                $id = (empty($_POST['pkey']) ? db_result(db_query("SHOW TABLE STATUS FROM `" . DBDATE . "` LIKE 'prefix_contentslider'"), 0, 'Auto_increment') : $_POST['pkey']);
                if (!empty($_FILES['imgbig_file']['name'])) {
                    $rile_type = ic_mime_type($_FILES['imgbig_file']['tmp_name']);
                    $parts = pathinfo(escape($_FILES['imgbig_file']['name'], 'string'));
                    $extension = trim($parts['extension']);
                    $exAr = array('gif', 'png', 'jpg', 'jpeg');
                    if (in_array($extension, $exAr) and substr($rile_type, 0, 6) == 'image/') {
                        $nname = strtolower('include/images/contentslider/' . $id . '_' . genkey(6) . '.' . $extension);
                        if (move_uploaded_file($_FILES['imgbig_file']['tmp_name'], $nname)) {
                            @chmod($nname, 0777);
                            $imgbig_update = "`banner` = '" . $nname . "',";
                            $imgbig_in = $nname;
                            $imgupdate = true;
                        }
                    }
                }
                if (empty($_POST['pkey']) and $_POST['action'] == 'new') {
                    $_POST['pos'] = db_result(db_query("SELECT COUNT(*) FROM `prefix_contentslider`"), 0);
                    $q = sprintf("INSERT INTO `prefix_contentslider` (`name`,`banner`,`link`,`target`,`pos`,`status`) VALUES ('%s','%s','%s','%s','%d','%d')", $_POST['name'], $imgbig_in, $_POST['link'], $_POST['target'], $_POST['pos'], $_POST['status']);
                } else {
                    $q = db_query(sprintf("SELECT `id`,`banner`,`name` FROM `prefix_contentslider` WHERE `id` = '%d'", $_POST['pkey']));
                    $r = db_fetch_assoc($q);
                    if (db_num_rows($q) > 0) {
                        if ($imgupdate === true and file_exists($r['banner']))
                            @unlink($r['banner']);
                        if (file_exists($r['banner'])) {
                            $parts = pathinfo($r['banner']);
                            $nname = strtolower('include/images/contentslider/' . $r['id'] . '_' . genkey(6) . '.' . $parts['extension']);
                            $imgbig_update = "`banner` = '" . $nname . "',";
                            rename($r['banner'], $nname);
                        }
                    }
                    $q = sprintf("UPDATE `prefix_contentslider` SET `name` = '%s', %s `link` = '%s', `target` = '%s', `status` = '%d' WHERE `id` = %d", $_POST['name'], $imgbig_update, $_POST['link'], $_POST['target'], $_POST['status'], $_POST['pkey']);
                }
                db_query($q);
                $wd = array('text' => empty($_POST['pkey']) ? $lang['insertsuccessful'] : 'Erfolgreich bearbeitet.', 'link' => 'contentslider');
            } else {
                $wd = array('text' => 'Bitte einen Titel angeben.', 'link' => empty($_POST['pkey']) ? 'contentslider-post' : 'contentslider-post-e' . $_POST['pkey']);
            }
            wd('admin.php?' . $wd['link'], $wd['text'], 1);
        } else {
            $tpl = new tpl('contentslider.htm', 1);
            $tpl->set('ANTISPAM', get_antispam('adminuser_action', 0, true));

            // aendern vorbereiten
            if ($menu->getA(2) == 'e') {
                $erg = db_query(sprintf("SELECT `id`,`name`,`banner`,`link`,`target`,`status` FROM `prefix_contentslider` WHERE `id` = %d", $menu->getE(2)));
                $_ilch = db_fetch_assoc($erg);
                $_ilch['pkey'] = $menu->getE(2);
                $_ilch['action'] = 'edit';
                if ($_ilch['target'] == '_self') {
                    $_ilch['target1'] = 'checked';
                    $_ilch['target2'] = '';
                } else {
                    $_ilch['target1'] = '';
                    $_ilch['target2'] = 'checked';
                }
                if (file_exists($_ilch['banner'])) {
                    $imgbig = '<a href="#" class="sliderbutton slidertip" style="padding-left:30px;"><img src="include/images/icons/image.png" alt="Preview" title="Preview" />Preview<span class="sliderbanner"><img src="' . $_ilch['banner'] . '" alt="' . $_ilch['name'] . '" title="' . $_ilch['name'] . '" /></span></a>' . "\n";
                } else {
                    $imgbig = '<div class="sliderbutton">Kein Banner vorhanden!</div>' . "\n";
                }
                $_ilch['img'] = $imgbig;
                $_ilch['head'] = 'Eintrag bearbeiten';
                $_ilch['atc'] = 'Bearbeiten';
            } else {
                $_ilch = array(
                    'pkey' => '',
                    'id' => '',
                    'banner' => '',
                    'status' => '',
                    'name' => '',
                    'link' => '',
                    'target1' => 'checked',
                    'target2' => '',
                    'img' => '',
                    'action' => 'new',
                    'head' => 'Neuen Eintrag',
                    'atc' => 'Eintragen'
                );
            }
            $tpl->set_ar_out($_ilch, 0);
        }
        break;

    case 'del' :

        // loeschen
        $state = false;
        $q = db_query(sprintf("SELECT `id`,`banner`,`pos` FROM `prefix_contentslider` WHERE `id` = '%d'", $menu->get(2)));
        if (db_num_rows($q) > 0) {
            $r = db_fetch_assoc($q);
            if (file_exists($r['banner']))
                @unlink($r['banner']);
            db_query(sprintf("DELETE FROM `prefix_contentslider` WHERE `id` = '%d'", $r['id']));
            db_query(sprintf("UPDATE `prefix_contentslider` SET `pos` = pos -1 WHERE `pos` > '%d'", $r['pos']));
            $state = true;
        }
        wd('admin.php?contentslider', $state == true ? $lang['deletesuccessful'] : 'OoooOpss', 1);
        break;

    case 'show' :

        // aktiv / inaktiv
        db_query(sprintf("UPDATE `prefix_contentslider` SET `status` = IF( `status` = 1,0,1 ) WHERE `id` = '%d' LIMIT 1", $menu->get(2)));
        wd('admin.php?contentslider', 'Erfolgreich bearbeitet.', 1);
}

$design->footer();
?>