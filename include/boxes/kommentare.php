<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 */
defined('main') or die('no direct access');
// -----------------------------------------------------------|
//Anzahl der der Kommentare
$anz = 5;
//Laenge der der Kommentare
$info = 15;
// -----------------------------------------------------------|
?>
<script language="Javascript" type="text/javascript">
$(document).ready(function() {
	$("a#fancyframecomment").fancybox({
		'overlayShow'		: true,				
		'width'				: '90%',
		'height'			: '90%',
		'autoScale'			: false,
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'type'				: 'iframe',
		'titleShow'         : false,
		'centerOnScroll'	: true
	});
});
</script>
<?php

// Kalender - Recht
$Ktyp = 'KALENDER';
if ($allgAr[ 'Kgkoms' ] == 0) { if (loggedin()) { $Ktyp = 'KALENDER'; } else { $Ktyp = ''; } }
if ($allgAr[ 'Kukoms' ] == 0) { $Ktyp = ''; }
// Gallery - Recht
$Gtyp = 'GALLERYIMG';
if ($allgAr[ 'Ggkoms' ] == 0) { if (loggedin()) { $Gtyp = 'GALLERYIMG'; } else { $Gtyp = ''; } }
if ($allgAr[ 'Gukoms' ] == 0) { $Gtyp = ''; }
// Gaestebuch - Recht
$GBtyp = 'GBOOK';
if ($allgAr[ 'GBgkoms' ] == 0) { if (loggedin()) { $GBtyp = 'GBOOK'; } else { $GBtyp = ''; } }
if ($allgAr[ 'GBukoms' ] == 0) { $GBtyp = ''; }
// News - Recht
$Ntyp = 'NEWS';
if ($allgAr[ 'Ngkoms' ] == 0) { if (loggedin()) { $Ntyp = 'NEWS'; } else { $Ntyp = ''; } }
if ($allgAr[ 'Nukoms' ] == 0) { $Ntyp = ''; }
// Downloads - Recht
$Dtyp = 'DOWNLOAD';
if ($allgAr[ 'Dgkoms' ] == 0) { if (loggedin()) { $Dtyp = 'DOWNLOAD'; } else { $Dtyp = ''; } }
if ($allgAr[ 'Dukoms' ] == 0) { $Dtyp = ''; }
// Wars - Recht
$Wtyp = '';
if ($allgAr['wars_last_komms'] < 0 AND has_right ($allgAr['wars_last_komms'])) { $Wtyp = 'WARSLAST'; }
//
$abf = "SELECT
			`a`.`id`,
			`a`.`name`,
			`a`.`uid`,
			`a`.`cat` as 'categorie',
			`a`.`text`,
			`a`.`time`,
			`b`.`news_id`,
			`b`.`news_recht`,
			`c`.`id`,
			`c`.`recht`,
			`d`.`id`,
			`d`.`cat`,			
			`e`.`id`,
			`e`.`recht`
		FROM
			`prefix_koms` AS `a`
		LEFT JOIN
			`prefix_news` AS `b` ON `a`.`uid` = `b`.`news_id`
		LEFT JOIN
			`prefix_kalender` AS `c` ON `a`.`uid` = `c`.`id`
		LEFT JOIN
			`prefix_gallery_imgs` AS `d` ON `a`.`uid` = `d`.`id`
		LEFT JOIN
			`prefix_gallery_cats` AS `e` ON `d`.`cat` = `e`.`id`
		WHERE
			(`a`.`cat` = '$Ktyp' AND `c`.`recht` >= '".$_SESSION['authright']."')
		OR
			(`a`.`cat` = '$Gtyp' AND (`e`.`recht` >= '".$_SESSION['authright']."' OR `d`.`cat` = 0))
		OR
			`a`.`cat` = '$GBtyp'
		OR
			(`a`.`cat` = '$Ntyp' AND `b`.`news_recht` >= '".$_SESSION['authright']."')
		OR 
			`a`.`cat` = '$Dtyp'
		OR			
			`a`.`cat` = '$Wtyp'
		ORDER BY 
			`a`.`id` DESC LIMIT 0,".$anz;
//

$erg = db_query($abf);
echo '<table width="100%" border="0" cellpadding="2" cellspacing="0">';

if (db_num_rows($erg) == 0)
{
	echo '<tr><td class="box" align="center">keine Kommentare vorhanden<td><tr>';
}
else
{
	while ($row = db_fetch_object($erg)) 
	{	

		$fancylink = '';

		if ($row->categorie == 'KALENDER') { 
				$kat = 'Kalender'; 
				$link = 'index.php?kalender-v1-e' . $row->uid; 
			}
		if ($row->categorie == 'GALLERYIMG') { 
				$id = db_result(db_query("SELECT `id` FROM `prefix_gallery_imgs` WHERE `id` = " . $row->uid), 0);
				$cid = db_result(db_query("SELECT `cat` FROM `prefix_gallery_imgs` WHERE `id` = " . $id), 0);			
				$anz = db_result(db_query("SELECT COUNT(*) FROM `prefix_gallery_imgs` WHERE `id` < " . $id . " AND `cat` = " . $cid), 0);
				$kat = 'Gallery'; $fancylink = 'id="fancyframecomment"';
				$link = 'index.php?gallery-show-' . $cid . '-p' . $anz; 
			}
		if ($row->categorie == 'GBOOK') { 
				$kat = 'Gbook'; 
				$link = 'index.php?gbook-show-' . $row->uid; 
			}
		if ($row->categorie == 'NEWS') { 
				$kat = 'News'; 
				$link = 'index.php?news-' . $row->uid; 
			}
		if ($row->categorie == 'DOWNLOAD') { 
				$kat = 'Download'; 
				$link = 'index.php?downloads-show-' . $row->uid; 
			}
		if ($row->categorie == 'WARSLAST') { 
				$kat = 'Wars'; 
				$link = 'index.php?wars-more-' . $row->uid; 
			}

		echo 	'<tr>
				   <td><b> &raquo; </b></td>
				   <td>
				     <a class="box" '.$fancylink.' href="'.$link.'" title="'.post_date($row->time,1).'">
					   '.((strlen($row->text)<$info) ? $row->text : substr($row->text,0,$info-3).'...').'
					 </a><br>
					 <span class="smalfont">'.$kat.' // '.$row->name.'</span>
				   </td>
				</tr>';
	}
}
echo '</table>';