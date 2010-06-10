<?php
$error_reporting = E_ALL > E_DEPRECATED ? E_ALL ^ E_DEPRECATED : E_ALL;
$error_reporting = @error_reporting($error_reporting);
class xajaxCall{var $sFunction;var $sReturnValue;var $aParameters;var $sMode;var $sRequestType;var $sResponseProcessor;var $sRequestURI;var $sContentType;function xajaxCall($sFunction=''){$this->sFunction=$sFunction;$this->aParameters=array();$this->sMode='';$this->sRequestType='';$this->sResponseProcessor='';$this->sRequestURI='';$this->sContentType='';}
function setFunction($sFunction){$this->sFunction=$sFunction;return $this;}
function clearParameters(){$this->aParameters=array();}
function addParameter($sParameter,$bUseQuotes=true){$this->aParameters[]=array($sParameter,$bUseQuotes);return $this;}
function addFormValuesParameter($sFormID){$this->aParameters[]=array('xajax.getFormValues("'.$sFormID.'")');return $this;}
function setMode($sMode){$this->sMode=$sMode;return $this;}
function setRequestType($sRequestType){$this->sRequestType=$sRequestType;return $this;}
function setResponseProcessor($sResponseProcessor){$this->sResponseProcessor=$sResponseProcessor;return $this;}
function setRequestURI($sRequestURI){$this->sRequestURI=$sRequestURI;return $this;}
function setContentType($sContentType){$this->sContentType=$sContentType;}
function setReturnValue($sReturnValue){$this->sReturnValue=$sReturnValue;}
function generate(){$output='xajax.call("';$output .=$this->sFunction;$output .='", {';$separator='';if(0 < count($this->aParameters)){$output .='parameters: [';foreach($this->aParameters as $aParameter){$output .=$separator;$bUseQuotes=$aParameter[1];if($bUseQuotes)
$output .='"';$output .=$aParameter[0];if($bUseQuotes)
$output .='"';$separator=',';}
$output .=']';}
if(0 < strlen($this->sMode)){$output .=$separator;$output .='mode:"';$output .=$this->sMode;$output .='"';$separator=',';}
if(0 < strlen($this->sRequestType)){$output .=$separator;$output .='requestType:"';$output .=$this->sRequestType;$output .='"';$separator=',';}
if(0 < strlen($this->sResponseProcessor)){$output .=$separator;$output .='responseProcessor:';$output .=$this->sResponseProcessor;$separator=',';}
if(0 < strlen($this->sRequestURI)){$output .=$separator;$output .='requestURI:"';$output .=$this->sRequestURI;$output .='"';$separator=',';}
if(0 < strlen($this->sContentType)){$output .=$separator;$output .='contentType:"';$output .=$this->sContentType;$output .='"';$separator=',';}
$output .='}); ';if(0 < strlen($this->sReturnValue)){$output .='return ';$output .=$this->sReturnValue;}else{$output .='return false;';}
return $output;}
}

class xajaxPluginManager{var $aRequestPlugins;var $aResponsePlugins;var $aConfigurable;var $aRegistrars;var $aProcessors;var $aClientScriptGenerators;function xajaxPluginManager(){$this->aRequestPlugins=array();$this->aResponsePlugins=array();$this->aConfigurable=array();$this->aRegistrars=array();$this->aProcessors=array();$this->aClientScriptGenerators=array();}
function&getInstance(){static $obj;if(!$obj){$obj=new xajaxPluginManager();}
return $obj;}
function loadPlugins($aFolders){foreach($aFolders as $sFolder){if(is_dir($sFolder))
if($handle=opendir($sFolder)){while(!(false===($sName=readdir($handle)))){$nLength=strlen($sName);if(8 < $nLength){$sFileName=substr($sName,0,$nLength-8);$sExtension=substr($sName,$nLength-8,8);if('.inc.php'==$sExtension){require $sFolder . '/' . $sFileName . $sExtension;}
}
}
closedir($handle);}
}
}
function _insertIntoArray(&$aPlugins,&$objPlugin,$nPriority){while(isset($aPlugins[$nPriority]))
$nPriority++;$aPlugins[$nPriority]=&$objPlugin;}
function registerPlugin(&$objPlugin,$nPriority=1000){if(is_a($objPlugin,'xajaxRequestPlugin')){$this->_insertIntoArray($this->aRequestPlugins,$objPlugin,$nPriority);if(method_exists($objPlugin,'register'))
$this->_insertIntoArray($this->aRegistrars,$objPlugin,$nPriority);if(method_exists($objPlugin,'canProcessRequest'))
if(method_exists($objPlugin,'processRequest'))
$this->_insertIntoArray($this->aProcessors,$objPlugin,$nPriority);}
else if(is_a($objPlugin,'xajaxResponsePlugin')){$this->aResponsePlugins[]=&$objPlugin;}
else{}
if(method_exists($objPlugin,'configure'))
$this->_insertIntoArray($this->aConfigurable,$objPlugin,$nPriority);if(method_exists($objPlugin,'generateClientScript'))
$this->_insertIntoArray($this->aClientScriptGenerators,$objPlugin,$nPriority);}
function canProcessRequest(){$bHandled=false;$aKeys=array_keys($this->aProcessors);sort($aKeys);foreach($aKeys as $sKey){$mResult=$this->aProcessors[$sKey]->canProcessRequest();if(true===$mResult)
$bHandled=true;else if(is_string($mResult))
return $mResult;}
return $bHandled;}
function processRequest(){$bHandled=false;$aKeys=array_keys($this->aProcessors);sort($aKeys);foreach($aKeys as $sKey){$mResult=$this->aProcessors[$sKey]->processRequest();if(true===$mResult)
$bHandled=true;else if(is_string($mResult))
return $mResult;}
return $bHandled;}
function configure($sName,$mValue){$aKeys=array_keys($this->aConfigurable);sort($aKeys);foreach($aKeys as $sKey)
$this->aConfigurable[$sKey]->configure($sName,$mValue);}
function register($aArgs){$aKeys=array_keys($this->aRegistrars);sort($aKeys);foreach($aKeys as $sKey){$objPlugin=&$this->aRegistrars[$sKey];$mResult=&$objPlugin->register($aArgs);if(is_a($mResult,'xajaxRequest'))
return $mResult;if(is_array($mResult))
return $mResult;if(is_bool($mResult))
if(true===$mResult)
return true;}
}
function generateClientScript(){$aKeys=array_keys($this->aClientScriptGenerators);sort($aKeys);foreach($aKeys as $sKey)
$this->aClientScriptGenerators[$sKey]->generateClientScript();}
function&getPlugin($sName){$aKeys=array_keys($this->aResponsePlugins);sort($aKeys);foreach($aKeys as $sKey)
if(is_a($this->aResponsePlugins[$sKey],$sName))
return $this->aResponsePlugins[$sKey];$bFailure=false;return $bFailure;}
}


class xajaxResponseManager{var $objResponse;var $sCharacterEncoding;var $bOutputEntities;var $aDebugMessages;function xajaxResponseManager(){$this->objResponse=NULL;$this->aDebugMessages=array();}
function&getInstance(){static $obj;if(!$obj){$obj=new xajaxResponseManager();}
return $obj;}
function configure($sName,$mValue){if('characterEncoding'==$sName){$this->sCharacterEncoding=$mValue;if(isset($this->objResponse))
$this->objResponse->setCharacterEncoding($this->sCharacterEncoding);}
else if('outputEntities'==$sName){if(true===$mValue||false===$mValue){$this->bOutputEntities=$mValue;if(isset($this->objResponse))
$this->objResponse->setOutputEntities($this->bOutputEntities);}
}
}
function clear(){$this->objResponse=NULL;}
function append($mResponse){if(is_a($mResponse,'xajaxResponse')){if(NULL==$this->objResponse){$this->objResponse=$mResponse;}else if(is_a($this->objResponse,'xajaxResponse')){if($this->objResponse!=$mResponse)
$this->objResponse->absorb($mResponse);}else{$objLanguageManager=&xajaxLanguageManager::getInstance();$this->debug(
$objLanguageManager->getText('XJXRM:MXRTERR')
. get_class($this->objResponse)
. ')'
);}
}else if(is_a($mResponse,'xajaxCustomResponse')){if(NULL==$this->objResponse){$this->objResponse=$mResponse;}else if(is_a($this->objResponse,'xajaxCustomResponse')){if($this->objResponse!=$mResponse)
$this->objResponse->absorb($mResponse);}else{$objLanguageManager=&xajaxLanguageManager::getInstance();$this->debug(
$objLanguageManager->getText('XJXRM:MXRTERR')
. get_class($this->objResponse)
. ')'
);}
}else{$objLanguageManager=&xajaxLanguageManager::getInstance();$this->debug($objLanguageManager->getText('XJXRM:IRERR'));}
}
function debug($sMessage){$this->aDebugMessages[]=$sMessage;}
function send(){if(NULL!=$this->objResponse){foreach($this->aDebugMessages as $sMessage)
$this->objResponse->debug($sMessage);$this->aDebugMessages=array();$this->objResponse->printOutput();}
}
function getCharacterEncoding(){return $this->sCharacterEncoding;}
function getOutputEntities(){return $this->bOutputEntities;}
}

class xajaxLanguageManager{var $aMessages;var $sLanguage;function xajaxLanguageManager(){$this->aMessages=array();$this->aMessages['en']=array(
'LOGHDR:01'=> '** xajax Error Log - ',
'LOGHDR:02'=> " **\n",
'LOGHDR:03'=> "\n\n\n",
'LOGERR:01'=> "** Logging Error **\n\nxajax was unable to write to the error log file:\n",
'LOGMSG:01'=> "** PHP Error Messages: **",
'CMPRSJS:RDERR:01'=> 'The xajax uncompressed Javascript file could not be found in the <b>',
'CMPRSJS:RDERR:02'=> '</b> folder.  Error ',
'CMPRSJS:WTERR:01'=> 'The xajax compressed javascript file could not be written in the <b>',
'CMPRSJS:WTERR:02'=> '</b> folder.  Error ',
'CMPRSPHP:WTERR:01'=> 'The xajax compressed file <b>',
'CMPRSPHP:WTERR:02'=> '</b> could not be written to.  Error ',
'CMPRSAIO:WTERR:01'=> 'The xajax compressed file <b>',
'CMPRSAIO:WTERR:02'=> '/xajaxAIO.inc.php</b> could not be written to.  Error ',
'DTCTURI:01'=> 'xajax Error: xajax failed to automatically identify your Request URI.',
'DTCTURI:02'=> 'Please set the Request URI explicitly when you instantiate the xajax object.',
'ARGMGR:ERR:01'=> 'Malformed object argument received: ',
'ARGMGR:ERR:02'=> ' <==> ',
'ARGMGR:ERR:03'=> 'The incoming xajax data could not be converted from UTF-8',
'XJXCTL:IAERR:01'=> 'Invalid attribute [',
'XJXCTL:IAERR:02'=> '] for element [',
'XJXCTL:IAERR:03'=> '].',
'XJXCTL:IRERR:01'=> 'Invalid request object passed to xajaxControl::setEvent',
'XJXCTL:IEERR:01'=> 'Invalid attribute (event name) [',
'XJXCTL:IEERR:02'=> '] for element [',
'XJXCTL:IEERR:03'=> '].',
'XJXCTL:MAERR:01'=> 'Missing required attribute [',
'XJXCTL:MAERR:02'=> '] for element [',
'XJXCTL:MAERR:03'=> '].',
'XJXCTL:IETERR:01'=> "Invalid end tag designation; should be forbidden or optional.\n",
'XJXCTL:ICERR:01'=> "Invalid class specified for html control; should be %inline, %block or %flow.\n",
'XJXCTL:ICLERR:01'=> 'Invalid control passed to addChild; should be derived from xajaxControl.',
'XJXCTL:ICLERR:02'=> 'Invalid control passed to addChild [',
'XJXCTL:ICLERR:03'=> '] for element [',
'XJXCTL:ICLERR:04'=> "].\n",
'XJXCTL:ICHERR:01'=> 'Invalid parameter passed to xajaxControl::addChildren; should be array of xajaxControl objects',
'XJXCTL:MRAERR:01'=> 'Missing required attribute [',
'XJXCTL:MRAERR:02'=> '] for element [',
'XJXCTL:MRAERR:03'=> '].',
'XJXPLG:GNERR:01'=> 'Response plugin should override the getName function.',
'XJXPLG:PERR:01'=> 'Response plugin should override the process function.',
'XJXPM:IPLGERR:01'=> 'Attempt to register invalid plugin: ',
'XJXPM:IPLGERR:02'=> ' should be derived from xajaxRequestPlugin or xajaxResponsePlugin.',
'XJXPM:MRMERR:01'=> 'Failed to locate registration method for the following: ',
'XJXRSP:EDERR:01'=> 'Passing character encoding to the xajaxResponse constructor is deprecated, instead use $xajax->configure("characterEncoding", ...);',
'XJXRSP:MPERR:01'=> 'Invalid or missing plugin name detected in call to xajaxResponse::plugin',
'XJXRSP:CPERR:01'=> "The \$sType parameter of addCreate has been deprecated.  Use the addCreateInput() method instead.",
'XJXRSP:LCERR:01'=> "The xajax response object could not load commands as the data provided was not a valid array.",
'XJXRSP:AKERR:01'=> 'Invalid tag name encoded in array.',
'XJXRSP:IEAERR:01'=> 'Improperly encoded array.',
'XJXRSP:NEAERR:01'=> 'Non-encoded array detected.',
'XJXRSP:MBEERR:01'=> 'The xajax response output could not be converted to HTML entities because the mb_convert_encoding function is not available',
'XJXRSP:MXRTERR'=> 'Error: Cannot mix types in a single response.',
'XJXRSP:MXCTERR'=> 'Error: Cannot mix content types in a single response.',
'XJXRSP:MXCEERR'=> 'Error: Cannot mix character encodings in a single response.',
'XJXRSP:MXOEERR'=> 'Error: Cannot mix output entities (true/false) in a single response.',
'XJXRM:IRERR'=> 'An invalid response was returned while processing this request.',
'XJXRM:MXRTERR'=> 'Error:  You cannot mix response types while processing a single request: '
);$this->sLanguage='en';}
function&getInstance(){static $obj;if(!$obj){$obj=new xajaxLanguageManager();}
return $obj;}
function configure($sName,$mValue){if('language'==$sName){if($mValue!==$this->sLanguage){$sFolder=dirname(__FILE__);@include $sFolder . '/xajax_lang_' . $mValue . '.inc.php';$this->sLanguage=$mValue;}
}
}
function register($sLanguage,$aMessages){$this->aMessages[$sLanguage]=$aMessages;}
function getText($sMessage){if(isset($this->aMessages[$this->sLanguage]))
if(isset($this->aMessages[$this->sLanguage][$sMessage]))
return $this->aMessages[$this->sLanguage][$sMessage];return '(Unknown language or message identifier)'
. $this->sLanguage
. '::'
. $sMessage;}
}

