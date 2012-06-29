<?php
class PasswdCrypt{
	
	private $hashAlgorithm = '5';
	
	const ONLY_LETTERS = 0;
	const WITH_NUMBERS = 1;
	const WITH_SPECIAL_CHARACTERS = 2;
	
	public function PasswdCrypt(){
		if(version_compare(PHP_VERSION, '5.0.0', '<')){ // Vor php5 war das der Konstructor
			$this->__construct();
		}
		return;
	}
	
	public function __construct(){
		mt_srand();
		
		if(version_compare(PHP_VERSION, '5.3.0', '<')){	//Prüfen welche Hash Funktionen Verfügbar sind. Ab 5.3 werden alle Mitgeliefert
			if($this->hashAlgorithm == '6' && !defined('CRYPT_SHA512')) $this->hashAlgoriathm = '5'; // Wenn SHA512 nicht verfügbar, versuche SHA256
			if($this->hashAlgorithm == '5' && !defined('CRYPT_SHA256')) $this->hashAlgorithm = '2a'; // Wenn SHA256 nicht verfügbar, versuche BLOWFISH
			if($this->hashAlgorithm == '2a' && !defined('CRYPT_BLOWFISH')) $this->hashAlgorithm = '1'; // Wenn BLOWFISH nicht verfügbar, nutze MD5
		}
	}
	
	
	public static function getRndString($size = 20, $url = self::ONLY_LETTERS){
		$pool = 'abcdefghijklmnopqrstuvwxyz';
		$pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($url & self::WITH_NUMBERS){
			$pool .='0123456789';
		}
	  
		if($url & self::WITH_SPECIAL_CHARACTERS){
			$pool .= ',.-;:_#+*~!$%&/()=?';
		}
	  
		$pool = str_shuffle($pool);
		$pool_size = strlen($pool);
		$string ='';
		for($i = 0;$i<$size; $i++){
			$string .= $pool[mt_rand(0, $pool_size - 1)]; //!TODO Zufallszahlen aus /dev/random bzw /dev/urandom wenn verfügbar
		}
		return $string;
	}
	
	public function getHashAlgorithm(){
		return $this->hashAlgorithm;
	}
	
	public function cryptPasswd($passwd, $salt = '', $rounds = 0){
		$salt_string = '';
		switch($this->hashAlgorithm){
			case '6':
			case '5':
				$salt = (empty($salt)?self::getRndString(16, self::WITH_NUMBERS):$salt);
				if($rounds < 1000 || $rounds >	999999999){
					$rounds = mt_rand(2000,10000);
				}
				$salt_string = '$'.$this->hashAlgorithm.'$rounds='.$rounds.'$'.$salt.'$';
			break;
			case '2a':
				$salt = (empty($salt)?self::getRndString(22, self::WITH_NUMBERS):$salt);
				if($rounds < 4 || $rounds >	31){
					$rounds = mt_rand(6,10);
				}
				$salt_string = '$'.$this->hashAlgorithm.'$'.$rounds.'$'.$salt.'$';
			break;
			case '1':
				$salt = (empty($salt)?self::getRndString(12, self::WITH_NUMBERS):$salt);
				$salt_string = '$'.$this->hashAlgorithm.'$'.$salt.'$';
			break;
		}
		$crypted_pw = crypt($passwd, $salt_string);
		if(strlen($crypted_pw) < 13){	return false;  }
		return $crypted_pw;
	}
	
	public function checkPasswd($passwd, $crypted_passwd){
		if(empty($crypted_passwd)) return false;		
		$new_chrypt_pw = crypt($passwd, $crypted_passwd);
		if(strlen($new_chrypt_pw) < 13){	return false;  }
		if($new_chrypt_pw == $crypted_passwd) return true;
		else return false;
	}
}