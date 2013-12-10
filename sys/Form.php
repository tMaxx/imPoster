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
		$r = array();
		foreach ($in as $k => $v)
			if(array_key_exists($k, $this->fields))
				$this->values[$k] = $v;

		return $this;
	}

	public function get($key = NULL) {
		if(isset($key))
		{
			if(is_array($key))
			 {
				$r = array();
				foreach($key as $v)
					$r[$v] = array_key_exists($v, $this->values) ? $this->values[$v] : NULL;
				return $r;
			}
			elseif(is_string($key))
			{
				$r = array_key_exists($key, $this->values) ? $this->values[$key] : NULL;
				return $r;
			}
		}
		else
			return $this->values;
	}
	///Render form
	public function r() {
		echo '<form method="post">';
		foreach ($this->fields as $k => $v) {
			if (($type = $v[0]) == 'raw') {
				echo $v[1];
				continue;
			}
			$name = $this->name.'::'.$k;
			$val = isset($this->values[$k]) ? $this->values[$k] : '';
			$attr = '';
			if (isset($v['attributes']))
				foreach ($v['attributes'] as $ak => $av)
					$attr .= $ak. (isset($av) ? '="'.$av.'" ' : ' ');

			echo '<label>', isset($v['label']) ? $v['label'] : $k, ' ';
			switch ($type) {
				//basic types
				case 'email':
				case 'password':
				case 'text':
				case 'submit':
				case 'search':
					echo '<input type="', $type, '" name="', $name,'" ', $attr, ' value="', $val, '" />';
					break;
				//select
				case 'select': {
					echo '<select name="', $name, '" ', $attr, " >";
					$options = $v['options'];

					echo '</select>';
					break;
				}
			}
			echo '</label>';
		}
		echo '</form>';
	}
    
    public function submitted() {
        $r = array();
        foreach ($this->fields as $k => $v)
            $r[] = $k;
        
        $this->values = CMS::vars($this->fields, $r);
    }
}
