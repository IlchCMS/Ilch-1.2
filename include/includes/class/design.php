<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');

if (!isset($ILCH_HEADER_ADDITIONS)) {
    $ILCH_HEADER_ADDITIONS = '';
}
if (!isset($ILCH_BODYEND_ADDITIONS)) {
    $ILCH_BODYEND_ADDITIONS = '';
}
class design extends tpl {
    var $html;
    var $design;
    var $vars;
    var $was;
    var $file;

    function design ($title, $hmenu, $was = 1, $file = null) {
        global $allgAr;

        if (!is_null($file)) {
            echo '<div style="display: block; background-color: #FFFFFF; border: 2px solid #ff0000;">!!Man konnte in einer PHP Datei eine spezielle Index angeben. Damit das Design fuer diese Datei anders aussieht. Diese Funktion wurde ersetzt. Weitere Informationen im Forum auf ilch.de ... Thema: <a href="http://www.ilch.de/forum-showposts-13758-p1.html#108812">http://www.ilch.de/forum-showposts-13758-p1.html#108812</a></div>';
        }

        $this->vars = array();
        $this->file = $file; # setzte das file standart 0 weil durch was definiert
        $this->was = $was; # 0 = smalindex, 1 = normal index , 2 = admin

        $this->design = $this->get_design();
        $link = $this->htmlfile();

        $tpl = new tpl ($link, 2);
        if ($tpl->list_exists ('boxleft')) {
            $tpl->set ('boxleft' , $this->get_boxes ('l', $tpl));
        }
        if ($tpl->list_exists ('boxright')) {
            $tpl->set ('boxright' , $this->get_boxes ('r', $tpl));
        }
        // ab 0.6 =  ... 5 menu listen moeglich
        for($i = 1;$i <= 5;$i++) {
            if ($tpl->list_exists ('menunr' . $i)) {
                $tpl->set ('menunr' . $i , $this->get_boxes ($i, $tpl));
            }
        }

        $ar = array
        ('TITLE' => $this->escape_explode($title),
            'HMENU' => $this->escape_explode($hmenu),
            'SITENAME' => $this->escape_explode($allgAr['title']),
            'hmenuende' => '',
            'vmenuende' => '',
            'hmenubegi' => '',
            'vmenubegi' => '',
            'hmenupoint' => '',
            'vmenupoint' => '',
            'DESIGN' => $this->design
            );
        $tpl->set_ar($ar);
        $this->html = $tpl->get(0);
        $this->html .= '{EXPLODE}';
        $this->html .= $tpl->get(1);
        unset ($tpl);

        $zsave0 = array();
        preg_match_all ("/\{_boxes_([^\{\}]+)\}/" , $this->html , $zsave0);

        $this->replace_boxes($zsave0[1]);
        unset ($zsave0);
        $this->vars_replace();
        unset ($this->vars);

        $this->html = explode('{EXPLODE}', $this->html);
    }

    function addheader($text) {
        if (isset($this->html[0])) {
            $this->html[0] = str_replace('</head>', $text . "\n</head>" , $this->html[0]);
            return true;
        } else {
            return false;
        }
    }

    function header ($addons = '') {
        global $ILCH_HEADER_ADDITIONS;
		$ILCH_HEADER_ADDITIONS .= $this->load_addons($addons);
        $this->addheader($ILCH_HEADER_ADDITIONS);
        echo $this->html[0];
        unset ($this->html[0]);
    }
	
