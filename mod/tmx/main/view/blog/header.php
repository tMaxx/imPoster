<div class="post-meta">
	<div class="post-title"><?

		if (empty($link_it)):
			echo $name;
		else:
			?><a href="/blog:<?= $id ?>"><?= $name ?></a><?
		endif;

	?></div><span class="post-info"><?= datef($ts_publ) ?></span><?

	if (isset($ts_mod)):
		?><span class="post-info">&#8635; <?= datef($ts_mod, true) ?></span><?
	endif;

	if (!empty($tags)):
		?><span class="post-info tags"><?
			foreach ($tags as &$t)
				$t = '<a href="/blog/cat:'.urlencode($t).'">'.$t.'</a>';
			unset($t);reset($tags);

			echo implode(', ', $tags);
		?></span><?
	endif;

	if (isset($ts_mod)):
		?><span class="post-info">&#8635; <?= datef($ts_mod, true) ?></span><?
	endif;

	if (isset($is_draft)):
		?><span class="post-info <?= $is_draft ? 'red' : 'green' ?>"><?= $is_draft ? '&#x2718;' : '&#x2714;' ?></span><?
	endif;


?></div>
