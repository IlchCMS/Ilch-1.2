<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

/**
 * PasswdCrypt
 *
 * @author finke <Surf-finke@gmx.de>
 * @copyright Copyright (c) 2012
 */
class PasswdCrypt{
    //Konstanten für den Zufallszahlen Generator
    const ONLY_LETTERS = 0;
    const WITH_NUMBERS = 1;
    const WITH_SPECIAL_CHARACTERS = 2;

    //Konstanten für die Verschlüsselung
    const MD5 = '1';
    const BLOWFISH = '2a';
    const SHA256 = '5';
    const SHA512 = '6';

    private $hashAlgorithm = self::SHA256;

    /**
     * @param string $lvl Gibt den zu verwendenden Hashalgorithmus an (Klassenkonstante)
     */
    public function __construct($lvl = ''){
        mt_srand();

        if(preg_match('/^([156]|2a)$/',$lvl)){
            $this->hashAlgorithm = $lvl;
        }

        if(version_compare(PHP_VERSION, '5.3.0', '<')){    //Prüfen welche Hash Funktionen Verfügbar sind. Ab 5.3 werden alle Mitgeliefert
            if($this->hashAlgorithm == self::SHA512 && !defined('CRYPT_SHA512') && CRYPT_SHA512 !== 1){
                $this->hashAlgoriathm = self::SHA256; // Wenn SHA512 nicht verfügbar, versuche SHA256
            }
            if($this->hashAlgorithm == self::SHA256 && !defined('CRYPT_SHA256') && CRYPT_SHA256 !== 1){
                $this->hashAlgorithm = self::BLOWFISH; // Wenn SHA256 nicht verfügbar, versuche BLOWFISH
            }
            if($this->hashAlgorithm == self::BLOWFISH && !defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH !== 1){
                $this->hashAlgorithm = self::MD5; // Wenn BLOWFISH nicht verfügbar, nutze MD5
            }
        }
    }

    /**
     * Erstellt eine zufällige Zeichenkette
     *
     * @param integer $size Länge der Zeichenkette
     * @param integer $chars Angabe welche Zeichen für die Zeichenkette verwendet werden
     * @return string
     */
    public static function getRndString($size = 20, $chars = self::ONLY_LETTERS) {
        $pool = 'abcdefghijklmnopqrstuvwxyz';
        $pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if($chars & self::WITH_NUMBERS){
            $pool .='0123456789';
        }

        if($chars & self::WITH_SPECIAL_CHARACTERS){
            $pool .= ',.-;:_#+*~!$%&/()=?';
        }

        $pool = str_shuffle($pool);
        $pool_size = strlen($pool);
        $string ='';
        for($i = 0; $i < $size; $i++){
            $string .= $pool[mt_rand(0, $pool_size - 1)]; //!TODO Zufallszahlen aus /dev/random bzw /dev/urandom wenn verfügbar
        }
        return $string;
    }

    /**
     * Prüft, ob der übergebene Hash, im crpyt Format ist
     *
     * @param mixed $hash
     * @return boolean
     */
    public static function isCryptHash($hash) {
        return preg_match('/^\$([156]|2a)\$?/', $hash) === 1;
    }

    /**
     * Gibt den Code der gewählten/genutzen Hashmethode zurück (Crpyt Konstante)
     *
     * @return string
     */
    public function getHashAlgorithm() {
        return $this->hashAlgorithm;
    }

    /**
     * Erstellt ein Hash für das übergebene Passwort
     *
     * @param string $passwd Klartextpasswort
     * @param string $salt Salt für den Hashalgorithus
     * @param integer $rounds Anzahl der Runden für den verwendeten Hashalgorithmus
     * @return string Hash des Passwortes (Ausgabe von crypt())
     */
    public function cryptPasswd($passwd, $salt = '', $rounds = 0) {
        $salt_string = '';
        switch($this->hashAlgorithm){
            case self::SHA512:
            case self::SHA256:
                $salt = (empty($salt)?self::getRndString(16, self::WITH_NUMBERS):$salt);
                if($rounds < 1000 || $rounds >    999999999){
                    $rounds = mt_rand(2000,10000);
                }
                $salt_string = '$'.$this->hashAlgorithm.'$rounds='.$rounds.'$'.$salt.'$';
            break;
            case self::BLOWFISH:
                $salt = (empty($salt)?self::getRndString(22, self::WITH_NUMBERS):$salt);
                if($rounds < 4 || $rounds > 31){
                    $rounds = mt_rand(6,10);
                }
                $salt_string = '$'.$this->hashAlgorithm.'$'.$rounds.'$'.$salt.'$';
            break;
            case self::MD5:
                $salt = (empty($salt)?self::getRndString(12, self::WITH_NUMBERS):$salt);
                $salt_string = '$'.$this->hashAlgorithm.'$'.$salt.'$';
            break;
            default:
                return false;
        }
        $crypted_pw = crypt($passwd, $salt_string);
        if(strlen($crypted_pw) < 13){
            return false;
        }
        return $crypted_pw;
    }

    /**
     * Prüft, ob das Klartextpasswort dem Hash "entspricht"
     *
     * @param mixed $passwd Klartextpasswort
     * @param mixed $crypted_passwd Hash des Passwortes (aus der Datenbank)
     * @return boolean
     */
    public function checkPasswd($passwd, $crypted_passwd) {
        if (empty($crypted_passwd)) {
            return false;
        }
        if (self::isCryptHash($crypted_passwd)) {
            $new_chrypt_pw = crypt($passwd, $crypted_passwd);
            if (strlen($new_chrypt_pw) < 13) {
                return false;
            }
        } else {
            $new_chrypt_pw = md5($passwd);
        }
        if ($new_chrypt_pw == $crypted_passwd) {
            return true;
        } else {
            return false;
        }
    }
}
