<?php ///r3vCMS /sys/Form.php

/**
 * HTML <form> handling class
 * Simplyfy all stuff ;)
 */
class Form {

	protected $fields;
	protected $values;
	protected $name;
	protected $submitted;
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
		if (!is_array($def))
			throw new CMS\Error("Form definition must be an array");
			
		$this->name = $def['name'];
		$this->fields = $def['fields'];
		unset($def['name'], $def['fields']);
		$this->values = array();
		$this->def = $def;
		if (!isset($this->def['attributes']))
			$this->def['attributes'] = NULL;

		$field_keys = array_keys($this->fields);
		$prefix = $this->name.'::';
		foreach ($field_keys as $k => $v)
			$field_keys[$k] = $prefix.$v;

		$field_keys = CMS\Vars::POST($field_keys, true);
		if ($field_keys) {
			$prefix = strlen($prefix);
			foreach ($field_keys as $k => $v)
				$this->values[substr($k, $prefix)] = $v;
			$this->submitted = true;
		} else
			$this->submitted = false;
	}

	public function set(array $in) {
		foreach ($in as $k => $v)
			if (array_key_exists($k, $this->fields))
				$this->values[$k] = $v;

		return $this;
	}

	public function get($key = NULL) {
		if (isset($key)) {
			if (is_array($key)) {
				$r = array();
				foreach ($key as $v)
					$r[$v] = array_key_exists($v, $this->values) ? $this->values[$v] : NULL;
				return $r;
			} elseif (is_string($key)) {
				$r = array_key_exists($key, $this->values) ? $this->values[$key] : NULL;
				return $r;
			}
		} else
			return $this->values;
	}

	protected static function attrib($in) {
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

	public function error($msg, $field = NULL) {
		if ($field === NULL)
			$this->def['error'] = $msg;
		elseif (isset($this->fields[$field]))
			$this->fields[$field]['error'] = $msg;
	}

	///Render form
	public function r() {
		echo '<form method="post"', self::attrib($this->def['attributes']), '>';
		if (isset($this->def['error']))
			echo '<span class="form-error">', $this->def['error'], '</span>';
		foreach ($this->fields as $k => $v) {
			if (($type = $v[0]) == 'raw') {
				echo $v[1];
				continue;
			}

			$val = '';
			if (isset($this->values[$k]))
				$val = $this->values[$k];
			elseif (isset($v['value']))
				$val = $v['value'];

			if (isset($this->def['placeholders']) && $v[0] != 'submit') {
				$v['attributes']['placeholder'] = $v['label'];
				$v['label'] = '';
			}

			$attr = '';
			if (isset($v['attributes']))
				$attr = self::attrib($v['attributes']);

			$name = $this->name.'::'.$k;
			echo '<span class="form-label">';
			if (isset($v['label']) && $v['label']) {
				if ($type == 'submit' && !$val)
					$val = $v['label'];
				else
					echo '<span class="form-label-text">'.$v['label'].'</span>';
			}
			switch ($type) {
				case 'string':
					$type = 'text';
				//basic types
				case 'email':
				case 'password':
				case 'text':
				case 'submit':
				case 'search':
				case 'file';
				case 'checkbox':
					echo '<input type="', $type, '" name="', $name,'" ', $attr, ' value="', $val, '">';
					break;
				//select
				case 'select': {
					///TODO: check following option
					echo '<select name="', $name, '" ', $attr, '>';
					foreach ($v['options'] as $ok => $ov)
						echo '<option value="', $ok, '"', (($val === $ok) ? ' selected' : ''), '>', $ov, '</option>';
					echo '</select>';
					break;
				}
				case 'textarea': {
					echo '<textarea name="', $name, '" ', $attr, '>', nl2br($val), '</textarea>';
					break;
				}
				default:
					throw new CMS\Error('Unsupported field type');
					break;
			}
			if (isset($v['error']))
				echo '<span class="form-error">', $v['error'], '</span>';
			echo '</span>';
		}
		echo '</form>';
	}

	public function submitted() {
		return $this->submitted;
	}

	public function __get($name) {
		switch ($name) {
			case 'submitted':
				return $this->submitted;
				break;
		}
	}
}
