<?php //teo /app/Model.php

class Model {
	protected static $TABLE;

	public function set(array $a) {
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->$k = $v;
		return $this;
	}

	public function __construct(array $a = array()) {
		if ($a)
			$this->set($a);
	}

	abstract public function toArray();

	public static function getPK() {
		return strtolower(get_called_class()).'_id';
	}
	
	public function getId() {
		if(isset($this->{static::getPK()}))
			return $this->{static::getPK()};
		else
			return NULL;
	}

	public function setId($v) {
		$this->{static::getPK()} = $v;
		return $this;
	}
	
	public function table() {
		return static::$TABLE;
	}

} 

