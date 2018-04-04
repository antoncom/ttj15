<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgButtonTeamtimeAttachFiles extends JPlugin {

	private $path;

	private function init() {
		$app = JFactory::getApplication();
		$language = JFactory::getLanguage();

		$language->load('plg_teamtimeattachfiles', JPATH_SITE);

		$this->path = JURI::root() . "plugins/editors-xtd/teamtimeattachfiles/";

		$url = JURI::root(true) . "media/com_teamtime/assets/";

		// init scripts and styles
		JHTML::script('jquery-1.7.2.min.js', $url . '/js/libs/jquery/');

		JHTML::script('jquery.mousewheel-3.0.6.pack.js', $url . '/js/libs/jquery/');
		JHTML::script('jquery.fancybox.js', $url . '/js/libs/jquery/fancybox/');
		JHTML::stylesheet('jquery.fancybox.css', $url . '/js/libs/jquery/fancybox/');
		JHTML::stylesheet('jquery.fancybox-buttons.css',
				$url . '/js/libs/jquery/fancybox/helpers/');
		JHTML::script('jquery.fancybox-buttons.js',
				$url . '/js/libs/jquery/fancybox/helpers/');
		JHTML::stylesheet('jquery.fancybox-thumbs.css',
				$url . '/js/libs/jquery/fancybox/helpers/');
		JHTML::script('jquery.fancybox-thumbs.js',
				$url . '/js/libs/jquery/fancybox/helpers/');

		JHTML::script('jquery.form.js', $this->path . 'assets/js/libs/jquery/');

		/*
		  JHTML::script('jquery.uploadify-3.1.js',
		  $this->path . 'assets/js/libs/jquery/uploadify/');
		  JHTML::stylesheet('uploadify.css',
		  $this->path . 'assets/js/libs/jquery/uploadify/'); */

		JHTML::script('init.js', $this->path . 'assets/js/');

		//JHTML::stylesheet('redactor.css', $this->path . 'assets/js/redactor/css/');
		// init inline scripts
		$doc = & JFactory::getDocument();
		$result = array();
		$result[] = "plgButtonTeamtimeAttachFiles.baseUrl = '" . $this->path . "'";
		$result[] = "plgButtonTeamtimeAttachFiles.rootUrl = '" . JURI::base() . "'";
		$result[] = "plgButtonTeamtimeAttachFiles.lang = {}";
		$result[] = "plgButtonTeamtimeAttachFiles.lang.label_loaded_results = '" .
				JText::_("RESULT_ATTACHED_FILES") . "'";

		//$session = & JFactory::getSession();
		//$result[] = "plgButtonTeamtimeAttachFiles.sessionName = '" . $session->getName() . "'";
		//$result[] = "plgButtonTeamtimeAttachFiles.sessionId = '" . $session->getId() . "'";

		$doc->addScriptDeclaration(implode(";\n", $result) . ";");
	}

	public function onDisplay($name) {
		global $mainframe;

		$this->init();

		// see for example 'image.php' plugin
		//$doc = & JFactory::getDocument();
		//$template = $mainframe->getTemplate();
		//$link = JURI::root(true) . 
		//		'index.php?option=com_teamtime&amp;tmpl=component&amp;controller=attachments';

		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->set('modal', false);
		$button->set('link', "#");
		$button->set('onclick',
				'plgButtonTeamtimeAttachFiles.uploadDialog(\'' . $name . '\');return false;');
		$button->set('text', JText::_('Files'));
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 570, y: 400}}");

		return $button;
	}

}
