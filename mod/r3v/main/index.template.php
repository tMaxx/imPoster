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
<div id="main">
	<a href="/" id="logo"><small>the</small><big>M</big>aksiu &nbsp; <small class="blue">burza w szklance wody</small></a>
	<?= $BODY ?>
</div>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/static/js:script"></script>
	<script src="/static/js:ga"></script>
<div id="footer">{"env":"<?= r3v\Conf::envType() ?>", "exec_ms":<?= ms_from_start() ?>, ["&amp;copy;", 2014]}</div>
</body>
</html>
