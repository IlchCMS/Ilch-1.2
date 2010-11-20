<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

abstract class AbstractProfileFieldType {
    protected $name;

    protected $defaultUserValue = "";

    public abstract function renderProfileEdit($ar);

    public function getName() {
        return $this->name;
    }

    /**
     * Macht aus den Werten die Variable für die Config
     * Die Werte kommen aus dem Adminmenü, wenn ein neues
     * Profilfeld angelegt bzw. editiert wird.
     *
     * Achtung! Noch nicht serialisieren, sondern einen Array zurückgeben!
     *
     * @param array $ar die Werte
     */
    public function getConfigValue($ar) {
        return $ar;
    }

    public function setConfigValue($ar) {
        return $ar;
    }

    public function update($ar) {
    }

    public function insert($ar) {
        $pos = db_count_query("SELECT COUNT(*) as `anz` FROM `prefix_profilefields`");
        $config_value = serialize($this->getConfigValue($ar));
        db_query(sprintf("INSERT INTO `prefix_profilefields` (
								`id` ,
								`show` ,
								`pos` ,
								`func` ,
								`config_value`
							)
						VALUES (
							NULL , '%s', '%d', '%d', '%s'
						);" , $ar["show"], $pos, $ar["func"], $config_value));
    }

    public function get($id) {
        $erg = db_query(sprintf("SELECT * FROM `prefix_profilefields` WHERE `id` = %d LIMIT 1", $id));
        if (db_num_rows($erg) != 1) throw new Exception("Could not find entry with id = " . $id);
        else {
            $row = db_fetch_assoc($erg);
            $row["config_value"] = unserialize($row["config_value"]);
            $row = $this->setConfigValue($row);
            return $row;
        }
    }

    public function getUserValue($fieldId, $userId) {
        $erg = db_query(sprintf("SELECT * FROM `prefix_userfields` WHERE `fid` = %d AND `uid` = %d", $fieldId, $userId));
        if (db_num_rows($erg) != 1) return $this->defaultUserValue;
        else {
            $row = db_fetch_assoc($erg);
            return $row["val"];
        }
    }

    /**
     * rendert die "Extra Info" im Adminmenü,
     * die nachgeladen wird, wenn die entsprechende Klasse gewählt wird
     *
     * standardmäßig leer
     *
     * @param array $ar die Informationen über das zu rendernde Profilfeld
     * Kann bis auf $ar["func"] leer sein, wenn ein neues Profilfeld erstellt wird
     *
     * TODO ausgabe in tpl auslagern
     */
    public function renderAdmin($ar) {
    }

    /**
     * wird aufgerufen, wenn das Element vom Stack entfernt wird
     *
     * TODO: sollte hier im adminmenü, profil, edit nicht versch. fkt aufgerufen werden?
     */
    public function removedFromStack($value) {
    }
}

/* EOF */