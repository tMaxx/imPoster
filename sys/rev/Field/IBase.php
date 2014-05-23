<?php ///rev engine \rev\Field\IBase
namespace rev\Field;

/**
 * Base interface for all fields
 * 	NOTE: Form generator expects
 * 	class property $value to be
 * 	accessible at all times.
 * 	For reference see \rev\Field\Base.
 */
interface IBase {

	/**
	 * Constructor
	 * @param $name field name
	 * @param $def field definition
	 */
	function __construct($name, $def);

	/**
	 * Echoes field content
	 * Should echo in a format like this:
	 * 	<label>
	 * 		text/title
	 * 		{input}
	 * 	</label>
	 */
	function render();
}
