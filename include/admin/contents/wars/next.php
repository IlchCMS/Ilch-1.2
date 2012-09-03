<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
regGlobals($_POST);
$tpl = new tpl ( 'wars/next', 1);
if ( $menu->getA(2)!='u' AND $menu->getA(2)!='e' AND $menu->getA(2)!='i' ) {
	$tpl->out(0);
	$class='Cnorm';
	$limit = 10;  // Limit
	$page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1 );
	$MPL = db_make_sites ($page , 'WHERE status=1' , $limit , "?wars" , 'wars' );
	$anfang = ($page - 1) * $limit;
	$erg=db_query("SELECT id,gameid,datime,groupid,oppid,adduid,addtime,chuid,chtime FROM `prefix_wars` WHERE status=1 LIMIT ".$anfang.",".$limit);
	while($row=db_fetch_assoc($erg)){
		$class=($class=='Cmite'?'Cnorm':'Cmite');
		$row['class']=$class;
		$row['game']=db_result(db_query('SELECT name FROM prefix_wars_games WHERE id='.$row['gameid']),0);
		$row['icon']=db_result(db_query('SELECT icon FROM prefix_wars_games WHERE id='.$row['gameid']),0);
		$row['gegner']=db_result(db_query('SELECT name FROM prefix_wars_opponents WHERE id='.$row['oppid']),0);
		$row['team']=db_result(db_query('SELECT name FROM prefix_groups WHERE id='.$row['groupid']),0);
		$row['evon']=($row['adduid']!=0?db_result(db_query('SELECT name FROM prefix_user WHERE id='.$row['adduid']),0):'');
		$row['cvon']=($row['chuid']!=0?db_result(db_query('SELECT name FROM prefix_user WHERE id='.$row['chuid']),0):'');
		$row['cam']=($row['chuid']!=0?'Am '.substr($row['chtime'],8,2).'.'.substr($row['chtime'],5,2).'.'.substr($row['chtime'],0,4).' um '.substr($row['chtime'],11,2).':'.substr($row['chtime'],14,2).':'.substr($row['chtime'],17,2).' Uhr':'');
	         $row['day']=substr($row['datime'],8,2);
		$row['mon']=substr($row['datime'],5,2);
		$row['jahr']=substr($row['datime'],0,4);
		$row['stu']=substr($row['datime'],11,2);
		$row['min']=substr($row['datime'],14,2);
		$row['sek']=substr($row['datime'],17,2);
	         $row['eday']=substr($row['addtime'],8,2);
		$row['emon']=substr($row['addtime'],5,2);
		$row['ejahr']=substr($row['addtime'],0,4);
		$row['estu']=substr($row['addtime'],11,2);
		$row['emin']=substr($row['addtime'],14,2);
		$row['esek']=substr($row['addtime'],17,2);
		$tpl->set_ar_out($row,1);
	}
	unset($row);
	$r['MPL']=$MPL;
	$tpl->set_ar_out($r,2);
}elseif ( $menu->getA(2) == 'i' AND $menu->getA(3) == 'l') {
	if(isset($sub) AND $game != 0 AND $gametypeid != 0 AND $matchtypeid != 0 AND (sizeof($locations) >= 1)){
                 if(isset($opponents) AND $opponents!=0){
			db_query("UPDATE `prefix_wars_opponents` SET tag = '$tag', name = '$name', contact= '$contact', email= '$email', icq= '$icq', aim= '$aim', yim= '$yim', msn= '$msn', xfire= '$xfire', ircnw= '$ircnw', ircch= '$ircch', url = '$url', country= '$country' WHERE id = '$opponents'");
		} elseif ($tag != "" && $name != ""){
                         $q="INSERT INTO `prefix_wars_opponents` (tag,name,contact,email,icq,aim,yim,msn,xfire,ircnw,ircch,url,country) VALUES ('".$tag."','".$name."','".$contact."','".$email."','".$icq."','".$aim."','".$yim."','".$msn."','".$xfire."','".$ircnw."','".$ircch."','".$url."','".$country."')";
			db_query($q);
			$opponents = db_last_id();
		}
                 if(isset($servers) AND $servers!=0){
			db_query("UPDATE `prefix_wars_server` SET name = '$servername', ip = '$ip' WHERE id = '$servers'");
		} else if ($servername != "" && $ip != ""){
			db_query("INSERT INTO `prefix_wars_server` (name,ip) VALUES ('".$servername."','".$ip."')");
			$servers = db_last_id();
		}
         	$datum= mktime($stu, $min, $sek, $mon , $day, $jahr);;
		$datime = date('Y-m-d H:i:s',$datum);
		db_query("INSERT INTO `prefix_wars` (`gameid`, `gametypeid`, `matchtypeid`, `groupid`, `oppid`, `serverid`, `serverpw`, `ppt`, `txt`, `status`, `datime`, `adduid`, `addtime`) VALUES ('".$game."', '".$gametypeid."', '".$matchtypeid."', '".$gid."', '".$opponents."', '".$servers."', '".$pw."', '".$ppt."', '".$txt."', 1, '".$datime."', '".$_SESSION['authid']."', NOW())");
                 $lastinsertid = db_last_id();
		for ($i = 1; $i < sizeof($locations); $i++){
			db_query("INSERT INTO prefix_wars_scores (wid,locationid) VALUES ('$lastinsertid','$locations[$i]')");
		}
		wd('admin.php?wars-last-u'.$lastinsertid,'Erfolgreich eingetragen weiter zum abschluss',1);
	} else{
		#Gegnerdetails
		$r['opponents']= arlistee ($opponents, get_opponents() );
		if(isset($opponents) AND $opponents!=0){
			$oppar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_opponents` WHERE inaktive = 0 AND id='.$opponents));
		}
		$r['country']=(isset($oppar['country'])?$oppar['country']:(isset($country)?$country:''));
		$clancountry   = arlistee ($r['country'], get_nationality_array() );
		$r['country'] = ereg_replace (".gif<", "<", $clancountry );
		$r['tag']=(isset($oppar['tag'])?$oppar['tag']:(isset($tag)?$tag:''));
		$r['name']=(isset($oppar['name'])?$oppar['name']:(isset($name)?$name:''));
		$r['contact']=(isset($oppar['contact'])?$oppar['contact']:(isset($contact)?$contact:''));
		$r['email']=(isset($oppar['email'])?$oppar['email']:(isset($email)?$email:''));
		$r['icq']=(isset($oppar['icq'])?$oppar['icq']:(isset($icq)?$icq:''));
		$r['aim']=(isset($oppar['aim'])?$oppar['aim']:(isset($aim)?$aim:''));
		$r['yim']=(isset($oppar['yim'])?$oppar['yim']:(isset($yim)?$yim:''));
		$r['msn']=(isset($oppar['msn'])?$oppar['msn']:(isset($msn)?$msn:''));
		$r['xfire']=(isset($oppar['xfire'])?$oppar['xfire']:(isset($xfire)?$xfire:''));
		$r['ircnw']=(isset($oppar['ircnw'])?$oppar['ircnw']:(isset($ircnw)?$ircnw:''));
		$r['ircch']=(isset($oppar['ircch'])?$oppar['ircch']:(isset($ircch)?$ircch:''));
		$r['url']=(isset($oppar['url'])?$oppar['url']:(isset($url)?$url:''));
		#Wardetails
		$r['team']= arlistee ($gid, get_groups() );
         	if(isset($gid) AND $gid!=0){
			$teamar=db_fetch_assoc(db_query('SELECT * FROM `prefix_groups` WHERE is_inactive = 0 AND id='.$gid));
		}
		$gametemp=(isset($teamar['game'])?$teamar['game']:(isset($game)?$game:''));
         	$r['game']= arlistee ($gametemp, get_clangames() );
		$r['gametype']= arlistee ($gametypeid, get_gametypes() );
		$r['matchtype']= arlistee ($matchtypeid, get_matchtypes() );
         	$r['day']=(isset($day)?$day:date('d'));
		$r['mon']=(isset($mon)?$mon:date('m'));
		$r['jahr']=(isset($jahr)?$jahr:date('Y'));
		$r['stu']=(isset($stu)?$stu:date('H'));
		$r['min']=(isset($min)?$min:date('i'));
		$r['sek']=(isset($sek)?$sek:date('s'));
		$r['ppt']=(isset($ppt)?$ppt:'');
		$r['txt']=(isset($txt)?$txt:'');
		#Locations
		$tpl->set_ar_out($r,12);
		for ($i = 0;$i <= sizeof($locationid); $i++){
			$locationnumber = $i+1;
         	        if ($locations[$locationnumber] != 0){
				$locationid[$locationnumber] = $locationid;
			}
         	        if ($locations[$locationnumber] != 0 || $locationnumber == 1 || $locations[$locationnumber-1] != 0){
         	                $locar['nr']= $locationnumber;
				$locar['locations']=arlistee ($locations[$locationnumber], get_locations($gametemp) );
				$tpl->set_ar_out($locar,13);
			}
		}
		#Server
		$x['servers']= arlistee ($servers, get_server() );
         	if(isset($servers) AND $servers!=0){
			$serar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_server` WHERE inaktive = 0 AND id='.$servers));
		}
	         $x['servername']=(isset($serar['name'])?$serar['name']:(isset($servername)?$servername:''));
		$x['ip']=(isset($serar['ip'])?$serar['ip']:(isset($ip)?$ip:''));
		$x['pw']=(isset($pw)?$pw:'');
		$tpl->set_ar_out($x,14);
	}
}elseif ( $menu->getA(2) == 'i' ) {
	if(isset($sub) AND $game != 0 AND $gametypeid != 0 AND $matchtypeid != 0 AND (sizeof($locations) >= 1) AND $locations[1]!=0){
                 if(isset($opponents) AND $opponents!=0){
			db_query("UPDATE `prefix_wars_opponents` SET tag = '$tag', name = '$name', contact= '$contact', email= '$email', icq= '$icq', aim= '$aim', yim= '$yim', msn= '$msn', xfire= '$xfire', ircnw= '$ircnw', ircch= '$ircch', url = '$url', country= '$country' WHERE id = '$opponents'");
		} elseif ($tag != "" && $name != ""){
                         $q="INSERT INTO `prefix_wars_opponents` (tag,name,contact,email,icq,aim,yim,msn,xfire,ircnw,ircch,url,country) VALUES ('".$tag."','".$name."','".$contact."','".$email."','".$icq."','".$aim."','".$yim."','".$msn."','".$xfire."','".$ircnw."','".$ircch."','".$url."','".$country."')";
			db_query($q);
			$opponents = db_last_id();
		}
                 if(isset($servers) AND $servers!=0){
			db_query("UPDATE `prefix_wars_server` SET name = '$servername', ip = '$ip' WHERE id = '$servers'");
		} else if ($servername != "" && $ip != ""){
			db_query("INSERT INTO `prefix_wars_server` (name,ip) VALUES ('".$servername."','".$ip."')");
			$servers = db_last_id();
		}
                 $datum= mktime($stu, $min, $sek, $mon , $day, $jahr);;
		$datime = date('Y-m-d H:i:s',$datum);
		db_query("INSERT INTO `prefix_wars` (`gameid`, `gametypeid`, `matchtypeid`, `groupid`, `oppid`, `serverid`, `serverpw`, `ppt`, `txt`, `status`, `datime`, `adduid`, `addtime`) VALUES ('".$game."', '".$gametypeid."', '".$matchtypeid."', '".$gid."', '".$opponents."', '".$servers."', '".$pw."', '".$ppt."', '".$txt."', 1, '".$datime."', '".$_SESSION['authid']."', NOW())");
                 $lastinsertid = db_last_id();
		for ($i = 1; $i < sizeof($locations); $i++){
			db_query("INSERT INTO prefix_wars_scores (wid,locationid) VALUES ('$lastinsertid','$locations[$i]')");
		}
		wd('admin.php?wars-next','Erfolgreich eingetragen',1);
	} else{
		#Gegnerdetails
		$r['opponents']= arlistee (@$opponents, get_opponents() );
		if(isset($opponents) AND $opponents!=0){
			$oppar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_opponents` WHERE inaktive = 0 AND id='.$opponents));
		}
		$r['country']=(isset($oppar['country'])?$oppar['country']:(isset($country)?$country:''));
        $r['country'] = arlistee ($r['country'], get_nationality_array() );
		$r['tag']=(isset($oppar['tag'])?$oppar['tag']:(isset($tag)?$tag:''));
		$r['name']=(isset($oppar['name'])?$oppar['name']:(isset($name)?$name:''));
		$r['contact']=(isset($oppar['contact'])?$oppar['contact']:(isset($contact)?$contact:''));
		$r['email']=(isset($oppar['email'])?$oppar['email']:(isset($email)?$email:''));
		$r['icq']=(isset($oppar['icq'])?$oppar['icq']:(isset($icq)?$icq:''));
		$r['aim']=(isset($oppar['aim'])?$oppar['aim']:(isset($aim)?$aim:''));
		$r['yim']=(isset($oppar['yim'])?$oppar['yim']:(isset($yim)?$yim:''));
		$r['msn']=(isset($oppar['msn'])?$oppar['msn']:(isset($msn)?$msn:''));
		$r['xfire']=(isset($oppar['xfire'])?$oppar['xfire']:(isset($xfire)?$xfire:''));
		$r['ircnw']=(isset($oppar['ircnw'])?$oppar['ircnw']:(isset($ircnw)?$ircnw:''));
		$r['ircch']=(isset($oppar['ircch'])?$oppar['ircch']:(isset($ircch)?$ircch:''));
		$r['url']=(isset($oppar['url'])?$oppar['url']:(isset($url)?$url:''));
		#Wardetails
		$r['team']= arlistee (@$gid, get_groups() );
         	if(isset($gid) AND $gid!=0){
			$teamar=db_fetch_assoc(db_query('SELECT * FROM `prefix_groups` WHERE is_inactive = 0 AND id='.$gid));
		}
		$gametemp=(isset($teamar['game'])?$teamar['game']:(isset($game)?$game:''));
        $r['game']= arlistee ($gametemp, get_games() );
		$r['gametype']= arlistee (@$gametypeid, get_gametypes() );
		$r['matchtype']= arlistee (@$matchtypeid, get_matchtypes() );
         	$r['day']=(isset($day)?$day:date('d'));
		$r['mon']=(isset($mon)?$mon:date('m'));
		$r['jahr']=(isset($jahr)?$jahr:date('Y'));
		$r['stu']=(isset($stu)?$stu:date('H'));
		$r['min']=(isset($min)?$min:date('i'));
		$r['sek']=(isset($sek)?$sek:date('s'));
		$r['ppt']=(isset($ppt)?$ppt:'');
		$r['txt']=(isset($txt)?$txt:'');
		#Locations
		$tpl->set_ar_out($r,3);
		for ($i = 0;$i <= sizeof(@$locationid); $i++){
			$locationnumber = $i+1;
         	        if (@$locations[$locationnumber] != 0){
				$locationid[$locationnumber] = $locationid;
			}
         	        if (@$locations[$locationnumber] != 0 || $locationnumber == 1 || $locations[$locationnumber-1] != 0){
         	                $locar['nr']= $locationnumber;
				$locar['locations']=arlistee (@$locations[$locationnumber], get_locations($gametemp) );
				$tpl->set_ar_out($locar,4);
			}
		}
		#Server
		$x['servers']= arlistee (@$servers, get_server() );
         	if(isset($servers) AND $servers!=0){
			$serar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_server` WHERE inaktive = 0 AND id='.$servers));
		}
	         $x['servername']=(isset($serar['name'])?$serar['name']:(isset($servername)?$servername:''));
		$x['ip']=(isset($serar['ip'])?$serar['ip']:(isset($ip)?$ip:''));
		$x['pw']=(isset($pw)?$pw:'');
		$tpl->set_ar_out($x,5);
	}
}elseif ($menu->getA(2)=='u') {
	if(isset($sub) AND $gid != 0 AND $game != 0 AND $gametypeid != 0 AND $matchtypeid != 0 AND (sizeof($locations) >= 1)){
                 if(isset($opponents) AND $opponents!=0){
			db_query("UPDATE `prefix_wars_opponents` SET tag = '$tag', name = '$name', contact= '$contact', email= '$email', icq= '$icq', aim= '$aim', yim= '$yim', msn= '$msn', xfire= '$xfire', ircnw= '$ircnw', ircch= '$ircch', url = '$url', country= '$country' WHERE id = '$opponents'");
		} elseif ($tag != "" && $name != ""){
                         $q="INSERT INTO `prefix_wars_opponents` (tag,name,contact,email,icq,aim,yim,msn,xfire,ircnw,ircch,url,country) VALUES ('".$tag."','".$name."','".$contact."','".$email."','".$icq."','".$aim."','".$yim."','".$msn."','".$xfire."','".$ircnw."','".$ircch."','".$url."','".$country."')";
			db_query($q);
			$opponents = db_last_id();
		}
                 if(isset($servers) AND $servers!=0){
			db_query("UPDATE `prefix_wars_server` SET name = '$servername', ip = '$ip' WHERE id = '$servers'");
		} else if ($servername != "" && $ip != ""){
			db_query("INSERT INTO `prefix_wars_server` (name,ip) VALUES ('".$servername."','".$ip."')");
			$servers = db_last_id();
		}
                 $datum= mktime($stu, $min, $sek, $mon , $day, $jahr);;
		$datime = date('Y-m-d H:i:s',$datum);
		db_query("INSERT INTO `prefix_wars` (`gameid`, `gametypeid`, `matchtypeid`, `groupid`, `oppid`, `serverid`, `serverpw`, `ppt`, `txt`, `status`, `datime`, `adduid`, `addtime`) VALUES ('".$game."', '".$gametypeid."', '".$matchtypeid."', '".$gid."', '".$opponents."', '".$servers."', '".$pw."', '".$ppt."', '".$txt."', 1, '".$datime."', '".$_SESSION['authid']."', NOW())");
                 $lastinsertid = db_last_id();
		for ($i = 1; $i < sizeof($locations); $i++){
			db_query("INSERT INTO prefix_wars_scores (wid,locationid) VALUES ('$lastinsertid','$locations[$i]')");
		}
		db_query("DELETE FROM prefix_wars_challanges WHERE id=".$menu->getE(2));
		wd('admin.php?wars-next','Erfolgreich eingetragen',1);
	} else{
                 $ccv = db_fetch_assoc(db_query("SELECT * FROM `prefix_wars_challanges` WHERE id = ".$menu->getE(2)));
		$r['id'] = $menu->getE(2);
		#Gegnerdetails
		$r['opponents']= arlistee ($opponents, get_opponents() );
		if(isset($opponents) AND $opponents!=0){
			$oppar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_opponents` WHERE inaktive = 0 AND id='.$opponents));
		}
		$r['country']=(isset($oppar['country'])?$oppar['country']:(isset($country)?$country:$ccv['country']));
		$clancountry   = arlistee ($r['country'], get_nationality_array() );
		$r['country'] = ereg_replace (".gif<", "<", $clancountry );
		$r['tag']=(isset($oppar['tag'])?$oppar['tag']:(isset($tag)?$tag:$ccv['tag']));
		$r['name']=(isset($oppar['name'])?$oppar['name']:(isset($name)?$name:$ccv['name']));
		$r['contact']=(isset($oppar['contact'])?$oppar['contact']:(isset($contact)?$contact:$ccv['contact']));
		$r['email']=(isset($oppar['email'])?$oppar['email']:(isset($email)?$email:$ccv['email']));
		$r['icq']=(isset($oppar['icq'])?$oppar['icq']:(isset($icq)?$icq:$ccv['icq']));
		$r['aim']=(isset($oppar['aim'])?$oppar['aim']:(isset($aim)?$aim:$ccv['aim']));
		$r['yim']=(isset($oppar['yim'])?$oppar['yim']:(isset($yim)?$yim:$ccv['yim']));
		$r['msn']=(isset($oppar['msn'])?$oppar['msn']:(isset($msn)?$msn:$ccv['msn']));
		$r['xfire']=(isset($oppar['xfire'])?$oppar['xfire']:(isset($xfire)?$xfire:$ccv['xfire']));
		$r['ircnw']=(isset($oppar['ircnw'])?$oppar['ircnw']:(isset($ircnw)?$ircnw:$ccv['ircnw']));
		$r['ircch']=(isset($oppar['ircch'])?$oppar['ircch']:(isset($ircch)?$ircch:$ccv['ircch']));
		$r['url']=(isset($oppar['url'])?$oppar['url']:(isset($url)?$url:$ccv['url']));
		#Wardetails
		$gid=(isset($gid)?$gid:$ccv['groupid']);
		$r['team']= arlistee ($gid, get_groups() );
         	if(isset($gid) AND $gid!=0){
			$teamar=db_fetch_assoc(db_query('SELECT * FROM `prefix_groups` WHERE is_inactive = 0 AND id='.$gid));
		}
		$gametemp=(isset($teamar['game'])?$teamar['game']:(isset($game)?$game:$ccv['gameid']));
         	$r['game']= arlistee ($gametemp, get_clangames() );
		$gametypeid=(isset($gametypeid)?$gametypeid:$ccv['gametypeid']);
		$r['gametype']= arlistee ($gametypeid, get_gametypes() );
		$matchtypeid=(isset($matchtypeid)?$matchtypeid:$ccv['matchtypeid']);
		$r['matchtype']= arlistee ($matchtypeid, get_matchtypes() );
         	$r['day']=(isset($day)?$day:substr($ccv['datime'],8,2));
		$r['mon']=(isset($mon)?$mon:substr($ccv['datime'],5,2));
		$r['jahr']=(isset($jahr)?$jahr:substr($ccv['datime'],0,4));
		$r['stu']=(isset($stu)?$stu:substr($ccv['datime'],11,2));
		$r['min']=(isset($min)?$min:substr($ccv['datime'],14,2));
		$r['sek']=(isset($sek)?$sek:substr($ccv['datime'],17,2));
		$r['ppt']=(isset($ppt)?$ppt:$ccv['ppt']);
		$r['txt']=(isset($txt)?$txt:$ccv['txt']);
		#Locations
		$tpl->set_ar_out($r,6);
                 $locationinfo = split("\|\|", $ccv['locations']);
		for ($i = 0; $i <= sizeof($locationinfo); $i++){
				$locationsput[$i+1] = $locationinfo[$i];
                 }
                 if($locations[1] == 0){
                 	$locations=$locationsput;
                 }
		for ($i = 0;$i <= sizeof($locationid); $i++){
			$locationnumber = $i+1;
         	        if ($locations[$locationnumber] != 0){
				$locationid[$locationnumber] = $locationid;
			}
         	        if ($locations[$locationnumber] != 0 || $locationnumber == 1 || $locations[$locationnumber-1] != 0){
         	                $locar['nr']= $locationnumber;
				$locar['locations']=arlistee ($locations[$locationnumber], get_locations($gametemp) );
				$tpl->set_ar_out($locar,7);
			}
		}
		#Server
		$x['servers']= arlistee ($servers, get_server() );
         	if(isset($servers) AND $servers!=0){
			$serar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_server` WHERE inaktive = 0 AND id='.$servers));
		}
	        $x['servername']=(isset($serar['name'])?$serar['name']:(isset($servername)?$servername:''));
		$x['ip']=(isset($serar['ip'])?$serar['ip']:(isset($ip)?$ip:''));
		$x['pw']=(isset($pw)?$pw:'');
		$tpl->set_ar_out($x,8);
	}
}elseif ($menu->getA(2)=='e') {
	if($menu->getA(3)=='a'){
		unset($tpl);
		if(isset($sub)){
			$wid=$menu->getE(2);
			db_query("INSERT INTO prefix_wars_scores (wid,locationid) VALUES ('$wid','$location')");
                 	?>
			<html>
			<head>
			<script language="JavaScript" type="text/javascript">
			<!--
			opener.location.reload();
			function closeThisWindow() {
				opener.focus();
	       			window.close();
			}
			closeThisWindow()
			//-->
			</script>
			</head>
			<body>
			</body>
			</html>
			<?php
		} else{
			$erg = db_query("SELECT * FROM `prefix_wars_scores` WHERE wid = ".$menu->getE(2)." ORDER BY id");
			?>
                         <html>
			<head>
			<script language="JavaScript" type="text/javascript">
			<!--
			opener.location.reload();
			//-->
			</script>
			</head>
			<body>
			</body>
			</html>
                         <form action="admin.php?wars-next-e<? echo $menu->getE(2); ?>-a" method="POST">
			<table width="400px" cellpadding="2" cellspacing="1" border="0" class="border">
			<tr class="Cdark">
				<td colspan="2">Locations</td>
			</tr><tr>
				<td width="20%" class="Cmite">#<? echo db_num_rows($erg) + 1; ?></td>
				<td width="30%" class="Cnorm"><select name="location"><? echo arlistee ('', get_locations($menu->getE(3)) );?></select></td>
			</tr><tr class="Cdark">
				<td></td>
				<td><input type="submit" value="Location Hinzufügen" name="sub" /></td>
			</tr>
			</table>
			<?php
		}

	} elseif($menu->getA(3)=='d'){
	  	unset($tpl);
		$num=db_num_rows(db_query('SELECT * FROM prefix_wars_scores WHERE wid = '.$menu->getE(2)));
		if($num>1){
			$sql="DELETE FROM prefix_wars_scores WHERE id=".$menu->getE(3)." LIMIT 1";
			db_query($sql);
		}
         	?>
		<html>
		<head>
		<script language="JavaScript" type="text/javascript">
		<!--
		opener.location.reload();
		function closeThisWindow() {
			opener.focus();
			window.close();
		}
		closeThisWindow()
		//-->
		</script>
		</head>
		<body>
		</body>
		</html>
		<?php
	} else{
		if(isset($sub) AND $gid != 0 AND $game != 0 AND $gametypeid != 0 AND $matchtypeid != 0 AND (sizeof($locations) >= 1)){
         	        if(isset($opponents) AND $opponents!=0){
				db_query("UPDATE `prefix_wars_opponents` SET tag = '$tag', name = '$name', contact= '$contact', email= '$email', icq= '$icq', aim= '$aim', yim= '$yim', msn= '$msn', xfire= '$xfire', ircnw= '$ircnw', ircch= '$ircch', url = '$url', country= '$country' WHERE id = '$opponents'");
			}
         	        if(isset($servers) AND $servers!=0){
				db_query("UPDATE `prefix_wars_server` SET name = '$servername', ip = '$ip' WHERE id = '$servers'");
			}
                         $datum= mktime($stu, $min, $sek, $mon , $day, $jahr);;
			$datime = date('Y-m-d H:i:s',$datum);
			db_query("UPDATE `prefix_wars` SET `gameid`='".$game."',`gametypeid`='".$gametypeid."',`matchtypeid`='".$matchtypeid."',`groupid`='".$gid."',`oppid`='".$opponents."',`serverid`='".$servers."',`serverpw`='".$pw."',`ppt`='".$ppt."',`txt`='".$txt."',`datime`='".$datime."',`chuid`='".$_SESSION['authid']."',`chtime`= NOW() WHERE id=".$menu->getE(2));
         	        $erg = db_query("SELECT * FROM `prefix_wars_scores` WHERE wid = ".$menu->getE(2)." ORDER BY id");
			$count=0;
         	        while ($ccs=db_fetch_assoc($erg)){
				$count++;
				$scoreid[$count] = $ccs['id'];
			}
			for ($i = 1;$i <= db_num_rows($erg); $i++){
				db_query("UPDATE prefix_wars_scores SET locationid='$locations[$i]' WHERE id='$scoreid[$i]'");
			}
			wd('admin.php?wars-next','Erfolgreich geändert',1);
		} else{
			$ccw = db_fetch_assoc(db_query("SELECT * FROM `prefix_wars` WHERE id = ".$menu->getE(2)));
			$ccv = db_fetch_assoc(db_query("SELECT * FROM `prefix_wars_opponents` WHERE id = ".$ccw['oppid']));
			$r['id'] = $menu->getE(2);
			#Gegnerdetails
			$opponents=(isset($opponents)?$opponents:$ccw['oppid']);
			$r['opponents']= arlistee ($opponents, get_opponents() );
			if(isset($opponents) AND $opponents!=0){
				$oppar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_opponents` WHERE inaktive = 0 AND id='.$opponents));
			}
			$r['country']=(isset($oppar['country'])?$oppar['country']:(isset($country)?$country:$ccv['country']));
			$clancountry   = arlistee ($r['country'], get_nationality_array() );
			$r['country'] = ereg_replace (".gif<", "<", $clancountry );
			$r['tag']=(isset($oppar['tag'])?$oppar['tag']:(isset($tag)?$tag:$ccv['tag']));
			$r['name']=(isset($oppar['name'])?$oppar['name']:(isset($name)?$name:$ccv['name']));
			$r['contact']=(isset($oppar['contact'])?$oppar['contact']:(isset($contact)?$contact:$ccv['contact']));
			$r['email']=(isset($oppar['email'])?$oppar['email']:(isset($email)?$email:$ccv['email']));
			$r['icq']=(isset($oppar['icq'])?$oppar['icq']:(isset($icq)?$icq:$ccv['icq']));
			$r['aim']=(isset($oppar['aim'])?$oppar['aim']:(isset($aim)?$aim:$ccv['aim']));
			$r['yim']=(isset($oppar['yim'])?$oppar['yim']:(isset($yim)?$yim:$ccv['yim']));
			$r['msn']=(isset($oppar['msn'])?$oppar['msn']:(isset($msn)?$msn:$ccv['msn']));
			$r['xfire']=(isset($oppar['xfire'])?$oppar['xfire']:(isset($xfire)?$xfire:$ccv['xfire']));
			$r['ircnw']=(isset($oppar['ircnw'])?$oppar['ircnw']:(isset($ircnw)?$ircnw:$ccv['ircnw']));
			$r['ircch']=(isset($oppar['ircch'])?$oppar['ircch']:(isset($ircch)?$ircch:$ccv['ircch']));
			$r['url']=(isset($oppar['url'])?$oppar['url']:(isset($url)?$url:$ccv['url']));
			#Wardetails
			$gid=(isset($gid)?$gid:$ccw['groupid']);
			$r['team']= arlistee ($gid, get_groups() );
         		if(isset($gid) AND $gid!=0){
				$teamar=db_fetch_assoc(db_query('SELECT * FROM `prefix_groups` WHERE is_inactive = 0 AND id='.$gid));
			}
			$gametemp=(isset($teamar['game'])?$teamar['game']:(isset($game)?$game:$ccw['gameid']));
         		$r['game']= arlistee ($gametemp, get_clangames() );
			$gametypeid=(isset($gametypeid)?$gametypeid:$ccw['gametypeid']);
			$r['gametype']= arlistee ($gametypeid, get_gametypes() );
			$matchtypeid=(isset($matchtypeid)?$matchtypeid:$ccw['matchtypeid']);
			$r['matchtype']= arlistee ($matchtypeid, get_matchtypes() );
         		$r['day']=(isset($day)?$day:substr($ccw['datime'],8,2));
			$r['mon']=(isset($mon)?$mon:substr($ccw['datime'],5,2));
			$r['jahr']=(isset($jahr)?$jahr:substr($ccw['datime'],0,4));
			$r['stu']=(isset($stu)?$stu:substr($ccw['datime'],11,2));
			$r['min']=(isset($min)?$min:substr($ccw['datime'],14,2));
			$r['sek']=(isset($sek)?$sek:substr($ccw['datime'],17,2));
			$r['ppt']=(isset($ppt)?$ppt:$ccw['ppt']);
			$r['txt']=(isset($txt)?$txt:$ccw['txt']);
			#Locations
			$tpl->set_ar_out($r,9);
			$erg = db_query("SELECT * FROM `prefix_wars_scores` WHERE wid = ".$menu->getE(2)." ORDER BY id");
			$count=0;
         	        while ($ccs=db_fetch_assoc($erg)){
				$count++;
				$locationlist[$count] = $ccs['locationid'];
				$scoreid[$count] = $ccs['id'];
			}
			for ($i = 1;$i <= db_num_rows($erg); $i++){
				$locationnumber = $i;
         		        $locar['nr']= $locationnumber;
				$locar['wid']=$menu->getE(2);
				$locar['did']=$scoreid[$i];
				$locar['locations']=arlistee ($locationlist[$locationnumber], get_locations($gametemp) );
				$tpl->set_ar_out($locar,10);
			}
			#Server
			$x['wid']=$menu->getE(2);
			$x['gid']=$gametemp;
			$servers=(isset($servers)?$servers:$ccw['serverid']);
			$x['servers']= arlistee ($servers, get_server() );
         		if(isset($servers) AND $servers!=0){
				$serar=db_fetch_assoc(db_query('SELECT * FROM `prefix_wars_server` WHERE inaktive = 0 AND id='.$servers));
			}
		        $x['servername']=(isset($serar['name'])?$serar['name']:(isset($servername)?$servername:''));
			$x['ip']=(isset($serar['ip'])?$serar['ip']:(isset($ip)?$ip:''));
			$x['pw']=(isset($pw)?$pw:$ccw['serverpw']);
			$tpl->set_ar_out($x,11);
		}
	}
}
?>