$(document).ready(function() {

	// NEWS-KOMMENTAR					   
	$("#comments").validate({
		rules: {
			name: { required: true, minlength: 2 },
			txt: { required: true },
			number: { required: true }
		},
		messages: {
			name: { required: "Bitte Name angeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			txt: "Bitte ein Kommentar angeben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});