<?php ///r3v engine /sys/Mail.php
namespace r3v;

/**
 * Email handling class
 */
class Mail {
	///Objects cache
	private static $cache = array();

	///Add object to cache or create & add
	public static function create($email_to, $email_subject = NULL, $email_message = NULL, array $headers = array()) {
		if ($email_to instanceof Mail\Obj)
			self::$cache[] = $email_to;
		elseif (isset($email_subject, $email_message))
			self::$cache[] = new Mail\Obj($email_to, $email_subject, $email_message, $headers);

	}

	///Flush class cache
	public static function flush(){
		foreach (self::$cache as $v)
			$v->send();
		self::$cache = array();
	}
}

Mod::registerUnload(['\\r3v\\Mail::flush']);
