<?php ///r3vCMS \r3v\User\Obj
namespace r3v\User;

/**
 * User - user object
 */
class Obj implements DB\Instanceable {
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
