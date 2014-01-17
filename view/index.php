<div id="toppanel"><? $this->subnode('/user/panel') ?></div>
<div class="clear"></div>
<? $this->subnode('/menu') ?>
<? $this->subnode('/user/notices') ?>
<div class="content">
	<? $this->node('welcome') ?>
</div>
<? if (CMS\Me::id()) $this->subnode('/user/friends') ?>
