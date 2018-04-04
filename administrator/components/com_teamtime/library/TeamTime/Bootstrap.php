<?php

class TeamTime_Bootstrap_Base {

	private $controllerName;
	private $moduleName;
	private $actionName;

	public function __construct() {
		//spl_autoload_register(array($this, 'loader'));

		$this->moduleName = JRequest::getWord('option');
		$this->controllerName = JRequest::getWord('controller');
		$this->actionName = JRequest::getCmd('task');

		// validate controller
		$controllers = array(
			'type', 'project', 'task',
			'log', 'report', 'todo',
			'user', 'cpanel', 'config');
		if (!in_array($this->controllerName, $controllers)) {
			$user = & JFactory::getUser();
			if ($user->usertype == "Super Administrator" || $user->usertype == "Administrator") {
				$this->controllerName = 'cpanel';
			}
			else {
				$this->controllerName = 'report';
			}
		}

		$this->init();
	}

	public function init() {
		//$lang = & JFactory::getLanguage();

		define('TEAMLOG_ICON', 'teamtime.png');
		define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime Accounting') . ' - ');

		// component menu
		if ($this->controllerName == 'cpanel' || $this->controllerName == 'config') {
			foreach (TeamTime::helper()->getList() as $helper) {
				$helper->addonMenuItem($this->controllerName);
			}
		}
		else {
			JSubMenuHelper::addEntry(
					JText::_("Control Panel"), "index.php?option=com_teamtime&controller=cpanel",
					$this->controllerName == "cpanel" || $this->controllerName == 'config');
			JSubMenuHelper::addEntry(
					JText::_("Reports"), "index.php?option=com_teamtime&controller=report",
					$this->controllerName == "report");
			JSubMenuHelper::addEntry(
					JText::_("Todos"), "index.php?option=com_teamtime&controller=todo",
					$this->controllerName == "todo");
			JSubMenuHelper::addEntry(
					JText::_("Logs"), "index.php?option=com_teamtime&controller=log",
					$this->controllerName == "log");
			JSubMenuHelper::addEntry(
					JText::_("Projects"), "index.php?option=com_teamtime&controller=project",
					$this->controllerName == "project");
			JSubMenuHelper::addEntry(
					JText::_("Tasks"), "index.php?option=com_teamtime&controller=task",
					$this->controllerName == "task");
			JSubMenuHelper::addEntry(
					JText::_("Types"), "index.php?option=com_teamtime&controller=type",
					$this->controllerName == "type");
			JSubMenuHelper::addEntry(
					JText::_("Users"), "index.php?option=com_teamtime&controller=user",
					$this->controllerName == "user");
		}

		TeamTime::helper()->getBase()->addJavaScript(array(
			"option" => $this->moduleName,
			"controller" => $this->controllerName), true);
	}

	public function run() {
		require_once(JPATH_COMPONENT . '/controllers/' . $this->controllerName . '.php');

		$className = 'TeamtimeController' . $this->controllerName;
		
		$controller = new $className();
		$controller->execute($this->actionName);
		$controller->redirect();
	}

}
