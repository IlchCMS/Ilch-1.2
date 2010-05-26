<?php
// Copyright by Manuel Staechele
// Support www.ilch.de
defined('main') or die('no direct access');

class tpl {
    var $parts;
    var $keys;
    var $lists;
    var $lang;
    var $ort;

    /**
     * Der Konstruktor dieser Klasse
     *
     * Initialisiert das Template mit einer Datei (Ausnahme: $ort = 3),
     * die als Template verwendet werden soll.
     * Wie Templates genau aussehen, ist in der Entwicklerdoku beschrieben
     *
     * @param  $file , die Datei aus der das Template geladen werden soll
     * Einzige Ausnahme $ort = 3, dann steht das Template direkt als String
     * in $file
     * @param  $ort $ort = 0: das Template liegt im Ordner include/templates (normale Templates)
     * $ort = 1: das Template liegt in include/admin/templates (Admintemplates)
     * $ort = 2: anderer Ort, Pfad ist ab include/ (sonstige Templates)
     * $ort = 3: das Template ist schon in $file geladen
     */
    function tpl($file, $ort = 0) {
        // die arrays initialisieren
        $this->parts = array();
        $this->keys = array();
        $this->lists = array();
        $this->lang = array();
        $this->ort = $ort;
        // die Variable __BBCodeButtons__ wird immer durch die Buttons ersetzt
        $this->keys[ '__BBCodeButtons__' ] = getBBCodeButtons(); //BBCode2.0 Buttons
        // file bearbeiten, weil file auch ohne .htm angegeben werden kann.
        if (($ort != 3) AND (substr($file, - 4) != '.htm')) {
            $file .= '.htm';
        }
        // ort 0 = include/templates, ort 1 = include/admin/templates
        // bei ort 0 wird ausserdem gecheckt ob das template
        // evtl. im ordner include/design/DESIGN/templates liegt.
        // ort = 2 das template kommt von der design classe der pfad ist ab include
        // ort = 3 das template ist schon in der Variable $file geladen
        $design = $this->get_design();
        if ($this->ort == 0) {
            if (file_exists('include/designs/' . $design . '/templates/' . $file)) {
                $file = 'include/designs/' . $design . '/templates/' . $file;
            } else {
                $file = 'include/templates/' . $file;
            }
        } elseif ($this->ort == 1) {
            $file = 'include/admin/templates/' . $file;
        } elseif ($this->ort == 2) {
            $file = 'include/' . $file;
        } elseif ($this->ort == 3) {
            $inhalt = $file;
        }

        if ($ort != 3) {
            if (file_exists($file)) {
                $inhalt = implode("", file($file));
            } else {
                $dbbtrc = debug_backtrace();
                die('Template nicht gefunden.' . "\n" . '<!-- ' . $file . "\n" . ' <pre>' . print_r($dbbtrc[1], true) . '</pre> -->');
            }
        }
        // jetzt steckt in $inhalt der Inhalt des Templates
        // Sprachdateien einfügen
        global $lang;
        $this->lang = $lang;
        $inhalt = $this->replace_lang($inhalt);
        // Listen einfügen
        $inhalt = $this->replace_list($inhalt);
        // die einzelnen Teile setzen
        // wir splitten bei {EXPLODE[X]}, wobei [X] beliebig viele zeichen sind
        // dadurch können auch die Explode mit Keys ganz normal über
        // deren index angesprochen werden
        // siehe Entwicklerdokumentation
        $this->parts = preg_split('/\{EXPLODE( ".*")?}/', $inhalt);
        // die keys für {EXPLODE "my_key"} setzen
        $this->set_assoc_keys($inhalt);
    }

    function get_design() {
        if (file_exists('include/designs/' . $_SESSION[ 'authgfx' ] . '/index.htm')) {
            return ($_SESSION[ 'authgfx' ]);
        } elseif (file_exists('include/designs/ilchClan/index.htm')) {
            return ('ilchClan');
        } else {
            $od = opendir('include/designs');
            while ($f = readdir($od)) {
                if (file_exists('include/designs/' . $f . '/index.htm')) {
                    return ($f);
                    break;
                }
            }
            closedir($od);
        }
    }

    function replace_lang($var) {
        $lang_zwischenspeicher = array();
        preg_match_all("/\{_lang_([^\{\}]+)\}/", $var, $lang_zwischenspeicher);
        foreach ($lang_zwischenspeicher[ 1 ] as $v) {
            if (empty($this->lang[ $v ])) {
                $this->lang[ $v ] = str_replace('_', '', $v);
            }
            $var = str_replace('{_lang_' . $v . '}', $this->lang[ $v ], $var);
        }
        return ($var);
    }

    function replace_list($var) {
        $zwischenspeicher = array();
        preg_match_all("/\{_list_([^\{\}]+)\}/", $var, $zwischenspeicher);
        foreach ($zwischenspeicher[ 1 ] as $v) {
            list($key, $val) = explode('@', $v);
            $this->lists[ $key ] = $val;
            $var = str_replace('{_list_' . $v . '}', '{' . $key . '}', $var);
        }
        return ($var);
    }

