$(document).ready(function() {

	// FORUM-POSTEDIT-FORMULAR			   
	$("#postedit_form").validate({
		rules: {
			txt: { required: true }
		},
		messages: {
			txt: "Bitte ein Beitrag in das Textfeld schreiben!"
		}
	});
	
});