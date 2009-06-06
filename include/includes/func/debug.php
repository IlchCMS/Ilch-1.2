<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

$debug_output = '';

function debug ($d, $x = 0, $o = true) {
    global $debug_output;

    if ($o == 1 AND $x == 0) {
        $debug_output .= '<span style="background-color: #FFFFFF; border:1px solid grey; color: #000000">';
        $debug_output .= '&nbsp;' . $d . '&nbsp;</span><br />';
    }
    if ($x == 1 AND $o) { ?>
    <script language="JavaScript" type="text/javascript"><!--
    function closeDebugDivID () {
      if (document.getElementById('debugDivID').style.display == 'none') {
        document.getElementById('debugDivID').style.display = 'inline';
      } else {
        document.getElementById('debugDivID').style.display = 'none';
      }
    }
    //--></script>
    <div id="debugDiv" style="position:absolute; top:0px; left:0px; display:inline; width:500px;">
    <a href="javascript:closeDebugDivID();"><img src="include/images/icons/del.gif" alt=""></a>
    <div id="debugDivID">
    <?php echo $debug_output; ?>
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
    foreach(debug_backtrace() as $t) {
        $r .= "\t" . '@ ';
        if (isset($t['file'])) {
            $r .= basename($t['file']) . ':' . $t['line'];
        } else {
            $r .= '<PHP inner-code>';
        }

        $r .= ' -- ';

        if (isset($t['class'])) {
            $r .= $t['class'] . $t['type'];
        }

        $r .= $t['function'];
        if (isset($t['args']) && sizeof($t['args']) > 0) {
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