	// Fuegt Dynamische und Statische *.js und *.css Dateien in den Header ein
	// Kann jedoch nur uerber die header-Funktion aufgerufen werden
	function load_addons($addons = '') {
		$buffer = '';
		if ( !is_array ($addons) ) {
			$addons = Array( $addons );
		}
		
		// Ordner nach dynamischen Dateien durchsuchen
		$js = read_ext ('include/includes/js/global', 'js');
		$css = read_ext ('include/includes/css/global', 'css');
		
		// Dynamisches Javascript laden
		foreach ( $js as $file ){
			$buffer .= "\n<script type=\"text/javascript\" src=\"include/includes/js/global/".$file."\"></script>";
		}
		
		// Dynamisches CSS laden
		foreach ( $css as $file ){
			$buffer .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"include/includes/css/global/".$file."\" />";
		}
		
		// Alle statischen Inhalte pruefen
		foreach ( $addons as $addon ) {
			$dir = explode ('.', $addon);
			$dir = end ($dir);
			if ( file_exists ( 'include/includes/'.$dir.'/'.$addon ) ) {
				if ( $dir == 'js' ) {
					$buffer .= "\n<script type=\"text/javascript\" src=\"include/includes/".$dir."/".$addon."\"></script>";
				} else if ( $dir == 'css' ) {
					$buffer .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"include/includes/".$dir."/".$addon."\" />";
				}
			} else {
				$buffer = "\n<script language='javascript'>"
						 ."\nalert('Couldn\'t find the file \"include/includes/".$dir."/".$addon."\"!');"
						 ."\n</script>";
			}
		}
		
		return $buffer;
	}
    function addtobodyend($text) {
        if (isset($this->html[1])) {
            $this->html[1] = str_replace('</body>', $text . "\n</body>" , $this->html[1]);
            return true;
        } else {
            return false;
        }
    }

    function footer ($exit = 0) {
        global $ILCH_BODYEND_ADDITIONS;
        $this->addtobodyend($ILCH_BODYEND_ADDITIONS);
        echo $this->html[1];
        unset ($this->html[1]);
        if ($exit == 1) {
            exit();
        }
    }

    function escape_explode ($s) {
        $s = str_replace('{EXPLODE}', '&#123;EXPLODE&#125;', $s);
        return ($s);
    }

    function htmlfile_ini () {
        global $menu;
        $ma = $menu->get_string_ar();
        $ia = array();
        if (!file_exists('include/designs/' . $this->design . '/design.ini')) {
            return (false);
        }
        $ia = parse_ini_file ('include/designs/' . $this->design . '/design.ini');
        arsort($ma);
        krsort ($ia);
        foreach ($ia as $k => $v) {
            $k = preg_replace("/[^a-zA-Z0-9-*]/", "", $k);
            $k = str_replace('*', '[^-]+', $k);
            foreach ($ma as $k1 => $v1) {
                if (preg_match("/" . $k . "/", $k1) AND file_exists('include/designs/' . $this->design . '/' . $v)) {
                    return ($v);
                }
            }
        }
        return (false);
    }

    function htmlfile () {
        $ini = $this->htmlfile_ini ();
        /*
		if ( !is_null ($this->file) AND file_exists ('include/designs/'.$this->design.'/templates/'.$this->file)) {
      $f = 'designs/'.$this->design.'/templates/'.$this->file;
    } elseif ( !is_null ($this->file) AND file_exists ('include/templates/'.$this->file)) {
      $f = 'templates/'.$this->file;
		*/
        if ($this->was == 1 AND $ini !== false) {
            $f = 'designs/' . $this->design . '/' . $ini;
        } elseif ($this->was == 0 AND file_exists ('include/templates/' . $this->design . '/templates/small_index.htm')) {
            $f = 'templates/' . $this->design . '/templates/small_index.htm';
        } elseif ($this->was == 0) {
            $f = 'templates/small_index.htm';
        } elseif ($this->was == 1) {
            $f = 'designs/' . $this->design . '/index.htm';
        } elseif ($this->was == 2) {
            $f = 'admin/templates/index.htm';
        }
        return ($f);
    }

    function replace_boxes ($zsave0) {
        foreach ($zsave0 as $v) {
            $dat = strtolower($v);
            $buffer = $this->get_boxcontent ($dat);
            if ($buffer !== false) {
                $this->vars['_boxes_' . $v] = $buffer;
            }
        }
        if (!is_array($this->vars)) {
            $this->vars = array();
        }
    }

