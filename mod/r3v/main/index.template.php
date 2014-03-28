<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<meta name="author" content="MikWaw aka theMaxx (C)2014" />
	<?= implode(self::$HTML_head) ?>
	<link type="text/css" href="/static/scss:style" rel="stylesheet" />
	<title><?= self::title() ?></title>
</head>
<body>
<div id="main">
	<a href="/" id="logo"><small>r3v</small><span class="blue">M</span>aksiu<small class="orange">'s dragons</small></a>
	<?= $BODY ?>
</div>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/static/js:script"></script>
<div id="footer"><span id="exec-time"><?= ms_from_start() ?>ms</span> [&copy; 2014]</div>
</body>
</html>
