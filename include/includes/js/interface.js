// Interface für Formulare.
// Copyright 2004 by Thomas Bowe
// erweitert von Mairu und boehserdavid

//Funktion zum bestimmen der Elementkoordinaten
function getPageCoords (elementId) {
  var element;
  if (document.all) { 
    element = document.all[elementId];
  } else if (document.getElementById) {
    element = document.getElementById(elementId);
  }
  if (element) {
    var coords = {x: 0, y: 0};
    do {
      coords.x += element.offsetLeft;
      coords.y += element.offsetTop;
      element = element.offsetParent;
    } while (element)
    return coords;
  }
  else
   return null;
}

// Farbpalette verstecken/anzeigen
function hide_color() {
  if (document.getElementById("colorinput").style.display=="block") {
      document.getElementById("colorinput").style.display="none";
  } else {
      var pos = getPageCoords( "bbcode_color_button" );
      document.getElementById("colorinput").style.top = pos.y - 15 + "px";
      document.getElementById("colorinput").style.left = pos.x - 130 - document.getElementsByTagName('div')[0].offsetLeft + "px";
      document.getElementById("colorinput").style.display= "block";
  }
}

// BB-Code ins Textarea einfügen.
function bbcode_insert(tag,boxtext) {
	var formular = document.forms['form'].elements['txt'];  
	formular.focus();
	
	// Tags Definieren
	var begin_tag = "["+tag+"]";
	var end_tag = "[/"+tag+"]";
	var list_x = '';
	var list_text = '';
	
	// Für UserAgent IE.
	if(typeof document.selection !='undefined')  {
	 	// Einfügen der Tags.
		var range = document.selection.createRange();
		var prompt_box;
		
		// Box ausgeben mit Anforderung.
		if(tag == 'list') {
			if(range.text == null || range.text =='') {
				while ( list_x != null ) {
    				list_x = prompt (boxtext);
    				if ( list_x != null ) {
      					list_text = list_text + "[*]" + list_x + "\n";
    				}
  				}
  				
				if ( list_text != '' ) {
    				prompt_box = list_text; 
  				} 
			} else {
				while ( list_x != null ) {
    				list_x = prompt (boxtext,range.text);
    				if ( list_x != null ) {
      					list_text = list_text + "[*]" + list_x + "\n";
    				}
  				}
  				
				if ( list_text != '' ) {
    				prompt_box = list_text; 
  				}   
			}
		} else {
			if(range.text == null || range.text =='') {
				prompt_box = prompt(boxtext,"");  
			} else {
				prompt_box = prompt(boxtext,range.text);	  
			}
		}
		
		
		
		
		if(prompt_box != null && prompt_box !='') {
			range.text = begin_tag + prompt_box + end_tag;
			
			/* Anpassen der Cursorposition */
    		range = document.selection.createRange();
    
			if (prompt_box.length == 0) {
      			range.move('character', -end_tag.length);
    		} else {
    	  		range.moveStart('character', begin_tag.length + prompt_box.length + end_tag.length);      
    		}
   	 	
			range.select();
		}
	// Für UserAgents die auf Gecko basieren.
	} else if(typeof formular.selectionStart != 'undefined') {
	 	// Einfügen der Tags
		var start = formular.selectionStart;
    	var end = formular.selectionEnd;
 		var prompt_box;
		
		// Box ausgeben mit Anforderung.
		if(tag == 'list') {
			if(formular.value.substring(start, end) == null || formular.value.substring(start, end) =='') {
				while ( list_x != null ) {
    				list_x = prompt (boxtext);
    				if ( list_x != null ) {
      					list_text = list_text + "[*]" + list_x + "\n";
    				}
  				}
  				
				if ( list_text != '' ) {
    				prompt_box = list_text; 
  				} 
			} else {
				while ( list_x != null ) {
    				list_x = prompt (boxtext,formular.value.substring(start, end));
    				if ( list_x != null ) {
      					list_text = list_text + "[*]" + list_x + "\n";
    				}
  				}
  				
				if ( list_text != '' ) {
    				prompt_box = list_text; 
  				}   
			}
		} else {
			if(formular.value.substring(start, end) == null || formular.value.substring(start, end) =='') {
				prompt_box = prompt(boxtext,"");  
			} else {
				prompt_box = prompt(boxtext,formular.value.substring(start, end));	  
			} 
		}
		
		if(prompt_box != null && prompt_box !='') {
			if(tag == 'list') {
				formular.value = formular.value.substr(0, start) + begin_tag + "\n" + prompt_box + end_tag + formular.value.substr(end);
			} else {
				formular.value = formular.value.substr(0, start) + begin_tag + prompt_box + end_tag + formular.value.substr(end);
			}
			
			/* Anpassen der Cursorposition */
    		var pos;
    		if (prompt_box.length == 0) {
      			pos = start + begin_tag.length;
    		} else {
      			if(tag == 'list') {
					pos = start + begin_tag.length + prompt_box.length + end_tag.length +1;
				} else {
					pos = start + begin_tag.length + prompt_box.length + end_tag.length;
				}
    		}
    		
			formular.selectionStart = pos;
    		formular.selectionEnd = pos;
		}
	}
}

