<?php ///rev engine \rev\Form\Field\Select
namespace rev\Form\Field;

/** Select field */
class Select extends Base {

}

/*
echo '<select name="', $name, '" ', $attr, '>';
foreach ($v['options'] as $ok => $ov)
	echo '<option value="', $ok, '"', (($val === $ok) ? ' selected' : ''), '>', $ov, '</option>';
echo '</select>';
*/
