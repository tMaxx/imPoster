<?php ///rev engine \rev\Form\Form
namespace rev\Form;
use \rev\Error;

/**
 * HTML <form> handling class
 * Simplyfy all stuff ;)
 */
class Form {
	/** All fields objects */
	protected $fields;
	/** Form name */
	protected $name;
	/** Form error string */
	protected $error = null;
	/** Was form submitted? */
	protected $submitted = false;
	/** Form definition, without fields */
	protected $def;

	/**
	 * Construct form
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
		if (is_string($def)) {
			$name = $def;
			$def = \rev\File::jsonFromFile(\rev\View::getCurrentBasepath().'/form/'.$def.'.json');
			$def['name'] = $name; unset($name);

			if (!is_array($def))
				throw new Error('Form: could not read form definition');
		} elseif (!is_array($def))
			throw new Error("Form: definition must be an array or path");

		$this->name = isset($def['name']) ? $def['name'] : hash('crc32b', json_encode($def['name']));
		if (!isset($def['attributes']))
			$def['attributes'] = NULL;

		foreach ($def['fields'] as $k => $v) {
			if ($v[0] == 'raw') {
				$this->fields[$k] = $v[1];
				continue;
			}

			$type = '\\rev\\Form\\Field\\'.$v[0];
			unset($v[0]);
			$name = $this->name.'::'.$k;

			$this->fields[$k] = new $type($name, $v);
			if (isset($_POST[$name])) {
				$this->fields[$k]->value = $_POST[$name];
				unset($_POST[$name]);
				$this->submitted = true;
			}
		}

		unset($def['name'], $def['fields']);
		$this->def = $def;
	}

	/** Set RAW VALUE for specified array keys */
	public function set(array $in) {
		foreach ($in as $k => $v)
			if (array_key_exists($k, $this->fields))
				$this->fields[$k]->raw_value = $v;

		return $this;
	}

	/** Return all RAW VALUES from fields */
	public function get($key = null) {
		if (isset($key)) {
			if (is_array($key)) {
				$r = [];
				foreach ($key as $v)
					$r[$v] = $this->fields[$v]->raw_value;
				return $r;
			} elseif (is_string($key)) {
				return $this->fields[$key]->raw_value;
			}
		} else {
			$r = [];
			foreach ($this->fields as $k => $o)
				$r[$k] = $o->raw_value;
			return $r;
		}
	}

	/**
	 * Return array imploded in HTML attrib syntax
	 * @param $in
	 * 	key => [val, lav]
	 * 	becomes
	 * 	key="val lav"
	 * @return string
	 */
	public static function attr(array $in) {
		$attr = '';
		if (isset($in))
			foreach ($in as $k => $v) {
				$attr .= ' ';
				if (is_numeric($k)) {
					$attr .= (isset($v) && $v ? $v : '');
				} else {
					$attr .= $k;
					if (isset($v) && $v) {
						if (is_array($v))
							$v = implode(' ', $v);
					} else
						continue;
					$attr .= '="'.$v.'"';
				}
			}
		return $attr;
	}

	/** Assign error message to field or whole form */
	// public function error($msg, $field = null) {
	// 	if ($field === null)
	// 		$this->error = $msg;
	// 	else
	// 		$this->fields[$field]->error = $msg;
	// }

	/** Render form */
	public function r() {
		echo '<form method="post" name="',$this->name,'"', self::attr($this->def['attributes']), '>';
		if ($this->error)
			echo '<span class="form-error">', $this->error, '</span>';
		foreach ($this->fields as $k => $v) {
			if (is_string($v)) {
				echo $v;
				continue;
			}

			echo '<div class="field-wrap">';
			$v->render();
			echo '</div>';
		}
		echo '</form>';
	}

	/** Return properties value (reassignment-proof) */
	public function __get($name) {
		return $this->{$name};
	}
}