if(!defined('XAJAX_METHOD_UNKNOWN'))define('XAJAX_METHOD_UNKNOWN',0);if(!defined('XAJAX_METHOD_GET'))define('XAJAX_METHOD_GET',1);if(!defined('XAJAX_METHOD_POST'))define('XAJAX_METHOD_POST',2);class xajaxArgumentManager{var $aArgs;var $bDecodeUTF8Input;var $sCharacterEncoding;var $nMethod;var $aSequence;function convertStringToBool($sValue){if(0==strcasecmp($sValue,'true'))
return true;if(0==strcasecmp($sValue,'false'))
return false;if(is_numeric($sValue)){if(0==$sValue)
return false;return true;}
return false;}
function argumentStripSlashes(&$sArg){if(false==is_string($sArg))
return;$sArg=stripslashes($sArg);}
function argumentDecodeXML(&$sArg){if(false==is_string($sArg))
return;if(0==strlen($sArg))
return;$nStackDepth=0;$aStack=array();$aArg=array();$nCurrent=0;$nLast=0;$aExpecting=array();$nFound=0;list($aExpecting,$nFound)=$this->aSequence['start'];$nLength=strlen($sArg);$sKey='';$mValue='';while($nCurrent < $nLength){$bFound=false;foreach($aExpecting as $sExpecting=> $nExpectedLength){if($sArg[$nCurrent]==$sExpecting[0]){if($sExpecting==substr($sArg,$nCurrent,$nExpectedLength)){list($aExpecting,$nFound)=$this->aSequence[$sExpecting];switch($nFound){case 3:
$sKey='';break;case 4:
$sKey=str_replace(
array('<'.'![CDATA[',']]>'),
'',
substr($sArg,$nLast,$nCurrent-$nLast)
);break;case 5:
$mValue='';break;case 6:
if($nLast < $nCurrent){$mValue=str_replace(
array('<'.'![CDATA[',']]>'),
'',
substr($sArg,$nLast,$nCurrent-$nLast)
);$cType=substr($mValue,0,1);$sValue=substr($mValue,1);switch($cType){case 'S':$mValue=false===$sValue ? '':$sValue;break;case 'B':$mValue=$this->convertStringToBool($sValue);break;case 'N':$mValue=floatval($sValue);break;case '*':$mValue=null;break;}
}
break;case 7:
$aArg[$sKey]=$mValue;break;case 1:
++$nStackDepth;array_push($aStack,$aArg);$aArg=array();array_push($aStack,$sKey);$sKey='';break;case 8:
if(1 < $nStackDepth){$mValue=$aArg;$sKey=array_pop($aStack);$aArg=array_pop($aStack);--$nStackDepth;}else{$sArg=$aArg;return;}
break;}
$nCurrent+=$nExpectedLength;$nLast=$nCurrent;$bFound=true;break;}
}
}
if(false==$bFound){if(0==$nCurrent){$sArg=str_replace(
array('<'.'![CDATA[',']]>'),
'',
$sArg
);$cType=substr($sArg,0,1);$sValue=substr($sArg,1);switch($cType){case 'S':$sArg=false===$sValue ? '':$sValue;break;case 'B':$sArg=$this->convertStringToBool($sValue);break;case 'N':$sArg=floatval($sValue);break;case '*':$sArg=null;break;}
return;}
$nCurrent++;}
}
$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('ARGMGR:ERR:01')
. $sExpecting
. $objLanguageManager->getText('ARGMGR:ERR:02')
. $sArg
,E_USER_ERROR
);}
function argumentDecodeUTF8_iconv(&$mArg){if(is_array($mArg)){foreach(array_keys($mArg)as $sKey){$sNewKey=$sKey;$this->argumentDecodeUTF8_iconv($sNewKey);if($sNewKey!=$sKey){$mArg[$sNewKey]=$mArg[$sKey];unset($mArg[$sKey]);$sKey=$sNewKey;}
$this->argumentDecodeUTF8_iconv($mArg[$sKey]);}
}
else if(is_string($mArg))
$mArg=iconv("UTF-8",$this->sCharacterEncoding.'//TRANSLIT',$mArg);}
function argumentDecodeUTF8_mb_convert_encoding(&$mArg){if(is_array($mArg)){foreach(array_keys($mArg)as $sKey){$sNewKey=$sKey;$this->argumentDecodeUTF8_mb_convert_encoding($sNewKey);if($sNewKey!=$sKey){$mArg[$sNewKey]=$mArg[$sKey];unset($mArg[$sKey]);$sKey=$sNewKey;}
$this->argumentDecodeUTF8_mb_convert_encoding($mArg[$sKey]);}
}
else if(is_string($mArg))
$mArg=mb_convert_encoding($mArg,$this->sCharacterEncoding,"UTF-8");}
function argumentDecodeUTF8_utf8_decode(&$mArg){if(is_array($mArg)){foreach(array_keys($mArg)as $sKey){$sNewKey=$sKey;$this->argumentDecodeUTF8_utf8_decode($sNewKey);if($sNewKey!=$sKey){$mArg[$sNewKey]=$mArg[$sKey];unset($mArg[$sKey]);$sKey=$sNewKey;}
$this->argumentDecodeUTF8_utf8_decode($mArg[$sKey]);}
}
else if(is_string($mArg))
$mArg=utf8_decode($mArg);}
function xajaxArgumentManager(){$this->aArgs=array();$this->bDecodeUTF8Input=false;$this->sCharacterEncoding='UTF-8';$this->nMethod=XAJAX_METHOD_UNKNOWN;$this->aSequence=array(
'<'.'k'.'>'=> array(array(
'<'.'/k'.'>'=> 4
),3),
'<'.'/k'.'>'=> array(array(
'<'.'v'.'>'=> 3,
'<'.'/e'.'>'=> 4
),4),
'<'.'v'.'>'=> array(array(
'<'.'xjxobj'.'>'=> 8,
'<'.'/v'.'>'=> 4
),5),
'<'.'/v'.'>'=> array(array(
'<'.'/e'.'>'=> 4,
'<'.'k'.'>'=> 3
),6),
'<'.'e'.'>'=> array(array(
'<'.'k'.'>'=> 3,
'<'.'v'.'>'=> 3,
'<'.'/e'.'>'=> 4
),2),
'<'.'/e'.'>'=> array(array(
'<'.'e'.'>'=> 3,
'<'.'/xjxobj'.'>'=> 9
),7),
'<'.'xjxobj'.'>'=> array(array(
'<'.'e'.'>'=> 3,
'<'.'/xjxobj'.'>'=> 9
),1),
'<'.'/xjxobj'.'>'=> array(array(
'<'.'/v'.'>'=> 4
),8),
'start'=> array(array(
'<'.'xjxobj'.'>'=> 8
),9)
);if(isset($_POST['xjxargs'])){$this->nMethod=XAJAX_METHOD_POST;$this->aArgs=$_POST['xjxargs'];}else if(isset($_GET['xjxargs'])){$this->nMethod=XAJAX_METHOD_GET;$this->aArgs=$_GET['xjxargs'];}
if(1==get_magic_quotes_gpc())
array_walk($this->aArgs,array(&$this,'argumentStripSlashes'));array_walk($this->aArgs,array(&$this,'argumentDecodeXML'));}
function&getInstance(){static $obj;if(!$obj){$obj=new xajaxArgumentManager();}
return $obj;}
function configure($sName,$mValue){if('decodeUTF8Input'==$sName){if(true===$mValue||false===$mValue)
$this->bDecodeUTF8Input=$mValue;}else if('characterEncoding'==$sName){$this->sCharacterEncoding=$mValue;}
}
function getRequestMethod(){return $this->nMethod;}
function process(){if($this->bDecodeUTF8Input){$sFunction='';if(function_exists('iconv'))
$sFunction="iconv";else if(function_exists('mb_convert_encoding'))
$sFunction="mb_convert_encoding";else if($this->sCharacterEncoding=="ISO-8859-1")
$sFunction="utf8_decode";else{$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('ARGMGR:ERR:03')
,E_USER_NOTICE
);}
$mFunction=array(&$this,'argumentDecodeUTF8_' . $sFunction);array_walk($this->aArgs,$mFunction);$this->bDecodeUTF8Input=false;}
return $this->aArgs;}
}


