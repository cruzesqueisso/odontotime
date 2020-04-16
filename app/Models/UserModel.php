<?php namespace App\Models;

class UserModel extends BaseModel
{
	protected $columnSearch = [
		'web_user' => [
			'username',
			'name',
			'email'
		]
	];
	protected $columnOrder = [
		'web_user' => [
			'username',
			'name',
			'email',
			'is_admin',
			'is_blocked'
		]
	];
	protected $defaultTable = 'web_user';
	protected $tablesColumns = [
		'web_user' => [
			'id',
			'username',
			'password_hash',
			'name',
			'email',
			'is_admin',
			'is_blocked'
		]
	];
	protected $tablesPrimaryKey = [
		'web_user' => ['id']
	];
	protected $validationRules = [
		'web_user' => [
			'username' => 'required|alpha_dash|min_length[4]|max_length[32]|is_unique[web_user.username,id,{id}]',
			'password' => 'required|min_length[6]|matches[password_confirm]',
			'password_confirm' => 'required|matches[password]',
			'name' => 'required|max_length[128]',
			'email' => 'required|valid_email|is_unique[web_user.email,id,{id}]|max_length[128]|matches[email_confirm]',
			'email_confirm' => 'required|matches[email]',
			'is_admin' => 'required|in_list[0,1]',
			'is_blocked' => 'required|in_list[0,1]'
		]
	];
	protected $validationMessages = [
		'web_user' => [
			'username' => [
				'required' => 'Usuário é obrigatório.',
				'alpha_dash' => 'Usuário deve ser composto por apenas caracteres alfanuméricos, hifens e underline.',
				'min_length' => 'Usuário deve ter no minímo {param} caracteres.',
				'max_length' => 'Usuário deve ter no máximo {param} caracteres.',
				'is_unique' => 'Usuário já cadastrado.'
			],
			'password' => [
				'required' => 'Senha é obrigatório.',
				'min_length' => 'Senha deve ter no minímo {param} caracteres.',
				'matches' => 'Senhas devem ser iguais.'
			],
			'password_confirm' => [
				'required' => 'Confirmação da senha é obrigatório.',
				'matches' => 'Senhas devem ser iguais.'
			],
			'name' => [
				'required' => 'Nome é obrigatório.',
				'max_length' => 'Nome deve ter no máximo {param} caracteres.'
			],
			'email' => [
				'required' => 'Email é obrigatório.',
				'valid_email' => 'Email inválido.',
				'is_unique' => 'Email já cadastrado.',
				'max_length' => 'Email deve ter no máximo {param} caracteres.',
				'matches' => 'Emails devem ser iguais.'
			],
			'email_confirm' => [
				'required' => 'Confirmação do email é obrigatório.',
				'matches' => 'Emails devem ser iguais.'
			],
			'is_admin' => [
				'required' => 'É obrigatório informar se o usuário é administrador ou um usuário convencional.',
				'in_list' => 'Valor inválido fornecido'
			],
			'is_blocked' => [
				'required' => 'É obrigatório informar se o usuário esta bloqueado para realizar login no sistema.',
				'in_list' => 'Valor inválido fornecido.'
			]
		]
	];

	protected function _getColumnsData($data, $table = NULL, $excludePK = TRUE)
	{
		if(isset($data['password']))
			$this->_hashPassword($data);

		$ret = parent::_getColumnsData($data, $table, $excludePK);

		return $ret;
	}

	protected function _hashPassword(array &$data)
	{
		if ( ! isset($data['password'])) return $data;

		$data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
		unset($data['password']);

		return $data;
	}

	public function getLoginData($username)
	{
		$data = $this->db
			->table('web_user')
			->where('username', $username)
			->get()->getRowArray();

		if ( ! empty($data) && $data['is_blocked'] == 0)
		{
			return $data;
		}
		return [];
	}

	public function getDataTableColumns()
	{
		return $this->columnOrder['web_user'];
	}

	private function _dataTable($data)
	{
		$builder = $this->db->table('web_user');
		$builder->select($this->aliasOnSelect(['id', $this->columnOrder['web_user']]));
		$this->_dataTableActions('web_user', $builder, $data);
		return $builder;
	}

