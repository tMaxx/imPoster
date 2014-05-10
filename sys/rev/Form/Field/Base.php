<?php ///rev engine \rev\Form\Field\Base
namespace rev\Form\Field;

/** Base abstract class */
abstract class Base implements IBase {
	/** Current value */
	protected $value = null;
	/** Error string, if any */
	protected $error = null;
	/** Field name */
	protected $name;
	/** Full field definition */
	protected $def;

	/** Constructor: field name and definition */
	function __construct($name, $def) {
		$this->def = $def;
		$this->name = $name;
	}

	/** Return internal property value */
	public function __get($name) {
		switch ($name) {
			case 'value':
				if (method_exists($this, 'getFormattedValue')) {
					return $this->getFormattedValue();
					break;
				}
			case 'raw_value':
				return $this->value;
				break;

			default:
				return $this->{$name};
				break;
		}
	}

	/** Set internal property value */
	public function __set($name, $value) {
		switch ($name) {
			case 'value':
				if (method_exists($this, 'setFormattedValue')) {
					return $this->setFormattedValue($value);
					break;
				}
			case 'raw_value':
				return $this->value = $value;
				break;

			case 'name':
			case 'def';
				throw new \rev\Error('Setting internal values is not permitted');
				break;

			default:
				return $this->{$name} = $value;
				break;
		}
	}

	/** Glue all attributes together */
	protected function attr() {
		if (isset($this->def['attributes']))
			return \rev\Form\Form::attr($this->def['attributes']);
		return '';
	}

	/** Should render only input itself, optional */
	// protected function renderInput() {}

	/** Render field */
	public function render() {
		if (!empty($this->def['label']))
			echo '<label for="',$this->name,'" class="field-label">',$this->def['label'],'</label>';

		if (method_exists($this, 'renderInput'))
			$this->renderInput();
		else {
			$val = $this->value;
			if (!isset($val) && isset($this->def['value']))
				$val = $this->def['value'];

			echo '<input type="', $this->def[0], '" name="', $this->name, '" value="', $val, '"', $this->attr(),'>';
		}

		if ($this->error)
			echo '<label for="',$this->name,'" class="field-error">',$this->error,'</label>';
	}
}
