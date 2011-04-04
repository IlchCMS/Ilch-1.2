<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

class menu {
    private $menu_ar;
    private $type;

    public function __construct($type = 'contents') {
        $this->set_menu_ar();
        $this->type = $type;
    }
    // menustring suchen und finden und zerteilen
    // in die richtige reihenfolge usw. blahhh :)
    private function set_menu_ar($menustr = '') {
        $ar = array();
        if (empty($menustr) and isset($_SERVER[ 'QUERY_STRING' ])) {
            $q = $_SERVER[ 'QUERY_STRING' ];
        } else {
            $q = $menustr;
        }
        if (!empty($q)) {
            $q = preg_replace("/[^a-z0-9-_\&=]/i", "", $q);
            $fu = strpos($q, '&');
            $fi = strpos($q, '=');
            $ende = strlen($q);

            if ($fi !== false AND $fu !== false) {
                if ($fu < $fi) {
                    $ende = $fu;
                } elseif ($fi < $fu) {
                    $ende = $fi;
                }
            } elseif ($fu !== false) {
                $ende = $fu;
            } elseif ($fi !== false) {
                $ende = $fi;
            }
            $qs = substr($q, 0, $ende);
            $ar = explode('-', $qs);
        }
        $this->menu_ar = $ar;
    }
    // der url reseten (wichtig im adminbereich) fals ein user
    // nicht die entsprechenden rechte hat... wird der query
    // string des objekts manipuliert so das eine andere seite
    // angezeigt wird...
    public function set_url($index, $wert) {
        $index = escape($index, 'integer');
        $wert = preg_replace("/[^a-z0-9-_]/i", "", $wert);
        $this->menu_ar[ $index ] = $wert;
        return (true);
    }
    public function set_complete_url($newmenustring) {
        $this->set_menu_ar($newmenustring);
    }

    // gibt ein array mit strings aus was alle sinnvollen kombinationen des menu_ar enthaelt
    public function get_string_ar($remove_self = false) {
        $s = '';
        $a = array();
        if ($remove_self) {
            $menu_ar = array_slice($this->menu_ar, 1);
        } else {
            $menu_ar =& $this->menu_ar;
        }
        foreach ($menu_ar as $k => $v) {
            if ($s == '') {
                $s .= $v;
            } else {
                $s .= '-' . $v;
            }
            $a[ $s ] = $s;
        }
        return ($a);
    }

    //prüft ob der User das Recht hat die Seite zu betrachten,
    //gibt true zurück wenn das Recht vorhanden ist
    private function check_rights() {
        global $allgAr;
        $has_right = false;
        if ($this->type == 'contents') {
            $paths = array();
            foreach ($this->get_string_ar() as $path){
                $paths[] = '\'' . $path . '\'';
            }
            if ($this->get(0) == 'self') {
                foreach ($this->get_string_ar(true) as $path){
                    $paths[] = '\'' . $path . '\'';
                }
            }
            $qry = db_query('SELECT `recht`, `recht_type` FROM `prefix_menu` WHERE `was` IN (7,9) AND `path` IN ('.implode(',', $paths).') ORDER BY LENGTH(`path`), `recht_type`, `recht`');
            $lastlength = 0;
            while($row = db_fetch_assoc($qry)){
                $pathlength = strlen($row['path']);
                if ($has_right or ($lastlength != 0 and $lastlength != $pathlength)) {
                    break;
                } else {
                    $lastlength = $pathlength;
                }
                switch($row['recht_type']){
                    case 0: case 3: default:
                        $has_right = has_right($row['recht'], '', true);
                        break;
                    case 1:
                        $has_right = $row['recht'] == $_SESSION['authright'];
                        break;
                    case 2:
                        $has_right = $row['recht'] <= $_SESSION['authright'];
                        break;
                }
            }
        } elseif ($this->type == 'box') {
            $qry = db_query('SELECT `recht`, `recht_type` FROM `prefix_menu` WHERE `was` = 1 AND `path` = "' . $this->get(0) . '.php"');
            while($row = db_fetch_assoc($qry)){
                $pathlength = strlen($row['path']);
                if ($has_right) {
                    break;
                }
                switch($row['recht_type']){
                    case 0: case 3: default:
                        $has_right = has_right($row['recht'], '', true);
                        break;
                    case 1:
                        $has_right = $row['recht'] == $_SESSION['authright'];
                        break;
                    case 2:
                        $has_right = $row['recht'] <= $_SESSION['authright'];
                        break;
                }
            }
        }
        return $this->type == 'admin' || $has_right || $allgAr['allg_menupoint_access'] == 1;
    }

