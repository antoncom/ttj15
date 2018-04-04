<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.event.plugin');

class plgEditorJRedactor extends JPlugin {

	private $path;

	protected function getEditorName($name) {
		return preg_replace('#\W#', '_', $name);
	}

	public function onInit() {
		// set for debug
		//error_reporting(E_ALL ^ E_DEPRECATED);
		//ini_set("display_errors", "off");
		//ini_set("log_errors", 1);
		//ini_set("error_log", JPATH_ROOT . "/logs/error_log");

		$lang = & JFactory::getLanguage();
		list($currentLang) = explode("-", $lang->getTag());

		$this->path = JURI::root() . "plugins/editors/jredactor/";

		// init scripts and styles
		JHTML::script('jquery-1.7.2.min.js', 'media/com_teamtime/assets/js/libs/jquery/');
		JHTML::script('redactor.js', $this->path . 'assets/js/redactor/');
		if ($currentLang != "en") {
			JHTML::script($currentLang . '.js', $this->path . 'assets/js/redactor/langs/');
		}
		JHTML::script('init.js', $this->path . 'assets/js/');

		JHTML::stylesheet('redactor.css', $this->path . 'assets/js/redactor/css/');
		JHTML::stylesheet('styles.css', $this->path . 'assets/css/');

		// init inline scripts
		$doc = & JFactory::getDocument();
		$result = array();
		$result[] = "plgEditorJRedactor.baseUrl = '" . $this->path . "'";
		$result[] = "plgEditorJRedactor.rootUrl = '" . JURI::base() . "'";
		$result[] = "plgEditorJRedactor.lang = '" . $currentLang . "'";

		$doc->addScriptDeclaration(implode(";\n", $result) . ";");
	}

	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true) {
		$fieldType = "";
		$mainEditor = "";
		$id = $this->getEditorName($name);

		$html = '
			<textarea class="redactorEditor' . $mainEditor . '"
				name="' . $name . '" id="' . $id . '"
				cols="' . $col . '" rows="' . $row . '" ' .
				$fieldType .
				' style="width:' . $width . '; height:' . $height . '">' .
				$content . '</textarea>';

		return $html;
	}

	public function onGetContent($name) {
		$id = $this->getEditorName($name);
		return "plgEditorJRedactor.jQuery('#" . $id . "').getCode();";
	}

	public function onSetContent($name, $html) {
		$id = $this->getEditorName($name);
		return "plgEditorJRedactor.jQuery('#" . $id . "').setCode('" . $html . "');";
	}

	/*
	  public function onGetInsertMethod($name) {
	  return '';
	  }

	  public function onSave($name) {
	  return '';
	  }

	 */
}