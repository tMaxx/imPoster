<?php

class User extends Model {
	protected $user_id;
	protected $email;
	protected $login;
	protected $password;
	protected $ts_seen;
	protected $is_active;
	protected $is_removed;

	/**
	 * Get email
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set email
	 * @param string $email
	 * @return \User
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}


	/**
	 * Get login
	 * @return string
	 */
	public function getLogin() {
		return $this->login;
	}

	/**
	 * Set login
	 * @param string $login
	 * @return \User
	 */
	public function setLogin($login) {
		$this->login = $login;
		return $this;
	}

	/**
	 * Get last seen ts
	 * @return int
	 */
	public function getTsSeen() {
		return $this->ts_seen;
	}

	/**
	 * Set last seen ts
	 * @param int $ts_seen
	 * @return \User
	 */
	public function setTsSeen($ts_seen) {
		$this->ts_seen = $ts_seen;
		return $this;
	}

	/**
	 * Get is active
	 * @return bool
	 */
	public function getIsActive() {
		return !!$this->is_active;
	}

	/**
	 * Set is active
	 * @param bool $is_active
	 * @return \User
	 */
	public function setIsActive($is_active) {
		$this->is_active = !!$is_active;
		return $this;
	}

	public static function getViewLink($login) {
		return '/user:'.$login.'/view';
	}
}
