<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
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

if (DEBUG) {
    $ILCH_DEBUG_OUTPUT = '';
    /**
     * debug_nice_filename()
     * macht den Dateinamen kürzer und lesbarer (auf ilch Ordner Struktur bezogen)
     *
     * @param string $filename kompletten Dateinamen
     * @return string gekürzten Dateinamen
     */
    function debug_nice_filename($filename) {
        return str_replace(dirname($_SERVER['SCRIPT_FILENAME']) . '/', '', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
    }
    /**
     * debug()
     * Einfach Debugfunktion, mit Zeitausgabe
     *
     * @param mixed $d Gibt an was fürs Debugging ausgegeben werden soll, wenn leer wird der Datename + Zeilennummer angegeben
     * @param boolean $time Gibt an ob die Zeit mit ausgegeben
     */
    function debug($d = '', $time = true) {
        global $ILCH_DEBUG_OUTPUT;
        if ($time) {
            $time = round(microtime(true) - SCRIPT_START_TIME, 5) . ': ';
        } else {
            $time = '';
        }
        if (empty($d)) {
            // Filename + Linenumber ( + time);
            $array = debug_backtrace();
            $filename = debug_nice_filename($array[0]['file']);
            $ILCH_DEBUG_OUTPUT .= '<div>' . $time . $filename . ' - Line: ' . $array[0]['line'] . '</div>';
        } elseif (is_array($d) or is_object($d)) {
            if (DEVELOPER_MODE) {
              $d = manipulate_debug_output($d);
            }
            $ILCH_DEBUG_OUTPUT .= '<div style="white-space:pre">' . $time . var_export($d, true) . '</div>';
        } else {
            if (is_bool($d)) {
                $d = 'Bool: ' . ($d ? 'true' : 'false');
            }
            $ILCH_DEBUG_OUTPUT .= '<div>' . $time . $d . '</div>';
        }
    }

    /**
     * Funktion parst den backtrace output und kann seine ausgabe verändern
     *
     * @return array
     * @author annemarie`
     **/
    function manipulate_debug_output($d)
    {
      foreach ($d as $key => $rvalue) {
        if (!isset($rvalue['query'])) {
            return $d;
        }
        $q  = $rvalue['query'];
        if (!empty($q)) {
          if (preg_match('/(SELECT){1}/i',$q,$match)) {
            $new_query = link_querys($q, $key);
            $d[$key]['query'] = $new_query;
            $_SESSION['debug_backtrace']['SELECT'][$key] = $q;
          }
        }
      }
      return $d;
    }

    /**
     * soll später einen mysql query verlinken um seine ergebnisse angezeigt zu bekommen
     *
     * @return string
     * @author annemarie
     **/
    function link_querys($old_query, $key = NULL, $function = NULL, $href = 'javascript:FUNCTION;')
    {
      if ((preg_match('/(javascript:)(FUNCTION)(;)/i',$href, $match) AND !is_NULL($function)) OR preg_match('/(javascript:)(FUNCTION)(;)/i',$href, $match)) {
        $href = $match[1] . 'alert(' . $key . ')' . $match[3];
      }
      $new_query = '';
      $new_query = '<a href="';
      $new_query .= $href;
      $new_query .= '" title="Query ausf&uuml;hren">QUERY #'.$key.' ' . $old_query;
      $new_query .= '</a>';
      return $new_query;
    }

    /**
     * debug_bt()
     * from php.net
     * Gibt ein formatiertes debug_backtrace als String zurück
     * @return string
     */
    function debug_bt() {
        if (!function_exists('debug_backtrace')) {
            return 'function debug_backtrace does not exists' . "\r\n";
        }
        $r = 'Debug backtrace:' . "\r\n";
        $i = 0;

        foreach (debug_backtrace() as $t) {

            $i++;
            if ($i == 1) {
                continue;
            }
            $r .= "\t" . '@ ';
            if (isset($t[ 'file' ])) {
                $r .= debug_nice_filename($t[ 'file' ]) . ':' . $t[ 'line' ];
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
    /**
     * debug_out()
     * Gibt gespeicherte Debugmeldungen aus
     */
    function debug_out() {
        global $ILCH_DEBUG_OUTPUT, $ILCH_DEBUG_DB_QUERIES, $ILCH_DEBUG_DB_COUNT_QUERIES;

        debug('Scriptlaufzeit: ' . round(microtime(true) - SCRIPT_START_TIME, 5) . ' secs');
        debug('anzahl sql querys: ' . $ILCH_DEBUG_DB_COUNT_QUERIES);
        debug($ILCH_DEBUG_DB_QUERIES);

?>
<script type="text/javascript">
function toggleDebugDiv() {
    var div = document.getElementById('debugDiv');
    if (div.style.display == 'none') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
</script>
<style type="text/css">
#debugButton {position:absolute; top:0px; left:0px; display:block; width:50px; height:20px; line-height:20px; vertical-align:middle; border: 1px solid black; background: #ff9;}
#debugDiv {position: absolute; top:30px; left:50px; overflow: scroll; width:90%; height: 90%; background-color:#FFFFFF; border:1px solid grey; color: #000000; z-index: 1000;}
#debugDiv div {border-bottom: 1px dashed black; text-align: left;}
</style>
<div id="debugButton"><a href="javascript:toggleDebugDiv();">Debug</a></div>
<div id="debugDiv" style="display: none;">
<?php echo $ILCH_DEBUG_OUTPUT; ?>
</div>
<?php
    }
} else {
    function debug($d = '', $time = true) {}
    function debug_bt() {
    }
    function debug_out() {
    }
}

?>