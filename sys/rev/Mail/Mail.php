<?php ///rev engine \rev\Mail\Mail
namespace rev\Mail;

/** Email handling */
class Mail {
	/** Objects cache */
	private static $cache = [];

	/** Add object to cache or create & add */
	public static function create($email_to, $email_subject = null, $email_message = null, array $headers = array()) {
		if ($email_to instanceof Obj)
			self::$cache[] = $email_to;
		elseif (isset($email_subject, $email_message))
			self::$cache[] = new Obj($email_to, $email_subject, $email_message, $headers);

	}

	/** Flush class cache */
	public static function flush(){
		foreach (self::$cache as $v)
			$v->send();
		self::$cache = [];
	}
}

Mod::registerUnload(['\\rev\\Mail::flush'], 10);
