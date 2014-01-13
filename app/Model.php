<?php //teo /app/Model.php

class Model implements CMS\DB\Saveable, CMS\DB\Instanceable {
	static $TABLE;

	public function set(array $a) {
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->{$k} = $v;
		return $this;
	}

	function __construct(array $a = array()) {
		if ($a)
			$this->set($a);
	}

	public function toArray() {}

	final public static function getKeyName() {
		return strtolower(get_called_class()).'_id';
	}
	
	final public function getId() {
		if(isset($this->{static::getKeyName()}))
			return $this->{static::getKeyName()};
		else
			return NULL;
	}

	final public function setId($v) {
		$this->{static::getKeyName()} = $v;
		return $this;
	}
	
	final public function getTableName() {
		return static::$TABLE ? static::$TABLE : get_called_class();
	}

} 
