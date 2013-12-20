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
		$data = DB('UserSession')->where('hash=?')->param('s', $_COOKIE['session'])->row();
		if(!$data)
			return false;
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
        
        DB('UserSession')->where(array('hash' => self::$hash))->delete();
        setcookie('session', self::$hash, 1);
        self::$ts = NULL;
        self::$user = NULL;
        self::$hash = NULL;
        self::$signature = NULL;
        self::$data = array();
        
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
		DB('UserSession')->insert(array(
                                    'hash' => self::$hash, 
                                    'ts' => self::$ts, 
                                    'user' => self::$user, 
                                    '$signature' => self::$signature, 
                                    '$data' => self::$data
                ));
        setcookie('session', $hash, NOW+30*60*60);
        exec();
	}

	public static function end() {
		//funkcja przestarzała, do wywalenia
	}
}