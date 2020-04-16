<?php namespace App\Models;

class AddressModel extends BaseModel
{
	protected $columnSearch = [
		'address' => [
			'address',
			'number',
			'neighborhood',
			'city',
			'state'
		]
	];
	protected $columnOrder = [
		'address' => [
			'address',
			'number',
			'neighborhood',
			'city',
			'state',
			'complement'
		]
	];
	protected $tablesColumns = [
		'address' => [
			'id',
			'address',
			'number',
			'neighborhood',
			'city',
			'state',
			'complement'
		]
	];
	protected $tablesPrimaryKey = [
		'address' => ['id']
	];
	protected $validationRules = [
		'address' => [
			'address' => 'required|max_length[128]',
			'number' => 'required|integer',
			'neighborhood' => 'required|max_length[64]',
			'city' => 'required|max_length[64]',
			'state' => 'required|exact_length[2]'
		]
	];
	protected $validationMessages = [
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
		]
	];

	public function getData($addressID)
	{
		return $this->db->table('address')
			->where('id', $addressID)
			->get()->getRowArray();
	}

	public function insert(array $data)
	{
		$aux = $this->cleanValidationRules;
		$this->cleanValidationRules = FALSE;
		$valid = $this->_validate('address', $data);
		$this->cleanValidationRules = $aux;
		if ( ! $valid)
			return FALSE;

		if ($this->db->table('address')
		->insert($this->_getColumnsData($data, 'address')))
		{
			return $this->db->insertID();
		}

		return FALSE;
	}

	public function update($addressID, array $data)
	{
		if ( ! $this->_validate('address', $data))
			return FALSE;

		return $this->db->table('address')
			->where('id', $addressID)
			->update($this->_getColumnsData($data, 'address'));
	}

	public function delete($addressID)
	{
		return $this->db->table('address')
			->delete(['id' => $addressID]);
	}
}