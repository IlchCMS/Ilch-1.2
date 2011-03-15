$(document).ready(function() {
	
	// FORUM-NEWPOST-FORMULAR			   
	$("#newpost_form").validate({
		rules: {
			Gname: { required: true, minlength: 2 },
			txt: { required: true },
			number: { required: true }
		},
		messages: {
			Gname: { required: "Bitte dein Name f&uuml;r diese Antwort eingeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			txt: "Bitte eine Beitrag schreiben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});