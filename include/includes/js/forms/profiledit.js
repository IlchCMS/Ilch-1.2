$(document).ready(function() {
	
	// PROFILEDIT-FORMULAR			   
	$("#profiledit_form").validate({
		rules: {
			email: { required: true, email: true },
			//homepage: { required: true, url: true },
			wohnort: { required: true },
			//icq: { required: true },
			//msn: { required: true },
			//yahoo: { required: true },
			//aim: { required: true },
			gebdatum: { required: true },
			staat: { required: true },
			//sig: { required: true }
		},
		messages: {
			email: "Bitte eine g&uuml;ltige Emailadresse eingeben!",
			//homepage: "Bitte eine g&uuml;ltige Internetadresse eingeben!",
			wohnort: "Bitte gib deinen Wohnort an!",
			//icq: "Bitte gib deine ICQ Nummer an!",
			//msn: "Bitte gib deine Microsoft Network Kennung an!",
			//yahoo: "Bitte gib deine Yahoo Kennung an!",
			//aim: "Bitte gib dein AOL Instant Messenger Kennung an!",
			gebdatum: "Bitte gib dein Geburtsdatum, in Form von JJJJ-MM-TT an!",
			staat: "Bitte w&auml;hle dein Heimatland aus!",
			//sig: "Bitte gib eine Forumsignatur ein!"
		}
	});
	
	// DATEPICKER
	$( "#datepickerProfil" ).datepicker({ 
			autoSize: true,
			monthNames: ['Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
			monthNamesShort: ['Jan','Feb','M&auml;r','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'],
			dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
			showWeek: true,
			changeMonth: true,
			changeYear: true,
			firstDay: 1,
			dateFormat: 'yy-mm-dd',
			autoSize: true,
			yearRange: 'c-99:c+5',
		});
	
	// PASSWORD
	$(".password").pstrength();

});