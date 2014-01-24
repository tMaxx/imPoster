<?php
/**
 * Ping
 * Short messages in portal, can also carry Elem-ents
 */
class Ping extends Model {
	protected $ping_id;
	protected $user_id;
	protected $user_dest;
	protected $elem_id = NULL;
	protected $ts;
	protected $note = NULL;

	/**
	 * Get user id
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * Set user id
	 * @param int $user_id
	 * @return \Ping
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
		return $this;
	}

	/**
	 * Get user dest id
	 * @return int
	 */
	public function getUserDest() {
		return $this->user_dest;
	}

	/**
	 * Set user dest id
	 * @param int $user_dest
	 * @return \Ping
	 */
	public function setUserDest($user_dest) {
		$this->user_dest = $user_dest;
		return $this;
	}

	/**
	 * Get elem id
	 * @return int
	 */
	public function getElem_id() {
		return $this->elem_id;
	}

	/**
	 * Set elem id
	 * @param int $elem_id
	 * @return \Ping
	 */
	public function setElem_id($elem_id) {
		$this->elem_id = $elem_id;
		return $this;
	}

	/**
	 * Get timestamp
	 * @return int
	 */
	public function getTs() {
		return $this->ts;
	}

	/**
	 * Set timestamp
	 * @param int $ts
	 * @return \Ping
	 */
	public function setTs($ts) {
		$this->ts = $ts;
		return $this;
	}

	/**
	 * Get note
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * Set note
	 * @param string $note
	 * @return \Ping
	 */
	public function setNote($note) {
		$this->note = $note;
		return $this;
	}

}

