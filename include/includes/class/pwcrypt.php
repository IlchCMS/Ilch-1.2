<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

mt_srand();

/**
 * PwCrypt
 *
 * @author finke <Surf-finke@gmx.de>
 * @copyright Copyright (c) 2012
 */
class PwCrypt{  //Achtung beim Übertragen von mit 2a erzeugten Passwörtern auf einen anderen PC, dort kann es uU Passieren, das eine Authentifikation nicht mehr möglich ist. Versuche dann bitte 2x bzw 2y
    //Konstanten für den Zufallszahlen Generator
    const LETTERS = 1;	//0001
    const NUMBERS = 2;	//0010
	const FOR_ULR = 7;	//0111
    const SPECIAL_CHARACTERS = 8;	//1000
	

    //Konstanten für die Verschlüsselung
    const MD5 = '1';
    const BLOWFISH_OLD = '2a'; //2a liefert auf einigen System fehlerhafte Ergebnisse. Wenn verfügbar leiber 2x verwenden
	const BLOWFISH = '2x';
	const BLOWFISH_FALSE = '2y';
    const SHA256 = '5';
    const SHA512 = '6';

    private $hashAlgorithm = self::SHA256;

    /**
     * @param string $lvl Gibt den zu verwendenden Hashalgorithmus an (Klassenkonstante)
     */
    public function __construct($lvl = ''){

        if(!empty($lvl)){
            $this->hashAlgorithm = $lvl;
        }
		//Wenn 2a gewählt aber 2x verfügbar: nutze trotzdem 2x, da dies sicherer ist; wenn 2x oder 2y gewählt, aber nicht verfügbar, nutze 2a
		if(version_compare(PHP_VERSION, '5.3.5', '<') && ($this->hashAlgorithm == self::BLOWFISH || $this->hashAlgorithm == self::BLOWFISH_FALSE){
			$this->hashAlgorithm == self::BLOWFISH_OLD;
		}elseif(version_compare(PHP_VERSION, '5.3.5', '>=') && $this->hashAlgorithm == self::BLOWFISH_OLD){
			$this->hashAlgorithm = self::BLOWFISH;
		}

        if(version_compare(PHP_VERSION, '5.3.0', '<')){    //Prüfen welche Hash Funktionen Verfügbar sind. Ab 5.3 werden alle Mitgeliefert
            if($this->hashAlgorithm == self::SHA512 && !defined('CRYPT_SHA512') && CRYPT_SHA512 !== 1){
                $this->hashAlgoriathm = self::SHA256; // Wenn SHA512 nicht verfügbar, versuche SHA256
            }
            if($this->hashAlgorithm == self::SHA256 && !defined('CRYPT_SHA256') && CRYPT_SHA256 !== 1){
                $this->hashAlgorithm = self::BLOWFISH_OLD; // Wenn SHA256 nicht verfügbar, versuche BLOWFISH
            }
            if($this->hashAlgorithm == self::BLOWFISH_OLD && !defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH !== 1){
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
    public static function getRndString($size = 20, $chars = self::LETTERS) {
		if($chars & self::LETTERS){
			$pool = 'abcdefghijklmnopqrstuvwxyz';
			$pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		
        if($chars & self::NUMBERS){
            $pool .='0123456789';
        }
		
		if($chars & 4){	//In URLs sind nicht alle zeichen erlaubt
            $pool .= '.';
        }

        if($chars & self::SPECIAL_CHARACTERS){
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
        return (preg_match('/^$([156]|2[axy])$?/', $hash) === 1);
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
			case self::BLOWFISH_OLD:
			case self::BLOWFISH_FALSE:
                $salt = (empty($salt)?self::getRndString(22, self::WITH_NUMBERS):$salt);
                if($rounds < 4 || $rounds > 31){
                    $rounds = mt_rand(6,10);
                }
                $salt_string = '$'.$this->hashAlgorithm.'$'.$rounds.'$'.$salt.'$';	//Verwendet 2x, wenn verfügbar, auch wenn 2a angegeben wurde
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
	 * @param boolean $backup wenn Check fehlschlägt und das alte passwort mit BLOWFISH_OLD verschlüsselt wurde, werden beide Varianten noch einmal explizit geprüft, wenn verfügbar. Nur nach Transfer der Datenbank verwenden, da es ein sicherheitsrisiko darstellen kann
     * @return boolean
     */
    public function checkPasswd($passwd, $crypted_passwd, $backup = false) {
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
			if($backup == true
				&& version_compare(PHP_VERSION, '5.3.5', '>=')
				&& preg_match('/^$(2a)?$/', $hash) === 1				
			){
				$password_x = preg_replace('/^$(2a)\$/', '$2x$', $crypted_passwd);
				$password_y = preg_replace('/^$(2a)\$/', '$2y$', $crypted_passwd);
				$password_neu_x = crypt($passwd, $password_x);
				$password_neu_y = crypt($passwd, $password_y);
				if($password_neu_x == $password_x || $password_neu_y == $password_y) return true;				
			}
        }
		return false;
    }
}
