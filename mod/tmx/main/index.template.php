<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="Dziwno, straszno, ale (chyba) ciekawie. Przynajmniej taką mam nadzieję." />
	<meta name="author" content="MikWaw aka theMaxx (C)2014" />
	<?= implode(self::$HTML_head) ?>
	<link type="text/css" href="/static/scss:style" rel="stylesheet" />
	<title><?= self::title() ?></title>
</head>
<body>
<div id="logo">
	<div class="cnt-set">
		<a href="/" class=""><small>the</small><big>M</big>aksiu&nbsp;&nbsp;<small class="blue">burza w szklance wody</small></a>
	</div>
</div>
<div class="cnt-set">
	<?= $BODY ?>
</div>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/static/js:script"></script>
	<script src="/static/js:ga"></script>
<div id="footer">["<?= r3v\Conf::envType() ?>", <?= ms_from_start() ?>, {"&amp;copy;": <?= (($a=date('Y')) == '2014') ? 2014 : '"2014-'.$a.'"' ?>}]</div>
</body>
</html>
