<h2>arch</h2>

<? if ($entr):
	foreach ($entr as $single):
		if (isset($overflow)) {
			if ($overflow > 0)
				$overflow--;
			else {
				?><div class="separator">//-=-n-e-x-t-=-p-a-g-e-=-</div><?
				unset($overflow);
			}
		}
		$single['tags'] = tmx\Blog::getTags($single['id']);
		$single['link_it'] = true;
		$single[0] = '/blog/header';
		$this->view($single);
?>
	<div class="clear"></div>
<? endforeach;
	else: ?>
	<h3 class="blueish">Brak wpisów</h3>
<? endif; ?>

<div class="nav">
<? foreach ($pg as $_ => $p):
	if (is_array($p)): ?><a href="/blog/?page=<?= $p[1] ?>"><?= $p[0] ?></a><?
	else: ?><span><?= $p ?></span><?
	endif;
endforeach;?>
</div>
