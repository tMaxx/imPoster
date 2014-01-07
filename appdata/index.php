<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<meta name="author" content="MikWaw aka theMaxx (C)2013" />
	<? CMS\View::head(); ?>
	<link type="text/css" href="/static/style.css" rel="stylesheet" />
	<title><?= CMS\View::title() ?></title>
</head>
<body>
<div id="main">
	<a href="/" id="logo"><small><span class="blue">t</span>h<span class="blue">e</span></small><span class="blue">O</span>rganizer <small class="orange">dev</small></a>
	<? CMS\View::body() ?>
	<div id="footer"><? CMS\View::footer() ?></div>
</div>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/static/script.js"></script>
</body>
</html>
