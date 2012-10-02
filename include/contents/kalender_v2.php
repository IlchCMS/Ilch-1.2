<?php
/**
 *
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
Kalender-Script © BigEasy
uses FullCalendar by Adam Shaw
 */
defined('main') or die('no direct access');
// -----------------------------------------------------------|
$title = $allgAr[ 'title' ] . ' :: Kalender V2';
$hmenu = 'Kalender V2';
$design = new design($title, $hmenu);
$design->addheader("\n<link rel='stylesheet' type='text/css' href='include/includes/js/fcal/fullcalendar.css' />");
$design->addheader("\n<link rel='stylesheet' type='text/css' href='include/includes/js/fcal/fullcalendar.print.css' media='print' />");
$design->addheader("\n<link rel='stylesheet' type='text/css' href='include/includes/css/fcal/style.css' media='print' />");
$design->addheader("\n<script type='text/javascript' src='include/includes/js/fcal/fullcalendar.js'></script>");
$design->addheader("\n<script type='text/javascript' src='include/includes/js/jquery/jquery.qtip-1.0.0-rc3.min.js'></script>");

$json_encode = array();
$year = date('Y');
/**
     * Liest den Kalender aus und bereitet ihn für FullCalendar auf
     * Rückgabe entspricht dem nötigen json-thread welche später encoded werden muß
     *
     *  
     * 
     */
// -----------------------------------------------------------|
		$erg = db_query('SELECT * FROM `prefix_kalender`');
 
    while($row = db_fetch_assoc($erg)) {
        if ($_SESSION['authright'] <= $row['recht']) { 
				    $id = $row['id'];
				    $title = $row['title'];
				    $text = $row['text'];		
				    $date = date("Y-m-d H:i", $row['time']);
				    $end = date("Y-m-d H:i", $row['time']);
				    $time =  date("H:i:s", $row['time']);
				    $buildjson = array(
						    'id' => "$id",
						    'title' => "$title",
						    'start' => "$date",
						    'end' => "$end",
						    'time' => "$time",
						    'description' => "$text",
						    'allDay' => false
			    	);
	
				$json_encode[] = $buildjson;
	 		  }
	}
	
/**
     * Liest die Geburtstage für FullCalendar aus und bereitet ihn für FullCalendar auf
     * Rückgabe entspricht dem nötigen json-thread welche später encoded werden muß
     *
     *  
     * 
     */
// -----------------------------------------------------------|

    $erg = db_query('SELECT `name`, `gebdatum`, `recht`, `id`,
    CASE WHEN ( MONTH(`gebdatum`) < MONTH(NOW()) ) OR ( MONTH(`gebdatum`) <= MONTH(NOW()) AND DAYOFMONTH(`gebdatum`) < DAYOFMONTH(NOW()) ) THEN
    gebdatum + INTERVAL (YEAR(NOW()) - YEAR(`gebdatum`) + 1) YEAR
    ELSE
    `gebdatum` + INTERVAL (YEAR(NOW()) - YEAR(`gebdatum`)) YEAR
    END
    AS `gebtage`
    FROM `prefix_user` WHERE `gebdatum` > 0000-00-00 ORDER BY `gebtage`;');
	      
		while($row = db_fetch_assoc($erg)) {
		    if ($row['recht'] <= '-1') {
		        $gebtag = $year . '-' . date("m-d", strtotime($row['gebdatum']));
				    $id = $gebtag.$row['id'];
				    $title = 'Geburtstag von '.$row['name'];
				    $text = 'Geburtstag von '.$row['name'];
				    $date = $gebtag;
				    $end = $gebtag;
				    $url = 'index.php?user-details-'.$row['id'];
				    $buildjson = array(
						    'id' => "$id",
						    'title' => "$title",
						    'start' => "$date",
						    'end' => "$end",
						    'description' => "$text",
						    'url' => "$url",
						    'allDay' => true
			    	);    
		    $json_encode[] = $buildjson;  
		    }
		}


/**
     * Liest die Wars für FullCalendar aus und bereitet ihn für FullCalendar auf
     * Rückgabe entspricht dem nötigen json-thread welche später encoded werden muß
     *
     *  
     * 
     */
// -----------------------------------------------------------|

    $erg = db_query('SELECT `datime`, `gegner`, `txt`, `id`, `tid` FROM `prefix_wars`');
    
    while($row = db_fetch_assoc($erg)) {
		    $date = date("Y-m-d H:i:s", strtotime($row['datime']));
		    $end = date("Y-m-d", strtotime($row['datime']));
		    $time = date("H:i", strtotime($row['datime']));
		    $team = db_fetch_assoc(db_query('SELECT `name` FROM `prefix_groups` WHERE `id` = '.$row['tid']));
		    $title = 'angekündigter War von '.$team['name'];
				$text = 'War zwischen Team: '.$team['name'].' und '.$row['gegner'];
				$url = 'index.php?wars-more-'.$row['id'];
				$id = 'wars-'.$row[datime].$row['id'];
				$buildjson = array(
						    'id' => "$id",
						    'title' => "$title",
						    'start' => "$date",
						    'time' => "$time",
						    'end' => "$end",
						    'description' => "$text",
						    'url' => "$url",
						    'allDay' => false
			    	);    
		    $json_encode[] = $buildjson; 
		}
	
	
	

$design->header();
$tpl = new tpl('kalender_v2.htm'); 
$tpl->set('kalenderevents', json_encode($json_encode));  	     
$tpl->out();
$design->footer();
?>