if(false==defined('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT'))define('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT','XHTML');if(false==defined('XAJAX_HTML_CONTROL_DOCTYPE_VERSION'))define('XAJAX_HTML_CONTROL_DOCTYPE_VERSION','1.0');if(false==defined('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION'))define('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION','TRANSITIONAL');class xajaxControl{var $sTag;var $sEndTag;var $aAttributes;var $aEvents;var $sClass;function xajaxControl($sTag,$aConfiguration=array()){$this->sTag=$sTag;$this->clearAttributes();if(isset($aConfiguration['attributes']))
if(is_array($aConfiguration['attributes']))
foreach($aConfiguration['attributes'] as $sKey=> $sValue)
$this->setAttribute($sKey,$sValue);$this->clearEvents();if(isset($aConfiguration['event']))
call_user_func_array(array(&$this,'setEvent'),$aConfiguration['event']);else if(isset($aConfiguration['events']))
if(is_array($aConfiguration['events']))
foreach($aConfiguration['events'] as $aEvent)
call_user_func_array(array(&$this,'setEvent'),$aEvent);$this->sClass='%block';$this->sEndTag='forbidden';}
function getClass(){return $this->sClass;}
function clearAttributes(){$this->aAttributes=array();}
function setAttribute($sName,$sValue){$this->aAttributes[$sName]=$sValue;}
function getAttribute($sName){if(false==isset($this->aAttributes[$sName]))
return null;return $this->aAttributes[$sName];}
function clearEvents(){$this->aEvents=array();}
function setEvent($sEvent,&$objRequest,$aParameters=array(),$sBeforeRequest='',$sAfterRequest='; return false;'){$this->aEvents[$sEvent]=array(
&$objRequest,
$aParameters,
$sBeforeRequest,
$sAfterRequest
);}
function getHTML($bFormat=false){ob_start();if($bFormat)
$this->printHTML();else
$this->printHTML(false);return ob_get_clean();}
function printHTML($sIndent=''){$sClass=$this->getClass();if('%inline'!=$sClass)
if(false===(false===$sIndent))
echo $sIndent;echo '<';echo $this->sTag;echo ' ';$this->_printAttributes();$this->_printEvents();if('forbidden'==$this->sEndTag){if('HTML'==XAJAX_HTML_CONTROL_DOCTYPE_FORMAT)
echo '>';else if('XHTML'==XAJAX_HTML_CONTROL_DOCTYPE_FORMAT)
echo '/>';if('%inline'!=$sClass)
if(false===(false===$sIndent))
echo "\n";return;}
else if('optional'==$this->sEndTag){echo '/>';if('%inline'==$sClass)
if(false===(false===$sIndent))
echo "\n";return;}
}
function _printAttributes(){foreach($this->aAttributes as $sKey=> $sValue)
if('disabled'!=$sKey||'false'!=$sValue)
echo "{$sKey}='{$sValue}' ";}
function _printEvents(){foreach(array_keys($this->aEvents)as $sKey){$aEvent=&$this->aEvents[$sKey];$objRequest=&$aEvent[0];$aParameters=$aEvent[1];$sBeforeRequest=$aEvent[2];$sAfterRequest=$aEvent[3];foreach($aParameters as $aParameter){$nParameter=$aParameter[0];$sType=$aParameter[1];$sValue=$aParameter[2];$objRequest->setParameter($nParameter,$sType,$sValue);}
$objRequest->useDoubleQuote();echo "{$sKey}='{$sBeforeRequest}";$objRequest->printScript();echo "{$sAfterRequest}' ";}
}
function backtrace(){if(0 <=version_compare(PHP_VERSION,'4.3.0'))
return '<div><div>Backtrace:</div><pre>'
. print_r(debug_backtrace(),true)
. '</pre></div>';return '';}
}
class xajaxControlContainer extends xajaxControl{var $aChildren;var $sChildClass;function xajaxControlContainer($sTag,$aConfiguration=array()){xajaxControl::xajaxControl($sTag,$aConfiguration);$this->clearChildren();if(isset($aConfiguration['child']))
$this->addChild($aConfiguration['child']);else if(isset($aConfiguration['children']))
$this->addChildren($aConfiguration['children']);$this->sEndTag='required';}
function getClass(){$sClass=xajaxControl::getClass();if(0 < count($this->aChildren)&&'%flow'==$sClass)
return $this->getContentClass();else if(0==count($this->aChildren)||'%inline'==$sClass||'%block'==$sClass)
return $sClass;$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXCTL:ICERR:01')
. $this->backtrace()
,E_USER_ERROR
);}
function getContentClass(){$sClass='';foreach(array_keys($this->aChildren)as $sKey){if(''==$sClass)
$sClass=$this->aChildren[$sKey]->getClass();else if($sClass!=$this->aChildren[$sKey]->getClass())
return '%flow';}
if(''==$sClass)
return '%inline';return $sClass;}
function clearChildren(){$this->sChildClass='%inline';$this->aChildren=array();}
function addChild(&$objControl){$this->aChildren[]=&$objControl;}
function addChildren(&$aChildren){foreach(array_keys($aChildren)as $sKey)
$this->addChild($aChildren[$sKey]);}
function printHTML($sIndent=''){$sClass=$this->getClass();if('%inline'!=$sClass)
if(false===(false===$sIndent))
echo $sIndent;echo '<';echo $this->sTag;echo ' ';$this->_printAttributes();$this->_printEvents();if(0==count($this->aChildren)){if('optional'==$this->sEndTag){echo '/>';if('%inline'!=$sClass)
if(false===(false===$sIndent))
echo "\n";return;}
}
echo '>';$sContentClass=$this->getContentClass();if('%inline'!=$sContentClass)
if(false===(false===$sIndent))
echo "\n";$this->_printChildren($sIndent);if('%inline'!=$sContentClass)
if(false===(false===$sIndent))
echo $sIndent;echo '<' . '/';echo $this->sTag;echo '>';if('%inline'!=$sClass)
if(false===(false===$sIndent))
echo "\n";}
function _printChildren($sIndent=''){if(false==is_a($this,'clsDocument'))
if(false===(false===$sIndent))
$sIndent .="\t";foreach(array_keys($this->aChildren)as $sKey){$objChild=&$this->aChildren[$sKey];$objChild->printHTML($sIndent);}
}
}

class xajaxPlugin{}
class xajaxRequestPlugin extends xajaxPlugin{function configure($sName,$mValue){}
function register($aArgs){return false;}
function generateClientScript(){}
function canProcessRequest(){return false;}
function processRequest(){return false;}
}
class xajaxResponsePlugin extends xajaxPlugin{var $objResponse;function setResponse(&$objResponse){$this->objResponse=&$objResponse;}
function addCommand($aAttributes,$sData){$this->objResponse->addPluginCommand($this,$aAttributes,$sData);}
function getName(){}
function process(){}
}

