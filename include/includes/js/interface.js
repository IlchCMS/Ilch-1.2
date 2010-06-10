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
	} else {
		return null;
	}
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

//Textarea in die für BBcode genutzt wird
var bbcode_textarea = ['form', 'txt'];

function bbcode_insert_into_textarea(text){
	var formular = document.forms[bbcode_textarea[0]].elements[bbcode_textarea[1]];
	formular.focus();

	// Nachschauen, an welche Position cursor gesetzt werden soll
	if (bbcode_insert_into_textarea.arguments.length != 1) {
		var pos = bbcode_insert_into_textarea.arguments[1];
	} else {
		var pos = -1;
	}

	// Für UserAgent IE.
	if(typeof document.selection != 'undefined')  {
	 	// Einfügen der Tags.
		var range = document.selection.createRange();

		if(text != null && text !='') {
			range.text = text;
			range.select();
			/* Anpassen der Cursorposition */
    		range = document.selection.createRange();
   	  		if (pos == -1) {
   	  			range.moveStart('character', 0);//text.length);
   	  		} else {
				range.move('character', (text.length * -1) + pos);
			}

			range.select();
		}
	// Für UserAgents die auf Gecko basieren.
	} else if(typeof formular.selectionStart != 'undefined') {
	 	// Einfügen der Tags
		var start = formular.selectionStart;
    	var end = formular.selectionEnd;

		if(text != null && text !='') {
			formular.value = formular.value.substr(0, start) + text + formular.value.substr(end);
			/* Anpassen der Cursorposition */
    		var pos = start + (pos == -1 ? text.length : pos);

			formular.selectionStart = pos;
    		formular.selectionEnd = pos;
		}
	}
}

function bbcode_get_selection(){
	var formular = document.forms[bbcode_textarea[0]].elements[bbcode_textarea[1]];
	formular.focus();
	var text;
	// Für UserAgent IE.
	if(typeof document.selection !='undefined')  {
	 	// Einfügen der Tags.
		var range = document.selection.createRange();
		text = range.text;
	// Für UserAgents die auf Gecko basieren.
	} else if(typeof formular.selectionStart != 'undefined') {
	 	// Einfügen der Tags
		var start = formular.selectionStart;
    	var end = formular.selectionEnd;
    	text = formular.value.substring(start, end);
    }
    return text;
}

// BB-Code ins Textarea einfügen.
function bbcode_insert(tag, boxtext) {
	var formular = document.forms[bbcode_textarea[0]].elements[bbcode_textarea[1]];
	formular.focus();

	// Tags Definieren
	var begin_tag = "["+tag+"]";
	var end_tag = "[/"+tag+"]";
	var list_x = '';
	var list_text = '';
    var prompt_box;

	var selection = bbcode_get_selection();

	// Box ausgeben mit Anforderung.
	if(tag == 'list') {
		if(selection == null || selection =='') {
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
 				list_x = prompt (boxtext,selection);
 				if ( list_x != null ) {
   					list_text = list_text + "[*]" + list_x + "\n";
 				}
			}

			if ( list_text != '' ) {
 				prompt_box = list_text;
			}
		}
	} else {
		if(selection == null || selection == '') {
			prompt_box = prompt(boxtext, "");
		} else {
			bbcode_insert_into_textarea(begin_tag + selection + end_tag);
			return;
		}
	}
	if (prompt_box == null || prompt_box == '') {
		prompt_box = '';
		var pos = tag.length + 2;
	} else {
		var pos = -1;
	}
	bbcode_insert_into_textarea(begin_tag + prompt_box + end_tag, pos);
}

