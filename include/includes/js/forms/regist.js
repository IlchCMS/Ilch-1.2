$(document).ready(function() {

	// REGIST-FORMULAR				   
	$("#regist_form").validate({
		rules: {
			nutz: { required: true, minlength: 2 },
			email: { required: true, email: true }
		},
		messages: {
			nutz: { required: "Bitte Nicknamen eingeben!", minlength: "Dein Nickname muss mindestens 2 Zeichen haben!" },
			email: "Bitte eine g&uuml;ltige Emailadresse eingeben!"
		}
	});
	
	// PASSWORD
	$('.password').pstrength();

});