<div class="post-meta">
	<div class="post-title"><?= $single['name'] ?></div>
	<span class="post-info"><?= datef($single['ts_publ']) ?></span><span class="post-info">&#8635; <?= datef($single['ts_mod'], true) ?></span><span class="post-info tags"><a href="/blog/cat:one">tag1</a>, <a href="/blog/cat:two">tag2</a></span>
</div>
<div class="clear"></div>

<?= $single['content'] ?>