if(!defined('XAJAX_DEFAULT_CHAR_ENCODING'))define('XAJAX_DEFAULT_CHAR_ENCODING','utf-8');if(!defined('XAJAX_PROCESSING_EVENT'))define('XAJAX_PROCESSING_EVENT','xajax processing event');if(!defined('XAJAX_PROCESSING_EVENT_BEFORE'))define('XAJAX_PROCESSING_EVENT_BEFORE','beforeProcessing');if(!defined('XAJAX_PROCESSING_EVENT_AFTER'))define('XAJAX_PROCESSING_EVENT_AFTER','afterProcessing');if(!defined('XAJAX_PROCESSING_EVENT_INVALID'))define('XAJAX_PROCESSING_EVENT_INVALID','invalidRequest');class xajax{var $aSettings;var $bErrorHandler;var $aProcessingEvents;var $bExitAllowed;var $bCleanBuffer;var $sLogFile;var $sCoreIncludeOutput;var $objPluginManager;var $objArgumentManager;var $objResponseManager;var $objLanguageManager;function xajax($sRequestURI=null,$sLanguage=null){$this->bErrorHandler=false;$this->aProcessingEvents=array();$this->bExitAllowed=true;$this->bCleanBuffer=true;$this->sLogFile='';$this->__wakeup();$this->configureMany(
array(
'characterEncoding'=> XAJAX_DEFAULT_CHAR_ENCODING,
'decodeUTF8Input'=> false,
'outputEntities'=> false,
'defaultMode'=> 'asynchronous',
'defaultMethod'=> 'POST',
'wrapperPrefix'=> 'xajax_',
'debug'=> false,
'verbose'=> false,
'useUncompressedScripts'=> false,
'statusMessages'=> false,
'waitCursor'=> true,
'scriptDeferral'=> false,
'exitAllowed'=> true,
'errorHandler'=> false,
'cleanBuffer'=> false,
'allowBlankResponse'=> false,
'allowAllResponseTypes'=> false,
'generateStubs'=> true,
'logFile'=> '',
'timeout'=> 6000,
'version'=> $this->getVersion(),
'javascript URI' => 'include/includes/libs/xajax/'
)
);if(null!==$sRequestURI)
$this->configure('requestURI',$sRequestURI);else
$this->configure('requestURI',$this->_detectURI());if(null!==$sLanguage)
$this->configure('language',$sLanguage);if('utf-8'!=XAJAX_DEFAULT_CHAR_ENCODING)$this->configure("decodeUTF8Input",true);}
function __sleep(){$aMembers=get_class_vars(get_class($this));if(isset($aMembers['objLanguageManager']))
unset($aMembers['objLanguageManager']);if(isset($aMembers['objPluginManager']))
unset($aMembers['objPluginManager']);if(isset($aMembers['objArgumentManager']))
unset($aMembers['objArgumentManager']);if(isset($aMembers['objResponseManager']))
unset($aMembers['objResponseManager']);if(isset($aMembers['sCoreIncludeOutput']))
unset($aMembers['sCoreIncludeOutput']);return array_keys($aMembers);}
function __wakeup(){ob_start();$sLocalFolder=dirname(__FILE__);$aPluginFolders=array();$aPluginFolders[]=dirname($sLocalFolder). '/xajax_plugins';$this->objPluginManager=&xajaxPluginManager::getInstance();$this->objPluginManager->loadPlugins($aPluginFolders);$this->objLanguageManager=&xajaxLanguageManager::getInstance();$this->objArgumentManager=&xajaxArgumentManager::getInstance();$this->objResponseManager=&xajaxResponseManager::getInstance();$this->sCoreIncludeOutput=ob_get_clean();}
function&getGlobalResponse(){static $obj;if(!$obj){$obj=new xajaxResponse();}
return $obj;}
function getVersion(){return 'xajax 0.5';}
function register($sType,$mArg){$aArgs=func_get_args();$nArgs=func_num_args();if(2 < $nArgs){if(XAJAX_PROCESSING_EVENT==$aArgs[0]){$sEvent=$aArgs[1];$xuf=&$aArgs[2];if(false==is_a($xuf,'xajaxUserFunction'))
$xuf=&new xajaxUserFunction($xuf);$this->aProcessingEvents[$sEvent]=&$xuf;return true;}
}
if(1 < $nArgs){$aArgs[1]=&$mArg;}
return $this->objPluginManager->register($aArgs);}
function configure($sName,$mValue){if('errorHandler'==$sName){if(true===$mValue||false===$mValue)
$this->bErrorHandler=$mValue;}else if('exitAllowed'==$sName){if(true===$mValue||false===$mValue)
$this->bExitAllowed=$mValue;}else if('cleanBuffer'==$sName){if(true===$mValue||false===$mValue)
$this->bCleanBuffer=$mValue;}else if('logFile'==$sName){$this->sLogFile=$mValue;}
$this->objLanguageManager->configure($sName,$mValue);$this->objArgumentManager->configure($sName,$mValue);$this->objPluginManager->configure($sName,$mValue);$this->objResponseManager->configure($sName,$mValue);$this->aSettings[$sName]=$mValue;}
function configureMany($aOptions){foreach($aOptions as $sName=> $mValue)
$this->configure($sName,$mValue);}
function getConfiguration($sName){if(isset($this->aSettings[$sName]))
return $this->aSettings[$sName];return NULL;}
function canProcessRequest(){return $this->objPluginManager->canProcessRequest();}
function processRequest(){if($this->canProcessRequest()){if($this->bErrorHandler){$GLOBALS['xajaxErrorHandlerText']="";set_error_handler("xajaxErrorHandler");}
$mResult=true;if(isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE])){$bEndRequest=false;$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]->call(array(&$bEndRequest));$mResult=(false===$bEndRequest);}
if(true===$mResult)
$mResult=$this->objPluginManager->processRequest();if(true===$mResult){if($this->bCleanBuffer){$er=error_reporting(0);while(ob_get_level()> 0)ob_end_clean();error_reporting($er);}
if(isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER])){$bEndRequest=false;$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]->call(array(&$bEndRequest));if(true===$bEndRequest){$this->objResponseManager->clear();$this->objResponseManager->append($aResult[1]);}
}
}
else if(is_string($mResult)){if($this->bCleanBuffer){$er=error_reporting(0);while(ob_get_level()> 0)ob_end_clean();error_reporting($er);}
$this->objResponseManager->clear();$this->objResponseManager->append(new xajaxResponse());if(isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]))
$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]->call();else
$this->objResponseManager->debug($mResult);}
if($this->bErrorHandler){$sErrorMessage=$GLOBALS['xajaxErrorHandlerText'];if(!empty($sErrorMessage)){if(0 < strlen($this->sLogFile)){$fH=@fopen($this->sLogFile,"a");if(NULL!=$fH){fwrite(
$fH,
$this->objLanguageManager->getText('LOGHDR:01')
. strftime("%b %e %Y %I:%M:%S %p")
. $this->objLanguageManager->getText('LOGHDR:02')
. $sErrorMessage
. $this->objLanguageManager->getText('LOGHDR:03')
);fclose($fH);}else{$this->objResponseManager->debug(
$this->objLanguageManager->getText('LOGERR:01')
. $this->sLogFile
);}
}
$this->objResponseManager->debug(
$this->objLanguageManager->getText('LOGMSG:01')
. $sErrorMessage
);}
}
$this->objResponseManager->send();if($this->bErrorHandler)restore_error_handler();if($this->bExitAllowed)exit();}
}
function printJavascript($sJsURI="",$aJsFiles=array()){if(0 < strlen($sJsURI))
$this->configure("javascript URI",$sJsURI);if(0 < count($aJsFiles))
$this->configure("javascript files",$aJsFiles);$this->objPluginManager->generateClientScript();}
function getJavascript($sJsURI='',$aJsFiles=array()){ob_start();$this->printJavascript($sJsURI,$aJsFiles);return ob_get_clean();}
function autoCompressJavascript($sJsFullFilename=NULL,$bAlways=false){$sJsFile='xajax_js/xajax_core.js';if($sJsFullFilename){$realJsFile=$sJsFullFilename;}
else{$realPath=realpath(dirname(dirname(__FILE__)));$realJsFile=$realPath . '/'. $sJsFile;}
if(!file_exists($realJsFile)||true==$bAlways){$srcFile=str_replace('.js','_uncompressed.js',$realJsFile);if(!file_exists($srcFile)){trigger_error(
$this->objLanguageManager->getText('CMPRSJS:RDERR:01')
. dirname($realJsFile)
. $this->objLanguageManager->getText('CMPRSJS:RDERR:02')
,E_USER_ERROR
);}
require_once(dirname(__FILE__). '/xajaxCompress.inc.php');$javaScript=implode('',file($srcFile));$compressedScript=xajaxCompressFile($javaScript);$fH=@fopen($realJsFile,'w');if(!$fH){trigger_error(
$this->objLanguageManager->getText('CMPRSJS:WTERR:01')
. dirname($realJsFile)
. $this->objLanguageManager->getText('CMPRSJS:WTERR:02')
,E_USER_ERROR
);}
else{fwrite($fH,$compressedScript);fclose($fH);}
}
}
function _compressSelf($sFolder=null){if(null==$sFolder)
$sFolder=dirname(dirname(__FILE__));require_once(dirname(__FILE__). '/xajaxCompress.inc.php');if($handle=opendir($sFolder)){while(!(false===($sName=readdir($handle)))){if('.'!=$sName&&'..'!=$sName&&is_dir($sFolder . '/' . $sName)){$this->_compressSelf($sFolder . '/' . $sName);}else if(8 < strlen($sName)&&0==strpos($sName,'.compressed')){if('.inc.php'==substr($sName,strlen($sName)-8,8)){$sName=substr($sName,0,strlen($sName)-8);$sPath=$sFolder . '/' . $sName . '.inc.php';if(file_exists($sPath)){$aParsed=array();$aFile=file($sPath);$nSkip=0;foreach(array_keys($aFile)as $sKey)
if('//SkipDebug'==$aFile[$sKey])
++$nSkip;else if('//EndSkipDebug'==$aFile[$sKey])
--$nSkip;else if(0==$nSkip)
$aParsed[]=$aFile[$sKey];unset($aFile);$compressedScript=xajaxCompressFile(implode('',$aParsed));$sNewPath=$sPath;$fH=@fopen($sNewPath,'w');if(!$fH){trigger_error(
$this->objLanguageManager->getText('CMPRSPHP:WTERR:01')
. $sNewPath
. $this->objLanguageManager->getText('CMPRSPHP:WTERR:02')
,E_USER_ERROR
);}
else{fwrite($fH,$compressedScript);fclose($fH);}
}
}
}
}
closedir($handle);}
}
function _compile($sFolder=null,$bWriteFile=true){if(null==$sFolder)
$sFolder=dirname(__FILE__);require_once(dirname(__FILE__). '/xajaxCompress.inc.php');$aOutput=array();if($handle=opendir($sFolder)){while(!(false===($sName=readdir($handle)))){if('.'!=$sName&&'..'!=$sName&&is_dir($sFolder . '/' . $sName)){$aOutput[]=$this->_compile($sFolder . '/' . $sName,false);}else if(8 < strlen($sName)){if('.inc.php'==substr($sName,strlen($sName)-8,8)){$sName=substr($sName,0,strlen($sName)-8);$sPath=$sFolder . '/' . $sName . '.inc.php';if(
'xajaxAIO'!=$sName&&
'legacy'!=$sName&&
'xajaxCompress'!=$sName
){if(file_exists($sPath)){$aParsed=array();$aFile=file($sPath);$nSkip=0;foreach(array_keys($aFile)as $sKey)
if('//SkipDebug'==substr($aFile[$sKey],0,11))
++$nSkip;else if('//EndSkipDebug'==substr($aFile[$sKey],0,14))
--$nSkip;else if('//SkipAIO'==substr($aFile[$sKey],0,9))
++$nSkip;else if('//EndSkipAIO'==substr($aFile[$sKey],0,12))
--$nSkip;else if('<'.'?php'==substr($aFile[$sKey],0,5)){}
else if('?'.'>'==substr($aFile[$sKey],0,2)){}
else if(0==$nSkip)
$aParsed[]=$aFile[$sKey];unset($aFile);$aOutput[]=xajaxCompressFile(implode('',$aParsed));}
}
}
}
}
closedir($handle);}
if($bWriteFile){$fH=@fopen($sFolder . '/xajaxAIO.inc.php','w');if(!$fH){trigger_error(
$this->objLanguageManager->getText('CMPRSAIO:WTERR:01')
. $sFolder
. $this->objLanguageManager->getText('CMPRSAIO:WTERR:02')
,E_USER_ERROR
);}
else{fwrite($fH,'<'.'?php ');fwrite($fH,implode('',$aOutput));fclose($fH);}
}
return implode('',$aOutput);}
function _detectURI(){$aURL=array();if(!empty($_SERVER['REQUEST_URI'])){$_SERVER['REQUEST_URI']=str_replace(
array('"',"'",'<','>'),
array('%22','%27','%3C','%3E'),
$_SERVER['REQUEST_URI']
);$aURL=parse_url($_SERVER['REQUEST_URI']);}
if(empty($aURL['scheme'])){if(!empty($_SERVER['HTTP_SCHEME'])){$aURL['scheme']=$_SERVER['HTTP_SCHEME'];}else{$aURL['scheme']=
(!empty($_SERVER['HTTPS'])&&strtolower($_SERVER['HTTPS'])!='off')
? 'https'
:'http';}
}
if(empty($aURL['host'])){if(!empty($_SERVER['HTTP_X_FORWARDED_HOST'])){if(strpos($_SERVER['HTTP_X_FORWARDED_HOST'],':')> 0){list($aURL['host'],$aURL['port'])=explode(':',$_SERVER['HTTP_X_FORWARDED_HOST']);}else{$aURL['host']=$_SERVER['HTTP_X_FORWARDED_HOST'];}
}else if(!empty($_SERVER['HTTP_HOST'])){if(strpos($_SERVER['HTTP_HOST'],':')> 0){list($aURL['host'],$aURL['port'])=explode(':',$_SERVER['HTTP_HOST']);}else{$aURL['host']=$_SERVER['HTTP_HOST'];}
}else if(!empty($_SERVER['SERVER_NAME'])){$aURL['host']=$_SERVER['SERVER_NAME'];}else{echo $this->objLanguageManager->getText('DTCTURI:01');echo $this->objLanguageManager->getText('DTCTURI:02');exit();}
}
if(empty($aURL['port'])&&!empty($_SERVER['SERVER_PORT'])){$aURL['port']=$_SERVER['SERVER_PORT'];}
if(!empty($aURL['path']))
if(0==strlen(basename($aURL['path'])))
unset($aURL['path']);if(empty($aURL['path'])){$sPath=array();if(!empty($_SERVER['PATH_INFO'])){$sPath=parse_url($_SERVER['PATH_INFO']);}else{$sPath=parse_url($_SERVER['PHP_SELF']);}
if(isset($sPath['path']))
$aURL['path']=str_replace(array('"',"'",'<','>'),array('%22','%27','%3C','%3E'),$sPath['path']);unset($sPath);}
if(empty($aURL['query'])&&!empty($_SERVER['QUERY_STRING'])){$aURL['query']=$_SERVER['QUERY_STRING'];}
if(!empty($aURL['query'])){$aURL['query']='?'.$aURL['query'];}
$sURL=$aURL['scheme'].'://';if(!empty($aURL['user'])){$sURL.=$aURL['user'];if(!empty($aURL['pass'])){$sURL.=':'.$aURL['pass'];}
$sURL.='@';}
$sURL.=$aURL['host'];if(!empty($aURL['port'])
&&(($aURL['scheme']=='http'&&$aURL['port']!=80)
||($aURL['scheme']=='https'&&$aURL['port']!=443))){$sURL.=':'.$aURL['port'];}
$sURL.=$aURL['path'].@$aURL['query'];unset($aURL);$aURL=explode("?",$sURL);if(1 < count($aURL)){$aQueries=explode("&",$aURL[1]);foreach($aQueries as $sKey=> $sQuery){if("xjxGenerate"==substr($sQuery,0,11))
unset($aQueries[$sKey]);}
$sQueries=implode("&",$aQueries);$aURL[1]=$sQueries;$sURL=implode("?",$aURL);}
return $sURL;}
function setCharEncoding($sEncoding){$this->configure('characterEncoding',$sEncoding);}
function getCharEncoding(){return $this->getConfiguration('characterEncoding');}
function setFlags($flags){foreach($flags as $name=> $value){$this->configure($name,$value);}
}
function setFlag($name,$value){$this->configure($name,$value);}
function getFlag($name){return $this->getConfiguration($name);}
function setRequestURI($sRequestURI){$this->configure('requestURI',$sRequestURI);}
function getRequestURI(){return $this->getConfiguration('requestURI');}
function setDefaultMode($sDefaultMode){$this->configure('defaultMode',$sDefaultMode);}
function getDefaultMode(){return $this->getConfiguration('defaultMode');}
function setDefaultMethod($sMethod){$this->configure('defaultMethod',$sMethod);}
function getDefaultMethod(){return $this->getConfiguration('defaultMethod');}
function setWrapperPrefix($sPrefix){$this->configure('wrapperPrefix',$sPrefix);}
function getWrapperPrefix(){return $this->getConfiguration('wrapperPrefix');}
function setLogFile($sFilename){$this->configure('logFile',$sFilename);}
function getLogFile(){return $this->getConfiguration('logFile');}
function registerFunction($mFunction,$sIncludeFile=null){$xuf=&new xajaxUserFunction($mFunction,$sIncludeFile);return $this->register(XAJAX_FUNCTION,$xuf);}
function registerCallableObject(&$oObject){$mResult=false;if(0 > version_compare(PHP_VERSION,'5.0'))
eval('$mResult = $this->register(XAJAX_CALLABLE_OBJECT, &$oObject);');else
$mResult=$this->register(XAJAX_CALLABLE_OBJECT,$oObject);return $mResult;}
function registerEvent($sEventName,$mCallback){$this->register(XAJAX_PROCESSING_EVENT,$sEventName,$mCallback);}
}
function xajaxErrorHandler($errno,$errstr,$errfile,$errline){$errorReporting=error_reporting();if(($errno&$errorReporting)==0)return;if($errno==E_NOTICE){$errTypeStr='NOTICE';}
else if($errno==E_WARNING){$errTypeStr='WARNING';}
else if($errno==E_USER_NOTICE){$errTypeStr='USER NOTICE';}
else if($errno==E_USER_WARNING){$errTypeStr='USER WARNING';}
else if($errno==E_USER_ERROR){$errTypeStr='USER FATAL ERROR';}
else if(defined('E_STRICT')&&$errno==E_STRICT){return;}
else{$errTypeStr='UNKNOWN: ' . $errno;}
$sCrLf="\n";ob_start();echo $GLOBALS['xajaxErrorHandlerText'];echo $sCrLf;echo '----';echo $sCrLf;echo '[';echo $errTypeStr;echo '] ';echo $errstr;echo $sCrLf;echo 'Error on line ';echo $errline;echo ' of file ';echo $errfile;$GLOBALS['xajaxErrorHandlerText']=ob_get_clean();}

