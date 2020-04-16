<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\SystemModel;

class User extends RestrictAccess
{
	public static $friendlyName = 'Usuários';

	public function __construct(...$params)
    {
		parent::__construct(...$params);
	}

	public function index()
	{
		$this->_loadUserManagement();
	}

	protected function _loadUserManagement()
	{
		$ret['status'] = 0;
		try
		{
			$data = [
				'controller' => $this,
				'scripts' => [
					'js/owner/user/userManagement.js?v='.filemtime(FCPATH.'js/owner/user/userManagement.js'),
				]
			];
			$ret['content'] = view('user/userManagement', $data);
			$ret['status'] = 1;
		}
		catch (Exception $e)
		{
			$ret['errorMessage'] = 'Não foi possível carregar o conteúdo.';
		}

		echo json_encode($ret);
	}

	public function actListUsers()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$requestData = $this->request->getPost();
		$ret = [];
		$rows = [];
		$userModel = new UserModel();
		$roleModel = new RoleModel();
		$dbData = $userModel->getDataTable($requestData);
		$dtColumns = $userModel->getDataTableColumns();
		$canUpdate = $this->hasPermission('actUpdateUser');
		$canAlterPassword = $this->hasPermission('actAlterUserPassword');
		$canDelete = $this->hasPermission('actDeleteUser');
		$canBlock = $this->hasPermission('actBlockUser');
		$canUnblock = $this->hasPermission('actUnblockUser');
		foreach ($dbData['records'] as $user)
		{
			$actions = '<div style="display: inline-block;">';
			if ($canUpdate)
			{
				$actions .= '<button class="btn btn-primary btn-edit-user m-1"
							data-user_id="'. $user['id'] .'" 
							data-toggle="tooltip" title="Atualizar usuário">
							<i class="fa fa-edit"></i>
						</button>';
			}

			if ($canAlterPassword)
			{
				$actions .= '<button class="btn btn-warning btn-alter-user-pwd m-1"
							data-user_id="'. $user['id'] .'" 
							data-username="'. $user['username'] .'" 
							data-toggle="tooltip" title="Alterar senha do usuário">
							<i class="fas fa-key"></i>
						</button>';
			}

			if($user['is_blocked'] == 1)
			{
				if($canUnblock)
				{
					$actions .= '<button class="btn btn-info btn-unblock-user m-1"
								data-user_id="'. $user['id'] .'" 
								data-toggle="tooltip" title="Desbloquear usuário">
								<i class="fas fa-check"></i>
							</button>';
				}
			}
			else
			{
				if($canBlock)
				{
					$actions .= '<button class="btn btn-info btn-block-user m-1"
								data-user_id="'. $user['id'] .'" 
								data-toggle="tooltip" title="Bloquear usuário">
								<i class="fas fa-ban"></i>
							</button>';
				}
			}

			if($canDelete)
			{
				$actions .= '<button class="btn btn-danger btn-del-user m-1"
							data-user_id="'. $user['id'] .'" 
							data-toggle="tooltip" title="Deletar usuário">
							<i class="fa fa-times"></i>
						</button>';
			}

			$actions .= '</div>';
			$user['is_admin'] = ($user['is_admin'] == 1)? 'Sim':'Não';
			$user['is_blocked'] = ($user['is_blocked'] == 1)? 'Sim':'Não';
			$row = [];
			foreach ($dtColumns as $dtColumn)
				$row[] = $user[$dtColumn];
			$userRoles = $roleModel->getUserRoles($user['id']);
			if (empty($userRoles))
			{
				$roles = '';
			}
			else
			{
				$roles = [];
				foreach ($userRoles as $userRole)
					$roles[] = $userRole['name'];
				$roles = implode(', ', $roles);
			}
			$row[] = $roles;
			$row[] = $actions;
			$rows[] = $row;
		}