    // diese funktion wird nur im admin.php und index.php
    // aufgerufen. is aber relativ zentral gell weil ohne
    // deren ok und rueckgabe laueft gar nix :)...
    public function get_url($w = '') {
        global $allgAr;
        if ($w != '') {
            $this->type = $w;
        }
        // startwert und pfad zum pruefen raustuefteln.
        if ($this->type == 'contents') {
            $pfad = 'include/contents';
            $smod = $allgAr[ 'smodul' ];
        } elseif ($this->type == 'box') {
            $file = 'include/boxes/' . $this->get(0) . '.php';
            return (file_exists($file) and $this->check_rights()) ? $file : false;
        } else {
            $pfad = 'include/admin';
            $smod = 'admin';
        }
        // wennes also leer is wird das startmodul genommen
        if (empty($this->menu_ar[ 0 ])) {
            $this->set_url(0, $smod);
        }
        // diverse sachen geprueft zum zurueck geben,
        // is halt so dings wegen selfpages usw...
        if (!file_exists($pfad . '/' . $this->get(0) . '.php') AND file_exists($pfad . '/selfbp/selfp/' . $this->get(0) . '.php')) {
            $this->set_url(1, $this->get(0));
            $this->set_url(0, 'self');
        } elseif (!file_exists($pfad . '/' . $this->get(0) . '.php')) {
            if (substr($smod, 0, 5) == 'self-') {
                $this->set_url(1, substr($smod, 5));
                $this->set_url(0, 'self');
            } elseif (file_exists($pfad . '/selfbp/selfp/' . $smod . '.php')) {
                $this->set_url(1, $smod);
                $this->set_url(0, 'self');
            } else {
                $this->set_url(0, $smod);
            }
        }
        // pruefen ob der client die noetigen rechte hat
        // das modul zu sehen.. bzw. den menupunkt zu sehen
        $exit = ! $this->check_rights();
        // das usermodul kann aus eigener sicherheit nicht
        // gesperrt werden, sonst koennen sich member
        // usw. nicht mehr einloggen, bzw. es kann
        // sich sonst keiner registrieren. deshalb is das
        // user modul immer frei geschaltet
        $alwaysallowed = array(
            'regist',
            'login',
            '1',
            '2',
            'confirm',
            'remind',
            '13',
            '3',
            'logout'
            );
        if ($exit === true AND $this->get(0) == 'user' AND in_array($this->get(1), $alwaysallowed)) {
            $exit = false;
            debug('o');
        }

        if ($exit) {
            $title = $allgAr[ 'title' ] . ' :: Keine Berechtigung';
            $hmenu = 'Keine Berechtigung';
            $design = new design($title, $hmenu);

            if (loggedin()) {
                $design->header();
                if (is_coadmin()) {
                    echo 'Entweder diese Seite ist nicht in der Navigation verlinkt und die Option
<strong>Zugriff auf nicht im Menü verlinkte Module für alle?</strong> steht auf <strong>nein</strong> oder aber du hast kein Recht sie zu betrachten, deswegen kommt diese Meldung.<br />
Also entweder die Seite <strong>' . $this->get(0) . '</strong> in der <a href="admin.php?menu">Navigation</a> verlinken, oder die Option umstellen, ersteres wird empfohlen.';
                } else {
                    echo 'Du hast leider nicht die n&ouml;tigen Rechte, um diese Seite zu betrachten.';
                }
            } else {
                $tpl = new tpl('user/login');
                $design->addheader($tpl->get(0));
                $design->header();
                $tpl->set_out('WDLINK', 'index.php', 1);
            }
            $design->footer();
            exit();
        }

        return ($this->get(0) . '.php');
    }
    // ersten buchstaben erhalten
    // zb. wichtig fuer strings p1 (page nr 1)...
    public function getA($x) {
        $x = substr($this->get($x), 0, 1);
        return ($x);
    }
    // bei $x int alles nach dem ersten buchstaben erhalten z.b. die nummer der page..s.o
    // bei $x string -> direkt getE('p') und 1 zu erhalten, falls -p1
    public function getE($x) {
        if (is_int($x)) {
            $x = substr($this->get($x), 1);
        } else {
            $ar = array_filter($this->menu_ar, create_function('$a', 'return (preg_match("/^' . $x . '\d+$/",$a) == 1);'));
            $x = substr(array_shift($ar), 1);
        }
        $x = escape($x, 'integer');
        return ($x);
    }
    // Bsp. ?test-next
    // getN('test') -> 'next'
    public function getN($x) {
        if (in_array($x, $this->menu_ar)) {
            $t = array_search($x, $this->menu_ar);
            return $this->menu_ar[ $t + 1 ];
        }
        return false;
    }
    // Prüft ob ein Eintrag vorhanden ist
    public function exists($x) {
        return in_array($x, $this->menu_ar);
    }
    // hier wird ein spzeiller teil
    // des querystrings abgefragt
    public function get($n) {
        if (isset($this->menu_ar[ $n ])) {
            return ($this->menu_ar[ $n ]);
        } else {
            return ('');
        }
    }
    // Gibt das Adminmenu in einem Array zurueck
    public function get_menu() {
        $menuAr = Array();
        $kat = '';

        $erg = db_query("SELECT * FROM `prefix_modules` WHERE `menu` != '' ORDER BY `menu`, `pos` ASC");
        while ($row = db_fetch_assoc($erg)) {
            $menuAr[ $row[ 'url' ] ] = Array(
                'name' => $row[ 'name' ],
                'menu' => $row[ 'menu' ]
                );
        }

        return $menuAr;
    }
    // gibt den kompletten "Pfad" aus
    public function get_complete() {
        return implode('-', $this->menu_ar);
    }
}

?>