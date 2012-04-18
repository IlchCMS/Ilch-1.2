<?php
$get_check_status = db_result(db_query("SELECT `wert` FROM `prefix_config` WHERE schl = 'syscheckstatus'"));
$get_check_datum  = db_result(db_query("SELECT `wert` FROM `prefix_config` WHERE schl = 'syscheckdatum'"));

if ($get_check_status == 'OK') {
	$sysstatus = '<div style="color: #0c0; float:left;">&nbsp;OK</div>';
} else
if ($get_check_status == 'Warnung') {
	$sysstatus = '<div style="color: #c93; float:left;">&nbsp;Warnung</div>';
} else
if ($get_check_status == 'Fehler') {
	$sysstatus = '<div style="color: red; float:left;">&nbsp;Fehler</div>';
} else
if (!is_writeable('./include/cache')) {
	$sysstatus = '<div style="color: red; float:left;">&nbsp;fehlende Rechte</div>';
} else 
if ($get_check_status == 'ungeprüft' or $get_check_status === FALSE) {
	$sysstatus = '<div style="color: #c93; float:left;">&nbsp;ungeprüft</div>';
}
?><li>
<center><a href="admin.php?checkconf"><span>
    <div style="float:left;"><strong>Systemstatus</strong></div><?php echo $sysstatus; ?></span></a></center>
</li>