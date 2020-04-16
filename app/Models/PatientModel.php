<?php namespace App\Models;

class PatientModel extends BaseModel
{
	protected $columnSearch = [
		'patient' => [
			'patient.name',
			'patient.cpf',
			'patient.email',
			'address.address',
			'address.neighborhood',
			'address.city',
			'address.state',
			'patient.email'
		],
		'app_user' => [
			'app_user.username',
			'patient.cpf',
			'patient.name',
			'patient.email'
		]
	];
	protected $columnOrder = [
		'patient' => [
			'patient.name',
			'patient.cpf',
			'patient.email',
			'address.address',
			'address.number',
			'address.neighborhood',
			'address.city',
			'address.state'
		],
		'app_user' => [
			'app_user.username',
			'patient.name',
			'patient.email',
			'app_user.is_blocked'
		]
	];
	protected $tablesColumns = [
		'patient' => [
			'id',
			'name',
			'email',
			'cpf',
			'phone',
			'address_id'
		],
		'address' => [
			'id',
			'address',
			'number',
			'neighborhood',
			'city',
			'state',
			'complement'
		],
		'app_user' => [
			'id',
			'username',
			'password_hash',
			'is_blocked'
		]
	];
	protected $tablesPrimaryKey = [
		'patient' => ['id'],
		'address' => ['id'],
		'app_user' => ['id']
	];
	protected $validationRules = [
		'patient' => [
			'name' => 'required|max_length[128]',
			'email' => 'required|valid_email|max_length[128]|is_unique[patient.email,id,{id}]|matches[email_confirm]',
			'email_confirm' => 'required|matches[email]',
			'cpf' => 'required|cpf|is_unique[patient.cpf,id,{id}]',
			'phone' => 'required|phone'
		],
		'address' => [
			'address' => 'required|max_length[128]',
			'number' => 'required|integer',
			'neighborhood' => 'required|max_length[64]',
			'city' => 'required|max_length[64]',
			'state' => 'required|exact_length[2]'
		],
		'app_user' => [
			'username' => 'required|exact_length[11]|is_unique[app_user.username,id,{id}]|cpf',
			'password' => 'required|min_length[6]|matches[password_confirm]',
			'password_confirm' => 'required|matches[password]',
			'is_blocked' => 'required|in_list[0,1]'
		]
	];
	protected $validationMessages = [
		'patient' => [
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
			'cpf' => [
				'required' => 'CPF é obrigatório.',
				'cpf' => 'CPF é inválido.',
				'is_unique' => 'CPF já cadastrado.'
			],
			'phone' => [
				'required' => 'Telefone é obrigatório.',
				'phone' => 'Telefone inválido.'
			]
		],
		'address' => [
			'address' => [
				'required' => 'Endereço é obrigatório.',
				'max_length' => 'Endereço deve ter no máximo {param} caracteres.'
			],
			'number' => [
				'required' => 'Número é obrigatório.',
				'integer' => 'Deve conter apenas números.'
			],
			'neighborhood' => [
				'required' => 'Bairro é obrigatório.',
				'max_length' => 'Endereço deve ter no máximo {param} caracteres.'
			],
			'city' => [
				'required' => 'Cidade é obrigatório.',
				'max_length' => 'Endereço deve ter no máximo {param} caracteres.'
			],
			'state' => [
				'required' => 'Estado é obrigatório.',
				'exact_length' => 'Estado deve ser a sua abreviatura de {param} caracteres.'
			]
		],
		'app_user' => [
			'username' => [
				'required' => 'Usuário é obrigatório.',
				'exact_length' => 'Deve ter exatamente {param} caracteres, pois o padrão é utilizar o CPF do paciente sem a máscara.',
				'is_unique' => 'Usuário já cadastrado.',
				'cpf' => 'Usuário é um CPF inválido.'
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

	public function getData($patientID)
	{
		return $this->db->table('patient')
			->from('address')
			->where('patient.id', $patientID)
			->where('patient.address_id = address.id')
			->get()->getRowArray();
	}

	public function insert(array $data)
	{
		$aux = $this->cleanValidationRules;
		$this->cleanValidationRules = FALSE;
		$valid = $this->_validate('patient', $data) && $this->_validate('address', $data);
		$this->cleanValidationRules = $aux;
		if ( ! $valid)
			return FALSE;

		$this->db->transStart();

		$this->db->table('address')
			->insert($this->_getColumnsData($data, 'address'));
		$data['address_id'] = $this->db->insertID();

		$this->db->table('patient')
			->insert($this->_getColumnsData($data, 'patient'));
		$patientID = $this->db->insertID();

		$cpfWithoutMask = preg_replace('/[^0-9]/', '', $data['cpf']);
		$this->db->table('app_user')
			->insert($this->_getColumnsData(
				[
					'id' => $patientID,
					'username' => $cpfWithoutMask,
					'password' => $cpfWithoutMask,
					'is_blocked' => 0
				],
				'app_user',
				FALSE
			));

		if($this->db->transComplete())
			return $patientID;

		return FALSE;
	}

	public function update($patientID, array $data)
	{
		if ( ! $this->_validate('patient', $data)
		&& ! $this->_validate('address', $data))
			return FALSE;

		$this->db->transStart();

		$oldData = $this->getData($patientID);
		$this->db->table('address')
			->where('id', $oldData['address_id'])
			->update($this->_getColumnsData($data, 'address'));

		$this->db->table('patient')
			->where('id', $patientID)
			->update($this->_getColumnsData($data, 'patient'));

		$this->db->table('app_user')
			->where('id', $patientID)
			->update($this->_getColumnsData(
				['username' => preg_replace('/[^0-9]/', '', $data['cpf'])],
				'app_user'
			));

		return $this->db->transComplete();
	}

	public function delete($patientID)
	{
		$this->db->transStart();

		$oldData = $this->getData($patientID);
		$this->db->table('app_user')
			->where('id', $patientID)
			->delete();

		$this->db->table('patient')
			->where('id', $patientID)
			->delete();

		$this->db->table('address')
			->where('id', $oldData['address_id'])
			->delete();

		return $this->db->transComplete();
	}

	public function getDataTableColumns()
	{
		return $this->columnOrder['patient'];
	}

	private function _dataTable($data)
	{
		$builder = $this->db->table('patient')
			->from('address')
			->where('patient.address_id = address.id');
		$builder->select($this->aliasOnSelect(['patient.id', $this->columnOrder['patient']]));
		$this->_dataTableActions('patient', $builder, $data);
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
			'recordsTotal' => $this->db->table('patient')->countAll(),
			'recordsFiltered' => count($this->_dataTable($data)->get()->getResultArray())
		];
	}

	public function getDataTableAppUsersColumns()
	{
		return $this->columnOrder['app_user'];
	}

	private function _dataTableAppUsers($data)
	{
		$builder = $this->db->table('app_user')
			->from('patient')
			->where('app_user.id = patient.id');
		$builder->select($this->aliasOnSelect([
			'app_user.id', $this->columnOrder['app_user']
		]));
		$this->_dataTableActions('app_user', $builder, $data);
		return $builder;
	}

	public function getDataTableAppUsers(array $data)
	{
		$builder = $this->_dataTableAppUsers($data);
		if (isset($data['length']) && $data['length'] != -1)
		{
			$builder->limit($data['length'], $data['start']);
		}
		$records = $builder->get()->getResultArray();
		return [
			'records' => $records,
			'recordsTotal' => $this->db->table('app_user')->countAll(),
			'recordsFiltered' => count($this->_dataTableAppUsers($data)->get()->getResultArray())
		];
	}

	private function _setIsBlocked($appUserID, $value)
	{
		return $this->db->table('app_user')
			->where('id', $appUserID)
			->update(['is_blocked' => $value]);
	}

	public function blockUser($appUserID)
	{
		return $this->_setIsBlocked($appUserID, 1);
	}

	public function unblockUser($appUserID)
	{
		return $this->_setIsBlocked($appUserID, 0);
	}

	public function updateAppUserPassword($appUserID, array $data)
	{
		$data['id'] = $appUserID;
		if ( ! $this->_validate('app_user', $data))
			return FALSE;

		return $this->db->table('app_user')
			->where('id', $appUserID)
			->update($this->_getColumnsData($data, 'app_user'));
	}
}