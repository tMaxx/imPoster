<div class="post-meta">
	<div class="post-title"><?

		if (empty($header['link_it'])):
			echo $header['name'];
		else:
			?><a href="/blog:<?= $header['id'] ?>"><?= $header['name'] ?></a><?
		endif;

	?></div><span class="post-info"><?= datef($header['ts_publ']) ?></span><?

	if (isset($header['ts_mod'])):
		?><span class="post-info">&#8635; <?= datef($header['ts_mod'], true) ?></span><?
	endif;

	if (!empty($header['tags'])):
		?><span class="post-info tags"><?
			foreach ($header['tags'] as &$t)
				$t = '<a href="/blog/cat:'.urlencode($t).'">'.$t.'</a>';
			unset($t);reset($header['tags']);

			echo implode(', ', $header['tags']);
		?></span><?
	endif;

?></div>
