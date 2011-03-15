$(document).ready(function() {

	// JOINUS-FORMULAR
	$("#joinus_form").validate({
		rules: {
			name: { required: true, minlength: 2 },
			skill: { required: true },
			icqnumber: { required: true },
			favmap: { required: true },
			mail: { required: true, email: true },
			age: { required: true },
			hometown: { required: true },
			squad: { required: true },
			ground: { required: true },
			rules: { required: true },
			number: { required: true }
		},
		messages: {
			name: { required: "Bitte Name eingeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			skill: "Bitte den zutreffenden Skill ausw&auml;hlen!",
			icqnumber: "Bitte deine ICQ-Nummer angeben!",
			favmap: "Bitte die favorisierte Karte angeben!",
			mail: "Bitte eine g&uuml;ltige Emailadresse eingeben!",
			age: "Bitte dein Alter eingeben!",
			hometown: "Bitte dein Wohnort eingeben!",
			squad: "Bitte das gew&uuml;nschte Team ausw&auml;hlen!",
			ground: "Bitte einen Grund angeben!",
			rules: "Bitte die Regeln best&auml;tigen!<br/>",
			number: "Bitte den Antispam ausf&uuml;llen!"
		}
	});
	
});