// BBCode mit Werte Einfügen.
function bbcode_insert_with_value(tag,boxtext1,boxtext2) {
	var formular = document.forms['form'].elements['txt'];  
	formular.focus();
	var default_text;
	
	// Alternativen Text für die Box ausgeben.
	if(tag == 'url') {
		default_text = "http://";
	} else if(tag == 'size') {
		default_text = "12"; 
	} else {
		default_text ="";  
	}
	
	// Für UserAgent IE.
	if(typeof document.selection !='undefined') {
	 	// Einfügen der Tags mit Wert.
		var range = document.selection.createRange();
		var prompt_text1;
		var prompt_text2;
		var prompt_box;
		
		// Box ausgeben mit Anforderung.
		if(range.text == null || range.text =='') {
			prompt_text1 = prompt(boxtext1,"");  
		} else {
			prompt_text1 = prompt(boxtext1,range.text);	  
		}
		
		// Ausgabe der 2ten Box.
		prompt_text2 = prompt(boxtext2,default_text);		
		
		// Überprüfen ob prompt_text1 nicht Leer ist. Wenn True dann Format [XXX=XXX]XXX[/XXX]
		if(prompt_text1 != null && prompt_text1 !='') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"="+prompt_text2+"]"+prompt_text1+"[/"+tag+"]";  
			}
		// Wenn promptText1 Leer ist dann Format [XXX]XXX[/XXX] (Aber nur bei Gewünschten Tags)
		} else if(tag == 'url' || tag == 'email') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"]"+prompt_text2+"[/"+tag+"]";  
			}		  
		}
		
		// Wenn insText nicht Leer ist dann Tags Einfügen.
		if(prompt_box != null && prompt_box !='') {
			range.text = prompt_box;
			
			/* Anpassen der Cursorposition */
    		range = document.selection.createRange();
    
			if (prompt_box.length == 0) {
      			range.move('character', -tag.length);
    		} else {
    	  		range.moveStart('character', prompt_box.length);      
    		}
   	 	
			range.select();
		}
	// Für UserAgents die auf Gecko basieren.
	} else if(typeof formular.selectionStart != 'undefined') {
	 	// Einfügen der Tags
		var start = formular.selectionStart;
    	var end = formular.selectionEnd;
		var prompt_text1;
		var prompt_text2;
 		var prompt_box;
		
		// Box ausgeben mit Anforderung.
		if(formular.value.substring(start, end) == null || formular.value.substring(start, end) =='') {
			prompt_text1 = prompt(boxtext1,"");  
		} else {
			prompt_text1 = prompt(boxtext1,formular.value.substring(start, end));	  
		}
		
		// Ausgabe der 2ten Box.
		prompt_text2 = prompt(boxtext2,default_text);
		
		// Überprüfen ob promptText1 nicht Leer ist. Wenn True dann Format [XXX=XXX]XXX[/XXX]
		if(prompt_text1 != null && prompt_text1 !='') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"="+prompt_text2+"]"+prompt_text1+"[/"+tag+"]";  
			}
		// Wenn promptText1 Leer ist dann Format [XXX]XXX[/XXX] (Aber nur bei Gewünschten Tags)
		} else if(tag == 'url' || tag == 'email') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"]"+prompt_text2+"[/"+tag+"]";  
			}		  
		}
		
		// Wenn insText nicht Leer ist dann Tags Einfügen.
		if(prompt_box != null && prompt_box !='') {
			formular.value = formular.value.substr(0, start) + prompt_box + formular.value.substr(end);
			
			/* Anpassen der Cursorposition */
    		var pos;
    		if (prompt_box.length == 0) {
      			pos = start + tag.length;
    		} else {
      			pos = start + prompt_box.length;
    		}
    		
			formular.selectionStart = pos;
    		formular.selectionEnd = pos;
		}
	}
	
}

