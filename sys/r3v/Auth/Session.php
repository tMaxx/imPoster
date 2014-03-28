<?php ///r3v engine /sys/Session.php
namespace r3v;

/**
 * Session management class
 */
class Session extends \_Locks {
	const SESSION_PERIOD = 2592000;//30days*24hours*60minutes*60seconds
	protected static $ts = NULL;
	protected static $id = NULL;
	protected static $hash = NULL;
	protected static $signature = NULL;
	protected static $data = [];
	protected static $addit = '';

	///Return client signature based on UA & IP
	protected static function signature() {
		$str = strtoupper(Vars::server('HTTP_USER_AGENT')).':'.self::$ts.'P'.Vars::server('REMOTE_ADDR').HOST;
		return hash('sha256', self::$ts.'merryKissMyAss'.strrev($str));
	}

	///Return user session hash
	protected static function hash() {
		return hash('sha256', strrev('r3v'.self::$id.':'.self::$addit.'()'.self::$ts.'//'.self::$signature.'CMS'));
	}

	///Recalculate signature, hash, update ts and cookie
	public static function recalc() {
		self::$ts = NOW_MICRO;
		self::$signature = self::signature();
		self::$hash = self::hash();
		setcookie('session', self::$hash, NOW+self::SESSION_PERIOD, '/');
	}

	///Is current session valid?
	public static function valid() {
		if ((!self::$hash) || (!self::$signature) ||
			(self::$hash != self::hash()) ||
			(((NOW_MICRO - self::$ts)/10000) > self::SESSION_PERIOD) ||
			(self::$signature != self::signature())) {
			self::destroy();
			return false;
		}
		return true;
	}

	///Load session from DB, if any
	public static function load() {
		if (self::lock())
			return self::valid();
		$cookie = Vars::cookie('session');
		if (!$cookie)
			return false;
		$data = DB('Session')->select()->where('hash=?')->param('s', $cookie)->row();
		if(!$data)
			return false;
		self::$ts = $data['ts'];
		self::$id = $data['id'];
		self::$hash = $data['hash'];
		self::$signature = $data['signature'];
		self::$data = @json_decode($data['data'], true);
		return self::valid();
	}

	///Set id, data, generate new hashes
	public static function create($id, array $v = array()) {
		self::$id = $id;
		self::$data = $v;
		self::recalc();
	}

	///Delete session from DB, 'unset' variables
	public static function destroy() {
		if (self::$hash) {
			DB('Session')->delete()->where(array('hash' => self::$hash))->exec();
			setcookie('session', self::$hash, self::SESSION_PERIOD, '/');
		}
		self::$ts = NULL;
		self::$id = NULL;
		self::$hash = NULL;
		self::$signature = NULL;
		self::$data = [];
	}

	public static function getId() {
		return self::$id;
	}

	public static function addit($a) {
		self::$addit = $a;
	}

	public static function get($key, $ifndef = NULL) {
		if (isset(self::$data[$key]))
			return self::$data[$key];
		return $ifndef;
	}

	public static function set($key, $value) {
		self::$data[$key] = $value;
	}

	///Save session into DB
	public static function save() {
		if (!self::$hash || !self::$id)
			return;
		$db = DB('Session');
		$arr = [
			'ts' => self::$ts,
			'id' => self::$id,
			'signature' => self::$signature,
			'data' => json_encode(self::$data)
		];
		$hsh = ['hash' => self::$hash];
		if (self::$ts == NOW_MICRO)
			$db->insert($arr+$hsh);
		else
			$db->update($arr)->where($hsh);
		$db->exec();
	}
}

Mod::registerUnload(['\\r3v\\Session::save']);
