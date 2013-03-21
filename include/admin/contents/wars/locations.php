<?php
// todo

# edit



defined('main') or die('no direct access');
defined('admin') or die('only admin access');

include_once('include/includes/func/gallery.php');
$tpl = new tpl('wars/locations', 1);
$oneout = array();
$oneout['thumbwidth']	= 100;
$oneout['mapliste']	= '';
// Jquery Dialoge für Erfolg oder Misserfolg ok/nok
$eintrag_ok = '<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        $( "#okdialog" ).dialog({
            height: 200,
            width: 300,
            modal: true,
            autoOpen: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
</script>';
$eintrag_nok = '<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        $( "#Nokdialog" ).dialog({
            height: 200,
            width: 300,
            modal: true,
            autoOpen: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
</script>';
// Jquery Dialoge für Erfolg oder Aktivieren/Deaktivieren                
$aktiviert = '<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        $( "#akdialog" ).dialog({
            height: 200,
            width: 300,
            modal: true,
            autoOpen: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                    window.location = "admin.php?wars-locations";
                }
            }
        });
    });
</script>';
$deaktiviert = '<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        $( "#deakdialog" ).dialog({
            height: 200,
            width: 300,
            modal: true,
            autoOpen: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                    window.location = "admin.php?wars-locations";
                }
            }
        });
    });
</script>';

