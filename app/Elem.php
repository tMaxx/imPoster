<?php

class Elem extends Model {
	///Type: Visible on my wall
	const TYPE_WALL = 0;
	///Type: Visible by everyone, but not shown on wall
	const TYPE_PUBLIC = 1;
	///Type: Visible by me an anyone who has an invitation
	const TYPE_SHARED = 2;
	///Type: Only I can see it
	const TYPE_PRIVATE = 3;

	/**
	 * Element ID
	 * @var int
	 */
	protected $elem_id = null;
	/**
	 * List ID
	 * @var int
	 */
	protected $list_id = null;
	/**
	 * Owner user id
	 * @var int
	 */
	protected $user_id = null;
	/**
	 * Destination user id
	 * @var int
	 */
	protected $user_dest = null;
	/**
	 * Name
	 * @var string
	 */
	protected $name = null;
	/**
	 * Content
	 * @var string
	 */
	protected $content = null;
	/**
	 * Type
	 * @var int
	 */
	protected $type = null;
	/**
	 * Timestamp
	 * @var int
	 */
	protected $ts = null;
	/**
	 * Marked as read
	 * @var bool
	 */
	protected $is_read = false;

	public function toArray() {
		return array(
			'elem_id' => $this->elem_id,
			'list_id' => $this->list_id,
			'user_id' => $this->user_id,
			'user_dest' => $this->user_dest,
			'name' => $this->name,
			'content' => $this->content,
			'type' => $this->type,
			'ts' => $this->ts,
			'is_read' => $this->is_read,
		);
	}

	/**
	 * Get list id
	 * @return int
	 */
	public function getListId() {
		return $this->list_id;
	}

	/**
	 * Set list id
	 * @param $id
	 * @return \Elem
	 */
	public function setListId($id) {
		$this->list_id = $id;
		return $this;
	}

	/**
	 * Get owner id
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * Set owner id
	 * @param $id
	 * @return \Elem
	 */
	public function setUserId($id) {
		$this->user_id = $id;
		return $this;
	}

	/**
	 * Get receiver
	 * @return int
	 */
	public function getUserDest() {
		return $this->user_dest;
	}

	/**
	 * Set receiver
	 * @param $id
	 * @return \Elem
	 */
	public function setUserDest($id) {
		$this->user_dest = $id;
		return $this;
	}

	/**
	 * Get name
	 * @return int
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set name
	 * @param $name
	 * @return \Elem
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Get content
	 * @return int
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Set content
	 * @param $content
	 * @return \Elem
	 */
	public function setContent($content) {
		$this->content = $id;
		return $this;
	}

	/**
	 * Get item type
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	public function getTypeString() {
		return self::getTypeStringById($this->type);
	}

	/**
	 * Set item type
	 * @param $type
	 * @return \Elem
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Get unix timestamp
	 * @return int
	 */
	public function getTS() {
		return $this->ts;
	}

	/**
	 * Set unix timestamp
	 * @param $num
	 * @return \Elem
	 */
	public function setTS($num) {
		$this->ts = $num;
		return $this;
	}

	/**
	 * Get is read flag
	 * @return bool
	 */
	public function getIsRead() {
		return !!$this->is_read;
	}

	/**
	 * Set is read flag
	 * @param $bool
	 * @return \Elem
	 */
	public function setIsRead($bool) {
		$this->is_read = !!$bool;
		return $this;
	}

	public static function getAllTypes() {
		return array(
			NULL => 'Brak',
			self::TYPE_WALL => 'Publiczne, widoczne w profilu',
			self::TYPE_PUBLIC => 'Publiczne',
			self::TYPE_SHARED => 'Ja i osoby z zaproszeniem',
			self::TYPE_PRIVATE => 'Tylko ja'
		);
	}

	public static function getTypeStringById($v) {
		$r = self::getAllTypes();
		return $r[$v];
	}

	public function getEditLink() {
		return '/task:'.$this->getID().'/edit';
	}
}
