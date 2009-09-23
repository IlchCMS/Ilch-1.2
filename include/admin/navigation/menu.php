<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$umenu .= '<p><b>Menü auswählen</b></p>';
$umenu .= '<ul style="padding-left: 15px;">';
	
for($i=1;$i<=$allgAr['menu_anz'];$i++) { 
	$umenu .= '<li><a href="admin.php?menu-'.$i.'" style="color: #FFFFFF">Menü '.$i.'</a></li>';
}

$umenu .= '</ul>';

?>
