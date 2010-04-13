<?php
/**
 * statische Klasse, die die Profilfelder verwaltet
 * @author Korbinian
 */
class ProfilefieldRegistry {

	// stack, der beim anzeigen der profilfelder verwendet wird
	private static $view_stack = array();

	// hier werden die klassen verwaltet, die profilfeld-typen darstellen
	private static $profilefield_classes = array();


	/**
	 * Anzeige für das Profil
	 * @param array $ar Informationen über das zu rendernde Profilfeld
	 */
	public static function renderProfile($ar) {
		self::_checkClass($ar["func"]);

		// profilefield_classes[$ar["func"]] enthält jetzt bestimmt ein Objekt
		// vom Typ AbstractProfileFieldType (sonst hätte es schon eine Exception
		// gegeben. Also können wir rendern
		self::$profilefield_classes[$ar["func"]]->renderProfileEdit($ar);
	}

	/**
	 * Anzeige für den Adminbereich
	 * 
	 * $ar kann bis auf $ar["func"] leer sein, wenn ein neues Profilfeld erstell wird z.B
	 * 
	 * @param array $ar Informationen über das zu rendernde Profilfeld
	 */
	public static function renderAdmin($ar) {
		self::_checkClass($ar["func"]);
		
		self::$profilefield_classes[$ar["func"]]->renderAdmin($ar);
	}

	/**
	 * Gibt die Namen aller typen zurück
	 * Dazu wird bei allen Typen getName() (definiert in AbstractProfileField)
	 * aufgerufen.
	 *
	 * Rückgabe ist ein array ( $func => $name)
	 *
	 * KONVENTION: Die Dateien heißen profilefield_type_[0-9]+.php
	 */
	public static function getAllTypes() {
		// wir benötigen auf jeden fall den AbstractProfileFieldType
		require_once("include/includes/class/profilefield_types/AbstractProfileFieldType.php");

		$files = read_ext("include/includes/class/profilefield_types", "php", 0, 0);
		$types = array();
		foreach($files as $file) {
			if(preg_match("/^.*profilefield_type_[0-9]+.*$/", $file)) {
				$parts = explode("_", $file);
				$func = array_pop($parts);
				self::_loadClass($func);
				$types[$func] = self::$profilefield_classes[$func]->getName();
			}
		}
		return $types;
	}
	
	/**
	 * Ein neues Profilfeld einfügen
	 * 
	 * @param array $ar, enthält die ganzen infos
	 */
	public static function insert($ar) {
		self::_checkClass($ar["func"]);
		
		self::$profilefield_classes[$ar["func"]]->insert($ar);
	}
	
	public static function get($func, $id) {
		self::_checkClass($func);
		
		return self::$profilefield_classes[$func]->get($id);
	}
	
	public static function getUservalue($func, $fieldId, $userId) {
		self::_checkClass($func);
		
		return self::$profilefield_classes[$func]->getUserValue($fieldId, $userId);
	}
	
	public static function clearStack() {
		while(($item = array_pop(self::$view_stack)) != NULL) {
			self::_checkClass($item["func"]);
			self::$profilefield_classes[$item["func"]]->removedFromStack($item["value"]);
		}
	}
	
	public static function pushToStack($func, $value, $level) {
		$item = array_pop(self::$view_stack);
		while($item != NULL && $item["level"] <= $level) {
			self::_checkClass($item["func"]);
			self::$profilefield_classes[$item["func"]]->removedFromStack($item["value"]);
			// geht, weil php keinen fehler wirft
			$item = array_pop(self::$view_stack);
		}
		// jetzt wurden alle elemente ordentlich gecloset und 
		// wir können es uns auf dem stack gemütlich machen
		array_push(self::$view_stack, array("func" => $func, "level" => $level, "value" => $value));
	}

	/**
	 * Checkt, ob eine bestimmte Klasse schon geladen wurde
	 * @param int $func Funktionstyp der Klasse
	 */
	private static function _checkClass($func) {
		// wir benötigen auf jeden fall den AbstractProfileFieldType
		require_once("include/includes/class/profilefield_types/AbstractProfileFieldType.php");
		
		// wir checken ob die entsprechende klasse schon geladen wurde
		if(!isset($profilefield_classes[$func])) {
			// ok, wir müssen die entsprechende klasse noch laden
			// und instantiieren
			self::_loadClass($func);
		}
	}

	/**
	 * Lädt die angegebene Klasse nach
	 * KONVENTION: Die Datei heißt profilefield_type_{$func}.php und enthält genau eine Klasse!
	 * @param int $func gibt die datei an, die geladen werden muss
	 */
	private static function _loadClass($func) {
		$declared_classes = get_declared_classes();
		// wir können ruhig "require_once" machen
		// da wenn das file nicht existiert, eh was falsch gelaufen ist. dann
		// darf durchaus ein fehler geworfen werden.
		// TODO: eventuell try/catch und den webmaster entsprechend darüber informieren
		$path = "include/includes/class/profilefield_types/profilefield_type_{$func}.php";
		require_once($path);

		$new_class = array_diff(get_declared_classes(), $declared_classes);
		if(sizeof($new_class) != 1) {
			//throw new Exception("There are no classes / more than 1 classes in {$path}");
		} else {
			// keys neu anordnen
			$new_class = array_merge($new_class);
			$new_class = $new_class[0];
				
			// instantiieren
			$instance = new $new_class;
				
			// und im registry setzen
			self::$profilefield_classes[$func] = $instance;
		}
	}

}