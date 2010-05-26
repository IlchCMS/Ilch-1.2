<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');
/**
 * eine funktion für var_dump und <pre>print_r()</pre> zu vereinen
 *
 * @return formatierte Ausgabe eines Arrays oder Objectes
 * @author gecko
 */
function dump($src) {
    if (is_array($src)) {
        echo "<pre>----------\n";
        print_r($src);
        echo "----------</pre>";
    } elseif (is_object($src)) {
        echo "<pre>==========\n";
        var_dump($src);
        echo "==========</pre>";
    } else {
        echo "=========&gt; ";
        var_dump($src);
        echo " &lt;=========";
    }
}

$debug_output = '';

function debug($d, $x = 0, $o = true) {
    global $debug_output;

    if ($o and $x == 0) {
        if (is_array($d)) {
            $debug_output .= '<div style="white-space:pre">' . var_export($d, true) . '</div>';
        } else {
            if (is_bool($d)) {
                $d = 'Bool: ' . ($d ? 'true' : 'false');
            }
            $debug_output .= '<div>&nbsp;' . $d . '&nbsp;</div>';
        }
    } elseif ($x == 1 AND $o) {?>
	    <script language="JavaScript" type="text/javascript"><!--
	    function closeDebugDivID () {
	      if (document.getElementById('debugDivID').style.display == 'none') {
	        document.getElementById('debugDivID').style.display = 'block';
	      } else {
	        document.getElementById('debugDivID').style.display = 'none';
	      }
	    }
	    //--></script>
	    <style>#debugDivID div {border-bottom: 1px dashed black; text-align: left;}</style>
	    <div id="debugDiv" style="position:absolute; top:0px; left:0px; display:inline; width:50px; overflow: show;">
	    <a href="javascript:closeDebugDivID();"><img src="include/images/icons/del.gif" alt=""></a>
	    <div id="debugDivID" style="display: none; width: 700px; background-color: #FFFFFF; border:1px solid grey; color: #000000; z-index: 100;">
	    <?php
        echo $debug_output;

        ?>
	    </div></div><?php
    }
}
// debug_bt() from php.net
function debug_bt() {
    if (!function_exists('debug_backtrace')) {
        echo 'function debug_backtrace does not exists' . "\r\n";
        return;
    }
    $r = 'Debug backtrace:' . "\r\n";
    foreach (debug_backtrace() as $t) {
        $r .= "\t" . '@ ';
        if (isset($t[ 'file' ])) {
            $r .= basename($t[ 'file' ]) . ':' . $t[ 'line' ];
        } else {
            $r .= '<PHP inner-code>';
        }

        $r .= ' -- ';

        if (isset($t[ 'class' ])) {
            $r .= $t[ 'class' ] . $t[ 'type' ];
        }

        $r .= $t[ 'function' ];
        if (isset($t[ 'args' ]) && sizeof($t[ 'args' ]) > 0) {
            // $r .= '('.implode(',', $t['args']).')';
            $r .= '(...)';
        } else {
            $r .= '()';
        }

        $r .= "\r\n";
    }
    return $r;
}

?>