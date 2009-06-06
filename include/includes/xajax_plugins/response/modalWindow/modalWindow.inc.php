<?php
/*
+--------------------------------------------------------------------------
|   modalWindow Plugin
|   =============================================
|   by Oliver Trebes
|   (c) 2006 - 2007 amplified webservices
|   =============================================
|   http://www.angelmedia.de
+--------------------------------------------------------------------------
|   > $Date: 2008-08-27 06:25:56 +0200 (Mi, 27 Aug 2008) $
|   > $Revision: 56 $
|   > $Author: mentor $
+--------------------------------------------------------------------------
|   > Filename:     modalWindow.inc.php
|   > Date started: Tue Dec 18 10:53:23 CET 2007
+--------------------------------------------------------------------------
|   Desription:
|   Contains a class that creates and remove modal windows for hints,
|	actions and submissions.
|
|	Title: clsmodalWindow class
|
|	Please see <copyright.inc.php> for a detailed description, copyright
|	and license information.
|
+--------------------------------------------------------------------------
*/
class clsmodalWindow extends xajaxResponsePlugin
{
	/**
	 *  Used to store the base URI for where the javascript files are located.  This
	 *	enables the plugin to generate a script reference to it's javascript file
	 *	if the javascript code is NOT inlined.
	 *
	 * @var string
	 */
	private $sJavascriptURI	= null;
	
	/**
	 *  Used to store the value of the inlineScript configuration option.  When true,
	 *	the plugin will return it's javascript code as part of the javascript header
	 *	for the page, else, it will generate a script tag referencing the file by
	 *	using the <clsTableUpdater->sJavascriptURI>.
	 *
	 * @var boolean
	 */
	private $bInlineScript	= false;
	
	/**
	 * 
	 * String: sDefer
	 * 
	 * Configuration option that can be used to request that the
	 * javascript file is loaded after the page has been fully loaded.
	 * 
	 */
	private $sDefer			= null;
	
	/**
	 * clsmodalWindow constructor
	 *
	 */
	function __construct()
	{
	}

	/**
	 *	Function: configure
	 *	
	 *	Receives configuration settings set by <xajax> or user script calls to 
	 *	<xajax->configure>.
	 *	
	 *	@param string $sName :  The name of the configuration option being set.
	 *	@param mixed $mValue :  The value being associated with the configuration option.
	*/
	function configure($sName, $mValue)
	{
		if ( 'scriptDeferral' == $sName )
		{
			if ( true === $mValue || false === $mValue )
			{
				if ( $mValue )
				{
					$this->sDefer = 'defer="defer" ';
				}
			}
		}
		elseif ( 'javascript URI' == $sName )
		{
			$this->sJavascriptURI = $mValue;
		}
		elseif ( 'inlineScript' == $sName )
		{
			if ( true === $mValue || false === $mValue )
			{
				$this->bInlineScript = $mValue;
			}
		}
	}
	
	/**
	 * 	Function: generateClientScript
	 *  
	 *  Called by the <xajaxPluginManager> during the script generation phase.  This
	 *	will either inline the script or insert a script tag which references the
	 *	<tableUpdater.js> file based on the value of the <clsTableUpdater->bInlineScript>
	 *	configuration option.
	 */
	function generateClientScript()
	{
		if ($this->bInlineScript)
		{
			echo "\n<script type='text/javascript' " . $this->sDefer . "charset='UTF-8'>\n";
			echo "/* <![CDATA[ */\n";

			include(dirname(__FILE__) . '/modalWindow.js');

			echo "/* ]]> */\n";
			echo "</script>\n";
		}
		else
		{
			echo "\n<script type='text/javascript' src='" . $this->sJavascriptURI . "xajax_plugins/response/modalWindow/modalWindow.js' " . $this->sDefer . "charset='UTF-8'></script>\n";
		}
	}
	
	/**
	 * Function addWindow, adds a new modal Window Layer with the given content
	 *	
	 * @param string $content
	 * @param string $color
	 * @param string $opacity
	 * @param string $className
	 */
	function addWindow( $content, $color = null, $opacity = null, $className = null, $frame = null, $iPageOverFlow = 100 )
	{
		$command = array('n'=>'mw:aw');
		
		$content = ltrim( $content );
		
		$this->addCommand(
							array('cmd'=>'mw:aw'), 
							array( $content, $color, $opacity, $className, $frame, $iPageOverFlow )
		);
	}
	
	/**
	 * Function closeWindow, close the Window from the top layer
	 */
	function closeWindow( $frame = null )
	{
		$this->addCommand( 
							array('cmd'=>'mw:cw'), 
							array( $frame ) 
		);	
	}
	
	/**
	 * Function to load the javascript on demand
	 *
	 * @param string $uri
	 */
	function postLoadClientScript( $uri )
	{
		$this->objResponse->includeScriptOnce( $uri . 'xajax/xajax_plugins/response/modalWindow/modalWindow.js' );
		$this->objResponse->waitFor("'undefined' != typeof xjxmW", 10);
	}
	
	/**
	 * Returns the classname of this plugin class
	 *
	 * @return clsmodalWindow
	 */
	function getName()
	{
		return get_class($this);
	}
}

$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new clsmodalWindow());
