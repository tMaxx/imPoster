<?php ///rev engine \rev\Form\Field\Textarea
namespace rev\Form\Field;

/** Textarea field */
class Textarea extends Base {
	/** Render textarea field */
	public function renderInput() {
		$val = $this->value;
		if (!isset($val) && isset($this->def['value']))
			$val = $this->def['value'];

		echo '<textarea name="', $this->name, '" ', $this->attr(), '>', $val, '</textarea>';
	}
}
