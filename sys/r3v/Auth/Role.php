<?php //rev engine \r3v\Role
namespace r3v\Auth;

/**
 * Auth
 * Autorization class
 */
class Role {
	/**
	 * Holds a simple map of every action in system, half-processed
	 * This one has additional things:
	 * role1 => [role2, role4]
	 * This one is final:
	 * role2 => []
	 */
	protected static $map = [];

	/** User roles cache */
	protected static $user = [];

	public static function init() {
		if (self::$map)
			return;
		self::$map = \r3v\Conf::get('auth/map');
	}

	/** Retrieve user role by given $id and add it to cache */
	public static function cache($id) {
		if (is_numeric($id)) {
			if (isset(self::$user[$id]))
				return;
			$role = DB('User')->select('auth')->where(['id' => $id])->val();

			self::$user[$id] = $role;
		}
	}

	/**
	 * Micro-authorization
	 * Walks internal array of auth
	 * @param $start which array key we're in
	 * @param $role which role we're looking for
	 * @param &$limit how many jumps we can do
	 * @return bool
	 */
	protected static function microauth($start, $role, &$limit) {
		if (!$limit || !$start || !isset(self::$auth[$start]))
			return false;
		if ($start == $role)
			return true;
		$limit--;

		foreach (self::$auth[$start] as $r) {
			//role has an element we look for
			if ($r == $role)
				return true;
			return self::microauth($r, $role, $limit);
		}
		return false;
	}

	/**
	 * Check if current user has authorization for specified role
	 * @param $role name
	 * @return bool
	 */
	public static function auth($role) {
		if ($role == 'anonymous')
			return true;
		$id = User::id();
		if (!$id)
			return false;
		elseif ($role == 'user')
			return true;

		self::cache($id);
		if (self::$user[$id])
			return self::microauth(self::$user[$id], $role, 20);
		else
			return false;
	}
}
Role::init();
