<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<meta name="author" content="MikWaw aka theMaxx (C)2014" />
	<? r3v\View::head(); ?>
	<link type="text/css" href="/r3v/scss:style" rel="stylesheet" />
	<title><?= r3v\View::title() ?></title>
</head>
<body>
<div id="main">
	<a href="/" id="logo"><small><span class="blue">t</span>h<span class="blue">e</span></small><span class="blue">O</span>rganizer <small class="orange">beta</small></a>
	<? r3v\View::body() ?>
</div>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/static/script.js"></script>
<div id="footer"><? r3v\View::footer() ?> [&copy; 2014]</div>
</body>
</html>
