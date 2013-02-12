$(document).ready(function() {

	// GAESTEBUCH-FORMULAR					   
	$("#gbook_form").validate({
		rules: {
			name: { required: true, minlength: 2 },
			mail: { required: false, email: true },
			page: { required: false, url: true },
			txt: { required: true },
			number: { required: true }
		},
		messages: {
			name: { required: "Bitte Name eingeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			mail: "Bitte eine g&uuml;ltige Emailadresse eingeben!",
			page: "Bitte eine g&uuml;ltige Internetadresse eingeben!",			
			txt: "Bitte eine Nachricht eingeben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});	
	
	// GAESTEBUCH-KOMMENTAR					   
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