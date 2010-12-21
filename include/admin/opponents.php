<?php
// todo

# include/images/opponents -> rechte prüfen
# include/images/opponents -> ordnergröße


defined('main') or die('no direct access');
defined('admin') or die('only admin access');

include_once('include/includes/func/gallery.php');

    $design = new design('Ilch Admin-Control-Panel :: Gegner', '', 2);
	$tpl = new tpl('opponents', 1);
    $design->header();
	$outar = array();
	$outar['thumbwidth']	= 100;
	
	// Flaggen und Länder ausgeben
	$outar['nationen'] = '';
	$flagsar = get_nationality_array();
	
	foreach ( $flagsar as $key => $value ) {
		
		$outar['nationen'] .= '<option value="'.$key.'">'.$value.'</option>';
	
	}
	
	// Formular für Neue Gegner absenden
	if (isset($_POST['newsubmit'])) {
		debug('########## FormUpload-Log Start ##########');
			
		$newclantag			= escape($_POST['newgegnertag'], 'string');	
		$newclanname		= escape($_POST['newclanname'], 'string');
		$newurl				= escape($_POST['newnation'], 'url');
		$newnation			= escape($_POST['newnation'], 'string');
		$newicq				= escape($_POST['newicq'], 'integer');
		$newemail			= escape_for_email($_POST['newemail']);
		
		//file up
		if(!empty($_FILES['newlogo']['tmp_name'])) {
			debug('File Selected... generating filename');
			
			$updir			= 'include/images/opponents/';
			$last_id 		= db_result(db_query("SELECT MAX(id) FROM `prefix_opponents`"));
			$this_id		= $last_id + 1;
			$uploadname		= $this_id.'_'.$_FILES["newlogo"]["name"];
			
			debug('Filename is now '.$updir.$uploadname);
			
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
						
				}
				
			} else {
				
				debug('FILE Upload ERROR');			
				debug( $_FILES['newlogo']['error'] );
			}
	
		} else { 
		
			debug('NO File Selected...'); 
		}
		
		debug('########## FormUpload-Log END ##########');	
		
	} // form neue Gegner Ende
	
	
	$tpl->set_ar_out($outar, 0);

    $design->footer();

?>