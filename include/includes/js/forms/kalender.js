$(document).ready(function() {
	
	// KALENDER-KOMMENTAR					   
	$("#comments").validate({
		rules: {
			name: { required: true, minlength: 2 },
			text: { required: true },
			number: { required: true }
		},
		messages: {
			name: { required: "Bitte Name angeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			text: "Bitte ein Kommentar angeben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});