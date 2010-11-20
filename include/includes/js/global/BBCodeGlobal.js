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

addEvent(window, "load", ResizeBBCodeImages);

//Funktion die alle Bilder des BBCodes der Funktion SetSize übergibt
function ResizeBBCodeImages() {
  imgs = document.getElementsByTagName("img");
  for (ti=0;ti<imgs.length;ti++) {
    if (imgs[ti].className.indexOf("bbcode_image") != -1) {
      SetSize(imgs[ti]);
    }
  }
}

//Funktion zum Ändern der Bildgröße für zu große Bilder
function SetSize(img){
  var w = img.width;
  var h = img.height;
  var toChange = false;
  if (w>bbcodemaximagewidth) {
    h = bbcodemaximagewidth * h / w;
    w = bbcodemaximagewidth;
    toChange = true;
    }
  if (h>bbcodemaximageheight) {
    w = bbcodemaximageheight * w / h;
    h = bbcodemaximageheight;
    toChange = true;
    }
  if (toChange) {
    var src = img.getAttribute('src');
    if ( img.parentNode.nodeName.toLowerCase() == 'a' ) {
      img.setAttribute('width',w);
      img.setAttribute('height',h);
    } else {
      var ersatz = document.createElement('a');
      ersatz.setAttribute('href',src);
      ersatz.setAttribute('target','_blank');
      var newImg = document.createElement('img');
      newImg.setAttribute('src',src);
      newImg.setAttribute('width',w);
      newImg.setAttribute('height',h);
      newImg.setAttribute('border','0');
      newImg.setAttribute('style',img.getAttribute('style',0));
      ersatz.appendChild(newImg);
      img.parentNode.replaceChild(ersatz,img);
    }
  }
}

//Funktion für BBCode Klapptext
function Klapptext(str) {
	var KlappText = document.getElementById('layer_'+str);
	var KlappBild = document.getElementById('image_'+str);
	var medientuner_minus= "include/images/icons/minus.gif", medientuner_plus="include/images/icons/plus.gif";
	
	if (KlappText.style.display == 'none') {
		KlappText.style.display = 'block';
		KlappBild.src = medientuner_minus;
	} else {
		KlappText.style.display = 'none';
		KlappBild.src = medientuner_plus;
	}
}
