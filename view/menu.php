<? $this->guard_nonrequest() ?>
<div id="menu">
	<a href="/" id="cur">Strona główna</a>
<? if (CMS\Me::id()): ?>
	<a href="/task/my">Moje zadania</a>
<? endif; ?>
</div>
<div class="clear"></div>