class xajaxResponse{var $aCommands;var $sCharacterEncoding;var $bOutputEntities;var $returnValue;var $objPluginManager;function xajaxResponse(){if(0 < func_num_args()){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:EDERR:01')
,E_USER_ERROR
);}
$this->aCommands=array();$objResponseManager=&xajaxResponseManager::getInstance();$this->sCharacterEncoding=$objResponseManager->getCharacterEncoding();$this->bOutputEntities=$objResponseManager->getOutputEntities();$this->objPluginManager=&xajaxPluginManager::getInstance();}
function setCharacterEncoding($sCharacterEncoding){$this->sCharacterEncoding=$sCharacterEncoding;return $this;}
function setOutputEntities($bOutputEntities){$this->bOutputEntities=(boolean)$bOutputEntities;return $this;}
function&plugin(){$aArgs=func_get_args();$nArgs=func_num_args();if(false==(0 < $nArgs)){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:MPERR:01')
,E_USER_ERROR
);}
$sName=array_shift($aArgs);$objPlugin=&$this->objPluginManager->getPlugin($sName);if(false===$objPlugin){$bReturn=false;return $bReturn;}
$objPlugin->setResponse($this);if(0 < count($aArgs)){$sMethod=array_shift($aArgs);$aFunction=array(&$objPlugin,$sMethod);call_user_func_array($aFunction,$aArgs);}
return $objPlugin;}
function&__get($sPluginName){$objPlugin=&$this->plugin($sPluginName);return $objPlugin;}
function confirmCommands($iCmdNumber,$sMessage){return $this->addCommand(
array(
'cmd'=>'cc',
'id'=>$iCmdNumber
),
$sMessage
);}
function assign($sTarget,$sAttribute,$sData){return $this->addCommand(
array(
'cmd'=>'as',
'id'=>$sTarget,
'prop'=>$sAttribute
),
$sData
);}
function append($sTarget,$sAttribute,$sData){return $this->addCommand(
array(
'cmd'=>'ap',
'id'=>$sTarget,
'prop'=>$sAttribute
),
$sData
);}
function prepend($sTarget,$sAttribute,$sData){return $this->addCommand(
array(
'cmd'=>'pp',
'id'=>$sTarget,
'prop'=>$sAttribute
),
$sData
);}
function replace($sTarget,$sAttribute,$sSearch,$sData){return $this->addCommand(
array(
'cmd'=>'rp',
'id'=>$sTarget,
'prop'=>$sAttribute
),
array(
's'=> $sSearch,
'r'=> $sData
)
);}
function clear($sTarget,$sAttribute){return $this->assign(
$sTarget,
$sAttribute,
''
);}
function contextAssign($sAttribute,$sData){return $this->addCommand(
array(
'cmd'=>'c:as',
'prop'=>$sAttribute
),
$sData
);}
function contextAppend($sAttribute,$sData){return $this->addCommand(
array(
'cmd'=>'c:ap',
'prop'=>$sAttribute
),
$sData
);}
function contextPrepend($sAttribute,$sData){return $this->addCommand(
array(
'cmd'=>'c:pp',
'prop'=>$sAttribute
),
$sData
);}
function contextClear($sAttribute){return $this->contextAssign(
$sAttribute,
''
);}
function alert($sMsg){return $this->addCommand(
array(
'cmd'=>'al'
),
$sMsg
);}
function debug($sMessage){return $this->addCommand(
array(
'cmd'=>'dbg'
),
$sMessage
);}
function redirect($sURL,$iDelay=0){$queryStart=strpos($sURL,'?',strrpos($sURL,'/'));if($queryStart!==FALSE){$queryStart++;$queryEnd=strpos($sURL,'#',$queryStart);if($queryEnd===FALSE)
$queryEnd=strlen($sURL);$queryPart=substr($sURL,$queryStart,$queryEnd-$queryStart);parse_str($queryPart,$queryParts);$newQueryPart="";if($queryParts){$first=true;foreach($queryParts as $key=> $value){if($first)
$first=false;else
$newQueryPart .='&';$newQueryPart .=rawurlencode($key).'='.rawurlencode($value);}
}else if($_SERVER['QUERY_STRING']){$newQueryPart=rawurlencode($_SERVER['QUERY_STRING']);}
$sURL=str_replace($queryPart,$newQueryPart,$sURL);}
if($iDelay)
$this->script(
'window.setTimeout("window.location = \''
. $sURL
. '\';",'
.($iDelay*1000)
. ');'
);else
$this->script(
'window.location = "'
. $sURL
. '";'
);return $this;}
function script($sJS){return $this->addCommand(
array(
'cmd'=>'js'
),
$sJS
);}
function call(){$aArgs=func_get_args();$sFunc=array_shift($aArgs);return $this->addCommand(
array(
'cmd'=>'jc',
'func'=>$sFunc
),
$aArgs
);}
function remove($sTarget){return $this->addCommand(
array(
'cmd'=>'rm',
'id'=>$sTarget),
''
);}
function create($sParent,$sTag,$sId,$sType=null){if(false===(null===$sType)){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:CPERR:01')
,E_USER_WARNING
);}
return $this->addCommand(
array(
'cmd'=>'ce',
'id'=>$sParent,
'prop'=>$sId
),
$sTag
);}
function insert($sBefore,$sTag,$sId){return $this->addCommand(
array(
'cmd'=>'ie',
'id'=>$sBefore,
'prop'=>$sId
),
$sTag
);}
function insertAfter($sAfter,$sTag,$sId){return $this->addCommand(
array(
'cmd'=>'ia',
'id'=>$sAfter,
'prop'=>$sId
),
$sTag
);}
function createInput($sParent,$sType,$sName,$sId){return $this->addCommand(
array(
'cmd'=>'ci',
'id'=>$sParent,
'prop'=>$sId,
'type'=>$sType
),
$sName
);}
function insertInput($sBefore,$sType,$sName,$sId){return $this->addCommand(
array(
'cmd'=>'ii',
'id'=>$sBefore,
'prop'=>$sId,
'type'=>$sType
),
$sName
);}
function insertInputAfter($sAfter,$sType,$sName,$sId){return $this->addCommand(
array(
'cmd'=>'iia',
'id'=>$sAfter,
'prop'=>$sId,
'type'=>$sType
),
$sName
);}
function setEvent($sTarget,$sEvent,$sScript){return $this->addCommand(
array(
'cmd'=>'ev',
'id'=>$sTarget,
'prop'=>$sEvent
),
$sScript
);}
function addEvent($sTarget,$sEvent,$sScript){return $this->setEvent(
$sTarget,
$sEvent,
$sScript
);}
function addHandler($sTarget,$sEvent,$sHandler){return $this->addCommand(
array(
'cmd'=>'ah',
'id'=>$sTarget,
'prop'=>$sEvent
),
$sHandler
);}
function removeHandler($sTarget,$sEvent,$sHandler){return $this->addCommand(
array(
'cmd'=>'rh',
'id'=>$sTarget,
'prop'=>$sEvent
),
$sHandler);}
function setFunction($sFunction,$sArgs,$sScript){return $this->addCommand(
array(
'cmd'=>'sf',
'func'=>$sFunction,
'prop'=>$sArgs
),
$sScript
);}
function wrapFunction($sFunction,$sArgs,$aScripts,$sReturnValueVariable){return $this->addCommand(
array(
'cmd'=>'wpf',
'func'=>$sFunction,
'prop'=>$sArgs,
'type'=>$sReturnValueVariable
),
$aScripts
);}
function includeScript($sFileName,$sType=null,$sId=null){$command=array('cmd'=> 'in');if(false===(null===$sType))
$command['type']=$sType;if(false===(null===$sId))
$command['elm_id']=$sId;return $this->addCommand(
$command,
$sFileName
);}
function includeScriptOnce($sFileName,$sType=null,$sId=null){$command=array('cmd'=> 'ino');if(false===(null===$sType))
$command['type']=$sType;if(false===(null===$sId))
$command['elm_id']=$sId;return $this->addCommand(
$command,
$sFileName
);}
function removeScript($sFileName,$sUnload=''){$this->addCommand(
array(
'cmd'=>'rjs',
'unld'=>$sUnload
),
$sFileName
);return $this;}
function includeCSS($sFileName,$sMedia=null){$command=array('cmd'=> 'css');if(false===(null===$sMedia))
$command['media']=$sMedia;return $this->addCommand(
$command,
$sFileName
);}
function removeCSS($sFileName,$sMedia=null){$command=array('cmd'=>'rcss');if(false===(null===$sMedia))
$command['media']=$sMedia;return $this->addCommand(
$command,
$sFileName
);}
function waitForCSS($iTimeout=600){$sData="";$this->addCommand(
array(
'cmd'=>'wcss',
'prop'=>$iTimeout
),
$sData
);return $this;}
function waitFor($script,$tenths){return $this->addCommand(
array(
'cmd'=>'wf',
'prop'=>$tenths
),
$script
);}
function sleep($tenths){$this->addCommand(
array(
'cmd'=>'s',
'prop'=>$tenths
),
''
);return $this;}
function setReturnValue($value){$this->returnValue=$this->_encodeArray($value);return $this;}
function getContentType(){return 'text/xml';}
function getOutput(){ob_start();$this->_printHeader_XML();$this->_printResponse_XML();return ob_get_clean();}
function printOutput(){$this->_sendHeaders();$this->_printHeader_XML();$this->_printResponse_XML();}
function _sendHeaders(){$objArgumentManager=&xajaxArgumentManager::getInstance();if(XAJAX_METHOD_GET==$objArgumentManager->getRequestMethod()){header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s"). " GMT");header("Cache-Control: no-cache, must-revalidate");header("Pragma: no-cache");}
$sCharacterSet='';if($this->sCharacterEncoding&&0 < strlen(trim($this->sCharacterEncoding))){$sCharacterSet='; charset="' . trim($this->sCharacterEncoding). '"';}
$sContentType=$this->getContentType();header('content-type: ' . $sContentType . ' ' . $sCharacterSet);}
function getCommandCount(){return count($this->aCommands);}
function loadCommands($mCommands,$bBefore=false){if(is_a($mCommands,'xajaxResponse')){$this->returnValue=$mCommands->returnValue;if($bBefore){$this->aCommands=array_merge($mCommands->aCommands,$this->aCommands);}
else{$this->aCommands=array_merge($this->aCommands,$mCommands->aCommands);}
}
else if(is_array($mCommands)){if($bBefore){$this->aCommands=array_merge($mCommands,$this->aCommands);}
else{$this->aCommands=array_merge($this->aCommands,$mCommands);}
}
else{if(!empty($mCommands)){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:LCERR:01')
,E_USER_ERROR
);}
}
}
function absorb($objResponse){$this->loadCommands($objResponse);}
function addPluginCommand($objPlugin,$aAttributes,$mData){$aAttributes['plg']=$objPlugin->getName();return $this->addCommand($aAttributes,$mData);}
function addCommand($aAttributes,$mData){$aAttributes['data']=$this->_encodeArray($mData);$this->aCommands[]=$aAttributes;return $this;}
function _printHeader_XML(){echo '<';echo '?';echo 'xml version="1.0"';$sEncoding=trim($this->sCharacterEncoding);if($this->sCharacterEncoding&&0 < strlen($sEncoding)){echo ' encoding="';echo $sEncoding;echo '"';}
echo ' ?';echo '>';}
function _printResponse_XML(){echo '<';echo 'xjx>';if(null!==$this->returnValue){echo '<';echo 'xjxrv>';$this->_printArray_XML($this->returnValue);echo '<';echo '/xjxrv>';}
foreach(array_keys($this->aCommands)as $sKey)
$this->_printCommand_XML($this->aCommands[$sKey]);echo '<';echo '/xjx>';}
function _printCommand_XML(&$aAttributes){echo '<';echo 'cmd';$mData='';foreach(array_keys($aAttributes)as $sKey){if($sKey){if('data'!=$sKey){echo ' ';echo $sKey;echo '="';echo $aAttributes[$sKey];echo '"';}else
$mData=&$aAttributes[$sKey];}
}
echo '>';$this->_printArray_XML($mData);echo '<';echo '/cmd>';}
function _printArray_XML(&$mArray){if('object'==gettype($mArray))
$mArray=get_object_vars($mArray);if(false==is_array($mArray)){$this->_printEscapedString_XML($mArray);return;}
echo '<';echo 'xjxobj>';foreach(array_keys($mArray)as $sKey){if(is_array($mArray[$sKey])){echo '<';echo 'e>';foreach(array_keys($mArray[$sKey])as $sInnerKey){if(htmlspecialchars($sInnerKey,ENT_COMPAT,'UTF-8')!=$sInnerKey){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:AKERR:01')
,E_USER_ERROR
);}
if('k'==$sInnerKey||'v'==$sInnerKey){echo '<';echo $sInnerKey;echo '>';$this->_printArray_XML($mArray[$sKey][$sInnerKey]);echo '<';echo '/';echo $sInnerKey;echo '>';}else{$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:IEAERR:01')
,E_USER_ERROR
);}
}
echo '<';echo '/e>';}else{$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:NEAERR:01')
,E_USER_ERROR
);}
}
echo '<';echo '/xjxobj>';}
function _printEscapedString_XML(&$sData){if(is_null($sData)||false==isset($sData)){echo '*';return;}
if($this->bOutputEntities){if(false===function_exists('mb_convert_encoding')){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:MBEERR:01')
,E_USER_NOTICE
);}
echo call_user_func_array('mb_convert_encoding',array(&$sData,'HTML-ENTITIES',$this->sCharacterEncoding));return;}
$nCDATA=0;$bNoOpenCDATA=(false===strpos($sData,'<'.'![CDATA['));if($bNoOpenCDATA){$bNoCloseCDATA=(false===strpos($sData,']]>'));if($bNoCloseCDATA){$bSpecialChars=(htmlspecialchars($sData,ENT_COMPAT,'UTF-8')!=$sData);if($bSpecialChars)
$nCDATA=1;}else
$nCDATA=2;}else
$nCDATA=2;if(0 < $nCDATA){echo '<';echo '![CDATA[';if(is_string($sData)){echo 'S';}else if(is_int($sData)||is_float($sData)){echo 'N';}else if(is_bool($sData)){echo 'B';}
if(1 < $nCDATA){$aSegments=explode('<'.'![CDATA[',$sData);$aOutput=array();$nOutput=0;foreach(array_keys($aSegments)as $keySegment){$aFragments=explode(']]>',$aSegments[$keySegment]);$aStack=array();$nStack=0;foreach(array_keys($aFragments)as $keyFragment){if(0 < $nStack)
array_push($aStack,']]]]><','![CDATA[>',$aFragments[$keyFragment]);else
$aStack[]=$aFragments[$keyFragment];++$nStack;}
if(0 < $nOutput)
array_push($aOutput,'<','![]]><','![CDATA[CDATA[',implode('',$aStack));else
$aOutput[]=implode('',$aStack);++$nOutput;}
echo implode('',$aOutput);}else
echo $sData;echo ']]>';}else{if(is_string($sData)){echo 'S';}else if(is_int($sData)||is_float($sData)){echo 'N';}else if(is_bool($sData)){echo 'B';}
echo $sData;}
}
function _encodeArray(&$mData){if('object'===gettype($mData))
$mData=get_object_vars($mData);if(false===is_array($mData))
return $mData;$aData=array();foreach(array_keys($mData)as $sKey)
$aData[]=array(
'k'=>$sKey,
'v'=>$this->_encodeArray($mData[$sKey])
);return $aData;}
}
class xajaxCustomResponse{var $sOutput;var $sContentType;var $sCharacterEncoding;var $bOutputEntities;function xajaxCustomResponse($sContentType){$this->sOutput='';$this->sContentType=$sContentType;$objResponseManager=&xajaxResponseManager::getInstance();$this->sCharacterEncoding=$objResponseManager->getCharacterEncoding();$this->bOutputEntities=$objResponseManager->getOutputEntities();}
function setCharacterEncoding($sCharacterEncoding){$this->sCharacterEncoding=$sCharacterEncoding;}
function setOutputEntities($bOutputEntities){$this->bOutputEntities=$bOutputEntities;}
function clear(){$this->sOutput='';}
function append($sOutput){$this->sOutput .=$sOutput;}
function absorb($objResponse){if(false==is_a($objResponse,'xajaxCustomResponse')){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:MXRTERR')
,E_USER_ERROR
);}
if($objResponse->getContentType()!=$this->getContentType()){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:MXCTERR')
,E_USER_ERROR
);}
if($objResponse->getCharacterEncoding()!=$this->getCharacterEncoding()){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:MXCEERR')
,E_USER_ERROR
);}
if($objResponse->getOutputEntities()!=$this->getOutputEntities()){$objLanguageManager=&xajaxLanguageManager::getInstance();trigger_error(
$objLanguageManager->getText('XJXRSP:MXOEERR')
,E_USER_ERROR
);}
$this->sOutput .=$objResponse->getOutput();}
function getContentType(){return $this->sContentType;}
function getCharacterEncoding(){return $this->sCharacterEncoding;}
function getOutputEntities(){return $this->bOutputEntities;}
function getOutput(){return $this->sOutput;}
function printOutput(){$sContentType=$this->sContentType;$sCharacterSet=$this->sCharacterEncoding;header("content-type: {$sContentType}; charset={$sCharacterSet}");echo $this->sOutput;}
}

