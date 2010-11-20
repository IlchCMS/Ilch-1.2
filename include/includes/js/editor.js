/*
bbwy Editor 0.1
wandelt eine textarea in einen wysiwyg editor um

Copyright  2006 Manuel

Lizenz:
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

addEvent(window, "load", bbwy_init);

function bbwy_init () {  
	texts = document.getElementsByTagName("textarea");
  for (ti=0;ti<texts.length;ti++) {
    thisTbl = texts[ti];
    if ((thisTbl.className.indexOf("bbwy") != -1) && (thisTbl.id)) {
		
		  var n      = thisTbl.id;
		  var height = thisTbl.clientHeight;
		  var width  = thisTbl.clientWidth;
		  
		  var iframe  = '<table cellpadding="0" cellspacing="0" border="0" style="width:' + width + 'px; height:' + (height-50) + 'px; border: 1px solid #000000; background-color: #FFFFFF;"><tr><td v>\n'
		      iframe += '<iframe frameborder="0" id="wysiwyg' + n + '"></iframe></td></tr></table>';
		  
			var toolba  = bbwy_toolbars (1, n);
			
  		thisTbl.style.display = 'none'; 
      thisTbl.insertAdjacentHTML("afterEnd", toolba + iframe);
			
      document.getElementById("wysiwyg" + n).style.height = height + "px";
      document.getElementById("wysiwyg" + n).style.width = width + "px";
			
      var content = thisTbl.value;
			
			var doc = document.getElementById("wysiwyg" + n).contentWindow.document;
			
	    doc.open();
      doc.write(content);
      doc.close();
	
	    // Make the iframe editable in both Mozilla and IE
      doc.body.contentEditable = true;
      doc.designMode = "on";
	
  	  // Update the textarea with content in WYSIWYG when user submits form
      var browserName = navigator.appName;
      if (browserName == "Microsoft Internet Explorer") {
        for (var idx=0; idx < document.forms.length; idx++) {
          document.forms[idx].attachEvent('onsubmit', function() { bbwy_uptext(n); });
        }
      } else {
      	for (var idx=0; idx < document.forms.length; idx++) {
        	document.forms[idx].addEventListener('submit',function OnSumbmit() { bbwy_uptext(n); }, true);
        }
      }
			
			document.getElementById("wysiwyg" + n).contentWindow.focus();
			// doc.body.style.fontSize = "";
			// doc.body.style.fontFamily = "";
    }
  }
}

// get toolbars
function bbwy_toolbars (was, n) {

  var buttons = new Array();
	buttons[0]  = 'size';
	buttons[1]  = 'bold';
	buttons[2]  = 'italic';
	buttons[3]  = 'underline';
	buttons[4]  = 'seperator';
	buttons[5]  = 'justifyleft';
	buttons[6]  = 'justifycenter';
	buttons[7]  = 'justifyright';
	buttons[8]  = 'seperator';
	buttons[9] =  'insertunorderedlist';
	buttons[10] = 'insertorderedlist';
  buttons[11] = 'seperator';
	buttons[12] = 'image';
	buttons[13] = 'link';
	buttons[14] = 'change';

	var toolbar = '<table><tr>';
	for (var i = 0; i < buttons.length; i++) {
	  if (buttons[i] == 'seperator') {
		  toolbar += '<td style="width: 10px;"></td>';
		} else if (buttons[i] == 'size') {
		  toolbar += '<td style="width: 22px"><select id="size" onChange="bbwy_edit(this.id, \'' + n + '\', this.value);" style="border: 0px; padding: 0px; margin: 0px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option></select></td>';
		} else if (buttons[i] == 'change') {
		  toolbar += '<td nowrap style="width: 100px;"><input style="border: 0px; padding: 0px; margin: 0px;" type="checkbox" id="txh" onClick="bbwy_edit(this.id , \'' + n + '\');" /><label for="txh" style="cursor: pointer"> view&nbsp;Source</label></td>';
		} else {
		  toolbar += '<td style="width: 22px;"><img src="include/images/icons/button.' +buttons[i]+ '.gif" border=0 unselectable="on" title="' +buttons[i]+ '" id="' +buttons[i]+ '" class="button" onClick="bbwy_edit(this.id,\'' + n + '\');"></td>';
		} 
	}
	toolbar += '</tr></table>';
	
	return (toolbar);
}

/* ---------------------------------------------------------------------- *\
  Function    : insertAdjacentHTML(), insertAdjacentText() and insertAdjacentElement()
  Description : Emulates insertAdjacentHTML(), insertAdjacentText() and 
	              insertAdjacentElement() three functions so they work with 
								Netscape 6/Mozilla
  Notes       : by Thor Larholm me@jscript.dk
\* ---------------------------------------------------------------------- */
if(typeof HTMLElement!="undefined" && !HTMLElement.prototype.insertAdjacentElement){
  HTMLElement.prototype.insertAdjacentElement = function
  (where,parsedNode)
	{
	  switch (where){
		case 'beforeBegin':
			this.parentNode.insertBefore(parsedNode,this)
			break;
		case 'afterBegin':
			this.insertBefore(parsedNode,this.firstChild);
			break;
		case 'beforeEnd':
			this.appendChild(parsedNode);
			break;
		case 'afterEnd':
			if (this.nextSibling) 
      this.parentNode.insertBefore(parsedNode,this.nextSibling);
			else this.parentNode.appendChild(parsedNode);
			break;
		}
	}

	HTMLElement.prototype.insertAdjacentHTML = function
  (where,htmlStr)
	{
		var r = this.ownerDocument.createRange();
		r.setStartBefore(this);
		var parsedHTML = r.createContextualFragment(htmlStr);
		this.insertAdjacentElement(where,parsedHTML)
	}


	HTMLElement.prototype.insertAdjacentText = function
  (where,txtStr)
	{
		var parsedText = document.createTextNode(txtStr)
		this.insertAdjacentElement(where,parsedText)
	}
};

