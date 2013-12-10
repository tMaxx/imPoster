<div id="toppanel"><? $this->subnode('/user/panel') ?></div>
<div class="clear"></div>
<? $this->subnode('/menu') ?>
<? $this->subnode('/user/notices') ?>
<div class="content">
	<? $this->node('welcome') ?>
</div>
<?
pre_dump(Model::getPK(), (new Model)->getID());
new DB("SELECT *");