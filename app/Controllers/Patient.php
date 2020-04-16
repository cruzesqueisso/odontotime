<?php namespace App\Controllers;

use App\Models\PatientModel;

class Patient extends RestrictAccess
{
	public static $friendlyName = 'Pacientes';

	public function __construct(...$params)
    {
		parent::__construct(...$params);
	}

	public function index()
	{
		$this->_loadPatientManagement();
	}

	protected function _loadPatientManagement()
	{
		$ret['status'] = 0;
		try
		{
			$data = [
				'controller' => $this,
				'scripts' => [
					'/vendor/jquery-mask-plugin-master/src/jquery.mask.js',
					'js/owner/patient/patientManagement.js?v='.filemtime(FCPATH.'js/owner/patient/patientManagement.js'),
				]
			];
			$ret['content'] = view('patient/patientManagement', $data);
			$ret['status'] = 1;
		}
		catch (Exception $e)
		{
			$ret['errorMessage'] = 'Não foi possível carregar o conteúdo.';
		}

		echo json_encode($ret);
	}

	private function _savePatient(array $requestData, bool $isInsert, string $act)
	{
		if ( ! $this->hasPermission($act))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$patientModel = new PatientModel();

		if ($isInsert === TRUE)
		{
			if ( ! $patientModel->insert($requestData))
			{
				$errors = $patientModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Falha ao cadastrar paciente no banco de dados!';
			}
		}
		else if ($isInsert === FALSE)
		{
			if ( ! $patientModel->update($requestData['id'], $requestData))
			{
				$errors = $patientModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Falha ao atualizar paciente no banco de dados!';
			}
		}
		if (isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	public function actCreatePatient()
	{
		$this->_savePatient($this->request->getPost(), TRUE, __FUNCTION__);
	}

	public function actUpdatePatient()
	{
		$this->_savePatient($this->request->getPost(), FALSE, __FUNCTION__);
	}

	public function actListPatients()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$requestData = $this->request->getPost();
		$ret = [];
		$rows = [];
		$patientModel = new PatientModel();
		$dbData = $patientModel->getDataTable($requestData);
		$dtColumns = $patientModel->getDataTableColumns();
		$canUpdate = $this->hasPermission('actUpdatePatient');
		$canDelete = $this->hasPermission('actDeletePatient');
		foreach ($dbData['records'] as $patient)
		{
			$actions = '<div style="display: inline-block;">';
			if ($canUpdate)
			{
				$actions .= '<button class="btn btn-primary btn-edit-patient m-1"
							data-patient_id="'. $patient['patient.id'] .'" 
							data-toggle="tooltip" title="Atualizar paciente">
							<i class="fa fa-edit"></i>
						</button>';
			}

			if($canDelete)
			{
				$actions .= '<button class="btn btn-danger btn-del-patient m-1"
							data-patient_id="'. $patient['patient.id'] .'" 
							data-toggle="tooltip" title="Deletar paciente">
							<i class="fa fa-times"></i>
						</button>';
			}

			$actions .= '</div>';
			$row = [];
			foreach ($dtColumns as $dtColumn)
				$row[] = $patient[$dtColumn];
			$row[] = $actions;
			$rows[] = $row;
		}

		$ret['draw'] = $requestData['draw'];
		$ret['recordsTotal'] = $dbData['recordsTotal'];
		$ret['recordsFiltered'] = $dbData['recordsFiltered'];
		$ret['data'] = $rows;
		echo json_encode($ret);
	}

	public function actListAppUsers()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$requestData = $this->request->getPost();
		$ret = [];
		$rows = [];
		$patientModel = new PatientModel();
		$dbData = $patientModel->getDataTableAppUsers($requestData);
		$dtColumns = $patientModel->getDataTableAppUsersColumns();
		$canAlterPassword = $this->hasPermission('actAlterAppUserPassword');
		$canBlock = $this->hasPermission('actBlockAppUser');
		$canUnblock = $this->hasPermission('actUnblockAppUser');
		foreach ($dbData['records'] as $appUser)
		{
			$actions = '<div style="display: inline-block;">';
			
			if ($canAlterPassword)
			{
				$actions .= '<button class="btn btn-warning btn-alter-app-user-pwd m-1"
							data-id="'. $appUser['app_user.id'] .'" 
							data-username="'. $appUser['app_user.username'] .'" 
							data-toggle="tooltip" title="Alterar senha do usuário do APP">
							<i class="fas fa-key"></i>
						</button>';
			}

			if($appUser['app_user.is_blocked'] == 1)
			{
				if($canUnblock)
				{
					$actions .= '<button class="btn btn-info btn-unblock-app-user m-1"
								data-id="'. $appUser['app_user.id'] .'" 
								data-toggle="tooltip" title="Desbloquear usuário do APP">
								<i class="fas fa-check"></i>
							</button>';
				}
			}
			else
			{
				if($canBlock)
				{
					$actions .= '<button class="btn btn-info btn-block-app-user m-1"
								data-id="'. $appUser['app_user.id'] .'" 
								data-toggle="tooltip" title="Bloquear usuário do APP">
								<i class="fas fa-ban"></i>
							</button>';
				}
			}

			$actions .= '</div>';

			$appUser['app_user.is_blocked'] = ($appUser['app_user.is_blocked'] == 1)? 'Sim':'Não';
			$row = [];
			foreach ($dtColumns as $dtColumn)
				$row[] = $appUser[$dtColumn];
			$row[] = $actions;
			$rows[] = $row;
		}

		$ret['draw'] = $requestData['draw'];
		$ret['recordsTotal'] = $dbData['recordsTotal'];
		$ret['recordsFiltered'] = $dbData['recordsFiltered'];
		$ret['data'] = $rows;
		echo json_encode($ret);
	}

	public function actGetPatientData()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$patientModel = new PatientModel();
		$patientData = $patientModel->getData($this->request->getPost('patient_id'));
		if (empty($patientData))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Dados do paciente requisitado não existem!';
		}
		else
		{
			$ret['patient'] = $patientData;
		}
		echo json_encode($ret);
	}

	public function actDeletePatient()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$patientModel = new PatientModel();
		$requestData = $this->request->getPost();
		if ( ! $patientModel->delete($requestData['patient_id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao deletar paciente!';
		}
		echo json_encode($ret);
	}

	public function actBlockUser()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$patientModel = new PatientModel();
		$requestData = $this->request->getPost();
		if ( ! $patientModel->blockUser($requestData['id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao bloquear usuário do APP!';
		}
		echo json_encode($ret);
	}

	public function actUnblockUser()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$patientModel = new PatientModel();
		$requestData = $this->request->getPost();
		if ( ! $patientModel->unblockUser($requestData['id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao desbloquear usuário do APP!';
		}
		echo json_encode($ret);
	}

	public function actAlterAppUserPassword()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$patientModel = new PatientModel();
		$requestData = $this->request->getPost();
		if ( ! $patientModel->updateAppUserPassword($requestData['id'], $requestData))
		{
			$errors = $patientModel->errors();
			if (is_array($errors))
				$ret['errorList'] = $errors;
			else
				$ret['errorList']['submit_btn'] = 'Falha ao alterar senha de usuário!';
		}

		if (isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}
}