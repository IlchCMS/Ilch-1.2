<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$tpl = new tpl ('wars/server', 1);

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
                                    window.location = "admin.php?wars-server";
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
                                    window.location = "admin.php?wars-server";
                                }
                            }
                        });
                    });
                </script>';
                
// Formular für Neue Gegner absenden
if (isset($_POST['newsubmit']) and chk_antispam('adminuser_action', true)) {
    debug('########## FormUpload-Log Start ##########');
    $newservername		= escape($_POST['newservername'], 'string');
    $newserverip			= escape($_POST['newserverip'], 'ip');
    $newinaktive		= escape($_POST['newinaktive'], 'integer');
    $qry 	=	db_query("INSERT INTO `prefix_wars_server` (name,ip,inaktive) VALUES ('".$newservername."','".$newserverip."',".$newinaktive.")");
    if($qry) {
        debug('DB-Eintrag erfolgreich');
        echo $eintrag_ok;
    } else {
        echo $eintrag_nok;
    }
    debug('########## FormUpload-Log END ##########');
} // form neue Gegner Ende
$statusänderung = escape($menu->get(2), 'string');
$statusänderungsid = escape($menu->get(3), 'integer');

if ( $statusänderung == 'stat' AND !empty($statusänderungsid)) {
    $state=db_result(db_query('SELECT inaktive FROM `prefix_wars_server` WHERE id = '.$statusänderungsid),0);
    if($state==1){
        debug('########## Spiel aktivieren ##########');
        $reakqyr = db_query('UPDATE `prefix_wars_server` SET inaktive=0 WHERE id = "'.$statusänderungsid.'" LIMIT 1');
        if($reakqyr){
            echo $aktiviert;
        }
    } else {
         debug('########## Spiel deaktivieren ##########');
         $deakqyr = db_query('UPDATE `prefix_wars_server` SET inaktive=1 WHERE id = "'.$statusänderungsid.'" LIMIT 1');
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
			$editqry = db_query("SELECT * FROM `prefix_wars_server` WHERE id = ".$getid."");
			$outar = db_fetch_assoc($editqry);
				$outar['inactiven']=($outar['inaktive']==1?'checked':'');
                $outar['inactivej']=($outar['inaktive']==0?'checked':'');
				if (isset($_POST['editsubmit']) and chk_antispam('adminuser_action', true)) {
						$editservername			= @escape($_POST['editservername'], 'string');
						$editserverip			= @escape($_POST['editserverip'], 'ip');
						$editinaktive			= escape($_POST['editinaktive'], 'integer');
						// DB UPDATE
					db_query("UPDATE `prefix_wars_server` SET
												name 	= '".$editservername."',
												ip 	= '".$editserverip."',
												inaktive 	= '".$editinaktive."'
											WHERE
												id = ".$getid."");
							wd('admin.php?wars-server', 'Daten gespeichert', 3);
							$design->footer(1);
			}
			$outar['antispam'] = get_antispam('adminuser_action', 0, true);
			$tpl->set_ar_out($outar, 3);
		}
	break;
	default:
		// serverliste ausgeben
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
		$oneout['siteindex']	= db_make_sites ($page ,$WHERE ,$limit ,'admin.php?wars-server' ,'wars-server');
        $oneout['antispam'] = get_antispam('adminuser_action', 0, true);
		$tpl->set_ar_out($oneout, 0);
		$erg=db_query("SELECT * FROM `prefix_wars_server` LIMIT ".$anfang.",".$limit);
        while($row=db_fetch_assoc($erg)){
            $class=($class=='Cnorm'?'Cmite':'Cnorm');
            $row['class']=$class;
            $row['chekimg']=($row['inaktive']==1?'nop':'ok');
            $row['inaktive']=($row['inaktive']==1?'Inaktiv':'Aktiv');
            $tpl->set_ar_out($row,1);
        }
        $tpl->out(2);
	break;
}
?>