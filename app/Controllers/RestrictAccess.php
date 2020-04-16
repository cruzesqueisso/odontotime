<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class RestrictAccess extends BaseController
{
	private $userAllowedActs = NULL;

	public function __construct(...$params)
    {
		parent::__construct(...$params);
	}

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		if ( ! $this->_userIsLogged())
		{
			header('Location: '.base_url());
			exit();
		}

		$this->userAllowedActs = $this->_getAllowedActs();
	}

	private function _getAllowedActs()
	{
		$userModel = new UserModel();
		return $userModel->getUserCtrlActs(
			$this->session->user['id'],
			get_class($this)
		);
	}

	public function hasPermission($act)
	{
		if ($this->session->user['isAdmin'])
			return TRUE;

		foreach ($this->userAllowedActs as $allowedAct)
			if ($allowedAct['canonical_name'] == $act)
				return TRUE;

		return FALSE;
	}
}
