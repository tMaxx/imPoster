<?php ///rev engine /sys/Session.php
namespace rev\Auth;
use rev\Vars;

/**
 * Session management class
 */
class Session {
	const SESSION_PERIOD = 2592000;//30days*24hours*60minutes*60seconds
	protected static $ts = null;
	protected static $id = null;
	protected static $salt = null;
	protected static $hash = null;
	public static $data = [];

	/** Return user session hash */
	protected static function hash() {
		$t = self::$salt.self::$id.((string)self::$ts).$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].HOST;
		return base_convert(hash('sha256', $t), 16, 36);
	}

	/** Recalculate signature, hash, update ts and cookie, send to db */
	public static function recalc($id = null) {
		self::destroy();

		self::$id = $id;
		self::$ts = NOW;
		self::$salt = base_convert(bin2hex(openssl_random_pseudo_bytes(64)), 16, 36);
		self::$hash = self::hash();
		setcookie('session', self::$hash, self::$ts+self::SESSION_PERIOD, '/');
		\rev\DB\Q('Session')->insert([
			'ts' => (string)self::$ts,
			'user_id' => self::$id,
			'salt' => self::$salt,
			'data' => self::$data ? json_encode(self::$data) : null,
			'hash' => self::$hash,
		])->exec();
	}

	/** Is current session valid? */
	public static function valid() {
		if (!self::$hash)
			return false;
		if ((self::$hash != self::hash()) || ((NOW - self::$ts) > self::SESSION_PERIOD)) {
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
		$data = \rev\DB\Q('Session')->select()->where('hash=?')->param('s', $cookie)->row();
		if(!$data)
			return false;
		self::$ts = $data['ts'];
		self::$id = $data['user_id'];
		self::$hash = $data['hash'];
		self::$salt = $data['salt'];
		self::$data = $data['data'] ? json_decode($data['data'], true) : null;
		return self::valid();
	}

	/** Set id, data, generate new hashes */
	public static function create($id = null) {
		self::recalc($id);
	}

	/** Delete session from DB, 'unset' variables */
	public static function destroy() {
		if (!self::$hash)
			return;

		\rev\DB\Q('Session')->delete()->where(['hash' => self::$hash])->exec();
		setcookie('session', self::$hash, self::SESSION_PERIOD, '/');
		self::$ts = null;
		self::$id = null;
		self::$hash = null;
		self::$salt = null;
		self::$data = [];
	}

	/** Get id stored in session */
	public static function id() {
		return self::$id;
	}

	public static function dump() {
		if (DEBUG)
		return [
			'ts' => self::$ts,
			'user_id' => self::$id,
			'hash' => self::$hash,
			'salt' => self::$salt,
			'new_hash' => self::hash(),
			'rem_time' => (self::SESSION_PERIOD - (NOW - self::$ts)),
			'data' => self::$data,
		];
	}
}
