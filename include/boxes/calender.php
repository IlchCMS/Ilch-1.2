<?php
$dates = '[ ';
$erg = db_query('SELECT * FROM `prefix_kalender`');
while($row = db_fetch_assoc($erg)) {
    $year = date("Y", $row['time']);
    $month = date("m", $row['time']) -1;
    $day = date("d", $row['time']);
    $dates .= 'new Date('.$year.','.$month.','.$day.'), ';
}

$erg = db_query('SELECT `name`, `gebdatum`, `recht`, `id`,
    CASE WHEN ( MONTH(`gebdatum`) < MONTH(NOW()) ) OR ( MONTH(`gebdatum`) <= MONTH(NOW()) AND DAYOFMONTH(`gebdatum`) < DAYOFMONTH(NOW()) ) THEN
    gebdatum + INTERVAL (YEAR(NOW()) - YEAR(`gebdatum`) + 1) YEAR
    ELSE
    `gebdatum` + INTERVAL (YEAR(NOW()) - YEAR(`gebdatum`)) YEAR
    END
    AS `gebtage`
    FROM `prefix_user` WHERE `gebdatum` > 0000-00-00 ORDER BY `gebtage`;');
    
$year = date("Y");
while($row = db_fetch_assoc($erg)) {
    if ($row['recht'] <= '-1') {
        $month = date("m", strtotime($row['gebdatum'])) -1;
		    $day = date("d", strtotime($row['gebdatum']));
		    $dates .= 'new Date('.$year.','.$month.','.$day.'), ';
    }
}

$erg = db_query('SELECT `datime` FROM `prefix_wars`');

    while($row = db_fetch_assoc($erg)) {
        $year = date("Y", strtotime($row['datime']));
    		$month = date("m", strtotime($row['datime'])) -1;
    		$day = date("d", strtotime($row['datime']));
    		$dates .= 'new Date('.$year.','.$month.','.$day.'), ';
    }

$dates .= '];';

?>

<script>
var dates = <?php echo $dates ?>

$(function() {

	$( "#datepicker" ).datepicker({
			autoSize: true,
			monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
			monthNamesShort: ['Jan','Feb','M&auml;r','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'],
			dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
			showWeek: true,
			changeMonth: true,
			changeYear: true,
			firstDay: 1,
			dateFormat: 'dd.mm.yy',
			beforeShowDay: highlightDays,
			autoSize: true,
			buttonText: 'Choose',
			yearRange: 'c-10:c+10',
			onSelect: function(dateText, inst) { 
							document.form1.submit();
							var loc = '';
							var datesplit = dateText.split('.');
							location.href = loc+'?kalender-gotoDate-'+datesplit[2]+'-'+datesplit[1]+'-'+datesplit[0];
					}
		});
		
		function highlightDays(date) {
        for (var i = 0; i < dates.length; i++) {
              if (dates[i].getTime() == date.getTime()) {
                             return [true, 'highlight'];
                     }
             }
             return [true, ''];
    }
});



</script>
<style type="text/css">
#highlight, .highlight {
    background-color: red;
}
​</style>


<noscript>
Bitte JavaScript aktivieren
</noscript>
<center>
<div class="datepicker">

<p><form id="form1" name="form1" action="">

<div id="datepicker"></div>

    <div style="display: none;"><input type="submit" value="absenden" id="datepicker"/></div>
    </form></p>

</div>
<p></p>
</center>