if(!defined('XAJAX_FUNCTION'))define('XAJAX_FUNCTION','function');class xajaxFunctionPlugin extends xajaxRequestPlugin{var $aFunctions;var $sXajaxPrefix;var $sDefer;var $bDeferScriptGeneration;var $sRequestedFunction;function xajaxFunctionPlugin(){$this->aFunctions=array();$this->sXajaxPrefix='xajax_';$this->sDefer='';$this->bDeferScriptGeneration=false;$this->sRequestedFunction=NULL;if(isset($_GET['xjxfun']))$this->sRequestedFunction=$_GET['xjxfun'];if(isset($_POST['xjxfun']))$this->sRequestedFunction=$_POST['xjxfun'];}
function configure($sName,$mValue){if('wrapperPrefix'==$sName){$this->sXajaxPrefix=$mValue;}else if('scriptDefferal'==$sName){if(true===$mValue)$this->sDefer='defer ';else $this->sDefer='';}else if('deferScriptGeneration'==$sName){if(true===$mValue||false===$mValue)
$this->bDeferScriptGeneration=$mValue;else if('deferred'===$mValue)
$this->bDeferScriptGeneration=$mValue;}
}
function register($aArgs){if(1 < count($aArgs)){$sType=$aArgs[0];if(XAJAX_FUNCTION==$sType){$xuf=&$aArgs[1];if(false===is_a($xuf,'xajaxUserFunction'))
$xuf=&new xajaxUserFunction($xuf);if(2 < count($aArgs))
if(is_array($aArgs[2]))
foreach($aArgs[2] as $sName=> $sValue)
$xuf->configure($sName,$sValue);$this->aFunctions[]=&$xuf;return $xuf->generateRequest($this->sXajaxPrefix);}
}
return false;}
function generateClientScript(){if(false===$this->bDeferScriptGeneration||'deferred'===$this->bDeferScriptGeneration){if(0 < count($this->aFunctions)){echo "\n<script type='text/javascript' " . $this->sDefer . "charset='UTF-8'>\n";echo "/* <![CDATA[ */\n";foreach(array_keys($this->aFunctions)as $sKey)
$this->aFunctions[$sKey]->generateClientScript($this->sXajaxPrefix);echo "/* ]]> */\n";echo "</script>\n";}
}
}
function canProcessRequest(){if(NULL==$this->sRequestedFunction)
return false;return true;}
function processRequest(){if(NULL==$this->sRequestedFunction)
return false;$objArgumentManager=&xajaxArgumentManager::getInstance();$aArgs=$objArgumentManager->process();foreach(array_keys($this->aFunctions)as $sKey){$xuf=&$this->aFunctions[$sKey];if($xuf->getName()==$this->sRequestedFunction){$xuf->call($aArgs);return true;}
}
return 'Invalid function request received; no request processor found with this name.';}
}
$objPluginManager=&xajaxPluginManager::getInstance();$objPluginManager->registerPlugin(new xajaxFunctionPlugin(),100);
class xajaxCallableObject{var $obj;var $aConfiguration;function xajaxCallableObject(&$obj){$this->obj=&$obj;$this->aConfiguration=array();}
function getName(){return get_class($this->obj);}
function configure($sMethod,$sName,$sValue){$sMethod=strtolower($sMethod);if(false==isset($this->aConfiguration[$sMethod]))
$this->aConfiguration[$sMethod]=array();$this->aConfiguration[$sMethod][$sName]=$sValue;}
function generateRequests($sXajaxPrefix){$aRequests=array();$sClass=get_class($this->obj);foreach(get_class_methods($this->obj)as $sMethodName){$bInclude=true;if("__call"==$sMethodName)
$bInclude=false;if($sClass==$sMethodName)
$bInclude=false;if($bInclude)
$aRequests[strtolower($sMethodName)]=&
new xajaxRequest("{$sXajaxPrefix}{$sClass}.{$sMethodName}");}
return $aRequests;}
function generateClientScript($sXajaxPrefix){$sClass=get_class($this->obj);echo "{$sXajaxPrefix}{$sClass} = {};\n";foreach(get_class_methods($this->obj)as $sMethodName){$bInclude=true;if(2 < strlen($sMethodName))
if("__"==substr($sMethodName,0,2))
$bInclude=false;if($sClass==$sMethodName)
$bInclude=false;if($bInclude){echo "{$sXajaxPrefix}{$sClass}.{$sMethodName} = function() { ";echo "return xajax.request( ";echo "{ xjxcls: '{$sClass}', xjxmthd: '{$sMethodName}' }, ";echo "{ parameters: arguments";$sSeparator=", ";if(isset($this->aConfiguration['*']))
foreach($this->aConfiguration['*'] as $sKey=> $sValue)
echo "{$sSeparator}{$sKey}: {$sValue}";if(isset($this->aConfiguration[strtolower($sMethodName)]))
foreach($this->aConfiguration[strtolower($sMethodName)] as $sKey=> $sValue)
echo "{$sSeparator}{$sKey}: {$sValue}";echo " } ); ";echo "};\n";}
}
}
function isClass($sClass){if(get_class($this->obj)===$sClass)
return true;return false;}
function hasMethod($sMethod){return method_exists($this->obj,$sMethod)||method_exists($this->obj,"__call");}
function call($sMethod,$aArgs){$objResponseManager=&xajaxResponseManager::getInstance();$objResponseManager->append(
call_user_func_array(
array(&$this->obj,$sMethod),
$aArgs
)
);}
}

class xajaxEvent{var $sName;var $aConfiguration;var $aHandlers;function xajaxEvent($sName){$this->sName=$sName;$this->aConfiguration=array();$this->aHandlers=array();}
function getName(){return $this->sName;}
function configure($sName,$mValue){$this->aConfiguration[$sName]=$mValue;}
function addHandler(&$xuf){$this->aHandlers[]=&$xuf;}
function generateRequest($sXajaxPrefix,$sEventPrefix){$sEvent=$this->sName;return new xajaxRequest("{$sXajaxPrefix}{$sEventPrefix}{$sEvent}");}
function generateClientScript($sXajaxPrefix,$sEventPrefix){$sMode='';$sMethod='';if(isset($this->aConfiguration['mode']))
$sMode=$this->aConfiguration['mode'];if(isset($this->aConfiguration['method']))
$sMethod=$this->aConfiguration['method'];if(0 < strlen($sMode))
$sMode=", mode: '{$sMode}'";if(0 < strlen($sMethod))
$sMethod=", method: '{$sMethod}'";$sEvent=$this->sName;echo "{$sXajaxPrefix}{$sEventPrefix}{$sEvent} = function() { return xajax.request( { xjxevt: '{$sEvent}' }, { parameters: arguments{$sMode}{$sMethod} } ); };\n";}
function fire($aArgs){$objResponseManager=&xajaxResponseManager::getInstance();foreach(array_keys($this->aHandlers)as $sKey)
$this->aHandlers[$sKey]->call($aArgs);}
}

