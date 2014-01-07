<?php
namespace CMS;

/**
 * User - user object
 */
class User implements CMS\DB\Instanceable {
	protected $user_id;
	protected $email;
	protected $login;
	protected $password;
	protected $ts_seen;
	protected $is_active;
	protected $is_removed;

	public function set(array $a) {
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->{$k} = $v;
		return $this;
	}

	function __construct(array $a = array()) {
		if ($a)
			$this->set($a);
	}

	public function get($name = NULL) {
		// code...
	}

}


/**
 * User class handler
 */
class Me {
	protected static var $me = NULL;
	private static var $id = NULL;

	public static function login($user, $pass) {
		// code...
	}

	public static function load($e) {
		$res = DB('User')->select('*');
		if (is_numeric($e))
			$res->where(array('user_id' => $e));
		elseif (is_string($e))
			$res->where(array('email' => $e));
		else
			return null;
		return $res->obj('CMS\\User');
	}

	public static function logout() {
		// code...
	}

	public static function checkpw($pass) {
		// code...
	}

	public function id() {
		return self::$id;
	}
}
