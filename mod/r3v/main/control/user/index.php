<?
switch ($r = r3v\Vars::uri('user')) {
	case 'login':
		r3v\Auth\User::login_redirect();
		break;

	case 'logout':
		r3v\Auth\User::logout();
		$this->redirect('/');
		break;

	case 'callback':
		if (!isset($_GET['code']))
			throw new r3v\Error400();
		$r = r3v\Auth\User::callback_auth($_GET['code']);
		break;
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
			vdump(r3v\Auth\Session::dump());
			break;
		}

	default:
		if ($r)
			throw new Error404();

		return [
			'/user/notice',
			'msg' => 'Hi!'
		];
		break;
}
