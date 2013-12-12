<?php //teo /app/Model.php

class Model {
	static $TABLE;

	public function set(array $a) {
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->$k = $v;
		return $this;
	}

	public function toArray() {}

	final public static function getPK() {
		return strtolower(get_called_class()).'_id';
	}
	
	final public function getId() {
		if(isset($this->{static::getPK()}))
			return $this->{static::getPK()};
		else
			return NULL;
	}

	final public function setId($v) {
		$this->{static::getPK()} = $v;
		return $this;
	}
	
	final public function table() {
		return static::$TABLE;
	}

} 

