<?php
/**
 * Session management class
 */
class Session extends _Locks {
	protected static $ts = NULL;
	protected static $user = NULL;
	protected static $hash = NULL;
	protected static $signature = NULL;
	protected static $data = array();

	public static function valid() {
		//sprawdź, czy:
		//ts jest nie starszy niż tydzień
		//sygnatura zgadza się z danymi prezentowanymi przez przeglądarkę

	}

	public static function load() {
		if (self::lock())
			return false;
		if (!isset($_COOKIE['session']))
			return false;
		$data = new DB('UserSession');
		$data = $data->where('hash=?')->param('s', $_COOKIE['session'])->row();
        self::$ts = $data['ts'];
        self::$user = $data['user'];
        self::$hash = $data['hash'];
        self::$signature = $data['signature'];
        self::$data = $data['data'];
        return self::valid();
	}

	public static function create($user_id, array $v = array()) {
		//utwórz nową sesję na podstawie user id, dorzuć też dane opcjonalne $v
		//hash i signature tworzymy sami, do rozpisania, jak
	}

	public static function destroy() {
		// remove session from db, clear local variables
        
        $data->where(array('hash' => $hash))->delete();
        $ts = NULL;
        $user = NULL;
        $hash = NULL;
        $signature = NULL;
        $data = array();
        
	}

	public static function get($key, $ifndef = NULL) {
		if (isset(self::$data[$key]))
			return self::$data[$key];
		return $ifndef;
	}

	public static function set($key, $value) {
		self::$data[$key] = $value;
	}

	public static function save() {
		//save current session in DB
	}

	public static function end() {
		//funkcja przestarzała, do wywalenia
	}
}