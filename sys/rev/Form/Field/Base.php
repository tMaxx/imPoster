<?php ///rev engine \rev\Form\Field\Base
namespace rev\Form\Field;

/** Base abstract class */
abstract class Base implements IBase {
	/** Current value */
	protected $value;
	/** Error string, if any */
	protected $error;
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

	/** Render field */
	public function render() {
		echo '<label>';
			Example.
			<span class="error">Error</span>
			<input>
		echo '</label>';
	}
}
