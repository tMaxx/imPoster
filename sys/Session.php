<?php
namespace CMS;
/**
 * Session management class
 */
class Session extends \_Locks {
	const SESSION_PERIOD = 2592000;//30days*24hours*60minutes*60seconds
	const SALT_PRE = '$2y$05$';
	protected static $ts = NULL;
	protected static $id = NULL;
	protected static $hash = NULL;
	protected static $signature = NULL;
	protected static $data = array();
	protected static $addit = '';

	///Return client signature based on UA & IP
	protected static function signature() {
		$str = sha1(strtoupper($_SERVER['HTTP_USER_AGENT'])).':'.self::$ts.'P'.md5($_SERVER['REMOTE_ADDR']);
		return crypt(self::$ts.'merryKissMyAss'.$str, self::SALT_PRE.strrev($str));
	}

	///Return user session hash
	protected static function hash() {
		return crypt(strrev('r3v'.self::$id.':'.self::$addit.'()'.self::$ts.'//'.self::$signature.'CMS'), self::SALT_PRE.strrev(self::$signature));
	}

	///Return readable format of hash
	protected static function trhash() {
		return substr(self::$hash, strlen(self::SALT_PRE));
	}

	protected static function trsign() {
		return substr(self::$signature, strlen(self::SALT_PRE));
	}

	protected static function tr($in) {
		return substr($in, strlen(self::SALT_PRE));
	}

	///Recalculate signature, hash, update ts and cookie
	public static function recalc() {
		self::$ts = NOW;
		self::$signature = self::signature();
		self::$hash = self::hash();
		setcookie('session', self::trhash(), NOW+self::SESSION_PERIOD, '/');
	}

	///Is current session valid?
	public static function valid() {
		if ((!self::$hash) || (!self::$signature) ||
			(self::$hash != self::hash()) ||
			(NOW - self::$ts > self::SESSION_PERIOD) ||
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
		if (!isset($_COOKIE['session']))
			return false;
		$data = DB('Session')->select()->where('hash=?')->param('s', $_COOKIE['session'])->row();
		if(!$data)
			return false;
		self::$ts = $data['ts'];
		self::$id = $data['id'];
		self::$hash = self::SALT_PRE.$data['hash'];
		self::$signature = self::SALT_PRE.$data['signature'];
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
		return;
		if (self::$hash) {
			DB('Session')->delete()->where(array('hash' => self::trhash()))->exec();
			setcookie('session', self::trhash(), self::SESSION_PERIOD, '/');
		}
		self::$ts = NULL;
		self::$id = NULL;
		self::$hash = NULL;
		self::$signature = NULL;
		self::$data = array();
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
		$arr = array(
			'ts' => self::$ts,
			'id' => self::$id,
			'signature' => self::trsign(),
			'data' => json_encode(self::$data)
		);
		$hsh = array('hash' => self::trhash());
		if (self::$ts == NOW)
			$db->insert($arr+$hsh);
		else
			$db->update($arr)->where($hsh);
		$db->exec();
	}
}