class xajaxUserFunction{var $sAlias;var $uf;var $sInclude;var $aConfiguration;function xajaxUserFunction($uf,$sInclude=NULL,$aConfiguration=array()){$this->sAlias='';$this->uf=&$uf;$this->sInclude=$sInclude;$this->aConfiguration=array();foreach($aConfiguration as $sKey=> $sValue)
$this->configure($sKey,$sValue);if(is_array($this->uf)&&2 < count($this->uf)){$this->sAlias=$this->uf[0];$this->uf=array_slice($this->uf,1);}
}
function getName(){if(is_array($this->uf))
return $this->uf[1];return $this->uf;}
function configure($sName,$sValue){if('alias'==$sName)
$this->sAlias=$sValue;else
$this->aConfiguration[$sName]=$sValue;}
function generateRequest($sXajaxPrefix){$sAlias=$this->getName();if(0 < strlen($this->sAlias))
$sAlias=$this->sAlias;return new xajaxRequest("{$sXajaxPrefix}{$sAlias}");}
function generateClientScript($sXajaxPrefix){$sFunction=$this->getName();$sAlias=$sFunction;if(0 < strlen($this->sAlias))
$sAlias=$this->sAlias;echo "{$sXajaxPrefix}{$sAlias} = function() { ";echo "return xajax.request( ";echo "{ xjxfun: '{$sFunction}' }, ";echo "{ parameters: arguments";$sSeparator=", ";foreach($this->aConfiguration as $sKey=> $sValue)
echo "{$sSeparator}{$sKey}: {$sValue}";echo " } ); ";echo "};\n";}
function call($aArgs=array()){$objResponseManager=&xajaxResponseManager::getInstance();if(NULL!=$this->sInclude){ob_start();require_once $this->sInclude;$sOutput=ob_get_clean();}
$mFunction=$this->uf;$objResponseManager->append(call_user_func_array($mFunction,$aArgs));}
}