// Formular für Neue Karte absenden
if (isset($_POST['newsubmit']) and chk_antispam('adminuser_action', true)) {
    debug('########## FormUpload-Log Start ##########');
    $newmapname         = escape($_POST['newmapname'], 'string');
    $newgame            = escape($_POST['newgame'], 'integer');
    $newicon            = escape($_POST['newicon'], 'string');
    $newinaktive        = escape($_POST['newinaktive'], 'integer');
    $outar['thumbwidth']= 100;
    $uploadname         = '';
    //file up
    if(!empty($_FILES['newpic']['tmp_name'])) {
        debug('File Selected... beginning upload');
        $plaindir           = 'include/images/locations/';
        $gamedirqyr         = db_query('SELECT shortname FROM `prefix_wars_games` WHERE inaktive = 0 AND id='.$newgame);
        $gamedirplain       = db_result($gamedirqyr,0);
        $gamedir            = strtolower($gamedirplain);
        $updir              = $plaindir.$gamedir.'/';
        $dirok = 0;
        if(is_dir($updir) && is_writeable($updir)) {
            debug ('Verzeichnis vorhanden und beschreibbar');
            $dirok = 1;
        } elseif(!is_dir($updir) && is_writeable($plaindir)){
            if(mkdir($updir, 0777)){
               debug ('Verzeichnis erstellt');
               $dirok = 1;
            } else {
                debug ('Verzeichnis '.$plaindir.' nicht beschreibbar. Verzeichnis '.$updir.' nicht vorhanden');
                $dirok = 0;
            }
        } else {
            debug ('Verzeichnis '.$updir.' nicht beschreibbar oder nicht vorhanden');
            $dirok = 0;
        }
        if($dirok){
            debug ('Verzeichnisprüfung abgeschlossen Datei handling beginnt');
            $uploadname = escape($_FILES['newpic']['name'], 'string');;
            $tmp = explode('.',$uploadname);
            $tm1 = count($tmp) -1;
            $endung = $tmp[$tm1];
            unset($tmp[$tm1]);
            $uploadname = implode('',$tmp);
            // Auf MIME TYPE prüfen
            $extype = explode('/', $_FILES["newpic"]["type"], 2);
            $mime   = $extype[0];
            debug(' Datei hat den Typ '.$mime);
            if ($mime == 'image') {
                move_uploaded_file($_FILES["newpic"]["tmp_name"], $updir.$uploadname.'.'.$endung);
                create_thumb($updir.$uploadname.'.'.$endung, $updir.'thumb_'.$uploadname.'.'.$endung, $outar['thumbwidth']);
                $uploadname = $gamedir.'/'.$uploadname.'.'.$endung;
            } else {
                debug('Datei Ist Kein Bild...');
            }
        }
        if ($_FILES['newpic']['error'] == 0) {
            debug('File Upload OK');
            $qry 	=	db_query("INSERT INTO `prefix_wars_locations` (`name`,`pic`,`gid`,`inaktive`) VALUES ('".$newmapname."','".$uploadname."','".$newgame."',".$newinaktive.")");
            if($qry) {
                debug('DB-Eintrag erfolgreich');
                echo $eintrag_ok;
            }
        } else {
            echo $eintrag_nok;
            debug('FILE Upload ERROR');
            debug( $_FILES['newpic']['error'] );
        }
    } else {
        if(!empty($newicon)){
            $uploadname = $newicon;
            debug('icon name aus db');
        } elseif (file_exists('include/images/locations/.no-image-location.png') and file_exists('include/images/locations/thumb_.no-image-location.png')) {
            $uploadname = '.no-image-location.png';
            debug('default-pic und thumb vorhanden');
        }
        $qry 	=	db_query("INSERT INTO `prefix_wars_locations` (`name`,`pic`,`gid`,`inaktive`) VALUES ('".$newmapname."','".$uploadname."','".$newgame."',".$newinaktive.")");
        if($qry) {
            debug('DB-Eintrag erfolgreich');
            echo $eintrag_ok;
        } else {
            echo $eintrag_nok;
        }
    }
    debug('########## FormUpload-Log END ##########');
}
// form neue Karte Ende
$statusänderung = escape($menu->get(2), 'string');
$statusänderungsid = escape($menu->get(3), 'integer');
if ( $statusänderung == 'stat' AND !empty($statusänderungsid)) {
    $state=db_result(db_query('SELECT inaktive FROM `prefix_wars_locations` WHERE id = '.$statusänderungsid),0);
    if($state==1){
        debug('########## Karte aktivieren ##########');
        $reakqyr = db_query('UPDATE `prefix_wars_locations` SET inaktive=0 WHERE id = "'.$statusänderungsid.'" LIMIT 1');
        if($reakqyr){
            echo $aktiviert;
        }
    } else {
        debug('########## Karte deaktivieren ##########');
        $deakqyr = db_query('UPDATE `prefix_wars_locations` SET inaktive=1 WHERE id = "'.$statusänderungsid.'" LIMIT 1');
        if($deakqyr){
            echo $deaktiviert;
        }
    }
}
$seite	= escape($menu->getA(2), 'string');
$getid	= escape($menu->getE(2), 'integer');
switch ($seite) {
case 'e':
    if($getid != 0 and !empty($getid)) {
        $editqry = db_query("SELECT * FROM `prefix_wars_locations` WHERE id = ".$getid."");
        $outar = db_fetch_assoc($editqry);
        $outar['aktuellespic'] = '<img src="include/images/locations/'.$outar['pic'].'"/>';
        $game=(isset($_POST['editgame'])?$_POST['editgame']:$outar['gid']);
        $icon=(isset($_POST['editicon'])?$_POST['editicon']:$outar['pic']);
        $outar['game']=dblistee($game, 'SELECT id,name FROM `prefix_wars_games` WHERE inaktive = 0');
        $sx=db_query('SELECT shortname FROM `prefix_wars_games` WHERE inaktive = 0 AND id='.$game);
        if($sx){        
            $s=db_result($sx,0);
            $s=strtolower($s).'/';
        } else{
            $s='dberror';
        }
        $outar['icon']=arlistee($icon, get_locationpic_ar($s) );
        $outar['inactiven']=($outar['inaktive']==0?'checked':'');
        $outar['inactivej']=($outar['inaktive']==1?'checked':'');
        if (isset($_POST['editsubmit']) and chk_antispam('adminuser_action', true)) {
            $editmapname         = escape($_POST['editmapname'], 'string');
            $editgame            = escape($_POST['editgame'], 'integer');
            $editicon            = escape($_POST['editicon'], 'string');
            $editinaktive        = escape($_POST['editinaktive'], 'integer');
            $outar['thumbwidth']= 100;
            $uploadname         = '';
            $this_id		= $getid;
            //file up
            if(!empty($_FILES['editpic']['tmp_name'])) {
                debug('File Selected... beginning upload');
                $plaindir           = 'include/images/locations/';
                $gamedirqyr         = db_query('SELECT shortname FROM `prefix_wars_games` WHERE inaktive = 0 AND id='.$editgame);
                $gamedirplain       = db_result($gamedirqyr,0);
                $gamedir            = strtolower($gamedirplain);
                $updir              = $plaindir.$gamedir.'/';
                $dirok = 0;
                if(is_dir($updir) && is_writeable($updir)) {
                    debug ('Verzeichnis vorhanden und beschreibbar');
                    $dirok = 1;
                } elseif(!is_dir($updir) && is_writeable($plaindir)){
                    if(mkdir($updir, 0777)){
                        debug ('Verzeichnis erstellt');
                        $dirok = 1;
                    } else {
                        debug ('Verzeichnis '.$plaindir.' nicht beschreibbar. Verzeichnis '.$updir.' nicht vorhanden');
                        $dirok = 0;
                    }
                } else {
                    debug ('Verzeichnis '.$updir.' nicht beschreibbar oder nicht vorhanden');
                    $dirok = 0;
                }
                if($dirok){
                    debug ('Verzeichnisprüfung abgeschlossen Datei handling beginnt');
                    $uploadname = escape($_FILES['editpic']['name'], 'string');;
                    $tmp = explode('.',$uploadname);
                    $tm1 = count($tmp) -1;
                    $endung = $tmp[$tm1];
                    unset($tmp[$tm1]);
                    $uploadname = implode('',$tmp);
                    // Auf MIME TYPE prüfen
                    $extype = explode('/', $_FILES["editpic"]["type"], 2);
                    $mime   = $extype[0];
                    debug(' Datei hat den Typ '.$mime);
                    if ($mime == 'image') {
                        move_uploaded_file($_FILES["editpic"]["tmp_name"], $updir.$uploadname.'.'.$endung);
                        create_thumb($updir.$uploadname.'.'.$endung, $updir.'thumb_'.$uploadname.'.'.$endung, $outar['thumbwidth']);
                        $uploadname = $gamedir.'/'.$uploadname.'.'.$endung;
                    } else {
                        debug('Datei Ist Kein Bild...');
                    }
                }
                if ($_FILES['editpic']['error'] == 0) {
                    debug('File Upload OK');
                     // DB UPDATE (`name`,`pic`,`gid`,`inaktive`)
                    $qry = db_query("UPDATE `prefix_wars_locations` SET
                        name 	= '".$editmapname."',
                        pic 	= '".$uploadname."',
                        gid 	= '".$editgame."',
                        inaktive = '".$editinaktive."'
                        WHERE
                        id = ".$getid."");
                    if($qry) {
                        debug('DB-Eintrag erfolgreich');
                        wd('admin.php?wars-locations', 'Daten gespeichert', 3);
                    }
                } else {
                    wd('admin.php?wars-locations-'.$seite.$getid, 'Speicher fehlgeschlagen', 3);
                    debug('FILE Upload ERROR');
                    debug( $_FILES['editpic']['error'] );
                }
            } else {
                if(!empty($editicon)){
                    $uploadname = $editicon;
                    debug('icon name aus db');
                } elseif (file_exists('include/images/locations/.no-image-location.png') and file_exists('include/images/locations/thumb_.no-image-location.png')) {
                    $uploadname = '.no-image-location.png';
                    debug('default-pic und thumb vorhanden');
                }
                // DB UPDATE (`name`,`pic`,`gid`,`inaktive`)
                    $qry = db_query("UPDATE `prefix_wars_locations` SET
                        name 	= '".$editmapname."',
                        pic 	= '".$uploadname."',
                        gid 	= '".$editgame."',
                        inaktive = '".$editinaktive."'
                        WHERE
                        id = ".$getid."");
                if($qry) {
                    debug('DB-Eintrag erfolgreich');
                    wd('admin.php?wars-locations', 'Daten gespeichert', 3);
                } else {
                    wd('admin.php?wars-locations-'.$seite.$getid, 'Speicher fehlgeschlagen', 3);
                }
            }
        }
        debug('########## FormUpload-Log END ##########');           
        $outar['ANTISPAM'] = get_antispam('adminuser_action', 0, true);
        $tpl->set_ar_out($outar, 5);
    }
break;
case 'j';
    $sx=db_query('SELECT shortname FROM `prefix_wars_games` WHERE inaktive = 0 AND id='.$menu->getE(2));
    if($sx){        
        $s=db_result($sx,0);
        $s=strtolower($s).'/';
    } else{
        $s='dberror';
    }
    echo arlistee('', get_locationpic_ar($s) );
break;
default:
    $_POST['idtemp']=(isset($_POST['idtemp'])?$_POST['idtemp']:'');
    if (is_numeric($_POST['idtemp'])) {
        $r = db_fetch_assoc(db_query("SELECT * FROM `prefix_wars_locations` WHERE id = ".$_POST['idtemp']));
        $oneout['id'] = $_POST['idtemp'];
    } else {
        $oneout = array ('id' => '', 'name' => '', 'gid' => '', 'icon' => '', 'inaktive' => 0);
    }
    // Kartenliste ausgeben
    $limit  = 15;
    $page   = ( $menu->getA(1) == 'p' ? $menu->getE(1) : 1 );
    $anfang = ($page - 1) * $limit;
    if (isset($_POST['submit'])) {
        $suchstr    = escape($_POST['suche'], 'string');
        $WHERE      = "WHERE name LIKE '%".$suchstr."%' OR tag LIKE '%".$suchstr."%'";
    } else {
        $WHERE      = '';
    }
    $class='Cmite';
    $oneout['siteindex']	= db_make_sites ($page ,$WHERE ,$limit ,'admin.php?locations' ,'wars_locations');
    $oneout['antispam'] = get_antispam('adminuser_action', 0, true);
    $oneout['name']=(isset($_POST['name'])?$_POST['name']:$oneout['name']);
    $game=(isset($_POST['newgame'])?$_POST['newgame']:$oneout['gid']);
    $icon=(isset($_POST['newicon'])?$_POST['newicon']:$oneout['icon']);
    $oneout['inactiven']=($oneout['inaktive']==0?'checked':'');
    $oneout['inaktivej']=($oneout['inaktive']==1?'checked':'');
    $oneout['game']=dblistee($game, 'SELECT id,name FROM `prefix_wars_games` WHERE inaktive = 0');
    $oneout['icon']=arlistee($icon, get_locationpic_ar('choose') );
    $tpl->set_ar_out($oneout, 0);
    $erg=db_query("SELECT * FROM `prefix_wars_locations` ".$WHERE." ORDER BY name LIMIT ".$anfang.",".$limit);
    while($row=db_fetch_assoc($erg)){
        $class=($class=='Cnorm'?'Cmite':'Cnorm');
        $row['class']=$class;
        $row['pic']=preg_replace('#/#', '/thumb_', $row['pic']);
        $row['game']=get_game_icon($row['gid']);
        $row['chekimg']=($row['inaktive']==1?'nop':'ok');
        $row['inaktive']=($row['inaktive']==1?'Inaktiv':'Aktiv');
        $tpl->set_ar_out($row,1);
    }
    $tpl->out(2);
    $erg2=db_query("SELECT * FROM `prefix_wars_locations` ".$WHERE." ORDER BY name LIMIT ".$anfang.",".$limit);
    while($row2=db_fetch_assoc($erg2)){
        $row['pic']=preg_replace('#/#', '/thumb_', $row['pic']);
        $row2['inaktive']=($row2['inaktive']==1?'Nein':'Ja');
        $tpl->set_ar_out($row2,3);
    }
    $tpl->out(4);
break;
}
?>