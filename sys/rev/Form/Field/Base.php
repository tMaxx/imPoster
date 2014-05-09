<?php ///rev engine \rev\Form\Field\Base
namespace rev\Form\Field;

/** Base abstract class */
abstract class Base implements IBase {
	protected $value;
	protected $def;

	function __construct($def) {
		$this->def = $def;
	}

	protected function getPropertyBindings() {
		return [
			'value' => 'value'
		];
	}

	/** Return internal property value */
	public function __get($name) {
		if (method_exists($this, $name.'IGet'))
			return $this->{$name.'IGet'};

		return $this->{$this->getPropertyBindings[$name]};
	}

	/** Set internal property value */
	public function __set($name, $value) {
		if (method_exists($this, $name.'ISet'))
			$value = $this->{$name.'ISet'}($value);

		$name = $this->getPropertyBindings[$name];
		return $this->{$name} = $value;
	}
}
