<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<meta name="author" content="MikWaw aka theMaxx (C)2014" />
	<? CMS\View::head(); ?>
	<link type="text/css" href="/r3v/scss:style" rel="stylesheet" />
	<title><?= CMS\View::title() ?></title>
</head>
<body>
<div id="main">
	<a href="/" id="logo"><small><span class="blue">t</span>h<span class="blue">e</span></small><span class="blue">O</span>rganizer <small class="orange">dev</small></a>
	<? CMS\View::body() ?>
</div>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/static/script.js"></script>
<div id="footer"><? CMS\View::footer() ?> [&copy; 2014]</div>
</body>
</html>