	public function getDataTable(array $data)
	{
		$builder = $this->_dataTable($data);
		if (isset($data['length']) && $data['length'] != -1)
		{
			$builder->limit($data['length'], $data['start']);
		}
		$records = $builder->get()->getResultArray();
		return [
			'records' => $records,
			'recordsTotal' => $this->db->table('web_user')->countAll(),
			'recordsFiltered' => count($this->_dataTable($data)->get()->getResultArray())
		];
	}

	public function getUserCtrlActs($userID, $ctrlName)
	{
		return $this->db->table('web_user_role')
			->from('role_action')
			->from('restrict_action')
			->from('restrict_controller')
			->select('restrict_action.canonical_name')
			->where('web_user_role.user_id', $userID)
			->where('web_user_role.role_id = role_action.role_id')
			->where('role_action.action_id = restrict_action.id')
			->where('restrict_action.controller_id = restrict_controller.id')
			->where('restrict_controller.canonical_name', $ctrlName)
			->get()->getResultArray();
	}

	public function hasAnyCtrlPermission($userID, $ctrlName)
	{
		return count(
			$this->db->table('web_user_role')
			->from('role_action')
			->from('restrict_action')
			->from('restrict_controller')
			->where('web_user_role.user_id', $userID)
			->where('web_user_role.role_id = role_action.role_id')
			->where('role_action.action_id = restrict_action.id')
			->where('restrict_action.controller_id = restrict_controller.id')
			->where('restrict_controller.canonical_name', $ctrlName)
			->get()->getResultArray()
		) > 0;
	}

	public function getData($userID)
	{
		$userData = $this->db->table('web_user')
			->where('id', $userID)
			->get()->getRowArray();

		if ( ! empty($userData))
		{
			$userData['roles'] = $this->db
				->table('web_user_role')
				->from('role')
				->where('web_user_role.user_id', $userID)
				->where('web_user_role.role_id = role.id')
				->get()->getResultArray();
		}

		return $userData;
	}

	public function updateProfile($userID, array $data)
	{
		$data['id'] = $userID;
		if ( ! $this->_validate('web_user', $data))
			return FALSE;

		return $this->db->table('web_user')
			->where('id', $userID)
			->update($this->_getColumnsData($data, 'web_user'));
	}

	public function updatePassword($userID, array $data)
	{
		$data['id'] = $userID;
		if ( ! $this->_validate('web_user', $data))
			return FALSE;

		return $this->db->table('web_user')
			->where('id', $userID)
			->update($this->_getColumnsData($data, 'web_user'));
	}

	private function _insertUserRoles($userID, $data)
	{
		if ( ! isset($data['role_id']))
			return;

		$builder = $this->db->table('web_user_role');
		foreach ($data['role_id'] as $role_id)
		{
			$builder->insert([
				'user_id' => $userID,
				'role_id' => $role_id
			]);
		}
	}

	public function insert(array $data)
	{
		$aux = $this->cleanValidationRules;
		$this->cleanValidationRules = FALSE;
		$valid = $this->_validate('web_user', $data);
		$this->cleanValidationRules = $aux;
		if ( ! $valid)
			return FALSE;

		$this->db->transStart();

		// User general data
		$this->db->table('web_user')
			->insert($this->_getColumnsData($data, 'web_user'));
		$userID = $this->db->insertID();

		// User permissions roles
		$this->_insertUserRoles($userID, $data);

		if ($this->db->transComplete())
			return $userID;

		return FALSE;
	}

	public function update($userID, array $data)
	{
		$this->db->transStart();

		$this->db->table('web_user')
			->where('id', $userID)
			->update($this->_getColumnsData($data));

		// Delete all old permissions and insert the new ones
		$this->db
			->table('web_user_role')
			->where('user_id', $userID)
			->delete();

		// User permissions roles
		$this->_insertUserRoles($userID, $data);

		return $this->db->transComplete();
	}

	public function delete($userID)
	{
		$this->db->transStart();

		$this->db->table('web_user_role')
			->where('user_id', $userID)
			->delete();

		$this->db->table('web_user')
			->where('id', $userID)
			->delete();

		return $this->db->transComplete();
	}

	private function _setIsBlocked($userID, $value)
	{
		return $this->db->table('web_user')
			->where('id', $userID)
			->update(['is_blocked' => $value]);
	}

	public function blockUser($userID)
	{
		return $this->_setIsBlocked($userID, 1);
	}

	public function unblockUser($userID)
	{
		return $this->_setIsBlocked($userID, 0);
	}
}