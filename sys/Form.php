<?php
/**
 * HTML <form> handling class
 * Simplyfy all stuff ;)
 */
class Form {

	protected $fields;
	protected $values;
	protected $name;
	protected $submitted = false;
	protected $def;

	/**
	 * 
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
		unset($def['name'], $def['fields']);
		$this->values = array();
		$this->def = $def;
		if (!isset($this->def['attributes']))
			$this->def['attributes'] = NULL;

		$this->submitted();
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
			foreach ($in as $ak => $av)
				$attr .= ' '.$ak. (isset($av) ? '="'.$av.'"' : '');
		return $attr;
	}

	///Render form
	public function r() {
		echo '<form method="post"', self::attrib($this->def['attributes']), '>';
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

			$attr = '';
			if (isset($v['attributes']))
				$attr = self::attrib($v['attributes']);

			$name = $this->name.'::'.$k;
			echo '<label>';
			if (isset($v['label']) && $v['label'])
				echo '<span class="form-label">'.$v['label'].'</span>';
			switch ($type) {
				//basic types
				case 'email':
				case 'password':
				case 'text':
				case 'submit':
				case 'search':
				case 'file';
				case 'checkbox':
					echo '<input type="', $type, '" name="', $name,'" ', $attr, ' value="', $val, '" />';
					break;
				//select
				case 'select': {
					///TODO: check following option
					echo '<select name="', $name, '" ', $attr, " >";
					foreach ($v['options'] as $ok => $ov)
						echo '<option value="', $ok, '"', (($val === $ok) ? ' selected' : ''), '>', $ov, '</option>';
					echo '</select>';
					break;
				}
				case 'textarea': {
					echo '<textarea name="', $name, '" ', $attr, '>';
					echo nl2br($val);
					echo '</textarea>';
					break;
				}
				default:
					throw new ErrorCMS('Unsupported field type');
					break;
			}
			echo '</label>';
		}
		echo '</form>';
	}

	public function submitted() {
		if (!$this->submitted && CMS::varIsSet('POST', array_keys($this->fields))) {
			$r = array();
			foreach ($this->fields as $k => $v)
				$r[] = $k;

			$this->values = CMS::vars('GET', $r, NULL, TRUE);
			$this->submitted = true;
		}
		return $this->submitted;
	}
}
