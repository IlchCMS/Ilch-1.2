<?php
// todo

# include/images/opponents -> rechte prüfen in install.php oder server-konfig
# include/images/opponents -> ordnergröße in server-info ausgeben lassen


defined('main') or die('no direct access');
defined('admin') or die('only admin access');

include_once('include/includes/func/gallery.php');

    $design = new design('Ilch Admin-Control-Panel :: Gegner', '', 2);
	$tpl = new tpl('opponents', 1);
    $design->header();
	$outar = array();
	$outar['thumbwidth']	= 100;
	$outar['gegnerliste']	= '';
	$outar['page']			= 'http://';
	// Flaggen und Länder ausgeben
	$outar['nationen'] = '';
	$selectedNation = '';
	$flagsar = get_nationality_array();

	foreach ( $flagsar as $key => $value ) {
		
		$outar['nationen'] .= '<option value="'.$key.'" selected="'.$selectedNation.'">'.$value.'</option>';
	
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
						
	// Formular für Neue Gegner absenden
	if (isset($_POST['newsubmit'])) {
		debug('########## FormUpload-Log Start ##########');
			
		$newclantag			= escape(htmlentities($_POST['newgegnertag']), 'string');	
		$newclanname		= escape(htmlentities($_POST['newclanname']), 'string');
		$newurl				= escape($_POST['newwebsite'], 'url');
		$newnation			= escape($_POST['newnation'], 'string');
		$newicq				= escape($_POST['newicq'], 'integer');
		$newemail			= escape_for_email($_POST['newemail']);
		$updir			= 'include/images/opponents/';
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
				
				$qry 	=	db_query("INSERT INTO `prefix_opponents` (name, tag, page, email, icq, nation, logo)
									VALUES ('".$newclanname."', '".$newclantag."', '".$newurl."', '".$newemail."', '".$newicq."', '".$newnation."', '".$uploadname."')");
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
			if (file_exists('include/images/opponents/.no-image-opponent.png') and file_exists('include/images/opponents/thumb_.no-image-opponent.png')) {
				$uploadname = '.no-image-opponent.png';	
				debug('default-pic und thumb vorhanden');
			}
			$qry 	=	db_query("INSERT INTO `prefix_opponents` (name, tag, page, email, icq, nation, logo)
								VALUES ('".$newclanname."', '".$newclantag."', '".$newurl."', '".$newemail."', '".$newicq."', '".$newnation."', '".$uploadname."')");
			if($qry) {
				
				debug('DB-Eintrag erfolgreich');
				echo $eintrag_ok;
			} else {
				echo $eintrag_nok;
			}
		}
		
		debug('########## FormUpload-Log END ##########');	
		
	} // form neue Gegner Ende
	
	// Eintrag löschen
	$seite	= escape($menu->get(1), 'string');
	$getid	= escape($menu->get(2), 'integer');
	switch ($seite) {
		case 'del':
		
		if($getid != 0 and !empty($getid)) {

			$design->footer(1);
		}
		
		break;
		
		case 'delok':
			if($getid != 0 and !empty($getid)) {
				$getpicname = db_result(db_query("SELECT logo FROM `prefix_opponents` WHERE id = ".$getid.""));
				$countpicname = db_num_rows(db_query("SELECT COUNT(name) FROM `prefix_opponents` WHERE name = ".$getpicname.""));
				if($countpicname >= 2) {
					debug("Bild wird nicht gel&ouml;scht da es von einem weiteren DB-Eintrag genutzt wird");				
					
					
					db_query("DELETE FROM `prefix_opponents` WHERE id = ".$getid."");
				
				} else {
					
					if ($getpicname != 0 and 
						$getpicname != '.no-image-opponent.png' and 
						$getpicname != 'thumb_.no-image-opponent.png') 
					{
							@unlink('include/images/opponents/'.$getpicname.'');
							@unlink('include/images/opponents/thumb_'.$getpicname.'');
							
					} else {
						debug('Bild wurde NICHT gel&ouml;scht..!');
					}
					db_query("DELETE FROM `prefix_opponents` WHERE id = ".$getid."");
					wd('admin.php?opponents', 'Gegner erfolgreich gel&ouml;scht', 3);
					$design->footer(1);
				}
			}
		break;
		
		case 'edit':
			if($getid != 0 and !empty($getid)) {
				$getpicname = db_result(db_query("SELECT logo FROM `prefix_opponents` WHERE id = ".$getid.""));
				$editqry = db_query("SELECT * FROM `prefix_opponents` WHERE id = ".$getid."");
				$outar = db_fetch_assoc($editqry);
				
					$outar['nationen'] = '';
					$flagsar = get_nationality_array();
					foreach ( $flagsar as $key => $value ) {
						if($outar['nation'] == $key) {
							$outar['nationen'] .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
						} else {
							$outar['nationen'] .= '<option value="'.$key.'" >'.$value.'</option>';
						}
					
					}
	
				$outar['aktuellesLogo'] = '<img src="include/images/opponents/thumb_'.$outar['logo'].'"/>';
				
				if (isset($_POST['editsubmit'])) {
							$editclantag			= @escape(htmlentities($_POST['editgegnertag']), 'string');	
							$editclanname			= @escape(htmlentities($_POST['editclanname']), 'string');
							$editurl				= escape($_POST['editwebsite'], 'url');
							$editnation				= escape($_POST['editnation'], 'string');
							$editicq				= escape($_POST['editicq'], 'integer');
							$editemail				= escape_for_email($_POST['editemail']);

							$updir			= 'include/images/opponents/';
							$this_id		= $getid;
							$outar['thumbwidth']	= 100;
								
							if(!empty($_FILES['editlogo']['tmp_name'])) {
								
								$uploadname		= $getid.'_'.$_FILES["editlogo"]["name"];
							
								if ($getpicname != '.no-image-opponent.png' and
									$getpicname != 'thumb_.no-image-opponent.png') {
									@unlink('include/images/opponents/'.$getpicname.'');
									@unlink('include/images/opponents/thumb_'.$getpicname.'');
								}
								
								move_uploaded_file($_FILES["editlogo"]["tmp_name"], $updir.$uploadname);
								create_thumb($updir.$uploadname, $updir.'thumb_'.$uploadname, $outar['thumbwidth']);
							} else {
								$uploadname = $getpicname;
							}
							// DB UPDATE
							db_query("UPDATE `prefix_opponents` SET
													name 	= '".$editclanname."',
													tag 	= '".$editclantag."',
													page 	= '".$editurl."',
													email 	= '".$editemail."',
													icq 	= '".$editicq."',
													nation 	= '".$editnation."',
													logo 	= '".$uploadname."'
												WHERE
													id = ".$getid."");
								wd('admin.php?opponents', 'Daten gespeichert', 3);
								$design->footer(1);
				}
				$tpl->set_ar_out($outar, 1);
				$design->footer();
			}
		break;
		
		default:
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
			
			$outar['siteindex']	= db_make_sites ($page ,$WHERE ,$limit ,'admin.php?opponents' ,'opponents');
			
			$listqry = db_query("SELECT * FROM `prefix_opponents` ".$WHERE." ORDER BY name LIMIT ".$anfang.", ".$limit." ");
			while ($listrow = db_fetch_assoc($listqry)) {
				
				$outar['gegnerliste'] .= '
						<tr>
							<td><img src="include/images/opponents/thumb_'.$listrow['logo'].'" /></td>
							<td><a href="admin.php?opponents-edit-'.$listrow['id'].'"><img src="include/images/icons/edit.png" onClick="openEditOpp();"/></a>
								<img src="include/images/icons/del.png" style="cursor: pointer; cursor: hand;" onClick="openloschfrage('.$listrow['id'].')"/></td>
							<td><b>'.$listrow['name'].'</b></td>
							<td>'.$listrow['tag'].'</td>
							<td><a href="'.$listrow['page'].'" target="_blank">'.$listrow['page'].'</a></td>
							<td><img src="http://status.icq.com/online.gif?icq='.$listrow['icq'].'&img=5" />'.$listrow['icq'].'</td>
						</tr>
				';
				
			}
			
			$tpl->set_ar_out($outar, 0);

    		$design->footer();
		break;
		
	}



?>