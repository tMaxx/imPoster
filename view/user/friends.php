<?php $this->guard_nonrequest(); $meid = $this->guard_user(); ?>
<div class="friends-panel">
<?
$result = DB('SELECT user_id, login FROM User WHERE user_id IN (
	SELECT user_one FROM UserFriends WHERE user_two = ? AND status IS NULL
)')->param('i', $meid)->objs('CMS\\User');
if ($result): ?>
<div class="panel-requests">
<h4>Zaproszenia do znajomych:</h4>
<? foreach ($result as $v): ?>
	<div class="panel-item"><?= $v->get('login') ?>
		<a href="/user/friends:<?= $v->get('user_id') ?>?accept">Akceptuj</a>
		<a href="/user/friends:<?= $v->get('user_id') ?>?reject">Odrzuć</a>
	</div>
<? endforeach; ?>
</div>
<? endif;
$result = DB('SELECT user_id, login, ts_seen FROM User WHERE user_id IN (
	SELECT user_one AS id FROM UserFriends WHERE user_two = ? AND user_one != ? AND status = 1
	UNION
	SELECT user_two AS id FROM UserFriends WHERE user_one = ? AND user_two != ? AND status = 1
)')->params('iiii', array($meid, $meid, $meid, $meid))->objs('CMS\\User');
foreach ($result as $v): ?>
    <div class="panel-item<?= ($v->get('ts_seen') + 36000) ? ' active' : '' ?>"><?= $v->get('login') ?></div>
<? endforeach; ?>
</div>
