<?php //teo /app/Model.php

class Model {
	abstract static $TABLE;

	public function set(array $a) {
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->$k = $v;
		return $this;
	}

	public function save() {
		$s = $this->toArray();
        
		if($this->getId())
			DB::update($this->table(), $this->getId(), $s);  
		else
			DB::insert($this->table(), $s);
	}
    
    public function getId() {
        if(isset($this->($prefix.'_id')))
            return $this->($prefix.'_id');
        else
            return NULL;
    }
    
    public function table() {
    	if(isset(static::$TABLE))
    		return static::$TABLE;
    }

} 

