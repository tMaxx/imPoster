<?php ///rev engine \rev\Field\Multiple
namespace rev\Field;

/** Multiple field handler */
class Multiple extends Base {
	protected $value = [];

	protected function renderSubInput($index, $value) {
		echo '<input name="', $this->name, $index, '"',
			$value ? ' value="'.htmlspecialchars($value).'"' : '',
			$this->attr(), '/>';
	}

	protected function renderInput() {
		$count = count($value) + 1;
		if (empty($this->def['fields']))
			$this->def['fields'] = '';
		$arr = is_array($this->def['fields']);

		for ($i=0; $i < $count; $i++)
			if ($arr)
				foreach ($this->def['fields'] as $f)
					$this->renderSubInput("[$i][$f]", !isset($this->value[$i][$f]) ?: $this->value[$i][$f]);
			else
				$this->renderSubInput("[$i]");
	}
}
