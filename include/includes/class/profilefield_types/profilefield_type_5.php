<?php

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