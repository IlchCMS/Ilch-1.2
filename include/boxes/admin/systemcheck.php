<?php
$get_check_status = @db_result(db_query("SELECT `wert` FROM `prefix_config` WHERE schl = 'syscheckstatus'"));
$get_check_datum  = @db_result(db_query("SELECT `wert` FROM `prefix_config` WHERE schl = 'syscheckdatum'"));
define('OK', '#0c0');
define('FEHLER', 'red');
define('WARNUNG', '#c93');

if ($get_check_status == 'OK') {
	$sysstatus = '<div style="color: '.OK.'; float:left;">&nbsp;OK</div>';
} else
if ($get_check_status == 'Warnung') {
	$sysstatus = '<div style="color:'.WARNUNG.'; float:left;">&nbsp;Warnung</div>';
} else
if ($get_check_status == 'Fehler') {
	$sysstatus = '<div style="color: '.FEHLER.'; float:left;">&nbsp;Fehler</div>';
} else
if (!is_writeable('./include/cache')) {
	$sysstatus = '<div style="color: '.FEHLER.'; float:left;">&nbsp;fehlende Rechte</div>';
} else 
if ($get_check_status == 'ungeprüft' or $get_check_status === FALSE) {
	$sysstatus = '<div style="color: '.WARNUNG.'; float:left;">&nbsp;ungeprüft</div>';
}
if (strtotime($get_check_datum) <= strtotime('NOW - 7days') ) { // Meldet wenn letzter Check älter als 7 Tage ist
    $sysstatus = '<div style="color: '.WARNUNG.'; float:left;">&nbsp;veraltet</div>';
}
?><li>
<center>
    <a href="admin.php?checkconf-1">
    <span>
    <div style="float:left;">
        <strong>Systemstatus</strong>
    </div>
        <?php echo $sysstatus; ?>
    </span>
    </a>
</center>
</li>