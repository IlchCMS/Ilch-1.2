$(document).ready(function() {

	// UPLOAD-FORMULAR			   
	$("#upload_form").validate({
		rules: {
			name: { required: true, minlength: 2 },
			version: { required: true },
			autor: { required: true },
			//url:  { required: true, url: true },
			//file:  { required: true },
			//surl: { required: true },
			//ssurl: { required: true },
			desc: { required: true },
			descl: { required: true }
		},
		messages: {
			name: { required: "Bitte Name eingeben!", minlength: "Dein Name muss mindestens 2 Zeichen haben!" },
			version: "Bitte Version angeben!",
			autor: "Bitte Autor angeben!",
			//url: "Bitte eine g&uuml;ltige Internetadresse eingeben!",
			//file: "Bitte eine Datei ausw&auml;hlen!",
			//surl: "Bitte einen Demolink angeben!",
			//ssurl: "Bitte ein Link zur Vorschau angeben!",
			desc: "Bitte eine kurze Beschreibung angeben!",
			descl: "Bitte eine ausf&uuml;hrliche Beschreibung eingeben!"
		}
	});
	
});