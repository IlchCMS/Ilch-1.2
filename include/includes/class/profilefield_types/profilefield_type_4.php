<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

class SliderProfileField extends AbstractProfileFieldType {
    protected $name = "Slider";

    private $javascript_printed = false;

    private $tpl;

    public function __construct() {
        $this->tpl = new tpl("user/profilefield/slider");
    }

    public function renderProfileEdit($ar) {
        if (!$this->javascript_printed) {
            $this->tpl->out("slider javascript");
            $this->javascript_printed = true;
        }
        if (!isset($ar["val"])) $ar["val"] = 0;
        $this->tpl->set_ar_out($ar, "slider");
    }

    public function renderAdmin($ar) {
        $ar = array_set_missing_keys($ar, array("textlinks" => "", "textrechts" => ""));
        $this->tpl->set_ar_out($ar, "slider admin");
    }

    public function setConfigValue($ar) {
        $ar["textlinks"] = $ar["config_value"]["textlinks"];
        $ar["textrechts"] = $ar["config_value"]["textrechts"];
        return $ar;
    }
}