<?php

class TeamTime_EventHandlers_Base {

	private function initLogger() {
		if (file_exists(JPATH_ROOT . "/enviroment")) {
			define("APPLICATION_ENV", file_get_contents(JPATH_ROOT . "/enviroment"));
		}
		else {
			define("APPLICATION_ENV", "production");
		}

		$user = &JFactory::getUser();
		if ($user->guest) {
			return;
		}

		if (APPLICATION_ENV != "production") {
			// log errors by firebug php
			require_once("fb.php");

			$firephp = FirePHP::getInstance(true);
			$firephp->registerErrorHandler();
			$firephp->registerExceptionHandler();
			$firephp->registerAssertionHandler();
		}
	}

	public function onInit() {
		$this->initLogger();

		$path = TeamTime::$filePath;
		$url = TeamTime::$basePath;

		// include js libs

		JHTML::script('jquery-1.7.2.min.js', $url . '/assets/js/libs/jquery/');
		JHTML::script('jquery.noconflict.js', $url . '/assets/js/');

		JHTML::script('jquery-ui-1.8.17.custom.min.js',
				$url . '/assets/js/libs/jquery-ui/js/');
		JHTML::stylesheet('jquery-ui-1.8.17.custom.css',
				$url . '/assets/js/libs/jquery-ui/css/ui-lightness/');

		JHTML::script('jquery.balloon.js', $url . '/assets/js/libs/jquery/');

		JHTML::script('jquery.ui.position.js',
				$url . '/assets/js/libs/jquery/contextmenu/');
		JHTML::script('jquery.contextMenu.js',
				$url . '/assets/js/libs/jquery/contextmenu/');
		JHTML::stylesheet('jquery.contextMenu.css',
				$url . '/assets/js/libs/jquery/contextmenu/');

		JHTML::script('jquery.mousewheel-3.0.6.pack.js',
				$url . '/assets/js/libs/jquery/');
		JHTML::script('jquery.fancybox.js', $url . '/assets/js/libs/jquery/fancybox/');
		JHTML::stylesheet('jquery.fancybox.css',
				$url . '/assets/js/libs/jquery/fancybox/');
		JHTML::stylesheet('jquery.fancybox-buttons.css',
				$url . '/assets/js/libs/jquery/fancybox/helpers/');
		JHTML::script('jquery.fancybox-buttons.js',
				$url . '/assets/js/libs/jquery/fancybox/helpers/');
		JHTML::stylesheet('jquery.fancybox-thumbs.css',
				$url . '/assets/js/libs/jquery/fancybox/helpers/');
		JHTML::script('jquery.fancybox-thumbs.js',
				$url . '/assets/js/libs/jquery/fancybox/helpers/');

		JHTML::script('autoNumeric-1.7.4.js', $url . '/assets/js/libs/jquery/');

		JHTML::script('jquery.colorselect.js', $url . "/assets/js/libs/jquery/");

		JHTML::script('raphael.js', $url . '/assets/js/libs/');
		JHTML::script('raphael-piechart.js', $url . "/assets/js/");

		JHTML::script('underscore.js', $url . '/assets/js/libs/');
		JHTML::script('json2.js', $url . '/assets/js/libs/');
		JHTML::script('purl.js', $url . '/assets/js/libs/');

		JHTML::script('default.js', $url . '/assets/js/');
		JHTML::script('init-fancybox.js', $url . '/assets/js/');

		// set the table directory
		JTable::addIncludePath($path . '/tables');

		// include models
		require_once($path . "/models/task.php");
		require_once($path . "/models/todo.php");
		require_once($path . "/models/log.php");

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

}