if(!defined('XAJAX_EVENT'))define('XAJAX_EVENT','xajax event');if(!defined('XAJAX_EVENT_HANDLER'))define('XAJAX_EVENT_HANDLER','xajax event handler');class xajaxEventPlugin extends xajaxRequestPlugin{var $aEvents;var $sXajaxPrefix;var $sEventPrefix;var $sDefer;var $bDeferScriptGeneration;var $sRequestedEvent;function xajaxEventPlugin(){$this->aEvents=array();$this->sXajaxPrefix='xajax_';$this->sEventPrefix='event_';$this->sDefer='';$this->bDeferScriptGeneration=false;$this->sRequestedEvent=NULL;if(isset($_GET['xjxevt']))$this->sRequestedEvent=$_GET['xjxevt'];if(isset($_POST['xjxevt']))$this->sRequestedEvent=$_POST['xjxevt'];}
function configure($sName,$mValue){if('wrapperPrefix'==$sName){$this->sXajaxPrefix=$mValue;}else if('eventPrefix'==$sName){$this->sEventPrefix=$mValue;}else if('scriptDefferal'==$sName){if(true===$mValue)$this->sDefer='defer ';else $this->sDefer='';}else if('deferScriptGeneration'==$sName){if(true===$mValue||false===$mValue)
$this->bDeferScriptGeneration=$mValue;else if('deferred'===$mValue)
$this->bDeferScriptGeneration=$mValue;}
}
function register($aArgs){if(1 < count($aArgs)){$sType=$aArgs[0];if(XAJAX_EVENT==$sType){$sEvent=$aArgs[1];if(false===isset($this->aEvents[$sEvent])){$xe=&new xajaxEvent($sEvent);if(2 < count($aArgs))
if(is_array($aArgs[2]))
foreach($aArgs[2] as $sKey=> $sValue)
$xe->configure($sKey,$sValue);$this->aEvents[$sEvent]=&$xe;return $xe->generateRequest($this->sXajaxPrefix,$this->sEventPrefix);}
}
if(XAJAX_EVENT_HANDLER==$sType){$sEvent=$aArgs[1];if(isset($this->aEvents[$sEvent])){if(isset($aArgs[2])){$xuf=&$aArgs[2];if(false===is_a($xuf,'xajaxUserFunction'))
$xuf=&new xajaxUserFunction($xuf);$objEvent=&$this->aEvents[$sEvent];$objEvent->addHandler($xuf);return true;}
}
}
}
return false;}
function generateClientScript(){if(false===$this->bDeferScriptGeneration||'deferred'===$this->bDeferScriptGeneration){if(0 < count($this->aEvents)){echo "\n<script type='text/javascript' ";echo $this->sDefer;echo "charset='UTF-8'>\n";echo "/* <![CDATA[ */\n";foreach(array_keys($this->aEvents)as $sKey)
$this->aEvents[$sKey]->generateClientScript($this->sXajaxPrefix,$this->sEventPrefix);echo "/* ]]> */\n";echo "</script>\n";}
}
}
function canProcessRequest(){if(NULL==$this->sRequestedEvent)
return false;return true;}
function processRequest(){if(NULL==$this->sRequestedEvent)
return false;$objArgumentManager=&xajaxArgumentManager::getInstance();$aArgs=$objArgumentManager->process();foreach(array_keys($this->aEvents)as $sKey){$objEvent=&$this->aEvents[$sKey];if($objEvent->getName()==$this->sRequestedEvent){$objEvent->fire($aArgs);return true;}
}
return 'Invalid event request received; no event was registered with this name.';}
}
$objPluginManager=&xajaxPluginManager::getInstance();$objPluginManager->registerPlugin(new xajaxEventPlugin(),103);
class xajaxIncludeClientScriptPlugin extends xajaxRequestPlugin{var $sJsURI;var $aJsFiles;var $sDefer;var $sRequestURI;var $sStatusMessages;var $sWaitCursor;var $sVersion;var $sDefaultMode;var $sDefaultMethod;var $bDebug;var $bVerboseDebug;var $nScriptLoadTimeout;var $bUseUncompressedScripts;var $bDeferScriptGeneration;var $sLanguage;var $nResponseQueueSize;function xajaxIncludeClientScriptPlugin(){$this->sJsURI='';$this->aJsFiles=array();$this->sDefer='';$this->sRequestURI='';$this->sStatusMessages='false';$this->sWaitCursor='true';$this->sVersion='unknown';$this->sDefaultMode='asynchronous';$this->sDefaultMethod='POST';$this->bDebug=false;$this->bVerboseDebug=false;$this->nScriptLoadTimeout=2000;$this->bUseUncompressedScripts=false;$this->bDeferScriptGeneration=false;$this->sLanguage=null;$this->nResponseQueueSize=null;}
function configure($sName,$mValue){if('javascript URI'==$sName){$this->sJsURI=$mValue;}else if("javascript files"==$sName){$this->aJsFiles=$mValue;}else if("scriptDefferal"==$sName){if(true===$mValue)$this->sDefer="defer ";else $this->sDefer="";}else if("requestURI"==$sName){$this->sRequestURI=$mValue;}else if("statusMessages"==$sName){if(true===$mValue)$this->sStatusMessages="true";else $this->sStatusMessages="false";}else if("waitCursor"==$sName){if(true===$mValue)$this->sWaitCursor="true";else $this->sWaitCursor="false";}else if("version"==$sName){$this->sVersion=$mValue;}else if("defaultMode"==$sName){if("asynchronous"==$mValue||"synchronous"==$mValue)
$this->sDefaultMode=$mValue;}else if("defaultMethod"==$sName){if("POST"==$mValue||"GET"==$mValue)
$this->sDefaultMethod=$mValue;}else if("debug"==$sName){if(true===$mValue||false===$mValue)
$this->bDebug=$mValue;}else if("verboseDebug"==$sName){if(true===$mValue||false===$mValue)
$this->bVerboseDebug=$mValue;}else if("scriptLoadTimeout"==$sName){$this->nScriptLoadTimeout=$mValue;}else if("useUncompressedScripts"==$sName){if(true===$mValue||false===$mValue)
$this->bUseUncompressedScripts=$mValue;}else if('deferScriptGeneration'==$sName){if(true===$mValue||false===$mValue)
$this->bDeferScriptGeneration=$mValue;else if('deferred'==$mValue)
$this->bDeferScriptGeneration=$mValue;}else if('language'==$sName){$this->sLanguage=$mValue;}else if('responseQueueSize'==$sName){$this->nResponseQueueSize=$mValue;}
}
function generateClientScript(){if(false===$this->bDeferScriptGeneration){$this->printJavascriptConfig();$this->printJavascriptInclude();}
else if(true===$this->bDeferScriptGeneration){$this->printJavascriptInclude();}
else if('deferred'==$this->bDeferScriptGeneration){$this->printJavascriptConfig();}
}
function getJavascriptConfig(){ob_start();$this->printJavascriptConfig();return ob_get_clean();}
function printJavascriptConfig(){$sCrLf="\n";echo $sCrLf;echo '<';echo 'script type="text/javascript" ';echo $this->sDefer;echo 'charset="UTF-8">';echo $sCrLf;echo '/* <';echo '![CDATA[ */';echo $sCrLf;echo 'try { if (undefined == xajax.config) xajax.config = {}; } catch (e) { xajax = {}; xajax.config = {}; };';echo $sCrLf;echo 'xajax.config.requestURI = "';echo $this->sRequestURI;echo '";';echo $sCrLf;echo 'xajax.config.statusMessages = ';echo $this->sStatusMessages;echo ';';echo $sCrLf;echo 'xajax.config.waitCursor = ';echo $this->sWaitCursor;echo ';';echo $sCrLf;echo 'xajax.config.version = "';echo $this->sVersion;echo '";';echo $sCrLf;echo 'xajax.config.legacy = false;';echo $sCrLf;echo 'xajax.config.defaultMode = "';echo $this->sDefaultMode;echo '";';echo $sCrLf;echo 'xajax.config.defaultMethod = "';echo $this->sDefaultMethod;echo '";';if(false===(null===$this->nResponseQueueSize)){echo $sCrLf;echo 'xajax.config.responseQueueSize = ';echo $this->nResponseQueueSize;echo ';';}
echo $sCrLf;echo '/* ]]> */';echo $sCrLf;echo '<';echo '/script>';echo $sCrLf;}
function getJavascriptInclude(){ob_start();$this->printJavascriptInclude();return ob_get_clean();}
function printJavascriptInclude(){$aJsFiles=$this->aJsFiles;$sJsURI=$this->sJsURI;if(0==count($aJsFiles)){$aJsFiles[]=array($this->_getScriptFilename('xajax_js/xajax_core.js'),'xajax');if(true===$this->bDebug)
$aJsFiles[]=array($this->_getScriptFilename('xajax_js/xajax_debug.js'),'xajax.debug');if(true===$this->bVerboseDebug)
$aJsFiles[]=array($this->_getScriptFilename('xajax_js/xajax_verbose.js'),'xajax.debug.verbose');if(null!==$this->sLanguage)
$aJsFiles[]=array($this->_getScriptFilename('xajax_js/xajax_lang_' . $this->sLanguage . '.js'),'xajax');}
if($sJsURI!=''&&substr($sJsURI,-1)!='/')
$sJsURI .='/';$sCrLf="\n";foreach($aJsFiles as $aJsFile){echo '<';echo 'script type="text/javascript" src="';echo $sJsURI;echo $aJsFile[0];echo '" ';echo $this->sDefer;echo 'charset="UTF-8"><';echo '/script>';echo $sCrLf;}
if(0 < $this->nScriptLoadTimeout){foreach($aJsFiles as $aJsFile){echo '<';echo 'script type="text/javascript" ';echo $this->sDefer;echo 'charset="UTF-8">';echo $sCrLf;echo '/* <';echo '![CDATA[ */';echo $sCrLf;echo 'window.setTimeout(';echo $sCrLf;echo ' function() {';echo $sCrLf;echo '  var scriptExists = false;';echo $sCrLf;echo '  try { if (';echo $aJsFile[1];echo '.isLoaded) scriptExists = true; }';echo $sCrLf;echo '  catch (e) {}';echo $sCrLf;echo '  if (!scriptExists) {';echo $sCrLf;echo '   alert("Error: the ';echo $aJsFile[1];echo ' Javascript component could not be included. Perhaps the URL is incorrect?\nURL: ';echo $sJsURI;echo $aJsFile[0];echo '");';echo $sCrLf;echo '  }';echo $sCrLf;echo ' }, ';echo $this->nScriptLoadTimeout;echo ');';echo $sCrLf;echo '/* ]]> */';echo $sCrLf;echo '<';echo '/script>';echo $sCrLf;}
}
}
function _getScriptFilename($sFilename){if($this->bUseUncompressedScripts){return str_replace('.js','_uncompressed.js',$sFilename);}
return $sFilename;}
}
$objPluginManager=&xajaxPluginManager::getInstance();$objPluginManager->registerPlugin(new xajaxIncludeClientScriptPlugin(),99);
if(!defined('XAJAX_CALLABLE_OBJECT'))define('XAJAX_CALLABLE_OBJECT','callable object');class xajaxCallableObjectPlugin extends xajaxRequestPlugin{var $aCallableObjects;var $sXajaxPrefix;var $sDefer;var $bDeferScriptGeneration;var $sRequestedClass;var $sRequestedMethod;function xajaxCallableObjectPlugin(){$this->aCallableObjects=array();$this->sXajaxPrefix='xajax_';$this->sDefer='';$this->bDeferScriptGeneration=false;$this->sRequestedClass=NULL;$this->sRequestedMethod=NULL;if(!empty($_GET['xjxcls']))$this->sRequestedClass=$_GET['xjxcls'];if(!empty($_GET['xjxmthd']))$this->sRequestedMethod=$_GET['xjxmthd'];if(!empty($_POST['xjxcls']))$this->sRequestedClass=$_POST['xjxcls'];if(!empty($_POST['xjxmthd']))$this->sRequestedMethod=$_POST['xjxmthd'];}
function configure($sName,$mValue){if('wrapperPrefix'==$sName){$this->sXajaxPrefix=$mValue;}else if('scriptDefferal'==$sName){if(true===$mValue)$this->sDefer='defer ';else $this->sDefer='';}else if('deferScriptGeneration'==$sName){if(true===$mValue||false===$mValue)
$this->bDeferScriptGeneration=$mValue;else if('deferred'===$mValue)
$this->bDeferScriptGeneration=$mValue;}
}
function register($aArgs){if(1 < count($aArgs)){$sType=$aArgs[0];if(XAJAX_CALLABLE_OBJECT==$sType){$xco=&$aArgs[1];if(false===is_a($xco,'xajaxCallableObject'))
$xco=&new xajaxCallableObject($xco);if(2 < count($aArgs))
if(is_array($aArgs[2]))
foreach($aArgs[2] as $sKey=> $aValue)
foreach($aValue as $sName=> $sValue)
$xco->configure($sKey,$sName,$sValue);$this->aCallableObjects[]=&$xco;return $xco->generateRequests($this->sXajaxPrefix);}
}
return false;}
function generateClientScript(){if(false===$this->bDeferScriptGeneration||'deferred'===$this->bDeferScriptGeneration){if(0 < count($this->aCallableObjects)){$sCrLf="\n";echo $sCrLf;echo '<';echo 'script type="text/javascript" ';echo $this->sDefer;echo 'charset="UTF-8">';echo $sCrLf;echo '/* <';echo '![CDATA[ */';echo $sCrLf;foreach(array_keys($this->aCallableObjects)as $sKey)
$this->aCallableObjects[$sKey]->generateClientScript($this->sXajaxPrefix);echo '/* ]]> */';echo $sCrLf;echo '<';echo '/script>';echo $sCrLf;}
}
}
function canProcessRequest(){if(NULL==$this->sRequestedClass)
return false;if(NULL==$this->sRequestedMethod)
return false;return true;}
function processRequest(){if(NULL==$this->sRequestedClass)
return false;if(NULL==$this->sRequestedMethod)
return false;$objArgumentManager=&xajaxArgumentManager::getInstance();$aArgs=$objArgumentManager->process();foreach(array_keys($this->aCallableObjects)as $sKey){$xco=&$this->aCallableObjects[$sKey];if($xco->isClass($this->sRequestedClass)){if($xco->hasMethod($this->sRequestedMethod)){$xco->call($this->sRequestedMethod,$aArgs);return true;}
}
}
return 'Invalid request for a callable object.';}
}
$objPluginManager=&xajaxPluginManager::getInstance();$objPluginManager->registerPlugin(new xajaxCallableObjectPlugin(),102);
class xajaxScriptPlugin extends xajaxRequestPlugin{var $sRequest;var $sHash;var $sRequestURI;var $bDeferScriptGeneration;var $bValidateHash;var $bWorking;function xajaxScriptPlugin(){$this->sRequestURI='';$this->bDeferScriptGeneration=false;$this->bValidateHash=true;$this->bWorking=false;$this->sRequest='';$this->sHash=null;if(isset($_GET['xjxGenerateJavascript'])){$this->sRequest='script';$this->sHash=$_GET['xjxGenerateJavascript'];}
if(isset($_GET['xjxGenerateStyle'])){$this->sRequest='style';$this->sHash=$_GET['xjxGenerateStyle'];}
}
function configure($sName,$mValue){if('requestURI'==$sName){$this->sRequestURI=$mValue;}else if('deferScriptGeneration'==$sName){if(true===$mValue||false===$mValue)
$this->bDeferScriptGeneration=$mValue;}else if('deferScriptValidateHash'==$sName){if(true===$mValue||false===$mValue)
$this->bValidateHash=$mValue;}
}
function generateClientScript(){if($this->bWorking)
return;if(true===$this->bDeferScriptGeneration){$this->bWorking=true;$sQueryBase='?';if(0 < strpos($this->sRequestURI,'?'))
$sQueryBase='&';$aScripts=$this->_getSections('script');if(0 < count($aScripts)){$sHash=md5(implode($aScripts));$sQuery=$sQueryBase . "xjxGenerateJavascript=" . $sHash;echo "\n<script type='text/javascript' src='" . $this->sRequestURI . $sQuery . "' charset='UTF-8'></script>\n";}
$aStyles=$this->_getSections('style');if(0 < count($aStyles)){$sHash=md5(implode($aStyles));$sQuery=$sQueryBase . "xjxGenerateStyle=" . $sHash;echo "\n<link href='" . $this->sRequestURI . $sQuery . "' rel='Stylesheet' />\n";}
$this->bWorking=false;}
}
function canProcessRequest(){return(''!=$this->sRequest);}
function&_getSections($sType){$objPluginManager=&xajaxPluginManager::getInstance();$objPluginManager->configure('deferScriptGeneration','deferred');$aSections=array();ob_start();$objPluginManager->generateClientScript();$sScript=ob_get_clean();$aParts=explode('</' . $sType . '>',$sScript);foreach($aParts as $sPart){$aValues=explode('<' . $sType,$sPart,2);if(2==count($aValues)){list($sJunk,$sPart)=$aValues;$aValues=explode('>',$sPart,2);if(2==count($aValues)){list($sJunk,$sPart)=$aValues;if(0 < strlen($sPart))
$aSections[]=$sPart;}
}
}
$objPluginManager->configure('deferScriptGeneration',$this->bDeferScriptGeneration);return $aSections;}
function processRequest(){if($this->canProcessRequest()){$aSections=&$this->_getSections($this->sRequest);$sHash=md5(implode($aSections));if(false==$this->bValidateHash||$sHash==$this->sHash){$sType='text/javascript';if('style'==$this->sRequest)
$sType='text/css';$objResponse=&new xajaxCustomResponse($sType);foreach($aSections as $sSection)
$objResponse->append($sSection . "\n");$objResponseManager=&xajaxResponseManager::getInstance();$objResponseManager->append($objResponse);header('Expires: ' . gmdate('D, d M Y H:i:s',time()+(60*60*24)). ' GMT');return true;}
return 'Invalid script or style request.';trigger_error('Hash mismatch: ' . $this->sRequest . ': ' . $sHash . ' <==> ' . $this->sHash,E_USER_ERROR);}
}
}
$objPluginManager=&xajaxPluginManager::getInstance();$objPluginManager->registerPlugin(new xajaxScriptPlugin(),9999);
if(!defined('XAJAX_FORM_VALUES'))define('XAJAX_FORM_VALUES','get form values');if(!defined('XAJAX_INPUT_VALUE'))define('XAJAX_INPUT_VALUE','get input value');if(!defined('XAJAX_CHECKED_VALUE'))define('XAJAX_CHECKED_VALUE','get checked value');if(!defined('XAJAX_ELEMENT_INNERHTML'))define('XAJAX_ELEMENT_INNERHTML','get element innerHTML');if(!defined('XAJAX_QUOTED_VALUE'))define('XAJAX_QUOTED_VALUE','quoted value');if(!defined('XAJAX_JS_VALUE'))define('XAJAX_JS_VALUE','unquoted value');class xajaxRequest{var $sName;var $sQuoteCharacter;var $aParameters;function xajaxRequest($sName){$this->aParameters=array();$this->sQuoteCharacter='"';$this->sName=$sName;}
function useSingleQuote(){$this->sQuoteCharacter="'";}
function useDoubleQuote(){$this->sQuoteCharacter='"';}
function clearParameters(){$this->aParameters=array();}
function addParameter(){$aArgs=func_get_args();if(1 < count($aArgs))
$this->setParameter(
count($this->aParameters),
$aArgs[0],
$aArgs[1]);}
function setParameter(){$aArgs=func_get_args();if(2 < count($aArgs)){$nParameter=$aArgs[0];$sType=$aArgs[1];if(XAJAX_FORM_VALUES==$sType){$sFormID=$aArgs[2];$this->aParameters[$nParameter]=
"xajax.getFormValues("
. $this->sQuoteCharacter
. $sFormID
. $this->sQuoteCharacter
. ")";}
else if(XAJAX_INPUT_VALUE==$sType){$sInputID=$aArgs[2];$this->aParameters[$nParameter]=
"xajax.$("
. $this->sQuoteCharacter
. $sInputID
. $this->sQuoteCharacter
. ").value";}
else if(XAJAX_CHECKED_VALUE==$sType){$sCheckedID=$aArgs[2];$this->aParameters[$nParameter]=
"xajax.$("
. $this->sQuoteCharacter
. $sCheckedID
. $this->sQuoteCharacter
. ").checked";}
else if(XAJAX_ELEMENT_INNERHTML==$sType){$sElementID=$aArgs[2];$this->aParameters[$nParameter]=
"xajax.$("
. $this->sQuoteCharacter
. $sElementID
. $this->sQuoteCharacter
. ").innerHTML";}
else if(XAJAX_QUOTED_VALUE==$sType){$sValue=$aArgs[2];$this->aParameters[$nParameter]=
$this->sQuoteCharacter
. $sValue
. $this->sQuoteCharacter;}
else if(XAJAX_JS_VALUE==$sType){$sValue=$aArgs[2];$this->aParameters[$nParameter]=$sValue;}
}
}
function getScript(){ob_start();$this->printScript();return ob_get_clean();}
function printScript(){echo $this->sName;echo '(';$sSeparator='';foreach($this->aParameters as $sParameter){echo $sSeparator;echo $sParameter;$sSeparator=', ';}
echo ')';}
}
class xajaxCustomRequest extends xajaxRequest{var $aVariables;var $sScript;function xajaxCustomRequest($sScript){$this->aVariables=array();$this->sScript=$sScript;}
function clearVariables(){$this->aVariables=array();}
function setVariable($sName,$sValue){$this->aVariables[$sName]=$sValue;}
function printScript(){$sScript=$this->sScript;foreach($this->aVariables as $sKey=> $sValue)
$sScript=str_replace($sKey,$sValue,$sScript);echo $sScript;}
}
@error_reporting($error_reporting);
unset($error_reporting);