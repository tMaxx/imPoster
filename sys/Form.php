<?php
/**
 * HTML <form> handling class
 * Simplyfy all stuff ;)
 */

class Form {

	protected $fields;
	protected $values;
	protected $name;

	/**
	 * @todo everything
	 * @param $def
	 * array(
	 * 'name' => 'nazwwaaaa',
	 * 'fields' => array(
	 *     'pole' => array('typ', 'zmienna' => 'wartosc')
	 *     'pole2' => array('typx', 'zmienna' => 'wartosc')
	 *     )
	 * )
	 */
	function __construct($def) {
		if (!is_array($def))
			throw new ErrorCMS("Type not yet supported");
			
		$this->name = $def['name'];
		$this->fields = $def['fields'];
		$this->values = array();
	}

	public function set(array $in) {
		
	}

	public function get($key = NULL) {
		
	}

	///Render form
	public function r() {
		
	}
}
