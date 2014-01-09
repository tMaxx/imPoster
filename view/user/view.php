<?
if (!CMS\Me::id())
	throw new Error403();
?>
<h2>Profil użytkownika</h2>
Email: <b><?= str_replace(array('@', '.'), array(' at ', ' dot '), CMS\Me::$me->get('email')) ?></b>
<br>
Login: <b><?= CMS\Me::$me->get('login') ?></b>