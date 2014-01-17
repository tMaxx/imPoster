<? $this->guard_user();
$this->node('', array('user_id' => CMS\Me::id(), 'task' => (int) CMS\Vars::URI('task')));
