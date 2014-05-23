<?
\rev\Mod::loadMod('lib/google-authenticator');
\rev\Mod::loadMod('lib/endroid-qrcode', 'composer');

$ga = new Symm\GoogleAuthenticator\GoogleAuthenticator(
		'me@themaxx.pl', \rev\Conf::get('locker_secret')
	);
$ga->setIssuer('themaxx.pl');


$uri = \rev\Vars::uri(['locker']);
if ($uri) {
	if ($uri['locker'] != 'auth')
		return [404];
	\rev\Auth\User::roleThrow('admin');

	$ga->setQrCodeGenerator(new Symm\GoogleAuthenticator\QRCodeGenerator\EndroidQrCodeGenerator());

	return [
		'/locker_auth',
		'img_data' => $ga->getQRCodeUrl(),
	];
}

$form = new \rev\Form([
	'fields' => [
		'content' => [
			'Textarea',
			'attributes' => [
				'class' => 'big wide'
			]
		],
		'code' => [
			'text',
			'attributes' => [
				'placeholder' => 'kooood'
			]
		],
		'submit' => [
			'submit',
			'value' => 'Send into oblivion!'
		]
	]
]);

if ($form->submitted) {
	if ($ga->verifyCode($form->fields['code']->value)) {
		$ins = \rev\DB\Q('Locker')->insert([
			'ts' => NOW,
			'content' => $form->fields['content']->raw_value,
			'note' => 'Src: '.$ga->getAccountName()
		])->iid();

		if ($ins)
			return [
				'/success',
				'message' => 'Message successfully sent into oblivion'
			];
		else
			throw new Error500('Could not add new entry to locker');
	} else {
		$code = ($form->fields['code']);
		$code->error = 'Invalid code';
	}
}

return [
	'form' => $form
];
