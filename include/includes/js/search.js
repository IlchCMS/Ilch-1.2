
show_errmsg = 0;
searchInPage_nothingFound = "Der gesuchte Begriff konnte auf dieser Seite nicht gefunden werden.";

/*
Find In Page Script- 
By Mike Hall (MHall75819@aol.com)
*/

var NS4 = (document.layers);    // Which browser?
var IE4 = (document.all);

var win = window;    // window to search.
var n   = 0;

function findInPage(str) {

  var txt, i, found;

  if (str == "")
    return false;

  // Find next occurance of the given string on the page, wrap around to the
  // start of the page if necessary.

  if (NS4) {

    // Look for match starting at the current point. If not found, rewind
    // back to the first match.

    if (!win.find(str))
      while(win.find(str, false, true))
        n++;
    else
      n++;

    // If not found in either direction, give message.

    if (n == 0 && show_errmsg == 1)
      alert(searchInPage_nothingFound);
  }

  if (IE4) {
    txt = win.document.body.createTextRange();

    // Find the nth match from the top of the page.

    for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
      txt.moveStart("character", 1);
      txt.moveEnd("textedit");
    }

    // If found, mark it and scroll it into view.

    if (found) {
      txt.moveStart("character", -1);
      txt.findText(str);
      txt.select();
      txt.scrollIntoView(true);
      n++;
    }

    // Otherwise, start over at the top of the page and find first match.

    else {
      if (n > 0) {
        n = 0;
        findInPage(str);
      }

      // Not found anywhere, give message.

      else if (show_errmsg ==1) {
        alert(searchInPage_nothingFound);
      }
    }
  }

  return false;
}


function startSearchInPage()
{
if(document.searchinpage.query.value!='')
{
var pos=null;
if(window.pageYOffset)
{
pos = window.pageYOffset
}
else if(document.documentElement && document.documentElement.scrollTop)
{
pos = document.documentElement.scrollTop
}
else if(document.body)
{
pos = document.body.scrollTop
}

if(pos!=null && pos==0)
{
findInPage(document.searchinpage.query.value);
}
}
}