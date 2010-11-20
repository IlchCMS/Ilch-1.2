<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

/**
 * Die Klasse für Seiten
 *
 * @author lookout
 */
class PageProfileFieldType extends AbstractProfileFieldType {
    private $tpl;

    protected $name = "Seite";

    public function __construct() {
        $this->tpl = new tpl("user/profilefield/page");
    }

    public function renderProfileEdit($ar) {
        ProfilefieldRegistry::pushToStack($ar["func"], array(), 1);
        $this->tpl->out("page start");
    }

    public function removedFromStack($ar) {
        $this->tpl->out("page end");
    }
}