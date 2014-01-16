<?php $this->guard_nonrequest();$this->guard_user();
$meid = CMS\Me::id();
$result = DB('SELECT user_id, login, ts_seen FROM User WHERE user_id IN (
	SELECT user_one AS id FROM UserFriends WHERE user_two = ? AND user_one != ? AND status = 1
	UNION
	SELECT user_two AS id FROM UserFriends WHERE user_one = ? AND user_two != ? AND status = 1
)')->params(array($meid, $meid, $meid, $meid))->objs('CMS\\User');
?>
<div class="friends-panel">
<? foreach ($result as $v): ?>
    <div class="friends-panel-item<?= ($v->get('ts_seen') + 36000) ? 'active' : '' ?>"><?= $v->get('login') ?></div>
<? endforeach; ?>
</div>
