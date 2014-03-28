<?php ///r3v engine \r3v\User
namespace r3v\Auth;

/**
 * User class handler
 */
class User {
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
		if (CLI) return; //FIXME
		if (Session::load()) {
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

		return $res->obj('r3v\\Auth\\UserObj');
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

	///Return authentication status (bool)
	public static function auth($role) {
		return Role::auth($role);
	}
}
