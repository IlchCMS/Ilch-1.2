$(document).ready(function() {
	
	// AWAYCAL-FORMULAR			   
	$("#awaycal_form").validate({
		rules: {
			von: { required: true },
			bis: { required: true },
			betreff: { required: true }
		},
		messages: {
			von: "Bitte Datum des Beginns der Abwesenheit angeben!",
			bis: "Bitte Datum des Ende der Abwesenheit angeben!",
			betreff: "Bitte ein Grund f&uuml;r die Abwesenheit angeben!"
		}
	});
		
});