<h2>Archiwum wpisów</h2>

<? foreach ($entr as $single): ?>
<div class="post-meta">
	<div class="post-title"><a href="/blog:<?= $single['id'] ?>"><?= $single['name'] ?></a></div>
	<span class="post-info"><?= datef($single['ts_publ']) ?></span><span class="post-info tags"><a href="/blog/cat:one">tag1</a>, <a href="/blog/cat:two">tag2</a></span>
</div>
<div class="clear"></div>
<? endforeach; ?>

<code>TODO: dorzuć paginację...</code>