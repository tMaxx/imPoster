<?php //r3v engine \r3v\Role
namespace r3v;
trigger_user_error("Depracated.", E_USER_DEPRECATED);
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
	protected $map = array();

	///User roles cache
	protected $user = array();

	public static function init() {
		if (self::$map)
			return;
		$rows = DB('SELECT * FROM ACLRoles')->rows();
		foreach ($rows as $r) {
			$c = (array)explode(' ', $r['child']);
			$r = $r['role'];

			if (!isset(self::$map[$r]))
				self::$map[$r] = [];

			foreach ($c as $e)
				if ($e)
					self::$map[$r][] = $e;
		}
	}

	///Retrieve user role by given $id and add it to cache
	public static function cache($id) {
		if (is_numeric($id)) {
			if (isset(self::$user[$id]))
				return;
			$role = DB('UserACL')->select('role')->where(['user_id' => $id])->val();

			self::$user[$id] = $role;
		}
	}

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
			if ($r)
				return self::microauth($r, $role, $limit);
		}
		return false;
	}

	public static function auth($role) {
		if ($role == 'anonymous')
			return true;
		$id = Me::id();
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
