<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

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