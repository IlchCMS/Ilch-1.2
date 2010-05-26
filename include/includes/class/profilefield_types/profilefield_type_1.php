<?php

/**
 * Normales Profilfeld mit Text-Input
 *
 * @author Korbinian
 */
class ProfileField extends AbstractProfileFieldType {
    protected $name = "Feld";

    public function renderProfileEdit($ar) {
        $tpl = new tpl("user/profil_edit");
        $tpl->set_ar_out($ar, "profilefield");
    }
}

/* EOF */