<?php ///r3vCMS /sys/Me.php
namespace CMS;

/**
 * User - user object
 */
class User implements DB\Instanceable {
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
		if ($name && property_exists($this, $name))
			return $this->{$name};
		return array();
	}
}


/**
 * User class handler
 */
class Me {
	const SALT_PRE = '$2y$07$';
	public static $me = NULL;
	private static $id = NULL;

	public static function login($email, $pass) {
		if (self::$me || self::$id)
			return;

		if (!($obj = self::load($email)))
			return false;

		if (!self::checkpw($pass, $obj->get('password')))
			return 0;

		self::$me = $obj;
		Session::create($obj->get('user_id'));
		return true;
	}

	public static function autoload() {
		if ($st = Session::load()) {
			if ($obj = self::load(Session::getId())) {
				self::$id = $obj->get('user_id');
				self::$me = $obj;
			} else
				Session::destroy();
		}
	}

	protected static function load($e) {
		$res = DB('User')->select()->where('is_active!=0');
		if (is_numeric($e))
			$res->where('user_id=?')->param('i', $e);
		elseif (is_string($e))
			$res->where('email=?')->param('s', $e);
		else
			return null;

		return $res->obj('CMS\\User');
	}

	public static function logout() {
		if (self::$me || self::$id) {
			Session::destroy();
			self::$me = NULL;
			self::$id = NULL;
			return true;
		}
		return false;
	}

	public static function register($email, $login, $password, $active = 0) {
		if (DB('SELECT user_id FROM User WHERE email=? OR login=?')->params('ss', array($email, $login))->row())
			return false;

		$hash = crypt($password, $salt = self::SALT_PRE.Sys::randString(22));
		if ($hash[0] == '*')
			throw new \Error500('Error generating hash, salt='.$salt);
		$q = DB('User')->insert(array(
			'password' => $hash,
			'email' => $email,
			'login' => $login,
			'is_active' => $active,
		));
		if ($q->bool())
			return $q->getInsertID();
		else
			return 0;
	}

	///Check if passwords are the same
	public static function checkpw($pass, $hash) {
		return !!(crypt($pass, $hash) == $hash);
	}

	///Return user id
	public static function id() {
		return self::$id;
	}
}
