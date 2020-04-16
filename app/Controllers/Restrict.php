<?php namespace App\Controllers;

use App\Models\UserModel;

class Restrict extends BaseController
{
	public function __construct(...$params)
    {
		parent::__construct(...$params);
	}

	public function index()
	{
		if ($this->_userIsLogged())
		{
			$this->_loadRestrict();
		}
		else
		{
			$this->_loadLogin();
		}
	}

	private function _loadRestrict()
	{
		$userModel = new UserModel();
		$data = [
			'scripts' => [
				'vendor/jquery/jquery-3.4.1.min.js',
				'vendor/jquery-easing/jquery.easing.min.js',
				'vendor/bootstrap/js/bootstrap.bundle.min.js',
				'vendor/datatable/datatables.min.js',
				'vendor/sweetalert/sweetalert.min.js',
				'js/sb-admin-2.min.js',
				'js/owner/config.js?v='.filemtime(FCPATH.'js/owner/config.js'),
				'js/owner/util.js?v='.filemtime(FCPATH.'js/owner/util.js'),
				'js/owner/restrict/restrict.js?v='.filemtime(FCPATH.'js/owner/restrict/restrict.js'),
			],
			'styles' => [
				'vendor/datatable/datatables.min.css',
				'css/sb-admin-2.min.css',
				'vendor/fontawesome-free/css/all.min.css',
				'css/owner/restrict.css?v=' . filemtime(FCPATH.'css/owner/restrict.css')
			],
			'images' => [
				'mainLogo' => 'images/logos/odonto-time.png',
				'userAvatar' => 'images/icons/default-user-avatar-64x64.png'
			],
			'suportEmail' => 'odontotime.suporte@gmail.com',
			'controller' => $this,
			'permissions' => [
				'App\\Controllers\\User' => $userModel->hasAnyCtrlPermission($this->session->user['id'], 'App\\Controllers\\User'),
				'App\\Controllers\\Patient' => $userModel->hasAnyCtrlPermission($this->session->user['id'], 'App\\Controllers\\Patient'),
			]
		];

		echo view('restrict/templates/header', $data);
		echo view('restrict/templates/footer', $data);
	}

	private function _loadLogin()
	{
		$data = [
			'scripts' => [
				'vendor/jquery/jquery-3.4.1.min.js',
				'vendor/bootstrap/js/bootstrap.bundle.min.js',
				'vendor/sweetalert/sweetalert.min.js',
				'js/owner/config.js?v='.filemtime(FCPATH.'js/owner/config.js'),
				'js/owner/util.js?v='.filemtime(FCPATH.'js/owner/util.js'),
				'js/owner/restrict/login.js?v='.filemtime(FCPATH.'js/owner/restrict/login.js')
			],
			'styles' => [
				'css/sb-admin-2.min.css',
				'css/owner/login.css',
				'vendor/fontawesome-free/css/all.min.css'
			]
		];

		echo view('restrict/login', $data);
	}

	public function login()
	{
		$ret = [
			'status' => 0,
			'errorList' => [
				'username' => 'Usuário inválido.',
				'password' => 'Senha inválida.'
			]
		];
		$requestData = $this->request->getPost();
		$userModel = new UserModel();

		if (isset($requestData['username']) && isset($requestData['password']))
		{
			$dbData = $userModel->getLoginData($requestData['username']);
			if ( ! empty($dbData)
			&& password_verify($requestData['password'], $dbData['password_hash']))
			{
				$this->session->set([
					'user' => [
						'id' => $dbData['id'],
						'username' => $dbData['username'],
						'name' => $dbData['name'],
						'email' => $dbData['email'],
						'isAdmin' => $dbData['is_admin'],
						'loggedIn' => TRUE
					]
				]);
				$ret['status'] = 1;
				$ret['errorList'] = [];
			}
		}

		echo json_encode($ret);
	}

	public function logoff()
	{
		$this->session->destroy();
		header('Location: '.base_url());
		exit();
	}