// BBCode mit Werte Einfügen (andere Art).
function bbcode_insert_with_value_2(tag,boxtext1,boxtext2) {
	var formular = document.forms['form'].elements['txt'];  
	formular.focus();
	var default_text;
	
	// Alternativen Text für die Box ausgeben.
	if(tag == 'video') {
		default_text = "MyVideo";
	} else {
		default_text ="";  
	}
	
	// Für UserAgent IE.
	if(typeof document.selection !='undefined') {
	 	// Einfügen der Tags mit Wert.
		var range = document.selection.createRange();
		var prompt_text1;
		var prompt_text2;
		var prompt_box;
		
		// Box ausgeben mit Anforderung.
		prompt_text2 = prompt(boxtext2,default_text);
		
		// Ausgabe der 2ten Box.
		if(range.text == null || range.text =='') {
			prompt_text1 = prompt(boxtext1,"");  
		} else {
			prompt_text1 = prompt(boxtext1,range.text);	  
		}
		
		// Überprüfen ob prompt_text1 nicht Leer ist. Wenn True dann Format [XXX=XXX]XXX[/XXX]
		if(prompt_text1 != null && prompt_text1 !='') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"="+prompt_text2+"]"+prompt_text1+"[/"+tag+"]";  
			}
		// Wenn promptText1 Leer ist dann Format [XXX]XXX[/XXX] (Aber nur bei Gewünschten Tags)
		} else if(tag == 'url' || tag == 'email') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"]"+prompt_text2+"[/"+tag+"]";  
			}		  
		}
		
		// Wenn insText nicht Leer ist dann Tags Einfügen.
		if(prompt_box != null && prompt_box !='') {
			range.text = prompt_box;
			
			/* Anpassen der Cursorposition */
    		range = document.selection.createRange();
    
			if (prompt_box.length == 0) {
      			range.move('character', -tag.length);
    		} else {
    	  		range.moveStart('character', prompt_box.length);      
    		}
   	 	
			range.select();
		}
	// Für UserAgents die auf Gecko basieren.
	} else if(typeof formular.selectionStart != 'undefined') {
	 	// Einfügen der Tags
		var start = formular.selectionStart;
    	var end = formular.selectionEnd;
		var prompt_text1;
		var prompt_text2;
 		var prompt_box;
		
		// Ausgabe der 2ten Box.
		prompt_text2 = prompt(boxtext2,default_text);
		
		// Box ausgeben mit Anforderung.
		if(formular.value.substring(start, end) == null || formular.value.substring(start, end) =='') {
			prompt_text1 = prompt(boxtext1,"");  
		} else {
			prompt_text1 = prompt(boxtext1,formular.value.substring(start, end));	  
		}
		
		// Überprüfen ob promptText1 nicht Leer ist. Wenn True dann Format [XXX=XXX]XXX[/XXX]
		if(prompt_text1 != null && prompt_text1 !='') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"="+prompt_text2+"]"+prompt_text1+"[/"+tag+"]";  
			}
		// Wenn promptText1 Leer ist dann Format [XXX]XXX[/XXX] (Aber nur bei Gewünschten Tags)
		} else if(tag == 'url' || tag == 'email') {
			if(prompt_text2 != null && prompt_text2 !='') {
				prompt_box = "["+tag+"]"+prompt_text2+"[/"+tag+"]";  
			}		  
		}
		
		// Wenn insText nicht Leer ist dann Tags Einfügen.
		if(prompt_box != null && prompt_box !='') {
			formular.value = formular.value.substr(0, start) + prompt_box + formular.value.substr(end);
			
			/* Anpassen der Cursorposition */
    		var pos;
    		if (prompt_box.length == 0) {
      			pos = start + tag.length;
    		} else {
      			pos = start + prompt_box.length;
    		}
    		
			formular.selectionStart = pos;
    		formular.selectionEnd = pos;
		}
	}
	
}

