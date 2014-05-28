<?php
namespace tmx;

/**
 * Blog things
 */
class Blog {

	/**
	 * Get tag list for blog id
	 * @param $id
	 * @return array of tags
	 */
	public static function getTags($id) {
		return \rev\DB\Q('SELECT name FROM Tags WHERE blog_id=?')->param('i', $id)->vals();
	}

	public static function crudParseMarkdown(&$val, &$fields) {
		\rev\Mod::loadMod('lib/parsedown', 'composer');

		$pd = new \Parsedown();

		$fields['content'] = $pd->text($val);
	}
}
