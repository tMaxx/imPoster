<? $meid = $this->guard_user();
if ($id = CMS\Vars::uri('user')):
	$obj = DB('User')->select('user_id, login')->where('login = ?')->param('s', $id)->obj();
else: ?>
<h2>Profil użytkownika</h2>
Email: <b><?= str_replace(array('@', '.'), array(' at ', ' dot '), CMS\Me::$me->get('email')) ?></b>
<br>
Login: <b><?= CMS\Me::$me->get('login') ?></b>
<? endif;
if (isset($obj)): ?>
<h2>Użytkownik <?= $obj->getLogin() ?></h2>
<? if (!UserFriends::getRow($meid, $obj->getID())): ?>
	<a href="/user/friends:<?= $obj->getID() ?>?send">Dodaj do znajomych</a>
<? endif;endif; ?>
