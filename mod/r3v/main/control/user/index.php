<?
use r3v;
switch (Vars::uri('user')) {
	case 'login':
		Auth\GAuth::login_redirect();
		break;

	case 'logout':
		Auth\GAuth::logout();
		$this->redirect('/');
		break;

	default:
		# code...
		break;
}





//$this->node('view');