    function vars_replace() {
        foreach ($this->vars as $k => $v) {
            $this->html = str_replace('{' . $k . '}', $v, $this->html);
        }
    }
    // ####
    function get_boxes ($wo , $tpl) {
        global $lang, $allgAr, $menu;
        if (is_numeric($wo)) {
            $datei = 'menunr' . $wo;
        } elseif ($wo == 'l') {
            $datei = 'boxleft';
            $wo = 1;
        } elseif ($wo == 'r') {
            $datei = 'boxright';
            $wo = 2;
        }

        $retur = '';
        $ex_ebene = 0;
        $ex_was = 1;
        $firstmep = false;
        $hovmenup = '';
        $abf = "SELECT * FROM `prefix_menu` WHERE wo = " . $wo . " AND ( recht >= " . $_SESSION['authright'] . " OR recht = 0 ) ORDER by pos";
        $erg = db_query($abf);
        while ($row = db_fetch_assoc($erg)) {
            $subhauptx = $row['was'];
            $whileMenP = ($subhauptx >= 7 ? true : false);
            if (($row['was'] >= 7 AND $ex_was == 1) OR ($ex_ebene < ($row['ebene'] - 1)) OR ($ex_was <= 4 AND $row['ebene'] != 0) OR ($row['was'] >= 7 AND !$tpl->list_exists($hovmenup))) {
                /*
        echo '<pre>Das Menu ist Fehlerhaft, bitte benachrichtigen Sie den Administrator!';
        echo '<br /><br /><u>Informationen:</u>';
        echo '<br />Region:  '.$row['name'];
        echo '<br />Ebene:   '.$row['ebene'];
        echo '<br />exEbene: '.$ex_ebene;
        echo '<br />Typ:     '.$row['was'];
        echo '<br />exTyp:   '.$ex_was;
        echo '<br /><br /><u>Problemloesung:</u> Die Region gibt an um welchen Menupunkt, welches Menu oder welche Box es sich handelt.';
        echo '<br />Ist der Typ groesser oder 7 und der exTyp 1 wurde ein Menupunkt in einer falschen Position im Menu platziert.';
        echo '<br />Ist die exEbene 2 kleiner als die Ebene ist die Einrueckung im Menu falsch.';
        echo '<br />Sonst mit den oben gegebenen Informationen und einem Screenshot des betreffenden Menus auf <a href="http://www.ilch.de/">ilch.de</a> im Forum melden.';
        echo '<br /><br />Vielen Dank!</pre>';

        $retur  = '<pre>Das Menu ist Fehlerhaft, bitte benachrichtigen Sie den Administrator!';
        $retur .= '<br /><br /><u>Informationen:</u>';
        $retur .= '<br />Region:  '.$row['name'];
        $retur .= '<br />Ebene:   '.$row['ebene'];
        $retur .= '<br />exEbene: '.$ex_ebene;
        $retur .= '<br />Typ:     '.$row['was'];
        $retur .= '<br />exTyp:   '.$ex_was;
        $retur .= '<br /><br /><u>Problemloesung:</u> Die Region gibt an um welchen Menupunkt, welches Menu oder welche Box es sich handelt.';
        $retur .= '<br />Ist der Typ groesser oder 7 und der exTyp 1 wurde ein Menupunkt in einer falschen Position im Menu platziert.';
        $retur .= '<br />Ist die exEbene 2 kleiner als die Ebene ist die Einrueckung im Menu falsch.';
        $retur .= '<br />Sonst mit den oben gegebenen Informationen und einem Screenshot des betreffenden Menus auf <a href="http://www.ilch.de/">ilch.de</a> im Forum melden.';
        $retur .= '<br /><br />Vielen Dank!</pre>';
        $menuzw = '';
        */
                continue;
            }
            // nur wenn ein menu in die variable $menuzw geschrieben wurde
            // wird in diese if abfrage gesprungen
            if (($whileMenP === false) AND !empty($menuzw)) {
                $menuzw .= $this->get_boxes_get_menu_close ($ex_ebene, 0, $menuzw, $wmpE, $wmpTE, $wmpTEE);
                $retur .= $tpl->list_get($datei, array (htmlentities($boxname), $menuzw . $menuzwE));
                $menuzw = '';
            }
            if ($row['was'] == 1) {
                // die box wird direkt in die to return variable geschrieben
                $buffer = $this->get_boxcontent($row['path']);
                $retur .= $tpl->list_get($datei, array ($row['name'] , $buffer));
            } elseif ($row['was'] >= 2 AND $row['was'] <= 4) {
                // der name des menues wird gesetzt
                // und die variable wird gesetzt.
                $boxname = $row['name'];
                $menuzw = '';
                $menuzwE = '';
                $ex_ebene = 0; # ex ebene
                $hovmenu = '';
                if ($row['was'] == 2 AND $tpl->list_exists('hmenupoint')) {
                    $hovmenu = 'hmenu';
                } elseif ($row['was'] == 3 AND $tpl->list_exists('vmenupoint')) {
                    $hovmenu = 'vmenu';
                }
                $firstmep = true;
                if (!empty($hovmenu)) {
                    $menuzw .= $tpl->list_get($hovmenu . 'begi', array());
                    $menuzwE .= $tpl->list_get($hovmenu . 'ende', array());
                }
                $hovmenup = $hovmenu . 'point';
            } elseif ($whileMenP) {
                // menupunkt wird generiert
                $ebene = $row['ebene'];
                $menuTarget = ($subhauptx == 8 ? '_blank' : '_self');
                list ($wmpA, $wmpE, $wmpTE, $wmpTEE) = explode ('|', $tpl->list_get ($hovmenup, array ($menuTarget, ($subhauptx == 8 ? '' : 'index.php?') . $row['path'], $row['name'])));
                if (!empty($menuzw) AND $firstmep === false) {
                    $menuzw .= $this->get_boxes_get_menu_close ($ex_ebene, $ebene, $menuzw, $wmpE, $wmpTE, $wmpTEE);
                }
                $menuzw .= $wmpA;
                $firstmep = false;
            }

            $ex_was = $row['was'];
            $ex_ebene = $row['ebene'];
        }
        if (!empty($menuzw)) {
            $menuzw .= $this->get_boxes_get_menu_close ($ex_ebene, 0, $menuzw, $wmpE, $wmpTE, $wmpTEE);
            $retur .= $tpl->list_get($datei, array (htmlentities($boxname), $menuzw . $menuzwE));
        }
        return ($retur);
    }

