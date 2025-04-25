<?php

class TeamTime_Bootstrap_Career {

	private $controllerName;
	private $moduleName;
	private $actionName;

	public function __construct() {
		//spl_autoload_register(array($this, 'loader'));

		$this->moduleName = JRequest::getWord('option');
		$this->controllerName = JRequest::getWord('controller');
		$this->actionName = JRequest::getCmd('task');

		if ($this->controllerName == "") {
			$this->controllerName = "targetvector";
		}

		$this->init();
	}

	public function init() {
		define('TEAMLOG_ICON', 'teamtime.png');
		define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime DOTU') . ' - ');

		JHTML::addIncludePath(JPATH_COMPONENT . '/helpers');
		require_once(JPATH_COMPONENT . "/helpers/teamtimecareer.php");

		JHTML::script('default.js', "administrator/components/" . $this->moduleName . "/assets/js/");
		JHTML::stylesheet('default.css', "administrator/components/" . $this->moduleName . "/assets/css/");
		JHTML::stylesheet('errorvectortable.css',
				"administrator/components/" . $this->moduleName . "/assets/css/");

		// create component menu
		JSubMenuHelper::addEntry(
				JText::_("Control Panel"), "index.php?option=com_teamtime&controller=cpanel",
				$this->controllerName == "cpanel" || $this->controllerName == 'config');
		JSubMenuHelper::addEntry(
				JText::_("Vector of goals"),
				"index.php?option=" . $this->moduleName . "&controller=targetvector",
				$this->controllerName == "targetvector");
		JSubMenuHelper::addEntry(
				JText::_("State vector"), "index.php?option=" . $this->moduleName . "&controller=statevector",
				$this->controllerName == "statevector");
		JSubMenuHelper::addEntry(
				JText::_("Error vector"), "index.php?option=" . $this->moduleName . "&controller=errorvector",
				$this->controllerName == "errorvector");

		TeamTime::helper()->getBase()->addJavaScript(array(
			"option" => $this->moduleName,
			"controller" => $this->controllerName), true);
	}

	public function run() {
		error_log("@@@ " . JPATH_COMPONENT . '/controllers/' . $this->controllerName . '.php');
		require_once(JPATH_COMPONENT . '/controllers/' . $this->controllerName . '.php');

		$className = 'TeamtimecareerController' . $this->controllerName;
		if (!class_exists($className)) {
			$className = $this->controllerName . 'Controller';
		}

		$controller = new $className();
		$controller->execute($this->actionName);
		$controller->redirect();
	}

}
