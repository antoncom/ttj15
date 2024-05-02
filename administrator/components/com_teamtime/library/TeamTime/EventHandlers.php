<?php

class TeamTime_EventHandlers_Base {

	private function initLogger() {
		$user = &JFactory::getUser();
		if ($user->guest) {
			return;
		}

		if (APPLICATION_ENV != "production") {
			// log errors by firebug php
			//require_once("fb.php");
			//$firephp = FirePHP::getInstance(true);
			//$firephp->registerErrorHandler();
			//$firephp->registerExceptionHandler();
			//$firephp->registerAssertionHandler();
		}
	}

	private function initAssets() {
		define("COMPONENT_ASSETS", "administrator/components/com_" . TeamTime::$componentName . "/assets/");
		define("URL_COMPONENT_ASSETS",
				JURI::root(true) . "administrator/components/com_" . TeamTime::$componentName . "/assets/");
		define("URL_MEDIA_COMPONENT_ASSETS",
				JURI::root(true) . "media/com_" . TeamTime::$componentName . "/assets/");

		//var_dump(COMPONENT_ASSETS, URL_COMPONENT_ASSETS, URL_MEDIA_COMPONENT_ASSETS);
		// load mootools
		JHTML::_('behavior.mootools');

		$doc = & JFactory::getDocument();
		$jsCode = array();

		// include shared assets
		$url = TeamTime::$mediaPath . "/assets";

		JHTML::script('jquery-1.7.2.min.js', $url . '/js/libs/jquery/');

		JHTML::script('jquery-ui-1.8.17.custom.min.js', $url . '/js/libs/jquery-ui/js/');
		JHTML::stylesheet('jquery-ui-1.8.17.custom.css', $url . '/js/libs/jquery-ui/css/ui-lightness/');

		JHTML::script('jquery.balloon.js', $url . '/js/libs/jquery/');

		JHTML::script('jquery.ui.position.js', $url . '/js/libs/jquery/contextmenu/');
		JHTML::script('jquery.contextMenu.js', $url . '/js/libs/jquery/contextmenu/');
		JHTML::stylesheet('jquery.contextMenu.css', $url . '/js/libs/jquery/contextmenu/');

		JHTML::script('jquery.mousewheel-3.0.6.pack.js', $url . '/js/libs/jquery/');
		JHTML::script('jquery.fancybox.js', $url . '/js/libs/jquery/fancybox/');
		JHTML::stylesheet('jquery.fancybox.css', $url . '/js/libs/jquery/fancybox/');
		JHTML::stylesheet('jquery.fancybox-buttons.css', $url . '/js/libs/jquery/fancybox/helpers/');
		JHTML::script('jquery.fancybox-buttons.js', $url . '/js/libs/jquery/fancybox/helpers/');
		JHTML::stylesheet('jquery.fancybox-thumbs.css', $url . '/js/libs/jquery/fancybox/helpers/');
		JHTML::script('jquery.fancybox-thumbs.js', $url . '/js/libs/jquery/fancybox/helpers/');

		JHTML::script('autoNumeric-1.7.4.js', $url . '/js/libs/jquery/');

		JHTML::script('jquery.colorselect.js', $url . "/js/libs/jquery/");

		JHTML::script('jquery.treeTable.js', $url . "/js/libs/jquery/treetable/");
		JHTML::stylesheet('jquery.treeTable.css', $url . "/js/libs/jquery/treetable/");

		// NOTE include any other jquery plugins here

		JHTML::script('highslide-with-html.js', $url . "/modules/highslide/");
		JHTML::stylesheet('highslide.css', $url . "/modules/highslide/");
		$jsCode[] = "hs.graphicsDir = '" . JURI::root(true) . "/" .
				$url . "/modules/highslide/graphics/'";
		JHTML::script('init-highslide.js', $url . "/js/");

		JHTML::script('raphael.js', $url . '/modules/raphael/');
		JHTML::script('raphael-piechart.js', $url . "/js/");

		JHTML::script('underscore-min.js', $url . '/modules/underscore/');
		JHTML::script('underscore.string.min.js', $url . '/modules/underscore/');
		JHTML::script('json2.js', $url . '/modules/json/');
		JHTML::script('purl.js', $url . '/modules/purl/');

		JHTML::script('default.js', $url . '/js/');
		JHTML::script('init-fancybox.js', $url . '/js/');

		// include assets for administration pages
		if (JPATH_BASE == JPATH_ADMINISTRATOR) {
			JHTML::stylesheet('default.css',
					JURI::root(true) . 'administrator/components/com_teamtime/assets/css/');
		}

		if (sizeof($jsCode) > 0) {
			$doc->addScriptDeclaration(implode(";\n", $jsCode) . ";\n");
		}
	}

	public function onInit() {
		$this->initLogger();

		$path = TeamTime::$filePath;
		//$url = TeamTime::$basePath;

		$this->initAssets();

		// set joomla includes
		JTable::addIncludePath($path . "/tables");
		JHTML::addIncludePath($path . "/helpers");

		// include custom objects
		require_once($path . "/library/factory.php");

		// include helpers
		require_once($path . "/helpers/teamtime.php");
		require_once($path . "/helpers/date.php");

		// include models
		require_once($path . "/models/type.php");
		require_once($path . "/models/task.php");
		require_once($path . "/models/todo.php");
		require_once($path . "/models/log.php");
		require_once($path . "/models/project.php");
		require_once($path . "/models/report.php");
		require_once($path . "/models/user.php");

		require_once($path . '/views/report/view.html.php');
		require_once($path . '/controllers/report.php');

		// load language files for component
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtime", JPATH_BASE);
	}

	public function onSaveTodo($todoItem, $post) {
		//error_log("todo_id: " . $todoItem->id);
		//error_log("sendmail: " . (int) $post["sendmail"]);
		//error_log("clientmail: " . (int) $post["clientmail"]);
		// send mail for user
		if (isset($post["sendmail"]) && $post["sendmail"]) {
			TeamTime::helper()->getBase()->notifyUserByEmail($post);
		}
	}

	public function onDeleteLog($id) {
		$log = new Log($id);
		$todo = new Todo($log->todo_id);
		if ($todo->id) {
			$todo->hours_fact = $todo->getLogsSumm();
			$todo->save();
		}
	}

}