		$ret['draw'] = $requestData['draw'];
		$ret['recordsTotal'] = $dbData['recordsTotal'];
		$ret['recordsFiltered'] = $dbData['recordsFiltered'];
		$ret['data'] = $rows;
		echo json_encode($ret);
	}

	public function actListRoles()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$requestData = $this->request->getPost();
		$ret = [];
		$rows = [];
		$roleModel = new RoleModel();
		$dbData = $roleModel->getDataTableRoles($requestData);
		$dtColumns = $roleModel->getDataTableRolesColumns();
		$canUpdate = $this->hasPermission('actUpdateRole');
		$canDelete = $this->hasPermission('actDeleteRole');
		foreach ($dbData['records'] as $role)
		{
			$actions = '<div style="display: inline-block;">';
			if($canUpdate)
			{
				$actions .= '<button class="btn btn-primary btn-edit-role m-1"
							data-role_id="'. $role['id'] .'" 
							data-toggle="tooltip" title="Atualizar papel de usuário">
							<i class="fa fa-edit"></i>
						</button>';
			}

			if($canDelete)
			{
				$actions .= '<button class="btn btn-danger btn-del-role m-1"
							data-role_id="'. $role['id'] .'" 
							data-toggle="tooltip" title="Deletar papel de usuário">
							<i class="fa fa-times"></i>
						</button>';
			}

			$actions .= '</div>';
			$role['description'] = str_replace("\r\n", '<br>', $role['description']);
			$row = [];
			foreach ($dtColumns as $dtColumn)
				$row[] = $role[$dtColumn];
			$row[] = $actions;
			$rows[] = $row;
		}

		$ret['draw'] = $requestData['draw'];
		$ret['recordsTotal'] = $dbData['recordsTotal'];
		$ret['recordsFiltered'] = $dbData['recordsFiltered'];
		$ret['data'] = $rows;
		echo json_encode($ret);
	}

	public function actListRolesPermissions()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$requestData = $this->request->getPost();
		$ret = [];
		$rows = [];
		$roleModel = new RoleModel();
		$dbData = $roleModel->getDataTableRolesPermissions($requestData);
		$dtColumns = $roleModel->getDataTableRolesPermissionsColumns();
		foreach ($dbData['records'] as $role)
		{
			$row = [];
			foreach ($dtColumns as $dtColumn)
				$row[] = $role[$dtColumn];
			$rows[] = $row;
		}

		$ret['draw'] = $requestData['draw'];
		$ret['recordsTotal'] = $dbData['recordsTotal'];
		$ret['recordsFiltered'] = $dbData['recordsFiltered'];
		$ret['data'] = $rows;
		echo json_encode($ret);
	}

	public function actBlockUser()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$userModel = new UserModel();
		$requestData = $this->request->getPost();
		if ( ! $userModel->blockUser($requestData['user_id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao bloquear usuário!';
		}
		echo json_encode($ret);
	}

	public function actUnblockUser()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$userModel = new UserModel();
		$requestData = $this->request->getPost();
		if ( ! $userModel->unblockUser($requestData['user_id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao desbloquear usuário!';
		}
		echo json_encode($ret);
	}

	public function actGetUserData()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$userModel = new UserModel();
		$userData = $userModel->getData($this->request->getPost('user_id'));
		if (empty($userData))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Dados do usuário requisitado não existem!';
		}
		else
		{
			$ret['user'] = $userData;
		}
		echo json_encode($ret);
	}

	public function actDeleteUser()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$userModel = new UserModel();
		$requestData = $this->request->getPost();
		if ( ! $userModel->delete($requestData['user_id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao deletar usuário!';
		}
		echo json_encode($ret);
	}

	public function actGetRoleData()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$roleModel = new RoleModel();
		$roleData = $roleModel->getData($this->request->getPost('role_id'));
		if (empty($roleData))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Dados do papel requisitado não existem!';
		}
		else
		{
			$ret['role'] = $roleData;
		}
		echo json_encode($ret);
	}

	public function actDeleteRole()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$roleModel = new RoleModel();
		$requestData = $this->request->getPost();
		if ( ! $roleModel->delete($requestData['role_id']))
		{
			$ret['status'] = 0;
			$ret['error_message'] = 'Falha ao papel!';
		}
		echo json_encode($ret);
	}

	public function actGetControllerActions()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$systemModel = new SystemModel();
		$ret['actions'] = $systemModel->getRestrictCtrlActs($this->request->getPost('controller_id'));
		echo json_encode($ret);
	}

	public function actGetControllers()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$systemModel = new SystemModel();
		$ret['controllers'] = $systemModel->getRestrictCtrls();
		echo json_encode($ret);
	}

	public function actGetRoles()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$roleModel = new RoleModel();
		$ret['roles'] = $roleModel->getRoles();
		echo json_encode($ret);
	}

	private function _saveUser(array $requestData, bool $isInsert, string $act)
	{
		if ( ! $this->hasPermission($act))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$userModel = new UserModel();

		if ($isInsert === TRUE)
		{
			if ( ! $userModel->insert($requestData))
			{
				$errors = $userModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Falha ao cadastrar usuário no banco de dados!';
			}
		}
		else if ($isInsert === FALSE)
		{
			if ( ! $userModel->update($requestData['id'], $requestData))
			{
				$errors = $userModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Falha ao atualizar usuário no banco de dados!';
			}
		}
		if (isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	public function actCreateUser()
	{
		$this->_saveUser($this->request->getPost(), TRUE, __FUNCTION__);
	}

	public function actUpdateUser()
	{
		$this->_saveUser($this->request->getPost(), FALSE, __FUNCTION__);
	}

	public function actAlterUserPassword()
	{
		if ( ! $this->hasPermission(__FUNCTION__))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$userModel = new UserModel();
		$requestData = $this->request->getPost();
		if ( ! $userModel->updatePassword($requestData['user_id'], $requestData))
		{
			$errors = $userModel->errors();
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

	private function _save_role(array $requestData, bool $isInsert, string $act)
	{
		if ( ! $this->hasPermission($act))
			throw new \CodeIgniter\Exceptions\PageNotFoundException();

		$ret['status'] = 1;
		$roleModel = new RoleModel();

		if ($isInsert === TRUE)
		{
			if ( ! $roleModel->insert($requestData))
			{
				$errors = $roleModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Falha ao cadastrar papel no banco de dados!';
			}
		}
		else if ($isInsert === FALSE)
		{
			if ( ! $roleModel->update($requestData['id'], $requestData))
			{
				$errors = $roleModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Falha ao atualizar papel no banco de dados!';
			}
		}
		if (isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	public function actCreateRole()
	{
		$this->_save_role($this->request->getPost(), TRUE, __FUNCTION__);
	}

	public function actUpdateRole()
	{
		$this->_save_role($this->request->getPost(), FALSE, __FUNCTION__);
	}
}