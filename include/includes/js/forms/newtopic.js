$(document).ready(function() {

	// FORUM-NEWTOPIC-FORMULAR			   
	$("#newtopic_form").validate({
		rules: {
			topic: { required: true },
			Gname: { required: true, minlength: 2 },
			txt: { required: true },
			number: { required: true }
		},
		messages: {
			topic: "Bitte ein aussagekr&auml;ftigen Titel eingeben!",
			Gname: { required: "Bitte dein Name f&uuml;r den Foreneintrag eingeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			txt: "Bitte eine Beitrag schreiben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});