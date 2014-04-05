<?php ///r3v engine \r3v\Auth\GAuth
namespace r3v\Auth;

/**
 * Google auth/login class
 * Wrapper for .\Session
 *
 * On auth request it will get user info from G,
 * compare data and then create session with r3v user id.
 * TODO: Registration
 */
class GAuth {
	protected static $client;
	protected static $oauth;
	protected static $notices = [];
	protected static $userinfo = [];


	public static function init() {
		if (self::$client)
			return;
		\r3v\Mod::loadMod('google-api');

		self::$client = new \Google_Client();

		//currently online as nothing will be processed outside page
		self::$client->setAccessType('online');
		self::$client->setApplicationName(\r3v\Conf::get('site/name'));
		self::$client->setScopes(['openid', 'profile', 'email']);

		$conf = \r3v\Conf::get('google_oauth');

		self::$client->setClientId($conf['client_id']);
		self::$client->setClientSecret($conf['client_secret']);
		self::$client->setRedirectUri(HOST.'/user/google:callback/');

		self::$oauth = new \Google_Service_Oauth2(self::$client);

		if ($r = Session::load() && isset(Session::$data['token'])) {
			$token = Session::$data['token'];
			self::$client->setAccessToken($token);

			if (self::$client->isAccessTokenExpired())
				$r = !(self::$notices[] = 'Google auth token expired');
			elseif (self::$client->verifyIdToken()) {
				self::$userinfo = self::$oauth->userinfo->get();
				//FIXME\TODO: user login/auth based on registered users
			}
		}
		return $r;
	}

	public static function load() {
		return Session::load();
	}

	/** Return string info about client status */
	public static function notices() {
		return implode(NEWLINE, self::$notices);
	}

	/**
	 * Set auth code for client
	 */
	public static function auth($code) {
		self::init();
		self::$client->authenticate($code);
		Session::$data['token'] = self::$client->getAccessToken();
		Session::recalc();

		//FIXME: load user
		//compare email & google_id
		//get id, recalc session
	}

	public static function login_redirect() {
		self::init();
		\r3v\View::redirect(self::$client->createAuthUrl());
	}

	public static function logout() {
		Session::destroy();
	}

	public static function dump() {
		if (DEBUG)
		return self::$userinfo ?: false;
	}
}