// BBCode mit Werte Einfügen.
function bbcode_insert_with_value(tag, boxtext1, boxtext2) {
	var default_text;
	var selection = bbcode_get_selection();
	var prompt_text1;
	var prompt_text2;
	var prompt_box;
	var pos = -1;

	// Alternativen Text für die Box ausgeben.
	if(tag == 'url') {
		default_text = "http://";
	} else if(tag == 'size') {
		default_text = "12"; 
	} else {
		default_text ="";  
	}

	// Box ausgeben mit Anforderung.
	if(selection == null || selection =='') {
		prompt_text1 = prompt(boxtext1, "");
	} else {
		prompt_text1 = prompt(boxtext1, selection);
	}

	// Ausgabe der 2ten Box.
	prompt_text2 = prompt(boxtext2, default_text);

	// Überprüfen ob prompt_text1 nicht Leer ist. Wenn True dann Format [XXX=XXX]XXX[/XXX]
	if(prompt_text1 != null && prompt_text1 !='') {
		if(prompt_text2 != null && prompt_text2 !='') {
			prompt_box = "["+tag+"="+prompt_text2+"]"+prompt_text1+"[/"+tag+"]";
		}
	// Wenn promptText1 Leer ist dann Format [XXX]XXX[/XXX] (Aber nur bei Gewünschten Tags)
	} else if(tag == 'url' || tag == 'email') {
		if(prompt_text2 != null && prompt_text2 !='') {
			prompt_box = "["+tag+"]"+prompt_text2+"[/"+tag+"]";
		} else {
			prompt_box = "["+tag+"][/"+tag+"]";
			pos = tag.length + 2;
		}
	}

	if (prompt_box == null) {
		prompt_box = '';
		pos = tag.length + 2;
	}
	bbcode_insert_into_textarea(prompt_box, pos);
}

// BBCode mit vielen Werten einfügen
/* options = {tag:[question, default],  <-- tag:['Pfad zu ...', '']
	option1:[question, default],    <-- Bsp width:['Geben sie die Höhe an', 300]
	option2:[question, default],
	}
*/
function bbcode_insert_with_multiple_values(tag, options){
	var text = '['+tag;
	var endtext = '';
	for (var i in options) {
		if (i == 'tag') {
			var endtext = prompt(options[i][0], options[i][1]) + '[/' + tag + ']';
		} else {
			var prompt_text = prompt(options[i][0], options[i][1]);
			if (prompt_text.length > 0) {
				text = text + ' ' + i + '=\'' + prompt_text + '\'';
			}
		}
	}
	bbcode_insert_into_textarea(text + ']' + endtext);
}

// BBCode mit Werte Einfügen (andere Art).
function bbcode_insert_with_value_2(tag, boxtext1, boxtext2) {

	var default_text;

	// Alternativen Text für die Box ausgeben.
	if(tag == 'video') {
		default_text = "YouTube";
	} else {
		default_text ="";
	}

	var prompt_text1;
	var prompt_text2;
	var prompt_box;

	// Box ausgeben mit Anforderung.
	prompt_text2 = prompt(boxtext2, default_text);

	var selection = bbcode_get_selection();

	// Ausgabe der 2ten Box.
	if(selection == null || selection == '') {
		prompt_text1 = prompt(boxtext1, "");
	} else {
		prompt_text1 = prompt(boxtext1, selection);
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
		bbcode_insert_into_textarea(prompt_box);
	}
}

// Simples einfügen der Tags :-)
function bbcode_code_insert(tag, color) {
    
    // Tags Definieren
	if(color == "0"){
		var begin_tag = "["+tag+"]";
		var end_tag = "[/"+tag+"]";
		if (document.form.code != undefined) {
			document.form.code.options['0'].selected = true; // selectiert immer <Code einfügen>
		}
	} else if (tag == "code" || tag == "php" || tag == "html" || tag == "css") {
		var prompt_text1 = prompt("Format: dateiname;5  (Im Beispiel ist die Startzeile 5)\nSie können hier nun einen Dateinamen und eine Startzeile mit angeben,\nwobei die Startzeile optional ist und auch das komplette Feld leer gelassen werden kann.)","");
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
	var selection = bbcode_get_selection();
	if (selection.length != undefined && selection.length != 0) {
		var pos = -1;
	} else {
		var pos = begin_tag.length;
	}
	bbcode_insert_into_textarea(begin_tag + selection + end_tag, pos);
}

function bbcode_code_insert_codes(tag) {
	if (tag != "0") {
		bbcode_code_insert(tag,'1');
	}
}