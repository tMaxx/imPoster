<div id="toppanel"><? $this->subnode('/user/panel') ?></div>
<div class="clear"></div>
<? $this->subnode('/menu') ?>
<? $this->subnode('/user/notices') ?>
<div class="content">
	<? $this->node('welcome') ?>
</div>
<?
$q = new DB("SELECT * FROM Elem");
pre_dump($q->rows());
