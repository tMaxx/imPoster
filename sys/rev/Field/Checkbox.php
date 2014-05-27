<?php ///rev engine \rev\Field\Checkbox
namespace rev\Field;

/** Checkbox field */
class Checkbox extends Base {
	protected $value = false;

	/** Return internal property value */
	public function __get($name) {
		if ($name == 'value' || $name == 'raw_value')
			return !!$this->value;
		return parent::__get($name);
	}

	/** Set internal property value */
	public function __set($name, $val) {
		if ($name == 'value' || $name == 'raw_value')
			return $this->value = !!$val;
		return parent::__set($name, $val);
	}

}

/*echo '<input type="', $type, '" name="', $name,'" ', $attr, ' value="', $val, '">';*/
