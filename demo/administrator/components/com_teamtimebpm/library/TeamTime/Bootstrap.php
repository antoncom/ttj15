<?php

class TeamTime_Bootstrap_Bpm {

	private $controllerName;
	private $moduleName;
	private $actionName;

	public function __construct() {
		//spl_autoload_register(array($this, 'loader'));

		$this->moduleName = JRequest::getWord('option');
		$this->controllerName = JRequest::getWord('controller');
		$this->actionName = JRequest::getCmd('task');

		if ($this->controllerName == "") {
			$this->controllerName = "bpmnrole";
		}

		$this->init();
	}

	public function init() {
		$lang = & JFactory::getLanguage();

		define('TEAMLOG_ICON', 'teamtimebpm.png');
		define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime BPM') . ' - ');

		JHTML::stylesheet('default.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::stylesheet('spaces.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::stylesheet('processes.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::stylesheet('fullscreen.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');

		JHTML::script('default.js', URL_MEDIA_COMPONENT_ASSETS . 'js/');

		JSubMenuHelper::addEntry(JText::_("Control Panel"),
				"index.php?option=com_teamtime&controller=cpanel",
				$this->controllerName == "cpanel" || $this->controllerName == 'config');
		JSubMenuHelper::addEntry(JText::_("Spaces"),
				"index.php?option=" . $this->moduleName . "&controller=space", $this->controllerName == "space");
		JSubMenuHelper::addEntry(JText::_("Processes"),
				"index.php?option=" . $this->moduleName . "&controller=process",
				$this->controllerName == "process");
		JSubMenuHelper::addEntry(JText::_("Templates"),
				"index.php?option=" . $this->moduleName . "&controller=template",
				$this->controllerName == "template");
		JSubMenuHelper::addEntry(JText::_("Roles"),
				"index.php?option=" . $this->moduleName . "&controller=bpmnrole",
				$this->controllerName == "bpmnrole");

		TeamTime::helper()->getBase()->addJavaScript(array(
			"option" => $this->moduleName,
			"controller" => $this->controllerName), true);
	}

	public function run() {
		require_once(JPATH_COMPONENT . '/controllers/' . $this->controllerName . '.php');

		$className = 'TeamtimebpmController' . $this->controllerName;
		if (!class_exists($className)) {
			$className = $this->controllerName . 'Controller';
		}

		$controller = new $className();
		$controller->execute($this->actionName);
		$controller->redirect();
	}

}
