<? $this->guard_user();
if (!$task)
	throw new Error400('Nie wybrano zadania');

$el = DB('Elem')->select()->where(['elem_id' => $task])->obj();
if (!$el)
	throw new Error404();
Elem::auth($el);

/*
Assign to friend/noone
Mark as done
ping someone
*/
switch (CMS\Vars::uri('actions')) {
	case 'assign': {
		$options = [
			NULL => 'Nikt. Smuteczek.',
			CMS\Me::id() => 'Ja, wybierz mnie!!',
		];
		$options += UserFriends::getFriendsPairs();

		$form = new Form([
			'name' => 'taskactions',
			'fields' => [
				'user_dest' => [
					'select',
					'options' => $options,
					'label' => 'Znajomi',
				],
				'sub' => [
					'submit',
					'value' => 'Zapisz',
				],
			],
		]);

		if ($form->submitted) {
			if ($uid = $form->get('user_dest'))
				$uid = (int) $uid;
			else
				$uid = NULL;
			$el->setUserDest($uid);
			DB($el)->save();
			$this->redirect($el->getViewLink());
		} else
			$form->set($el->toArray()); ?>
<h4>Przypisujesz wpis: <a href="<?= $el->getViewLink() ?>"><?= $el->getName() ?></a></h4>
<?		$form->r();
	}	break;
	case 'finished': {
		if (!$el->getIsRead()) {
			$el->setIsRead(true);
			DB($el)->save();
		}
		$this->redirect($el->getViewLink());
	}	break;

	default: {
		throw new Error400('Brak akcji');
	}	break;
}