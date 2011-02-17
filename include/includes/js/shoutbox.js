function del() 
{
  if (anz = prompt(unescape("Wie viele Eintr%E4ge sollen erhalten bleiben%3F\n(Es werden die zuletzt geschriebenen erhalten)", "0")))
  {
    if (anz >= 0) 
	  { window.location.href = "index.php?shoutbox-delall-"+anz; } 
	  else alert(unescape("Du musst eine Zahl gr%F6%DFer gleich 0 eingeben"));
  }
}

$(document).ready(function() {
    $('#smilies').click(function() {
    	$dialog = $('#smiliesdiv')
    		.dialog({
    			autoOpen: true,
    			title: 'Smilies',
    			width: 200
    	    });
    });
});

function insert_sb(aTag,eTag)
{ 
  var input = document.forms['shoutboxform'].elements['shoutbox_textarea']; 
  input.focus(); 
  if(typeof document.selection != 'undefined') 
    { 
    var range = document.selection.createRange(); 
    var insText = range.text; range.text = aTag + insText + eTag;
    range = document.selection.createRange(); 
    if (insText.length == 0) 
      { range.move('character', -eTag.length); } 
      else 
      { range.moveStart('character', aTag.length + insText.length + eTag.length); } 
    range.select(); 
    } 
    else if(typeof input.selectionStart != 'undefined') 
    { 
    var start = input.selectionStart; 
    var end = input.selectionEnd; 
    var insText = input.value.substring(start, end); 
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end); 
    var pos; 
    if (insText.length == 0) 
      { pos = start + aTag.length; } 
      else 
      { pos = start + aTag.length + insText.length + eTag.length; } 
    input.selectionStart = pos; input.selectionEnd = pos; 
    }
    else
    { 
    var pos = input.value.length; 
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:"); 
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos); 
    }
}