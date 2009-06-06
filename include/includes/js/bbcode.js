	var tagOpen = '[';
	var tagClos = ']';
	var tagEnde = '/';


  
function simple(name) {
  aTag = tagOpen + name + tagClos;
  eTag = tagOpen + tagEnde + name + tagClos;
  simple_insert ( aTag, eTag );
}

function simple_insert(aTag,eTag) {
  
  var input = document.forms['form'].elements['txt'];
  input.focus();
  /* für Internet Explorer */
  if(typeof document.selection != 'undefined') {
    /* Einfügen des Formatierungscodes */
    var range = document.selection.createRange();
    var insText = range.text;
    range.text = aTag + insText + eTag;
    /* Anpassen der Cursorposition */
    range = document.selection.createRange();
    if (insText.length == 0) {
      range.move('character', -eTag.length);
    } else {
      range.moveStart('character', aTag.length + insText.length + eTag.length);      
    }
    range.select();
  }
  /* für neuere auf Gecko basierende Browser */
  else if(typeof input.selectionStart != 'undefined')
  {
    /* Einfügen des Formatierungscodes */
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var insText = input.value.substring(start, end);
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
    /* Anpassen der Cursorposition */
    var pos;
    if (insText.length == 0) {
      pos = start + aTag.length;
    } else {
      pos = start + aTag.length + insText.length + eTag.length;
    }
    input.selectionStart = pos;
    input.selectionEnd = pos;
  }
  /* für die übrigen Browser */
  else
  {
    /* Abfrage der Einfügeposition */
    var pos = input.value.length;
    
    /* Einfügen des Formatierungscodes */
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
  }
}

function simple_liste () {
  var x = '';
  var l = '';
  while ( x != null ) {
    x = prompt ("Um die Liste zu beenden 'Abbrechen' eingeben");
    if ( x != null ) {
      l = l + "[*]" + x + "\n";
    }
  }
  if ( l != '' ) {
    l = "[list]\n" + l + "[/list]"; 
    simple_insert ( l, '' );
  }
}

function  put ( towrite ) {
  simple_insert ( towrite, '' );
}

function check() {
	if ( form.txt.value == '' ) {
	  alert ( 'Bis jetzt wurde wohl noch nichts eingegeben, also schnell nachholen!' );
	  return false;
	} else {
	  if ( form.pageName.value == '' ) {
	    alert ( 'Bitte gib noch schnell einen Namen ein!' );
	    return false;
	  } else {
	    return true;
	  }
	}
  
}