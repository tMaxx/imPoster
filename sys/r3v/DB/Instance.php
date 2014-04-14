<?php ///rev engine /sys/DB/.php
namespace r3v\DB;

/**
 * Instance - model instance handler
 */
class Instance extends Base {
	///Instance
	protected $inst;

	function __construct(&$inst) {
		$this->inst = &$inst;
	}

	public function __destruct() {
		$this->inst = NULL;
	}

	///Reset state
	public function reset() {
		if (is_object($this->stmt))
			$this->stmt->close();
		$this->stmt = NULL;
		$this->stmt_types = '';
		$this->stmt_param = array();
		$this->query = '';
		$this->query_result = NULL;

	}

	///Perform saving of instance - insert or update
	public function save() {
		if (!$this->inst->getId())
			return $this->insert();

		$this->reset();

		if (method_exists($this->inst, 'preSave'))
			$this->inst->preSave();

		$vals = $this->inst->toArray();
		$pk_key = (array) $this->inst->getKeyName();
		foreach ($pk_key as $v)
			unset($vals[$v]);
		$pk_val = (array) $this->inst->getId();

		$vals_list = $this->implode(', ', $vals, true, true);
		$pks_list = $this->implode(' AND ', array_combine($pk_key, $pk_val), true);

		$this->query = 'UPDATE '.$this->inst->getTableName().' SET '.$vals_list.' WHERE '.$pks_list;
		$this->exec();

		if (method_exists($this->inst, 'postSave'))
			$this->inst->postSave();
		return $this;
	}

	///Insert instance into DB
	public function insert() {
		if (method_exists($this->inst, 'preInsert'))
			$this->inst->preInsert();

		$this->reset();

		$vals = $this->inst->toArray();
		$pks = (array) $this->inst->getKeyName();
		foreach ($pks as $v)
			unset($vals[$v]);
		unset($pks, $v);

		$val_args = implode(', ', array_keys($vals));
		$val_list = $this->implode(', ', $vals, true, true, ', ');

		$this->query = 'INSERT INTO '.$this->inst->getTableName().'('.$val_args.') VALUES ('.$val_list.')';
		$this->exec();

		$this->inst->setID(static::$db->insert_id);

		if (method_exists($this->inst, 'postInsert'))
			$this->inst->postInsert();
		return $this;
	}

	///Remove instance of object from DB by key names
	public function remove() {
		if (method_exists($this->inst, 'preRemove'))
			$this->inst->preRemove();

		$this->reset();

		$pks = (array) $this->inst->getKeyName();

		$pks_list = $this->implode(' AND ', $pks, true);

		$this->query = 'DELETE FROM '.$this->inst->getTableName().' WHERE '.$pks_list;
		$this->exec();

		if (method_exists($this->inst, 'postRemove'))
			$this->inst->postRemove();
		$this->inst = NULL;
		return $this;
	}
}
