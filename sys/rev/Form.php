<?php ///rev engine \rev\Form
namespace rev;

/**
 * HTML <form> handling class
 * Simplyfy all stuff ;)
 */
class Form {
	/** All fields objects */
	protected $fields;
	/** Form name (<form name=...>) */
	protected $name;
	/** Form error string */
	protected $error = null;
	/** Was this form submitted? */
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
	 * @param $fname name field override in def
	 */
	function __construct($def, $fname = null) {
		if (is_string($def)) {
			$name = $def;
			$def = File::jsonFromFile(View::getCurrentBasepath().'/form/'.$def.'.json');
			$def['name'] = $name; unset($name);

			if (!is_array($def))
				throw new Error('Form: could not read form definition');
		} elseif (!is_array($def))
			throw new Error("Form: definition must be an array or path");

		$this->name = isset($def['name']) ? $def['name'] : (
				$fname !== null ? $fname : hash('crc32b', json_encode($def))
			);
		if (!isset($def['attributes']))
			$def['attributes'] = null;

		foreach ($def['fields'] as $k => $v) {
			if (!isset($v[0])) {
				if (isset($v['type']))
					$v[0] = $v['type'];
				else
					throw new Error('Form: no field type set');
			}

			switch (strtolower($v[0])) {
				case 'raw':
					$this->fields[$k] = isset($v[1]) ? $v[1] : $v['content'];
					continue 2; //foreach
					break;

				case 'string':
					$v[0] = 'text';
				case 'submit':
					if ($v[0] == 'submit') $has_submit = true;
				case 'email':
				case 'password':
				case 'text':
				case 'search':
				case 'file':
					$type = '\\rev\\Field\\Base';
					break;

				default:
					$v[0][0] = strtoupper($v[0][0]);
					$type = '\\rev\\Field\\'.$v[0];
					break;
			}

			$name = $this->name.'::'.$k;

			$this->fields[$k] = new $type($name, $v);
			if (isset($_POST[$name])) {
				$this->fields[$k]->value = $_POST[$name];
				unset($_POST[$name]);
				$this->submitted = true;
			}
		}

		if (empty($has_submit) && empty($def['no_submit']))
			$this->fields['__submit'] = new \rev\Field\Base($this->name.'::__submit', ['submit', 'value'=>'Submit']);

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
				if (is_object($o))
					$r[$k] = $o->raw_value;
			return $r;
		}
	}

	/**
	 * Return array imploded in HTML5 attrib syntax
	 * @param $in
	 * 	key => [val, lav] becomes key="val lav"
	 * 	key => false        ==>   key
	 * 	(int) => type       ==>   type
	 * @return string
	 */
	public static function attr(array $in) {
		if (!$in)
			return '';

		$attr = '';
		foreach ($in as $k => $v) {
			$attr .= ' ';
			if (is_numeric($k))
				$attr .= empty($v) ? '' : $v;
			else {
				$attr .= $k;
				if (!$v)
					continue;
				if (is_array($v))
					$v = implode(' ', $v);
				$attr .= '="'.$v.'"';
			}
		}
		return $attr;
	}

	/** Render form */
	public function r() {
		echo '<form method="post" name="',$this->name,'"', isset($this->def['attributes']) ? self::attr($this->def['attributes']) : '', '>';
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
		if ($name == 'values') {
			$ret = [];
			foreach ($this->fields as $k => $f)
				if (is_object($f) && $k != 'submit')
					$ret[$k] = $f->raw_value;
			return $ret;
		}
		return $this->{$name};
	}

	/** Set internal property value */
	public function __set($name, $val) {
		if ($name == 'error') {
			return $this->error = $val;
		} elseif (is_array($val)) {
			if ($name == 'def') {
				foreach ($val as $k => $v)
					$this->def[$k] = $v;
				return $this->def;
			} elseif ($name == 'values') {
				foreach ($this->fields as $k => $f)
					if (is_object($f) && array_key_exists($k, $val))
						$this->fields[$k]->raw_value = $val[$k];
				return;
			}
		}
		throw new Error("Form: property '$name' setting not allowed");
	}
}
