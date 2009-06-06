	function check() {
	var email=document.form1.eemail.value, nutz=document.form1.nutz.value;
		 var cmail="",cnutz="";
	 if (email!="") {
		 if (document.form1.eemail.value.indexOf('@',0)==-1 || document.form1.eemail.value.indexOf('.',0)==-1)
	 cmail="Die eingegebene E-Mail Adresse ist nicht korrekt!\n";
		 }
	else
		var cmail="Du hast keine E-Mail Adresse angegeben!\n";
		 if (nutz=="")
		 	 var cnutz="Du hast keinen Nickname eingegeben!\n";
		 if (cmail!="" || cnutz!=""){
		 	 alert(cmail+cnutz)
		return false;
		 }
	  else
	  return true;
  }