    function list_get($key, $ar) {
        $zwischenspeicher = $this->lists[ $key ];
        krsort($ar);
        foreach ($ar as $k => $v) {
            $i = $k + 1;
            $zwischenspeicher = str_replace('%' . $i, $v, $zwischenspeicher);
        }
        return ($zwischenspeicher);
    }

    function list_exists($key) {
        if (isset($this->lists[ $key ])) {
            return (true);
        } else {
            return (false);
        }
    }

    function list_out($key, $ar) {
        echo $this->list_get($key, $ar);
    }

    function set($k, $v) {
        // $this->keys[$k] = unescape($v);
        $this->keys[ $k ] = $v;
    }

    function set_ar($ar) {
        foreach ($ar as $k => $v) {
            // $this->keys[$k] = unescape($v);
            $this->keys[ $k ] = $v;
        }
    }

    function set_ar_out($ar, $pos) {
        $this->set_ar($ar);
        $this->out($pos);
    }

    /**
     * Die assoziativen Keys für das Template setzen
     * Ein assoziativer Key wird über {EXPLODE "my_key"} gesetzt
     * für nähere Informationen siehe Entwicklerdokumentation
     *
     * @param  $inhalt der String, aus dem die assoziativen Keys gelesen werden sollen
     */
    function set_assoc_keys($inhalt) {
        // wir splitten zunächst bei [{EXPLODE]
        $parts = preg_split("/{EXPLODE/", $inhalt);
        // den ersten teil müssen wir wegschmeißen, da er
        // sicher irrelevant ist
        array_splice($parts, 0, 1);

        foreach($parts as $explodeKey => $part) {
            // wir splitten bei } und nehmen uns dann das erste ergebnis
            $keyParts = preg_split("/}/", $part);
            $key = $keyParts[0];
            // jetzt noch die anführungszeichen entfernen, also das
            // erste und das letzte zeichen
            $key = substr($key, 2, sizeof($key) - 2);

            if ($key != "") {
                // und dann in parts setzen
                array_splice($keyParts, 0, 1);
                $this->parts[$key] = implode("}", $keyParts);
            }
        }
    }

    function set_out($k, $v, $pos) {
        $this->set($k, $v);
        $this->out($pos);
    }

    function set_ar_get($ar, $pos) {
        $this->set_ar($ar);
        return ($this->get($pos));
    }

    function set_get($k, $v, $pos) {
        $this->set($k, $v);
        return ($this->get($pos));
    }

    function del($k) {
        unset($this->keys[ $k ]);
    }

    function del_ar($ar) {
        foreach ($ar as $k => $v) {
            unset($this->keys[ $k ]);
        }
    }

    function parse_if_do($tr) {
        if ($tr[ 1 ] == 'SESSION_AUTHRIGHT') {
            $this->keys[ $tr[ 1 ] ] = $_SESSION[ 'authright' ];
        }
        if (isset($this->keys[ $tr[ 1 ] ]) AND (($tr[ 2 ] == '==' AND $this->keys[ $tr[ 1 ] ] == $tr[ 3 ]) OR (($tr[ 2 ] == '!=' OR $tr[ 2 ] == '<>') AND $this->keys[ $tr[ 1 ] ] != $tr[ 3 ]) OR ($tr[ 2 ] == '<=' AND $this->keys[ $tr[ 1 ] ] <= $tr[ 3 ]) OR ($tr[ 2 ] == '>=' AND $this->keys[ $tr[ 1 ] ] >= $tr[ 3 ]) OR ($tr[ 2 ] == '<' AND $this->keys[ $tr[ 1 ] ] < $tr[ 3 ]) OR ($tr[ 2 ] == '>' AND $this->keys[ $tr[ 1 ] ] > $tr[ 3 ]))) {
            return ($tr[ 4 ]);
        } elseif (isset($this->keys[ $tr[ 1 ] ]) AND isset($tr[ 6 ])) {
            return ($tr[ 6 ]);
        }
        return ('');
    }

    function parse_if($pos) {
        $toout = $this->parts[ $pos ];

        $toout = preg_replace_callback("/\{_if_\{([^\}]+)\}(==|!=|<>|<|>|<=|>=)'([^']+)'\}(.*)(\{_else_\}(.*))?\{\/_endif\}/Us", array(&$this,
                'parse_if_do'
                ), $toout);

        return ($toout);
    }

    function get($pos) {
        $toout = $this->parse_if($pos);

        mt_srand((double) microtime() * 1000000);
        $z = '##@@' . mt_rand() . '@@##';

        foreach ($this->keys as $k => $v) {
            $toout = str_replace('{' . $k . '}', '{' . $z . $k . '}', $toout);
        }

        foreach ($this->keys as $k => $v) {
            $toout = str_replace('{' . $z . $k . '}', $v, $toout);
        }
        return ($toout);
    }

    function out($pos) {
        echo $this->get($pos);
    }
}