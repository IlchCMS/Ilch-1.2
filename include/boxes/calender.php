<script>
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
});
</script>
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