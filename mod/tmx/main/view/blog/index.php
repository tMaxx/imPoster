<h2>Archiwum wpisów</h2>

<? if ($entr):
	foreach ($entr as $single):
		$single['tags'] = tmx\Blog::getTags($single['id']);
		$single['link_it'] = true;
		$this->sub(
			'/blog/index',
			['header' => $single]
		);
?>
	<div class="clear"></div>
<? endforeach;
	else: ?>
	<h3 class="blueish">Brak wpisów</h3>
<? endif; ?>

<code>TODO: dorzuć paginację...</code>
