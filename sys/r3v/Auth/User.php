<?php ///rev engine \r3v\Auth\User
namespace r3v\Auth;

/**
 * Google auth/login class
 * Wrapper for .\Session
 *
 * On auth request it will get user info from G,
 * compare data and then create session with r3v user id.
 * TODO: Registration
 */
class User {
	protected static $_gclient;
	protected static $_goauth;

	public static $user = [];

	/** Init shitty Google Oauth2-ready libraries */
	public static function g_lib_init() {
		if (self::$_gclient)
			return;
		\r3v\Mod::loadMod('lib/google-api');

		self::$_gclient = new \Google_Client();

		//currently online as nothing will be processed outside page
		self::$_gclient->setAccessType('online');
		self::$_gclient->setApplicationName(\r3v\Conf::get('site/name'));
		self::$_gclient->setScopes(['openid', 'profile', 'email']);

		$conf = \r3v\Conf::get('google_oauth');

		self::$_gclient->setClientId($conf['client_id']);
		self::$_gclient->setClientSecret($conf['client_secret']);
		self::$_gclient->setRedirectUri(HOST.'/user:callback/');
	}

	/** Redirect to Session::load() */
	public static function load() {
		if (!Session::load())
			return false;

		if (Session::id())
			self::$user = DB('User')->select()->where(['id' => Session::id()])->row();

		return !!(self::$user);
	}

	/**
	 * Authorize request from G
	 * @param $code
	 * @return
	 * 	false: successfully logged in
	 *		string: notice to user
	 */
	public static function callback_auth($code) {
		self::g_lib_init();
		self::$_goauth = new \Google_Service_Oauth2(self::$_gclient);
		self::$_gclient->authenticate($code);

		$uinfo = self::$_goauth->userinfo->get();

		if (!ctype_digit($uinfo->id))
			throw new r3v\Error418('Interesting, user id is not numeric. Aborting.');

		$data = DB('SELECT
			id, email, name, auth, ts_seen, is_removed, is_active
			FROM User WHERE gid=?')->param('s', $uinfo->id)->row();

		if (!$data) { //register
			if (!$uinfo->verifiedEmail)
				return 'Email connected with this Google account is not verified, aborting registration';

			$q = DB('User')->insert([
				'email' => $uinfo->email,
				'name' => $uinfo->name,
				'is_active' => false,
				'is_removed' => false,
				'ts_seen' => NOW,
				'gid' => $uinfo->id,
			]);
			if ($q->bool()) {
				$r = 'Account successfully registered.<br>';
				$r .= 'Please proceed <a href="/user/confirm:';
				$r .= $q->getInsertID().'">here</a> to request a confirmation email.';
			} else
				$r = 'Error while trying to register account';
			return $r;
		}

		if ($data['is_removed'])
			throw new Error403();
		if (!$data['is_active'])
			return 'Account is inactive, login aborted';

		if ($data['auth'] == 'admin' && (!$uinfo->verifiedEmail || $data['email'] != $uinfo->email))
			throw new r3v\Error403('Invalid e-mail for auth "admin"; login aborted');

		Session::create($data['id']);
	}

	public static function login_redirect() {
		self::g_lib_init();
		\r3v\View::redirect(self::$_gclient->createAuthUrl());
	}

	public static function logout() {
		Session::destroy();
		self::$user = [];
	}

	/** Return user id */
	public static function id() {
		return isset(self::$user['id']) ? self::$user['id'] : null;
	}

	/** Return user name */
	public static function name() {
		return isset(self::$user['name']) ? self::$user['name'] : 'anonymous';
	}

	/** Return authentication status (bool) */
	public static function role($role) {
		if (self::$user['auth'] == $role)
			return true;
		return Role::auth($role);
	}
}
