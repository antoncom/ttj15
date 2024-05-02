<?php

class TeamTime_Bootstrap_Calendar {

	private $controllerName;
	private $moduleName;
	private $actionName;

	public function __construct() {
		//spl_autoload_register(array($this, 'loader'));

		$this->moduleName = JRequest::getWord('option');
		$this->controllerName = JRequest::getWord('controller');
		$this->actionName = JRequest::getCmd('task');

		$this->init();
	}

	public function init() {
		$lang = & JFactory::getLanguage();

		define('TEAMLOG_ICON', 'teamtime.png');
		define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime Calendar'));

		JHTML::script('Common.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');
		JHTML::script('datepicker_lang_' . $lang->getTag() . '.js',
				URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');

		JHTML::script('jquery.form.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');
		JHTML::script('jquery.validate.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');
		JHTML::script('jquery.dropdown.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');
		JHTML::script('jquery.colorselect.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');

		JHTML::script('jquery.alert.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');
		JHTML::script('wdCalendar_lang_' . $lang->getTag() . '.js',
				URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');
		JHTML::script('jquery.calendar.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');

		JHTML::stylesheet('dailog.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::stylesheet('calendar.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::stylesheet('dp.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::stylesheet('alert.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');

		TeamTime::helper()->getBase()->addJavaScript(array(
			"option" => $this->moduleName,
			"controller" => $this->controllerName), true);

		foreach (TeamTime::helper()->getList() as $helper) {
			$helper->addonMenuItem($this->controllerName);
		}
	}

	public function run() {
		require_once(JPATH_COMPONENT . '/controllers/' . $this->controllerName . '.php');

		$className = 'TeamtimecalendarController' . $this->controllerName;
		$controller = new $className();
		$controller->execute($this->actionName);
		$controller->redirect();
	}

}
