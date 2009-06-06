<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

function getsmiliear () {
    $ar = array();
    $o = opendir('include/images/smiles');
    while ($f = readdir($o)) {
        if ($f == '.' OR $f == '..') {
            continue;
        }
        $ar[$f] = $f;
    }
    return ($ar);
}
function admin_smilies_escape_smilies ($s) {
    $s = unescape($s);
    $s = preg_replace("/=\+:/", "", $s);
    return ($s);
}
function getpakar () {
    $ar = array();
    $o = opendir('include/images/smiles');
    while ($f = readdir($o)) {
        if (substr($f, - 4) == '.pak') {
            $ar[$f] = $f;
        }
    }
    return ($ar);
}
// pak paket erstellen.
if ($menu->get(1) == 'createpak') {
    $name = 'smilies' . date('Y-m-d') . '.pak';

    header("Content-type: application/octet-stream");
    header("Content-disposition: attachment; filename=" . $name);
    header("Pragma: no-cache");
    header("Expires: 0");

    $erg = db_query("SELECT emo, ent, url FROM prefix_smilies");
    while ($r = db_fetch_assoc($erg)) {
        echo admin_smilies_escape_smilies($r['url']) . '=+:' . admin_smilies_escape_smilies($r['emo']) . '=+:' . admin_smilies_escape_smilies($r['ent']) . "\n";
    }
    exit();
}
// header ausgeben
$design = new design ('Admins Area', 'Admins Area', 2);
$design->header();
// smilie loeschen
if ($menu->getA(1) == 'd' AND is_numeric($menu->getE(1))) {
    db_query('DELETE FROM `prefix_smilies` WHERE id = "' . $menu->getE(1) . '" LIMIT 1');
}
// eintragen / aendern
if (isset($_POST['sub'])) {
    $ent = escape($_POST['ent'], 'string');
    $emo = escape($_POST['emo'], 'string');
    $url = escape($_POST['url'], 'string');
    if (empty($_POST['sid'])) {
        db_query("INSERT INTO `prefix_smilies` (ent, url,emo) VALUES ('" . $ent . "','" . $url . "','" . $emo . "')");
    } else {
        $sid = escape($_POST['sid'], 'integer');
        db_query("UPDATE `prefix_smilies` SET ent = '" . $ent . "',emo = '" . $emo . "', url = '" . $url . "' WHERE id = " . $sid);
    }
}
// hochladen
if (isset($_POST['u'])) {
    foreach ($_FILES['ar']['name'] AS $k => $v) {
        if (!empty($_FILES['ar']['name'][$k])) {
            $name = $_FILES['ar']['name'][$k];
            $bild_url = 'include/images/smiles/' . $name;
            if (@move_uploaded_file ($_FILES['ar']['tmp_name'][$k], $bild_url)) {
                @chmod($bild_url, 0777);
                echo '"' . $name . '" wurde erfolgreich hochgeladen<br />';
            } else {
                echo 'konnte "' . $name . '" nicht hochladen<br />';
            }
            echo '<br />';
            echo '<br />';
        }
    }
}
// pak file eintragen
if (isset($_POST['i']) AND !empty($_POST['pak']) AND file_exists('include/images/smiles/' . str_replace('../', '', $_POST['pak']))) {
    $ar = @file ('include/images/smiles/' . str_replace('../', '', $_POST['pak']));
    echo '<table border="1"><tr><th>Status</th><th>Dateiname</th><th>Beschreibung</th><th>Smilie Code</th></tr>';
    foreach($ar as $v) {
        list($url, $emo, $ent) = explode('=+:', $v);
        $emo = trim(escape($emo, 'string'));
        $ent = trim(escape($ent, 'string'));
        $url = trim(escape($url, 'string'));
        if (empty($emo) OR empty($ent) OR empty($url) OR !file_exists('include/images/smiles/' . $url) OR 0 != db_result(db_query("SELECT COUNT(*) FROM prefix_smilies WHERE url = '" . $url . "' OR ent = '" . $ent . "' OR emo = '" . $emo . "'"), 0)) {
            echo '<tr><td>schon in der Datenbank oder Datei nicht vorhanden</td><td>' . $url . '</td><td>' . $emo . '</td><td>' . $ent . '</td></tr>';
        } else {
            db_query("INSERT INTO prefix_smilies (emo,ent,url) VALUES ('" . $emo . "','" . $ent . "','" . $url . "')");
            echo '<tr><td>eingetragen</td><td><img src="include/images/smiles/' . $url . '"></td><td>' . $emo . '</td><td>' . $ent . '</td></tr>';
        }
    }
    echo '</table><br /><br />';
}

$ar = array ('url' => '', 'ent' => '', 'emo' => '', 'id' => '');
if ($menu->getA(1) == 'e' AND is_numeric($menu->getE(1))) {
    $ar = db_fetch_assoc(db_query("SELECT url, ent, emo, id FROM prefix_smilies WHERE id = " . $menu->getE(1)));
}
$smilies_ar = getsmiliear();
$ar['surl'] = (empty($ar['url'])?key($smilies_ar):$ar['url']);
$ar['url'] = arlistee ($ar['url'], $smilies_ar);
$ar['pakfile'] = arlistee ('', getpakar());

$tpl = new tpl ('smilies', 1);
$tpl->set_ar_out($ar, 0);
$i = 0;
$class = 'Cnorm';
$o = opendir('include/images/smiles');
while ($f = readdir($o)) {
    if ($f == '.' OR $f == '..' OR 0 != db_result(db_query("SELECT COUNT(*) FROM prefix_smilies WHERE url = '" . $f . "'"), 0)) {
        continue;
    }
    // eintrage wenn vorhanden...
    if (isset($_POST['chk'][$f])) {
        if ($_POST['ak'] == 1) {
            $ent = escape($_POST['ent'][$f], 'string');
            $emo = escape($_POST['emo'][$f], 'string');
            $url = escape($f, 'string');
            db_query("INSERT INTO prefix_smilies (url,ent,emo) VALUES ('" . $url . "', '" . $ent . "', '" . $emo . "')");
        } elseif ($_POST['ak'] == 2) {
            @unlink ('include/images/smiles/' . $f);
        }
        continue;
    }
    // wenn nicht abgesendet dann nicht eintragen.
    $i++;
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $e = explode('.', $f);
    unset($e[count($e) - 1]);
    $e = implode('.', $e);
    $tpl->set('class', $class);
    $tpl->set('ent', ':' . $e . ':');
    $tpl->set('emo', $e);
    $tpl->set('url', $f);

    $tpl->out(1);
}
closedir($o);

if ($i <= 0) {
    echo '<tr class="Cmite"><td colspan="3">in dem Ordner sind keine neuen Smilies</td></tr>';
}

$tpl->out(2);
$clas = 'Cnorm';
$erg = db_query('SELECT * FROM `prefix_smilies`');
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row['class'] = $class;
    $tpl->set_ar_out($row, 3);
}
$tpl->out(4);

$design->footer();

?>