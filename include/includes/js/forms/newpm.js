$(document).ready(function() {
	
	// NEWPM-FORMULAR			   
	$("#newpm_form").validate({
		rules: {
			name: { required: true },
			bet: { required: true },
			txt: { required: true }
		},
		messages: {
			name: "Bitte ein Empf&auml;nger-Name eingeben oder finden!",
			bet: "Bitte ein Betreff angeben!",
			txt: "Bitte ein Beitrag in das Textfeld schreiben!"
		}
	});
	
});

<!--
  function finduser () 
  {
  var Fenster = window.open ('index.php?search-finduser', 'finduser', 'status=no,scrollbars=yes,height=200,width=350');
  Fenster.focus();
  return (false);
  }
//-->

<!--
  $(document).ready(function(){
    $('input[name=name]').autoComplete({ 
      ajax: 'index.php?user-search' 
    }); 
  });
//-->