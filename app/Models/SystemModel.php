<?php namespace App\Models;

class SystemModel extends BaseModel
{
	public function getRestrictCtrl(string $whereValue, string $whereKey = 'canonical_name', string $select = '')
	{
		$builder = $this->db->table('restrict_controller');
		if ( ! empty($select)) 
		{
			$builder->select($select);
		}

		return $builder
			->where($whereKey, $whereValue)
			->get()->getRowArray();
	}

	public function getRestrictCtrls()
	{
		return $this->db
			->table('restrict_controller')
			->get()->getResultArray();
	}

	public function insertRestrictCtrl(array $data)
	{
		if ($this->db->table('restrict_controller')
			->insert([
				'canonical_name' => $data['canonical_name'],
				'friendly_name' => $data['friendly_name']
			])
		)
		{
			return $this->db->insertID();
		}

		return FALSE;
	}

	public function insertRestrictCtrlAct(array $data)
	{
		if ($this->db->table('restrict_action')
			->insert([
				'controller_id' => $data['controller_id'],
				'canonical_name' => $data['canonical_name']
			])
		)
		{
			return $this->db->insertID();
		}

		return FALSE;
	}

	public function restrictCtrlExists(string $whereValue, string $whereKey = 'canonical_name')
	{
		return count(
			$this->db
			->table('restrict_controller')
			->where($whereKey, $whereValue)
			->get()->getResultArray()
		) > 0;
	}

	public function restrictActExists($controllerID, $actionName)
	{
		return count(
			$this->db
			->table('restrict_action')
			->where('controller_id', $controllerID)
			->where('canonical_name', $actionName)
			->get()->getResultArray()
		) > 0;
	}

	public function getRestrictCtrlActs($controllerID)
	{
		return $this->db->table('restrict_controller AS rc')
			->from('restrict_action AS ra')
			->select('rc.id AS "controller.id", rc.canonical_name AS "controller.canonical_name", rc.friendly_name AS "controller.friendly_name", ra.id AS "action.id", ra.controller_id AS "action.controller_id", ra.canonical_name AS "action.canonical_name"')
			->where('rc.id', $controllerID)
			->where('rc.id = ra.controller_id')
			->get()->getResultArray();
	}

	public function getRestrictCtrlsActs()
	{
		return $this->db->table('restrict_controller AS rc')
			->from('restrict_action AS ra')
			->select('rc.id AS "controller.id", rc.canonical_name AS "controller.canonical_name", rc.friendly_name AS "controller.friendly_name", ra.id AS "action.id", ra.controller_id AS "action.controller_id", ra.canonical_name AS "action.canonical_name"')
			->where('rc.id = ra.controller_id')
			->get()->getResultArray();
	}

	public function deleteRestrictCtrl($controllerID)
	{
		$this->db->transStart();

		$cmpCtrlActsIDs = $this->db
			->table('restrict_action')
			->select('id')
			->where('controller_id', $controllerID)
			->getCompiledSelect();

		$this->db
			->table('role_action')
			->where("action_id IN({$cmpCtrlActsIDs})", NULL, FALSE)
			->delete();

		$this->db
			->table('restrict_action')
			->delete(['controller_id' => $controllerID]);

		$this->db
			->table('restrict_controller')
			->delete(['id' => $controllerID]);

		return $this->db->transComplete();
	}

	public function deleteRestrictCtrlAct($actionID)
	{
		$this->db->transStart();

		$this->db
			->table('role_action')
			->delete(['action_id' => $actionID]);

		$this->db
			->table('restrict_action')
			->delete(['id' => $actionID]);

		return $this->db->transComplete();
	}
}