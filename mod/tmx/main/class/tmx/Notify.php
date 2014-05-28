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

	public static function getOne() {
		if (!($id = \rev\Auth\User::id()))
			return '';

		$db = new \rev\DB\Table('Notify');
		$db->select()->where(['user_id' => $id]);
		if (!($ret = $db->row()))
			return '';
		$db->clear();
		$db->delete()->where(['id' => $ret['id']])->exec();

		return $ret['content'];
	}
}
