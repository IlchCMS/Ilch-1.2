<?php
// todo

# include/images/locations -> rechte prüfen in install.php oder server-konfig
# include/images/locations -> ordnergröße in server-info ausgeben lassen


defined('main') or die('no direct access');
defined('admin') or die('only admin access');

include_once('include/includes/func/gallery.php');

	$tpl = new tpl('wars/locations', 1);
	$oneout = array();
	$oneout['thumbwidth']	= 100;
	$oneout['gegnerliste']	= '';
	$oneout['page']			= 'http://';
	// Flaggen und Länder ausgeben
	$oneout['nationen'] = '';
	$selectedNation = '';
	$flagsar = get_nationality_array();

	foreach ( $flagsar as $key => $value ) {

		$oneout['nationen'] .= '<option value="'.$key.'" selected="'.$selectedNation.'">'.$value.'</option>';

	}

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
                

	// Formular für Neue Gegner absenden
	if (isset($_POST['newsubmit']) and chk_antispam('adminuser_action', true)) {
		debug('########## FormUpload-Log Start ##########');

		$newmapname		    = escape($_POST['newmapname'], 'string');
		$newgame			= escape($_POST['newgame'], 'integer');
		$newicon    		= escape($_POST['newicon'], 'string');
	    $newinaktive        = escape($_POST['newinaktive'], 'integer');
		$updir			    = 'include/images/locations/';
		$outar['thumbwidth']	= 100;
		$uploadname = '';
		//file up
		if(!empty($_FILES['newlogo']['tmp_name'])) {
			debug('File Selected... generating filename');

			if(is_dir($updir) && is_writeable($updir)) {
				debug ('Verzeichnis vorhanden und beschreibbar');

				// Auf MIME TYPE prüfen
				$extype = explode('/', $_FILES["newlogo"]["type"], 2);
				$mime 	= $extype[0];
				debug(' Datei hat den Typ '.$mime);
				if ($mime == 'image') {

					while(file_exists($updir.$uploadname)) {
						$uploadname = mt_rand().'_'.$uploadname;
					}
					move_uploaded_file($_FILES["newlogo"]["tmp_name"], $updir.$uploadname);
					create_thumb($updir.$uploadname, $updir.'thumb_'.$uploadname, $outar['thumbwidth']);

				} else {

					debug('Datei Ist Kein Bild...');

				}
			} else {

				debug ('Verzeichnis '.$updir.' nicht beschreibbar oder nicht vorhanden');

			}


			if ($_FILES['newlogo']['error'] == 0) {

				debug('File Upload OK');

				$qry 	=	db_query("INSERT INTO `prefix_wars_locations` (tag,name,contact,email,icq,aim,yim,msn,xfire,ircnw,ircch,url,country,logo,inaktive)
                VALUES ('".$newclantag."','".$newclanname."','".$newcontact."','".$newemail."','".$newicq."','".$newaim."','".$newyim."','".$newmsn."','".$newxfire."','".$newircnw."','".$newircch."','".$newurl."','".$newnation."', '".$uploadname."',".$newinaktive.")");
                if($qry) {

					debug('DB-Eintrag erfolgreich');
					echo $eintrag_ok;
				}

			} else {
					echo $eintrag_nok;
				debug('FILE Upload ERROR');
				debug( $_FILES['newlogo']['error'] );
			}

		} else {
			if (file_exists('include/images/locations/.no-image-opponent.png') and file_exists('include/images/locations/thumb_.no-image-opponent.png')) {
				$uploadname = '.no-image-opponent.png';
				debug('default-pic und thumb vorhanden');
			}
            $qry 	=	db_query("INSERT INTO `prefix_wars_locations` (tag,name,contact,email,icq,aim,yim,msn,xfire,ircnw,ircch,url,country,logo,inaktive) VALUES ('".$newclantag."','".$newclanname."','".$newcontact."','".$newemail."','".$newicq."','".$newaim."','".$newyim."','".$newmsn."','".$newxfire."','".$newircnw."','".$newircch."','".$newurl."','".$newnation."', '".$uploadname."',".$inaktive.")");
			if($qry) {

				debug('DB-Eintrag erfolgreich');
				echo $eintrag_ok;
			} else {
				echo $eintrag_nok;
			}
		}

		debug('########## FormUpload-Log END ##########');

	} // form neue Gegner Ende
$statusänderung = escape($menu->get(2), 'string');
$statusänderungsid = escape($menu->get(3), 'integer');

if ( $statusänderung == 'stat' AND !empty($statusänderungsid)) {
    $state=db_result(db_query('SELECT inaktive FROM `prefix_wars_locations` WHERE id = '.$statusänderungsid),0);
    if($state==1){
        debug('########## Spiel aktivieren ##########');
        $reakqyr = db_query('UPDATE `prefix_wars_locations` SET inaktive=0 WHERE id = "'.$statusänderungsid.'" LIMIT 1');
        if($reakqyr){
            echo $aktiviert;
        }
    } else {
         debug('########## Spiel deaktivieren ##########');
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
				$getpicname = db_result(db_query("SELECT logo FROM `prefix_wars_locations` WHERE id = ".$getid.""));
				$editqry = db_query("SELECT * FROM `prefix_wars_locations` WHERE id = ".$getid."");
				$outar = db_fetch_assoc($editqry);

					$outar['nationen'] = '';
					$flagsar = get_nationality_array();
					foreach ( $flagsar as $key => $value ) {
						if($outar['country'] == $key) {
							$outar['nationen'] .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
						} else {
							$outar['nationen'] .= '<option value="'.$key.'" >'.$value.'</option>';
						}

					}

				$outar['aktuellesLogo'] = '<img src="include/images/locations/thumb_'.$outar['logo'].'"/>';
                $outar['inactiven']=($outar['inaktive']==1?'checked':'');
                $outar['inactivej']=($outar['inaktive']==0?'checked':'');
				if (isset($_POST['editsubmit']) and chk_antispam('adminuser_action', true)) {
							$editclantag			= @escape($_POST['editgegnertag'], 'string');
							$editclanname			= @escape($_POST['editclanname'], 'string');
							$editurl				= escape($_POST['editwebsite'], 'url');
							$editnation				= escape($_POST['editnation'], 'string');
							$editicq				= escape($_POST['editicq'], 'integer');
							$editemail				= escape_for_email($_POST['editemail']);
                            $editcontact            = escape($_POST['editcontact'], 'string');
                            $editaim                = escape($_POST['editaim'], 'string');
                            $edityim                = escape($_POST['edityim'], 'string');
                            $editmsn                = escape($_POST['editmsn'], 'string');
                            $editxfire              = escape($_POST['editxfire'], 'string');
                            $editircnw              = escape($_POST['editircnw'], 'string');
                            $editircch              = escape($_POST['editircch'], 'string');
                            $editinaktive           = escape($_POST['editinaktive'], 'integer');

							$updir			= 'include/images/locations/';
							$this_id		= $getid;
							$outar['thumbwidth']	= 100;

							if(!empty($_FILES['editlogo']['tmp_name'])) {

								$uploadname		= $getid.'_'.$_FILES["editlogo"]["name"];

								if ($getpicname != '.no-image-opponent.png' and
									$getpicname != 'thumb_.no-image-opponent.png') {
									@unlink('include/images/locations/'.$getpicname.'');
									@unlink('include/images/locations/thumb_'.$getpicname.'');
								}

								move_uploaded_file($_FILES["editlogo"]["tmp_name"], $updir.$uploadname);
								create_thumb($updir.$uploadname, $updir.'thumb_'.$uploadname, $outar['thumbwidth']);
							} else {
								$uploadname = $getpicname;
							}
							// DB UPDATE
							db_query("UPDATE `prefix_wars_locations` SET
													tag 	= '".$editclantag."',
                                                    name 	= '".$editclanname."',
													url 	= '".$editurl."',
													email 	= '".$editemail."',
													icq 	= '".$editicq."',
													country	= '".$editnation."',
													logo 	= '".$uploadname."',
                                                    contact = '".$editcontact."',
                                                    aim     = '".$editaim."',
                                                    yim     = '".$edityim."',
                                                    msn     = '".$editmsn."',
                                                    xfire   = '".$editxfire."',
                                                    ircnw   = '".$editircnw."',
                                                    ircch   = '".$editircch."',
                                                    inaktive = '".$editinaktive."'
												WHERE
													id = ".$getid."");
                                                       
			
								wd('admin.php?wars-locations', 'Daten gespeichert', 3);
				}
				$outar['ANTISPAM'] = get_antispam('adminuser_action', 0, true);
				$tpl->set_ar_out($outar, 5);
			}
		break;
        case 'j';
            if(db_query('SELECT shortname FROM `prefix_wars_games` WHERE inaktive = 0 AND id='.$menu->getE(2))){
                    $sx=db_query('SELECT shortname FROM `prefix_wars_games` WHERE inaktive = 0 AND id='.$menu->getE(2));
                    $s=db_result($sx,0);
                    $s=strtolower($s).'/';
                } else{
                    $s='dberror';
                }
            echo arlistee('', get_locationpic_ar($s) );
        break;
		default:
            if (is_numeric($_POST['idtemp'])) {
                $r = db_fetch_assoc(db_query("SELECT * FROM `prefix_wars_locations` WHERE id = ".$_POST['idtemp']));
                $oneout['id'] = $_POST['idtemp'];
            } else {
                $oneout = array ('id' => '', 'name' => '', 'gid' => '', 'icon' => '', 'inaktive' => 0);
            }
			// Gegnerliste ausgeben
			$limit				= 15;
			$page				= ( $menu->getA(1) == 'p' ? $menu->getE(1) : 1 );
			$anfang 			= ($page - 1) * $limit;

			if (isset($_POST['submit'])) {
				$suchstr = escape($_POST['suche'], 'string');
				$WHERE	= "WHERE name LIKE '%".$suchstr."%' OR tag LIKE '%".$suchstr."%'";
			} else {
				$WHERE				= '';
			}
            $class='Cmite';
			$oneout['siteindex']	= db_make_sites ($page ,$WHERE ,$limit ,'admin.php?locations' ,'wars_locations');
            $oneout['antispam'] = get_antispam('adminuser_action', 0, true);
            $oneout['name']=(isset($_POST['name'])?$_POST['name']:$oneout['name']);
            $game=(isset($_POST['game'])?$_POST['game']:$oneout['gid']);
            $icon=(isset($_POST['icon'])?$_POST['icon']:$oneout['icon']);
            $oneout['inactiven']=($oneout['inaktive']==0?'checked':'');
            $oneout['inaktivej']=($oneout['inaktive']==1?'checked':'');
            $oneout['game']=dblistee($game, 'SELECT id,name FROM `prefix_wars_games` WHERE inaktive = 0');
            $oneout['icon']=arlistee($icon, get_locationpic_ar('choose') );
            $tpl->set_ar_out($oneout, 0);
            $erg=db_query("SELECT * FROM `prefix_wars_locations` ".$WHERE." ORDER BY name LIMIT ".$anfang.",".$limit);
            while($row=db_fetch_assoc($erg)){
                $class=($class=='Cnorm'?'Cmite':'Cnorm');
                $row['class']=$class;
                $row['game']=get_game_icon($row['gid']);
                $row['chekimg']=($row['inaktive']==1?'nop':'ok');
                $row['inaktive']=($row['inaktive']==1?'Inaktiv':'Aktiv');
                $tpl->set_ar_out($row,1);
            }
            $tpl->out(2);
            $erg2=db_query("SELECT * FROM `prefix_wars_locations` ".$WHERE." ORDER BY name LIMIT ".$anfang.",".$limit);
            while($row2=db_fetch_assoc($erg2)){
                $row2['inaktive']=($row2['inaktive']==1?'Nein':'Ja');
                $tpl->set_ar_out($row2,3);
            }
            $tpl->out(4);
		break;

	}

?>