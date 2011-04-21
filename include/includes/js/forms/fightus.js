$(document).ready(function() {

	// FIGHTUS-FORMULAR
	$("#fightus_form").validate({
		rules: {
			clanname: { required: true, minlength: 2 },
			clantag: { required: true },
			clanpage: { required: true, url: true },
			clancountry: { required: true },
			mailaddy: { required: true, email: true },
			icqnumber: { required: true },
			meetingplace: { required: true },
			date: { required: true },
			stunde: { required: true, min: 0, max: 23 },
			minute: { required: true, min: 0, max: 59 },
			squad: { required: true },
			xonx: { required: true },
			game: { required: true },
			matchtype: { required: true },
			message: { required: true },
			number: { required: true }
		},
		messages: {
			clanname: { required: "Bitte euren Clannamen eingeben!", minlength: "Der Clanname muss mindestens 2 Zeichen haben!" },
			clantag: "Bitte euer Clank&uuml;rzel eingeben!",
			clanpage: "Bitte eine g&uuml;ltige Internetadresse eingeben!",
			clancountry: "Bitte euer Herkunftsland ausw&auml;hlen!",	
			mailaddy: "Bitte eine g&uuml;ltige Emailadresse eingeben!",
			icqnumber: "Bitte deine ICQ-Nummer angeben!",
			meetingplace: "Bitte den Treffpunkt angeben!",
			date: "Bitte gib das Datum, in Form von TT.MM.JJJJ, an!",
			stunde: { required: "Bitte die Stunde in Form von XX angeben!",
					min: "Der Stunde muss eine Zahl zwischen 00 und 23 sein!", 
					max: "Der Stunde muss eine Zahl zwischen 00 und 23 sein!" },
			minute: { required: "Bitte die Minute in Form von XX angeben!",
					min: "Der Minute muss eine Zahl zwischen 00 und 59 sein!", 
					max: "Der Minute muss eine Zahl zwischen 00 und 59 sein!" },
			squad: "Bitte das gew&uuml;nschte Team ausw&auml;hlen!",
			xonx: "Bitte das gew&uuml;nschte XonX ausw&auml;hlen!",
			game: "Bitte das gew&uuml;nschte Spiel ausw&auml;hlen!",
			matchtype: "Bitte den gew&uuml;nschten Spieltyp ausw&auml;hlen!",
			message: "Bitte eine Nachricht oder zus&auml;tzliche Infos angeben!",
			number: "Bitte den Antispam ausf&uuml;llen!"
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
			dateFormat: 'dd.mm.yy',
			autoSize: true,
			yearRange: 'c:c+1',
		});
});