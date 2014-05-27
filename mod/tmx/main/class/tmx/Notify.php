<?php ///tmx notify
namespace tmx;

/** Notification service */
class Notify {

	public static function add($content, $expires = true) {
		if (!\rev\Auth\User::id())
			return false;

		$db = new \rev\DB\Table('Notify');
		$db->insert([
			'user_id' => \rev\Auth\User::id(),
			'content' => $content,
			'auto_expire' => true
		])->exec();
	}
}
