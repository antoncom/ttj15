<?php

class TeamTime_Bootstrap_Formals {

	private $controllerName;
	private $moduleName;
	private $actionName;

	public function __construct() {
		//spl_autoload_register(array($this, 'loader'));

		$this->moduleName = JRequest::getWord('option');
		$this->controllerName = JRequest::getWord('controller');
		$this->actionName = JRequest::getCmd('task');

		if ($this->controllerName == "") {
			$this->controllerName = "formal";
		}

		$this->init();
	}

	public function init() {
		define('TEAMLOG_ICON', 'teamtime.png');
		define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime Formals') . ' - ');

		JHTML::addIncludePath(JPATH_BASE . '/components/' . $this->moduleName . '/helpers');
		JHTML::stylesheet('default.css', 'administrator/components/' . $this->moduleName . '/assets/css/');

		JSubMenuHelper::addEntry(JText::_(
						"Control Panel"), "index.php?option=com_teamtime&controller=cpanel",
				$this->controllerName == "cpanel" || $this->controllerName == 'config');
		JSubMenuHelper::addEntry(JText::_(
						"Formals Documents"), "index.php?option=" . $this->moduleName,
				$this->controllerName == "formal");
		JSubMenuHelper::addEntry(JText::_(
						"Formals Templates"), "index.php?option=" . $this->moduleName . "&controller=template",
				$this->controllerName == "template");
		JSubMenuHelper::addEntry(JText::_(
						"Formals Variables"), "index.php?option=" . $this->moduleName . "&controller=variable",
				$this->controllerName == "variable");
		JSubMenuHelper::addEntry(JText::_(
						"Formals Types"), "index.php?option=" . $this->moduleName . "&controller=doctype",
				$this->controllerName == "doctype");

		TeamTime::helper()->getBase()->addJavaScript(array(
			"option" => $this->moduleName,
			"controller" => $this->controllerName), true);
	}

	public function run() {
		require_once(JPATH_COMPONENT . '/controllers/' . $this->controllerName . '.php');

		$className = 'TeamtimeformalsController' . $this->controllerName;
		if (!class_exists($className)) {
			$className = $this->controllerName . 'Controller';
		}

		$controller = new $className();
		$controller->execute($this->actionName);
		$controller->redirect();
	}

}
