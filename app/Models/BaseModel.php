<?php namespace App\Models;

use Config\Database;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class BaseModel
{
	protected $columnSearch = [];
	protected $columnOrder = [];

	/**
	 * Default table name, is interessenting use this attribute only
	 * when the model only operate on one table
	 * 
	 * @var	NULL|string
	 */
	protected $defaultTable = NULL;

	/**
	 * Table columns names
	 * 
	 * @var	NULL|array	Example: [
	 *					'table1' => [
	 * 						'column1',
	 * 						'column2',
	 * 						'column3'
	 * 					],
	 * 					'table2' => [
	 * 						'column1',
	 * 						'column2'
	 * 					]
	 * 				]
	 */
	protected $tablesColumns = NULL;

	/**
	 * Tables primary keys, cloud be a composite key
	 * 
	 * @var	NULL|array	Example: [
	 * 						'table1' => ['column1'],
	 * 						'table2' => ['column1', 'column2']
	 * 					]
	 */
	protected $tablesPrimaryKey = NULL;
	protected $validation = NULL;
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $cleanValidationRules = TRUE;
	protected $skipValidation = FALSE;

	/**
	 * Class constructor
	 * 
	 * @return	void
	 */
	public function __construct(ConnectionInterface &$db = NULL, ValidationInterface $validation = null)
	{
		if ($db instanceof ConnectionInterface)
		{
			$this->db = & $db;
		}
		else
		{
			$this->db = Database::connect();
		}

		if (is_null($validation))
		{
			$validation = \Config\Services::validation(null, false);
		}

		$this->validation = $validation;
	}

	/**
	 * Filter fields from $data array getting only fields necessary to
	 * insert or update on $table
	 * 
	 * @uses	BaseModel::_columnsDataWithPK()
	 * @uses	BaseModel::_columnsDataWithoutPK()
	 * @see		BaseModel::_columnsDataWithPK()
	 * @see		BaseModel::_columnsDataWithoutPK()
	 * @param	array			$data	Data to be filtered
	 * @param	NULL|string		$table	Table name, if is NULL will
	 * 							use value from $this::$defaultTable
	 * @param	bool			$excludePK	Exclude primary keys in filtered
	 * @return	array			Filtered array with key/values compatibles
	 * 							with table definition present on
	 * 							$this::$tablesColumns[$table]
	 */
	protected function _getColumnsData($data, $table = NULL, $excludePK = TRUE)
	{
		$filtered_data = [];
		if(is_null($table))
			$table = $this->defaultTable;

		if(isset($this->tablesColumns[$table]))
		{
			if($excludePK)
				$this->_columnsDataWithoutPK($filtered_data, $data, $table);
			else
				$this->_columnsDataWithPK($filtered_data, $data, $table);
		}
		return $filtered_data;
	}

	/**
	 * Get filtered data with primary keys
	 * 
	 * @param	array	$filtered_data	array to be filled with filtered
	 * 									data
	 * @param	array	$data			$data to be filtered
	 * @param	string	$table			Table name
	 * @return	void
	 */
	private function _columnsDataWithPK(&$filtered_data, $data, $table)
	{
		foreach($this->tablesColumns[$table] as $column)
			if(array_key_exists($column, $data))
				$filtered_data[$column] = $data[$column];
	}

	/**
	 * Get filtered data without primary keys
	 * 
	 * @param	array	$filtered_data	array to be filled with filtered
	 * 									data
	 * @param	array	$data			$data to be filtered
	 * @param	string	$table			Table name
	 * @return	void
	 */
	private function _columnsDataWithoutPK(&$filtered_data, $data, $table)
	{
		foreach($this->tablesColumns[$table] as $column)
		{
			foreach($this->tablesPrimaryKey[$table] as $key)
				if($column == $key)
					continue 2;
			if(array_key_exists($column, $data))
				$filtered_data[$column] = $data[$column];
		}
	}

	protected function _dataTableActions(string $dtName, &$builder, array $data)
	{
		if (isset($data['search']))
		{
			$search = $data['search']['value'];
			$first = TRUE;
			foreach ($this->columnSearch[$dtName] as $field)
			{
				if ($first)
				{
					$builder->groupStart();
					$builder->like($field, $search);
					$first = FALSE;
				}
				else
				{
					$builder->orLike($field, $search);
				}
			}
			if ( ! $first)
			{
				$builder->groupEnd();
			}
		}

		if (isset($data['order']))
		{
			$builder->orderBy(
				$this->columnOrder[$dtName][$data['order'][0]['column']],
				$data['order'][0]['dir']
			);
		}
	}

	/**
	 * This function is the same from CodeIgniter\Model
	 */
	protected function _fillPlaceholders(array $rules, array $data): array
	{
		$replacements = [];

		foreach ($data as $key => $value)
		{
			$replacements["{{$key}}"] = $value;
		}

		if (! empty($replacements))
		{
			foreach ($rules as &$rule)
			{
				if (is_array($rule))
				{
					foreach ($rule as &$row)
					{
						// Should only be an `errors` array
						// which doesn't take placeholders.
						if (is_array($row))
						{
							continue;
						}

						$row = strtr($row, $replacements);
					}
					continue;
				}

				$rule = strtr($rule, $replacements);
			}
		}

		return $rules;
	}

	/**
	 * This function is the same from CodeIgniter\Model
	 */
	protected function _validate($table, $data): bool
	{
		if ($this->skipValidation === true || empty($this->validationRules[$table]) || empty($data))
		{
			return true;
		}
		
		// Query Builder works with objects as well as arrays,
		// but validation requires array, so cast away.
		if (is_object($data))
		{
			$data = (array) $data;
		}

		$rules = $this->validationRules[$table];

		// ValidationRules can be either a string, which is the group name,
		// or an array of rules.
		if (is_string($rules))
		{
			$rules = $this->validation->loadRuleGroup($rules);
		}

		$rules = $this->cleanValidationRules
			? $this->_cleanValidationRules($rules, $data)
			: $rules;

		// If no data existed that needs validation
		// our job is done here.
		if (empty($rules))
		{
			return true;
		}

		// Replace any placeholders (i.e. {id}) in the rules with
		// the value found in $data, if exists.
		$rules = $this->_fillPlaceholders($rules, $data);
		$this->validation->setRules($rules, $this->validationMessages[$table]);
		$valid = $this->validation->run($data);

		return (bool) $valid;
	}
	
	/**
	 * This function is the same from CodeIgniter\Model
	 */
	protected function _cleanValidationRules(array $rules, array $data = null): array
	{
		if (empty($data))
		{
			return [];
		}

		foreach ($rules as $field => $rule)
		{
			if (! array_key_exists($field, $data))
			{
				unset($rules[$field]);
			}
		}

		return $rules;
	}

	/**
	 * This function is the same from CodeIgniter\Model
	 */
	public function errors()
	{
		// Do we have validation errors?
		$errors = $this->validation->getErrors();

		if (! empty($errors))
		{
			return $errors;
		}

		// Still here? Grab the database-specific error, if any.
		$error = $this->db->getError();

		return $error['message'] ?? null;
	}

	/**
	 * This function is the same from CodeIgniter\Model
	 */
	public function cleanRules(bool $choice = false)
	{
		$this->cleanValidationRules = $choice;

		return $this;
	}

	private function _aliasOnField(string $field)
	{
		return $field.=' AS "'.$field.'"';
	}

	private function _aliasOnFields(array $fields)
	{
		foreach ($fields as &$field)
			$field = $this->_aliasOnField($field);
		return implode(', ', $fields);
	}

	protected function aliasOnSelect(array $list)
	{
		$pieces = [];
		foreach ($list as $fields)
		{
			if (is_array($fields))
			{
				$pieces[] = $this->_aliasOnFields($fields);
			}
			else if (is_string($fields))
			{
				$pieces[] = $this->_aliasOnField($fields);
			}
		}
		return implode(', ', $pieces);
	}

	public function getValidations(string $table = '')
	{
		if (empty($table))
		{
			return [
				'rules' => $this->validationRules,
				'messages' => $this->validationMessages
			];
		}

		return [
			'rules' => (isset($this->validationRules[$table]))?$this->validationRules[$table]:[],
			'messages' => (isset($this->validationMessages[$table]))?$this->validationMessages[$table]:[]
		];
	}

	public function setSkipValidation(bool $value)
	{
		$this->skipValidation = $value;
	}
}