<?php $meid = $this->guard_user();
$opt = CMS\Vars::get(array('accept', 'reject', 'send'));
if ($opt) {
	$fid = CMS\Vars::uri('user');
	if (DB('User')->select()->where(array('user_id' => $fid))->bool()) {
		$row = UserFriends::getRow($meid, $fid);

		//send request
		if (!$row && isset($opt['send']))
			DB('UserFriends')->insert(array('user_one' => $meid, 'user_two' => $id))->exec();
		//alter request, if it's been sent to us
		elseif ($row) {
			if (isset($opt['reject'])) {
				if ($row['status'] === null && $row['user_two'] == $meid) //sent to us
					$status = array('status' => true);
				elseif ($row['status'] === true)
					$status = array('status' => false);
			} elseif (isset($opt['accept']) && $row['status'] === null && $row['user_one'] == $fid && $row['user_two'] == $meid)
				$status = array('status' => false);

			if (isset($status))
				DB('UserFriends')->update($status)->where(array('user_one' => $row['user_one'], 'user_two' => $row['user_two']))->exec();
		} else
			throw new Error400('Invalid friend request');
		$this->redirect('');
	}
}

$this->guard_nonrequest();
?>
<div class="friends-panel">
<?
$result = DB('SELECT user_id, login FROM User WHERE user_id IN (
	SELECT user_one FROM UserFriends WHERE user_two = ? AND status IS NULL
)')->param('i', $meid)->objs('CMS\\User');
if ($result): ?>
<div class="panel-requests">
<h5>Zaproszenia do znajomych:</h5>
<? foreach ($result as $v): ?>
	<div class="panel-item"><a href="<?= User::getViewLink($v->get('login')) ?>"><?= $v->get('login') ?></a>
		<a href="/user/friends:<?= $v->get('user_id') ?>?accept">Akceptuj</a>
		<a href="/user/friends:<?= $v->get('user_id') ?>?reject">Odrzuć</a>
	</div>
<? endforeach; ?>
</div>
<? endif; ?>
<h5>Znajomi</h5>
<?
$result = DB('SELECT user_id, login, ts_seen FROM User WHERE user_id IN (
	SELECT user_one AS id FROM UserFriends WHERE user_two = ? AND user_one != ? AND status = 1
	UNION
	SELECT user_two AS id FROM UserFriends WHERE user_one = ? AND user_two != ? AND status = 1
)')->params('iiii', array($meid, $meid, $meid, $meid))->objs('CMS\\User');
foreach ($result as $v): ?>
    <a class="panel-item<?= ($v->get('ts_seen') + 36000) ? ' active' : '' ?>" href="<?= User::getViewLink($v->get('login')) ?>"><?= $v->get('login') ?></a>
<? endforeach; ?>
</div>
