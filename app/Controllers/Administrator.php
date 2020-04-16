<?php namespace App\Controllers;

use App\Models\SystemModel;

class Administrator extends BaseController
{
	public static $friendlyName = 'Painel do adminsitrador';

	public function __construct()
	{
		if ( ! is_cli())
		{
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
		}
	}

	public function index()
	{
		$this->migrateRestrictCtrlsActs();
	}

	public function migrateRestrictCtrlsActs()
	{
		$systemModel = new SystemModel();
		$config = [
			'controllersPath' => APPPATH.'Controllers/',
			'actionPrefix' => 'act'
		];

		$ctrls = [];
		if (is_dir($config['controllersPath'])
		&& $dh = opendir($config['controllersPath']))
		{
			while (($file = readdir($dh)) !== false)
			{
				$filePath = $config['controllersPath'] . $file;
				if (is_file($filePath)
				&& preg_match('/\.php$/', $file))
				{
					require_once($filePath);
					$className = preg_replace('/\.php$/', '', $file);
					$className = 'App\\Controllers\\'.$className;
					$parentClass = get_parent_class($className);
					if ($parentClass == 'App\\Controllers\\RestrictAccess')
					{
						$aux = [];
						$aux['className'] = $className;
						$actions = [];
						$classMethods = get_class_methods($className);
						$classAttr = get_class_vars($className);
						$aux['friendlyClassName'] = $classAttr['friendlyName'];
						foreach ($classMethods as $method)
							if (preg_match('/^'.$config['actionPrefix'].'/', $method))
								$actions[] = $method;
						$aux['actions'] = $actions;
						$ctrls[] = $aux;
					}
				}
			}
			closedir($dh);
		}

		foreach ($ctrls as $ctrl)
		{
			echo 'Class name: ', $ctrl['className'], PHP_EOL;

			$ctrlExistsInDB = $systemModel->restrictCtrlExists($ctrl['className']);

			if ( ! $ctrlExistsInDB)
			{
				$insertedID = $systemModel->insertRestrictCtrl([
					'canonical_name' => $ctrl['className'],
					'friendly_name' => $ctrl['friendlyClassName']
				]);
			}

			if ($ctrlExistsInDB || $insertedID !== FALSE)
			{
				if ($ctrlExistsInDB)
				{
					$ctrlID = $systemModel->getRestrictCtrl($ctrl['className'])['id'];
				}
				else if ($insertedID !== FALSE)
				{
					$ctrlID = $insertedID;
				}

				foreach ($ctrl['actions'] as $action)
				{
					$actionExistsInDB = $systemModel->restrictActExists($ctrlID, $action);
					if( ! $actionExistsInDB)
					{
						$status = $systemModel->insertRestrictCtrlAct([
							'controller_id' => $ctrlID,
							'canonical_name' => $action
						]);
					}

					if(isset($status) && ! $status)
						echo '[ERROR]: ', $action, PHP_EOL;
					else
						echo '[OK]: ', $action, PHP_EOL;
				}
			}

			echo PHP_EOL, PHP_EOL;
		}

		$controllersInDB = $systemModel->getRestrictCtrls();

		$founders = [];
		foreach($controllersInDB as $controllerInDB)
		{
			$found = FALSE;
			foreach($ctrls as $key => $ctrl)
				if($ctrl['className'] == $controllerInDB['canonical_name'])
				{
					$found = $key;
					break;
				}

			if($found !== FALSE)
			{
				$founders[] = $found;
			}
			else
			{
				if($systemModel->deleteRestrictCtrl($controllerInDB['id']))
					echo '[OK]';
				else
					echo '[ERROR]';
				echo ' : em deletar controlador "', $controllerInDB['canonical_name'], '"', PHP_EOL;
			}
		}

		$actsInDB = $systemModel->getRestrictCtrlsActs();

		foreach($founders as $key)
		{
			$ctrl = $ctrls[$key];

			foreach($actsInDB as $actInDB)
			{
				if($actInDB['controller.canonical_name'] != $ctrl['className'])
					continue;

				$found = FALSE;
				foreach($ctrl['actions'] as $action)
					if($actInDB['action.canonical_name'] == $action)
					{
						$found = TRUE;
						break;
					}

				if( ! $found)
				{
					if($this->system_model->delete_controller_action($actInDB['action_id']))
						echo '[OK]';
					else
						echo '[ERROR]';
					echo ': Deletar ação "', $actInDB['action.canonical_name'], '" do controlador "'.$actInDB['controller.canonical_name'].'"', PHP_EOL;
				}
			}
		}
	}
}