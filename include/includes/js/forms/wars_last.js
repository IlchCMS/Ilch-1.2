$(document).ready(function() {

	// WAR-KOMMENTAR					   
	$("#comments").validate({
		rules: {
			text: { required: true },
			number: { required: true }
		},
		messages: {
			text: "Bitte ein Kommentar angeben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});