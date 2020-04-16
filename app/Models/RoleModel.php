<?php namespace App\Models;

class RoleModel extends BaseModel
{
	protected $columnSearch = [
		'role' => [
			'name',
			'description'
		],
		'role_action' => [
			'role.name',
			'restrict_controller.canonical_name',
			'restrict_action.canonical_name'
		]
	];
	protected $columnOrder = [
		'role' => [
			'name',
			'description'
		],
		'role_action' => [
			'role.name',
			'restrict_controller.canonical_name',
			'restrict_action.canonical_name'
		]
	];
	protected $tablesColumns = [
		'role' => [
			'id',
			'name',
			'description'
		],
		'role_action' => [
			'role_id',
			'action_id'
		]
	];
	protected $tablesPrimaryKey = [
		'role' => ['id'],
		'role_action' => ['role_id', 'action_id']
	];
	protected $validationRules = [
		'role' => [
			'name' => 'required|max_length[64]',
			'description' => 'required|max_length[255]'
		]
	];
	protected $validationMessages = [
		'role' => [
			'name' => [
				'required' => 'Nome é obrigatório.',
				'max_length' => 'Nome deve ter no máximo {param} caracteres.'
			],
			'description' => [
				'required' => 'Descrição é obrigatório.',
				'max_length' => 'Descrição deve ter no máximo {param} caracteres.'
			]
		]
	];

	public function getData($roleID)
	{
		$data = $this->db->table('role')
			->where('id', $roleID)
			->get()->getRowArray();

		if ( ! empty($data))
		{
			$data['actions'] = $this->db
				->table('role_action')
				->from('restrict_action')
				->from('restrict_controller')
				->select(
				$this->aliasOnSelect([
					'restrict_action.controller_id',
					'restrict_action.id'
				]))
				->where('role_action.role_id', $roleID)
				->where('role_action.action_id = restrict_action.id')
				->where('restrict_action.controller_id = restrict_controller.id')
				->get()->getResultArray();
		}

		return $data;
	}

	public function getRoles()
	{
		return $this->db->table('role')
			->orderBy('name', 'ASC')
			->get()->getResultArray();
	}

	private function _insertPermitedActions($roleID, array $data)
	{
		foreach ($data['permissions'] as $controllerID => $permissions)
		{
			foreach ($permissions as $actionID => $privileges)
			{
				foreach ($privileges as $privilege)
				{
					if ($privilege == '1')
					{
						$this->db->table('role_action')
							->insert([
								'role_id' => $roleID,
								'action_id' => $actionID
							]);
						break;
					}
				}
			}
		}
	}

	public function insert(array $data)
	{
		$aux = $this->cleanValidationRules;
		$this->cleanValidationRules = FALSE;
		$valid = $this->_validate('role', $data);
		$this->cleanValidationRules = $aux;
		if ( ! $valid)
			return FALSE;

		$this->db->transStart();

		// Insert role general data
		$this->db->table('role')
			->insert($this->_getColumnsData($data, 'role'));
		$roleID = $this->db->insertID();

		// Insert role permited actions
		$this->_insertPermitedActions($roleID, $data);

		if ($this->db->transComplete())
		{
			return $roleID;
		}

		return FALSE;
	}

	public function update($roleID, array $data)
	{
		if ( ! $this->_validate('role', $data))
			return FALSE;
		$this->db->transStart();

		// Update role general data
		$this->db->table('role')
			->where('id', $roleID)
			->update($this->_getColumnsData($data, 'role'));

		// Delete all old permissions and insert the new ones
		$this->db
			->table('role_action')
			->delete(['role_id' => $roleID]);

		// Insert new role permited actions
		$this->_insertPermitedActions($roleID, $data);

		return $this->db->transComplete();
	}

	public function delete($roleID)
	{
		$this->db->transStart();

		$this->db->table('web_user_role')
			->delete(['role_id' => $roleID]);

		$this->db->table('role_action')
			->delete(['role_id' => $roleID]);

		$this->db->table('role')
			->delete(['id' => $roleID]);

		return $this->db->transComplete();
	}

	public function getDataTableRolesColumns()
	{
		return $this->columnOrder['role'];
	}

	private function _dataTableRoles(array $data)
	{
		$builder = $this->db->table('role');
		$builder->select($this->aliasOnSelect(['id', $this->columnOrder['role']]));
		$this->_dataTableActions('role', $builder, $data);
		return $builder;
	}

	public function getDataTableRoles(array $data)
	{
		$builder = $this->_dataTableRoles($data);
		if (isset($data['length']) && $data['length'] != -1)
		{
			$builder->limit($data['length'], $data['start']);
		}
		$records = $builder->get()->getResultArray();
		return [
			'records' => $records,
			'recordsTotal' => $this->db->table('role')->countAll(),
			'recordsFiltered' => count($this->_dataTableRoles($data)->get()->getResultArray())
		];
	}

	public function getDataTableRolesPermissionsColumns()
	{
		return $this->columnOrder['role_action'];
	}

	private function _dataTableRolesPermissions(array $data)
	{
		$builder = $this->db->table('role');
		$builder->from('role_action');
		$builder->from('restrict_action');
		$builder->from('restrict_controller');
		$builder->where('role.id = role_action.role_id');
		$builder->where('role_action.action_id = restrict_action.id');
		$builder->where('restrict_action.controller_id = restrict_controller.id');
		$builder->select(
			$this->aliasOnSelect(['role.id', $this->columnOrder['role_action']])
		);
		$this->_dataTableActions('role_action', $builder, $data);
		return $builder;
	}

	public function getDataTableRolesPermissions(array $data)
	{
		$builder = $this->_dataTableRolesPermissions($data);
		if (isset($data['length']) && $data['length'] != -1)
		{
			$builder->limit($data['length'], $data['start']);
		}
		$records = $builder->get()->getResultArray();
		return [
			'records' => $records,
			'recordsTotal' => $this->db->table('role_action')->countAll(),
			'recordsFiltered' => count($this->_dataTableRolesPermissions($data)->get()->getResultArray())
		];
	}

	public function getUserRoles($userID)
	{
		return $this->db->table('web_user_role')
			->from('role')
			->where('web_user_role.user_id', $userID)
			->where('web_user_role.role_id = role.id')
			->get()->getResultArray();
	}
}