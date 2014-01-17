<?php
/**
 * UserFriends
 */
class UserFriends extends Model {
	const FRIENDS_REQUEST = null;
	const FRIENDS_ACCEPTED = true;
	const FRIENDS_REJECTED = false; 

	protected $user_one;
	protected $user_two;
	protected $status;


	/**
	 * Get first user
	 * @return int
	 */
	public function getUserOne() {
		return $this->user_one;
	}
	
	/**
	 * Set user one
	 * @param int $user_one
	 * @return \UserFriends
	 */
	public function setUserOne($user_one) {
		$this->user_one = $user_one;
		return $this;
	}

	/**
	 * Get user two
	 * @return int
	 */
	public function getUserTwo() {
		return $this->user_two;
	}
	
	/**
	 * Set destination
	 * @param int $user_two
	 * @return \UserFriends
	 */
	public function setUserTwo($user_two) {
		$this->user_two = $user_two;
		return $this;
	}


	/**
	 * Get status
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * Set status
	 * @param int $status
	 * @return \UserFriends
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	/**
	 * Get friends entry from DB
	 * @param $f user_id
	 * @param $s user_id
	 * @return NULL|array
	 */
	public static function getRow($f, $s) {
		return DB('SELECT * FROM UserFriends WHERE (user_one=? AND user_two=?) OR (user_one=? AND user_two=?)')->params('iiii', array($f, $s, $s, $f))->row();
	}

	/**
	 * Get information whether two users are confirmed friends
	 * @param $u1 user_id
	 * @param $u2 user_id
	 * @return bool
	 */
	public static function areFriends($u1, $u2) {
		$r = self::getRow($u1, $u2);
		return !!(isset($r['status']) && $r['status']);
	}
}
