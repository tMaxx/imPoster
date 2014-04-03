<?php ///r3v engine /sys/Session.php
namespace r3v\Auth;
use r3v\Vars;

/**
 * Session management class
 */
class Session {
	const SESSION_PERIOD = 2592000;//30days*24hours*60minutes*60seconds
	protected static $ts = NULL;
	protected static $id = NULL;
	protected static $hash = NULL;
	public static $data = [];

	/** Return user session hash */
	protected static function hash() {
		return hash('sha256', self::$id.((string)self::$ts).$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].HOST);
	}

	/** Recalculate signature, hash, update ts and cookie */
	public static function recalc() {
		self::$ts = NOW_MICRO;
		self::$hash = self::hash();
		setcookie('session', self::$hash, NOW+self::SESSION_PERIOD, '/');
	}

	/** Is current session valid? */
	public static function valid() {
		if ((!self::$hash) || (self::$hash != self::hash()) ||
			(((int)((NOW_MICRO - self::$ts) / 10000.0)) > self::SESSION_PERIOD)) {
			self::destroy();
			return false;
		}
		return true;
	}

	/** Load session from DB, if any */
	public static function load() {
		static $lock = false;
		if ($lock)
			return self::valid();
		else
			$lock = true;

		$cookie = Vars::cookie('session');
		if (!$cookie)
			return false;
		$data = DB('Session')->select()->where('hash=?')->param('s', $cookie)->row();
		if(!$data)
			return false;
		self::$ts = $data['ts'];
		self::$id = $data['user_id'];
		self::$hash = $data['hash'];
		self::$data = @json_decode($data['data'], true) ?: [];
		return self::valid();
	}

	/** Set id, data, generate new hashes */
	public static function create($id = null) {
		self::$id = $id;
		self::recalc();
	}

	/** Delete session from DB, 'unset' variables */
	public static function destroy() {
		if (self::$hash) {
			DB('Session')->delete()->where(array('hash' => self::$hash))->exec();
			setcookie('session', self::$hash, self::SESSION_PERIOD, '/');
		}
		self::$ts = NULL;
		self::$id = NULL;
		self::$hash = NULL;
		self::$data = [];
	}

	/** Get id stored in session */
	public static function getId() {
		return self::$id;
	}

	/** Save session into DB */
	public static function save() {
		if (!self::$hash)
			return;
		$db = DB('Session');
		$arr = [
			'ts' => self::$ts,
			'user_id' => self::$id,
			'data' => (@json_encode(self::$data) ?: '{}')
		];
		$hsh = ['hash' => self::$hash];
		if (self::$ts == NOW_MICRO)
			$db->insert($arr+$hsh);
		else
			$db->update($arr)->where($hsh);
		$db->exec();
	}

	public static function dump() {
		if (DEBUG)
		return [
			'ts' => self::$ts,
			'user_id' => self::$id,
			'hash' => self::$hash,
			'new_hash' => self::hash(),
			'hash_match' => (self::$hash == self::hash()),
			'data' => self::$data,
		];
	}
}

\r3v\Mod::registerUnload(['\\r3v\\Auth\\Session::save']);
