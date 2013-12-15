<?php

class Elem extends Model {
	/**
	 * Element ID
	 * @var int
	 */
	protected $elem_id;
	/**
	 * List ID
	 * @var int
	 */
	protected $list_id;
	/**
	 * Content
	 * @var string
	 */
	protected $content;
	/**
	 * Timestamp
	 * @var int
	 */
	protected $ts;
	/**
	 * Marked as read
	 * @var bool
	 */
	protected $is_read;
	/**
	 * Note/additional info
	 * @var string
	 */
	protected $note;
    
    public function toArray() {
    	return array();
    }
}

