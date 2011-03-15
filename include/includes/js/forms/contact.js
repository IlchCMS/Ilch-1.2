$(document).ready(function() {

	// KONTAKT-FORUMULAR	
	$("#contact_form").validate({
		rules: {
			name: { required: true, minlength: 2 },
			mail: { required: true, email: true },
			subject: { required: true },
			txt: { required: true },
			number: { required: true }
		},
		messages: {
			name: { required: "Bitte Name eingeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			mail: "Bitte eine g&uuml;ltige Emailadresse eingeben!",
			subject: "Bitte ein Betreff angeben!",			
			txt: "Bitte eine Nachricht eingeben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});