	public function userProfile()
	{
		if ( ! $this->_userIsLogged())
		{
			header('Location: '.base_url());
			exit();
		}

		$userModel = new UserModel();
		$ret['status'] = 1;
		$userID = $this->request->getPost('user_id');
		if($userID != $this->session->user['id'])
		{
			$ret['error_message'] = 'Ocorreu uma manipulação ou corrupção de dados!';
		}
		else
		{
			$userData = $userModel->getData($userID);

			if(empty($userData))
			{
				$ret['error_message'] = 'Dados do usuário requisitado não existem!';
			}
			else
			{
				$ret['profile']['name'] = $userData['name'];
				$ret['profile']['email'] = $userData['email'];
			}
		}

		if(isset($ret['error_message']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	public function updateUserProfile()
	{
		if ( ! $this->_userIsLogged())
		{
			header('Location: '.base_url());
			exit();
		}

		$userModel = new UserModel();
		$ret['status'] = 1;
		$requestData = $this->request->getPost();
		if ($requestData['current_user_id'] != $this->session->user['id'])
		{
			$ret['errorList']['submit_btn'] = 'Ocorreu uma manipulação ou corrupção de dados!';
		}
		else
		{
			$requestData['email_confirm'] = $requestData['email'];
			if ($userModel->updateProfile($requestData['current_user_id'], $requestData))
			{
				$sessData = $this->session->get();
				$sessData['user']['name'] = $requestData['name'];
				$this->session->set($sessData);
			}
			else
			{
				$errors = $userModel->errors();
				if (is_array($errors))
					$ret['errorList'] = $errors;
				else
					$ret['errorList']['submit_btn'] = 'Não foi possível editar dados do perfil.';
			}
		}

		if (isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	public function alterUserPassword()
	{
		if ( ! $this->_userIsLogged())
		{
			header('Location: '.base_url());
			exit();
		}

		$userModel = new UserModel();
		$ret['status'] = 1;
		$requestData = $this->request->getPost();
		$validation = \Config\Services::validation();

		if($requestData['current_user_id'] != $this->session->user['id'])
		{
			$ret['errorList']['submit_btn'] = 'Ocorreu uma manipulação ou corrupção de dados!';
		}
		else
		{
			$validation->setRules([
				'current_password' => 'required'
			],[
				'current_password' => [
					'required' => 'Senha atual é obrigatória.'
				]
			]);

			if ($validation->run($requestData))
			{
				$dbData = $userModel->getData($this->session->user['id']);
				if (password_verify($requestData['current_password'], $dbData['password_hash']))
				{
					if ( ! $userModel->updatePassword(
						$this->session->user['id'],
						$requestData
					))
					{
						$errors = $userModel->errors();
						if (is_array($errors))
							$ret['errorList'] = $errors;
						else
							$ret['errorList']['submit_btn'] = 'Falha ao atualizar senha no banco de dados!';
					}
				}
				else
				{
					$ret['errorList']['current_password'] = 'Senha atual incorreta.';
				}
			}
			else
			{
				$ret['errorList'] = $validation->getErrors();
			}
		}

		if(isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	public function recoveryPassword()
	{
		$userModel = new UserModel();
		$ret['status'] = 1;
		$username = $this->request->getPost('username');
		if (empty($username))
		{
			$ret['errorList']['username'] = 'Login do usuário é obrigatório para recuperação!';
		}
		else if ( ! empty($userData = $userModel->getLoginData($username)))
		{
			$newPassword = $this->generatePassword(10);

			if ($userModel->updatePassword($userData['id'], ['password' => $newPassword, 'password_confirm' => $newPassword]))
			{
				$email = \Config\Services::email();
				$config['protocol'] = 'smtp';
				$config['SMTPHost'] = 'smtp.gmail.com';
				$config['SMTPUser'] = '';
				$config['SMTPPass'] = '';
				$config['SMTPPort'] = '587';
				$config['SMTPCrypto'] = 'tls';
				$config['mailType'] = 'html';
				$email->initialize($config);

				$email->setFrom('odontotime.suporte@gmail.com', 'Suporte OdontoTime');
				$email->setTo($userData['email']);
				$email->setSubject('Recuperação de senha OdontoTime');
				$template = '<table style="width: 100%;"><thead style="background-color: #4e73df; color: #fff; text-align: center;"><tr><th style="font-size: 150%;">E-mail de recuperação de senha</th></tr></thead><tbody><tr><td style="padding: 40px 20px;"><p>A sua nova senha é: <strong>{password}</strong></p><p>É recomendado alterar esta senha logo na primeira sessão realizada no sistema com a mesma.</p></td></tr></tbody><tfoot style="background-color: #4e73df; color: #fff; text-align: center;"><tr><td style="font-size: 75%;">Este e-mail é gerado por um sistema automatizado, <br>por favor não responder.</td></tr></tfoot></table>';
				$email->setMessage(str_replace('{password}', $newPassword, $template));
	
				if ( ! $email->send())
				{
					$ret['errorList']['submit_btn'] = 'Não foi possível enviar o e-mail contendo a nova senha';
				}
			}
			else
			{
				$ret['errorList']['submit_btn'] = 'Não foi possível atualizar o banco de dados com a nova senha';
			}
		}

		if (isset($ret['errorList']))
		{
			$ret['status'] = 0;
		}

		echo json_encode($ret);
	}

	/**
	 * Generate a string of pseudo-random bytes
	 * 
	 * @param	integer	$numBytes	Number of bytes to be generated
	 * @return	string	$bytes
	 */
	private function _getRandomBytes($numBytes = 32)
	{
		$bytes = openssl_random_pseudo_bytes($numBytes, $strong);
		if ($bytes !== false && $strong === true)
		{
			return $bytes;
		}
		else
		{
			throw new Exception('Unable to generate secure token from OpenSSL.');
		}
	}

	/**
	 * Generate password
	 * 
	 * @uses	App\Controllers\Restrict::_getRandomBytes()
	 * @see		App\Controllers\Restrict::_getRandomBytes()
	 * @param	integer	$length	Length of password to be generated
	 * @return	string
	 */
	private function generatePassword($length)
	{
		return substr(preg_replace('/[^a-zA-Z0-9]/', '', base64_encode($this->_getRandomBytes($length+1))), 0, $length);
	}
}
