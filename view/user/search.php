<? $me = $this->guard_user();

$form = new Form(array(
	'name' => 'usersearch',
	'placeholders' => TRUE,
	'fields' => array(
		'search' => array(
			'search',
			'label' => 'Szukaj użytkowników',
			'attributes' => array(
				'class' => 'wide',
			),
		),
		'submit' => array(
			'submit',
			'value' => 'Szukaj'
		),
	),
));

$form->r();

if ($form->submitted && ($search = $form->get('search'))):
	$search = '%'.str_replace(array('%','_'), array('\%','\_'), $search).'%';
	$rows = DB('User')->select('login')->where('login LIKE ?')->param('s', $search)->vals();
foreach ($rows as $v):
?>

<a href="<?= User::getViewLink($v) ?>"><?= $v ?></a><br>

<? endforeach;endif; ?>