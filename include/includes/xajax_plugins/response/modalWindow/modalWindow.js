/*
+--------------------------------------------------------------------------
|   modalWindow Plugin
|   =============================================
|   by Oliver Trebes
|   (c) 2006 - 2007 amplified webservices
|   =============================================
|   http://www.angelmedia.de
+--------------------------------------------------------------------------
|   > $Date$
|   > $Revision$
|   > $Author$
+--------------------------------------------------------------------------
|   > Filename:     modalWindow.js
|   > Date started: Tue Dec 18 11:49:23 CET 2007
+--------------------------------------------------------------------------
|	Title: Modal Window Plugin
|
|	Please see <copyright.inc.php> for a detailed description, copyright
|	and license information.
+--------------------------------------------------------------------------
|	javascript based on lightbox 1.0:
+--------------------------------------------------------------------------
|	Lokesh Dhakar - http://www.huddletogether.com
|
|	For more information on this script, visit:
|	http://huddletogether.com/projects/lightbox/
|
|	Licensed under the Creative Commons Attribution 2.5 License - 
|	http://creativecommons.org/licenses/by/2.5/
+--------------------------------------------------------------------------
*/

	try {
		if (undefined == xajax.ext)
			xajax.ext = {};
	} catch (e) {
		alert("Could not create xajax.ext namespace");
	}

	try {
		if (undefined == xajax.ext.modalWindow)
			xajax.ext.modalWindow = {};
	} catch (e) {
		alert("Could not create xajax.ext.modalWindow namespace");
	}

	xjxmW = xajax.ext.modalWindow;
	
	var lb_widgets = 0;

	/**
	 * Function getHeight to recieve the height of the element
	 */
	xjxmW.getHeight = function( e )
	{
		if ( e.style.Height )
		{
			return e.style.Height;
		}
		else
		{
			return e.offsetHeight;
		}
	}
	
	/**
	 * Function getWidth to recieve the height of the element
	 */
	xjxmW.getWidth = function( e )
	{
		if ( e.style.Width )
		{
			return e.style.Width;
		}
		else
		{
			return e.offsetWidth;
		}
	}
	
	/**
	 * Function getPageSize to recieve the size of the current window
	 */
	xjxmW.getPageSize = function( objDoc )
	{
		var xScroll, yScroll;
		
		if (objDoc.innerHeight && objDoc.scrollMaxY) {	
			xScroll = objDoc.body.scrollWidth;
			yScroll = objDoc.innerHeight + objDoc.scrollMaxY;
		} else if (objDoc.body.scrollHeight > objDoc.body.offsetHeight){ // all but Explorer Mac
			xScroll = objDoc.body.scrollWidth;
			yScroll = objDoc.body.scrollHeight;
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = objDoc.body.offsetWidth;
			yScroll = objDoc.body.offsetHeight;
		}
		
		var windowWidth, windowHeight;
		
		
		
		if (objDoc.innerHeight) {	// all except Explorer
			windowWidth = objDoc.innerWidth;
			windowHeight = objDoc.innerHeight;
		}
		else if (objDoc.documentElement && objDoc.documentElement.clientHeight )
		{ // Explorer 6 Strict Mode
			
			if ( objDoc.body )
			{
				objBodyWidth = objDoc.body.clientWidth;
				objBodyHeight = objDoc.body.clientHeight;

				windowWidth = objDoc.documentElement.clientWidth;
				windowHeight = objDoc.documentElement.clientHeight;
				
				windowHeight = objBodyHeight < windowHeight ? objBodyHeight : windowHeight;
				windowWidth = objBodyWidth < windowWidth ? objBodyWidth : windowWidth;
			}
			else
			{
				windowWidth = objDoc.documentElement.clientWidth;
				windowHeight = objDoc.documentElement.clientHeight;
			}
		}
		else if (objDoc.body && objDoc.body.clientHeight)
		{ // other Explorers
			windowWidth = objDoc.body.clientWidth;
			windowHeight = objDoc.body.clientHeight;
		}
			
		
		// for small pages with total height less then height of the viewport
		if(yScroll < windowHeight){
			pageHeight = windowHeight;
		} else { 
			pageHeight = yScroll;
		}
	
		// for small pages with total width less then width of the viewport
		if(xScroll < windowWidth){	
			pageWidth = windowWidth;
		} else {
			pageWidth = xScroll;
		}
	
		arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
		return arrayPageSize;
	}
	
	/**
	 * Function getPageSize to recieve the size of the current pagescroll
	 */
	xjxmW.getPageScroll = function( objDoc )
	{
		var yScroll;
	
		if (objDoc.pageYOffset) {
			yScroll = objDoc.pageYOffset;
		} else if (objDoc.documentElement && objDoc.documentElement.scrollTop){	 // Explorer 6 Strict
			yScroll = objDoc.documentElement.scrollTop;
		} else if (objDoc.body) {// all other Explorers
			yScroll = objDoc.body.scrollTop;
		}
	
		arrayPageScroll = new Array('',yScroll) 
		return arrayPageScroll;
	}
	
	/**
	 * Function mw:aw add a new modal window
	 */
	xjxmW.addWindow = function( args )
	{
		xjxmW.hideSelects( 'hidden' );

		var objDoc			= document;
		var pageOverFlow	= 100; 
		
		if( parent.frames.length && args.data[4].length > 0 )
		{
			objDoc = args.data[4] == 'parent' ? parent.document : parent.frames[args.data[4]].document;	
		}
		if ( !objDoc.lb_widgets )
		{
			objDoc.lb_widgets = 0;
		}
		
		objDoc.lb_widgets++;

		var objBody			= objDoc.getElementsByTagName("body").item(0) ? objDoc.getElementsByTagName("body").item(0) : objDoc.getElementsByTagName("html").item(0);
		var zIndex			= objDoc.lb_widgets ? objDoc.lb_widgets * 1000 : 1000;
		var arrayPageSize	= xjxmW.getPageSize( objDoc );
		var arrayPageScroll = xjxmW.getPageScroll( objDoc );
		var objOverlay 		= objDoc.createElement("div");
		
		objOverlay.setAttribute('id','lb_layer' + objDoc.lb_widgets );
		objOverlay.style.display 	= 'none';
		objOverlay.style.position	= 'absolute';
		objOverlay.style.top		= '0';
		objOverlay.style.left		= '0';
		objOverlay.style.zIndex		= zIndex;
	 	objOverlay.style.width		= '100%';
	 	objOverlay.style.height		= (arrayPageSize[1] + 'px');
	 	objOverlay.style.minHeight	= '100%';

	 	if ( args.data[1] && args.data[1] != null )
	 	{
	 		objOverlay.style.backgroundColor = args.data[1];
	 	}
	 	
	 	if ( args.data[2] )
	 	{
			if (navigator.appVersion.indexOf("MSIE")!=-1)
			{
				objOverlay.style.filter = "alpha(opacity=" + args.data[2] + ")";
			}
			else
			{
	 			objOverlay.style.opacity = ( args.data[2] / 100 );
			}
	 	}	 	
	 	
	 	if ( args.data[3] )
	 	{
			objOverlay.className = args.data[3];
	 	}	 	
	 	
	 	if ( args.data[5] )
	 	{
			pageOverFlow = parseInt(args.data[5]);
	 	}	 	

	 	objBody.appendChild(objOverlay);
		
		var objLockbox = objDoc.createElement("div");
		objLockbox.setAttribute('id','lb_content' + objDoc.lb_widgets );
		objLockbox.style.visibility	= 'hidden';
		objLockbox.style.position	= 'absolute';
		objLockbox.style.top		= '0';
		objLockbox.style.left		= '0';
		objLockbox.style.zIndex		= zIndex + 1 ;	
		
		objBody.appendChild(objLockbox);
		
		objLockbox.innerHTML = args.data[0];
		
		var objContent = objLockbox.firstChild;
		height	= xjxmW.getHeight( objContent );
		width	= xjxmW.getWidth( objContent );
		
		if ( height > arrayPageSize[1] )
		{
			arrayPageSize[3] = height + pageOverFlow;
			arrayPageSize[1] = height + pageOverFlow;
			
		}
	
		objOverlay.style.height		= (arrayPageSize[1] + 'px');
	
		cltop   = (arrayPageScroll[1] + ( (arrayPageSize[3] -  height ) / 2 ) );
		clleft	= (                     ( (arrayPageSize[0] -  width ) / 2 ) );

		objLockbox.style.top  = cltop  < 0 ? '0px' : cltop  + 'px';
		objLockbox.style.left = clleft < 0 ? '0px' : clleft + 'px';
		
		objOverlay.style.display = '';
		objLockbox.style.visibility = '';
		
	}
	
	/**
	 * Function mw:cw remove the highest modal window
	 */
	xjxmW.closeWindow = function( args )
	{
		var objDoc			= document;
		
		if( parent.frames.length && args.length > 0 )
		{
			objDoc = args == 'parent' ? parent.document : parent.frames[args].document;	
		}
		
		var activewidget = objDoc.lb_widgets;
		
		xjxmW.hideSelects(''); 
		
		lId = 'lb_layer' + activewidget;
		cId = 'lb_content' + activewidget;
	
		objElement = objDoc.getElementById(cId);
		
		if (objElement && objElement.parentNode && objElement.parentNode.removeChild)
		{
			objElement.parentNode.removeChild(objElement);
		}	
		
		objElement = objDoc.getElementById(lId);
		
		if (objElement && objElement.parentNode && objElement.parentNode.removeChild)
		{
			objElement.parentNode.removeChild(objElement);
			objDoc.lb_widgets--;
		}
	}
	/**
	 * Function closeWindow is an alias for mw:cw
	 */
	
	xajax.command.handler.register('mw:aw', function(args) {
		xajax.ext.modalWindow.addWindow(args);
		return true;
	});	
	xajax.command.handler.register('mw:cw', function(args) {
		xajax.ext.modalWindow.closeWindow(args);
		return true;
	});	
	
	/**
	 * Function hideSelects hide or show select-boxes expect the top layer
	 * this is an ie6-fix function
	 */
	xjxmW.hideSelects = function( visibility )
	{
		var selects = document.getElementsByTagName('select');
		
		for(i = 0; i < selects.length; i++)
		{
			if ( !selects[i].rel )
			{
				selects[i].rel = 'ddl_' + lb_widgets;
			}
			
			if ( selects[i].rel == 'ddl_' + lb_widgets )
			{
				selects[i].style.visibility = visibility;
			}
			
			if ( visibility != 'hidden' )
			{
				selects[i].rel = null;
			}
		}
	}