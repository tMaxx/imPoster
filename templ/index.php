<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<meta name="author" content="MikWaw aka theMaxx (C)2013" />
	<? View::head(); ?>
	<link type="text/css" href="/static/style.css" rel="stylesheet" />
	<title><?= View::title() ?></title>
</head>
<body>
<div id="main">
	<a href="/" id="logo"><small><span class="blue">t</span>h<span class="blue">e</span></small><span class="blue">O</span>rganizer</a>
	<? View::body() ?>
	<div id="footer"><? View::footer() ?></div>
</div>
</body>
</html>