// Simples einfügen der Tags :-)
function bbcode_code_insert(tag,color) {
  var formular = document.forms['form'].elements['txt'];  
  formular.focus();
  // Tags Definieren
  if(color == "0"){
    var begin_tag = "["+tag+"]";
    var end_tag = "[/"+tag+"]";
    if (document.form.code != undefined) {
      document.form.code.options['0'].selected = true; // selectiert immer <Code einfügen>
    }
  } else if (tag == "code" || tag == "php" || tag == "html" || tag == "css") {
    prompt_text1 = prompt("Format: dateiname;5  (Im Beispiel ist die Startzeile 5)\nSie können hier nun einen Dateinamen und eine Startzeile mit angeben,\nwobei die Startzeile optional ist und auch das komplette Feld leer gelassen werden kann.)","");
    if (prompt_text1 != "" && prompt_text1 != null) {
      var begin_tag = "["+tag+"="+prompt_text1+"]";
    } else {
      var begin_tag = "["+tag+"]";
    }
    var end_tag = "[/"+tag+"]";
  } else {
    var begin_tag = "["+tag+"="+color+"]";
    var end_tag = "[/"+tag+"]";
  }
	
	// Für UserAgent IE.
	if(typeof document.selection != 'undefined') {
	 	// Einfügen der Tags.
		var range = document.selection.createRange();
		var prompt_box = range.text;
		
		// Überprüfen ob es sich um den PHP Tag handelt und wenn ja Überprüfen ob der string folgende zeichenketten hat <? und ?>!
		if(tag == "php" && prompt_box.match(/(\<\?)/i) && prompt_box.match(/(\?\>)/i)) {
		 	prompt_box;	 
		} else if(tag == "php" && prompt_box != null && prompt_box !='') {
			prompt_box = "<?php\n"+prompt_box+"\n?>";	 
		}
		
        if(prompt_box != null && prompt_box !='') { 
            range.text = begin_tag + prompt_box + end_tag;
        }
			
		/* Anpassen der Cursorposition */
   		range = document.selection.createRange();
    
		if (prompt_box.length == 0) {
   			range.move('character', -end_tag.length);
   		} else {
    		range.moveStart('character', begin_tag.length + prompt_box.length + end_tag.length);      
   		}
   	 	
		range.select();
	// Für UserAgents die auf Gecko basieren.
	} else if(typeof formular.selectionStart != 'undefined') {
	 	// Einfügen der Tags
		var start = formular.selectionStart;
    	var end = formular.selectionEnd;
 		var prompt_box = formular.value.substring(start, end);
		
		// Überprüfen ob es sich um den PHP Tag handelt und wenn ja Überprüfen ob der string folgende zeichenketten hat <? und ?>!
		if(tag == "php" && prompt_box.match(/(\<\?)/i) && prompt_box.match(/(\?\>)/i)) {
		 	prompt_box;	 
		} else if(tag == "php" && prompt_box != null && prompt_box !='') {
			prompt_box = "<?php\n"+prompt_box+"\n?>";	 
		}
		
		
		if(prompt_box != null && prompt_box !='') {
            formular.value = formular.value.substr(0, start) + begin_tag + prompt_box + end_tag + formular.value.substr(end);
        }
			
		/* Anpassen der Cursorposition */
   		var pos;
   		if (prompt_box.length == 0) {
   			pos = start + begin_tag.length;
   		} else {
   			pos = start + begin_tag.length + prompt_box.length + end_tag.length;
   		}
    		
		formular.selectionStart = pos;
   		formular.selectionEnd = pos;
	}
}

function bbcode_code_insert_codes(tag) {
  if (tag != "0") {
    bbcode_code_insert(tag,'1');
  }
}