    function get_boxes_get_menu_close ($ex_ebene, $ebene, $menuzw, $wmpE, $wmpTE, $wmpTEE) {
        $menu1 = '';
        if ($ex_ebene == $ebene AND !empty($menuzw)) {
            $menu1 .= $wmpE . "\n";
        } elseif ($ex_ebene > $ebene) {
            $menu1 .= $wmpE . "\n";
            for($i = 0;$i < ($ex_ebene - $ebene); $i++) {
                $menu1 .= $wmpTEE . "\n";
            }
        } elseif ($ex_ebene < $ebene) {
            $menu1 .= $wmpTE . "\n";
        }
        return ($menu1);
    }

    function get_boxcontent ($box) {
        global $lang, $allgAr, $menu, $ILCH_HEADER_ADDITIONS, $ILCH_BODYEND_ADDITIONS;
        if (file_exists('include/boxes/' . $box)) {
            $pfad = 'include/boxes/' . $box;
        } elseif (file_exists ('include/contents/selfbp/selfb/' . str_replace('self_', '', $box))) {
            $pfad = 'include/contents/selfbp/selfb/' . str_replace('self_', '', $box);
        } elseif (file_exists('include/boxes/' . $box . '.php')) {
            $pfad = 'include/boxes/' . $box . '.php';
        } elseif (file_exists('include/boxes/' . $box . '.htm')) {
            $pfad = 'include/boxes/' . $box . '.htm';
        } elseif (file_exists ('include/contents/selfbp/selfb/' . str_replace('self_', '', $box) . '.php')) {
            $pfad = 'include/contents/selfbp/selfb/' . str_replace('self_', '', $box) . '.php';
        } elseif (file_exists ('include/contents/selfbp/selfb/' . str_replace('self_', '', $box) . '.htm')) {
            $pfad = 'include/contents/selfbp/selfb/' . str_replace('self_', '', $box) . '.htm';
        } else {
            return (false);
        }
        ob_start();
        require_once($pfad);
        $buffer = $this->escape_explode(ob_get_contents());
        ob_end_clean();
        return($buffer);
    }
}

?>