function bbwy_edit (id, n, selected) {
  document.getElementById("wysiwyg" + n).contentWindow.focus();

	if (id != 'txh' && document.getElementById('txh').checked == true) {
	  return (false);
	}
	
	if (id == "image") {
    var imglink = prompt ("Bitte den Link eingeben:", "http://");
		if (imglink) {
		  document.getElementById("wysiwyg" + n).contentWindow.document.execCommand("insertimage", false, imglink);
		}
	} else if (id == "link") {
		  var hyperLink = prompt ("Bitte den Link eingeben:", "http://");
			if (hyperLink) {
        document.getElementById("wysiwyg" + n).contentWindow.document.execCommand("CreateLink", false, hyperLink);
			} else {
			  document.getElementById("wysiwyg" + n).contentWindow.document.execCommand("unlink", false, null);
			}
			
	 // change from text to html <->
	 } else if (id == "txh") {
		 if (document.getElementById('txh').checked == true) {
  	   viewSource (n);
  	 } else {
	     viewText (n);			 
	   }
	 } else if (id == "size") {
      document.getElementById("wysiwyg" + n).contentWindow.document.execCommand("fontsize", false, selected);
	 } else if (id == "iCode") {
			insertHTML ('[code][/code]', n);
	} else {
  	document.getElementById("wysiwyg" + n).contentWindow.focus();
	  document.getElementById("wysiwyg" + n).contentWindow.document.execCommand(id, false, null);
  }
}

/* ---------------------------------------------------------------------- *\
  Function    : viewSource()
  Description : Shows the HTML source code generated by the WYSIWYG editor
  Usage       : showFonts(n)
  Arguments   : n   - The editor identifier (the textarea's ID)
\* ---------------------------------------------------------------------- */
function viewSource(n) {
  var getDocument = document.getElementById("wysiwyg" + n).contentWindow.document;
  var browserName = navigator.appName;
	
	// View Source for IE 	 
  if (browserName == "Microsoft Internet Explorer") {
    var iHTML = getDocument.body.innerHTML;
    getDocument.body.innerText = iHTML;
	}
 
  // View Source for Mozilla/Netscape
  else {
    var html = document.createTextNode(getDocument.body.innerHTML);
    getDocument.body.innerHTML = "";
    getDocument.body.appendChild(html);
	}
	
	// set the font values for displaying HTML source
	getDocument.body.style.fontSize = "12px";
	getDocument.body.style.fontFamily = "Courier New"; 
};



/* ---------------------------------------------------------------------- *\
  Function    : viewSource()
  Description : Shows the HTML source code generated by the WYSIWYG editor
  Usage       : showFonts(n)
  Arguments   : n   - The editor identifier (the textarea's ID)
\* ---------------------------------------------------------------------- */
function viewText(n) { 
  var getDocument = document.getElementById("wysiwyg" + n).contentWindow.document;
  var browserName = navigator.appName;
	
	// View Text for IE 	  	 
  if (browserName == "Microsoft Internet Explorer") {
    var iText = getDocument.body.innerText;
    getDocument.body.innerHTML = iText;
	}
  
	// View Text for Mozilla/Netscape
  else {
    var html = getDocument.body.ownerDocument.createRange();
    html.selectNodeContents(getDocument.body);
    getDocument.body.innerHTML = html.toString();
	}
	
	// reset the font values
  getDocument.body.style.fontSize = "";
	getDocument.body.style.fontFamily = ""; 
};

function bbwy_uptext (n) {
	document.getElementById(n).value = document.getElementById("wysiwyg" + n).contentWindow.document.body.innerHTML;
};

function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}
