<?
switch (rev\Vars::uri('user')) {
	case 'login':
		rev\Auth\User::login_redirect();
		break;

	case 'logout':
		rev\Auth\User::logout();
		$this->redirect('/');
		break;

	case 'callback':
		if (!isset($_GET['code']))
			throw new rev\Error400();
		$r = rev\Auth\User::callback_auth($_GET['code']);
		if (!$r)
			$this->redirect('/');
		else
			return [
				'/user/notice',
				'msg' => $r
			];
		break;

	case 'dump':
		if (DEBUG) {
			vdump(rev\Auth\Session::dump());
			break;
		}
}
throw new rev\Error404();
