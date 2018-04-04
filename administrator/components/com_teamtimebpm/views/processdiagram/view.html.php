<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimebpmViewProcessdiagram extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$assetsPath = URL_MEDIA_COMPONENT_ASSETS;

		// include editor files
		if (APPLICATION_ENV != "production") {
			foreach (file(JPATH_ROOT . "/" . $assetsPath . "/js/ordered.txt") as $f) {
				$f = trim($f);
				if ($f == "") {
					continue;
				}
				JHTML::script(basename($f), $assetsPath . dirname($f) . "/");
			}

			//JHTML::script('SwimlanePanelToolbar.js', $assetsPath . 'js/swimlane/');
			//JHTML::script('SwimlanePanelBlocksMenu.js', $assetsPath . 'js/swimlane/');
			//JHTML::script('SwimlanePanel.js', $assetsPath . 'js/swimlane/');
			//JHTML::script('app.js', $assetsPath . 'js/swimlane/');
		}
		else {
			JHTML::script('compiled-package.js', $assetsPath . 'js/');
		}

		$lang = &JFactory::getLanguage();
		JHTML::script('SwimlanePanel_lang_' .
				$lang->getTag() . '.js', $assetsPath . 'js/swimlane/');

		JHTML::stylesheet('swimlane.css', $assetsPath . 'css/');
		JHTML::stylesheet('diagram_elements.css', $assetsPath . 'css/');

		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// set toolbar items
		//$text = $edit ? JText::_('Edit') : JText::_('New');
		//JToolBarHelper::title(JText::_('Process') . ': <small><small>[ ' . $text . ' ]</small></small>',
		//		TEAMLOG_ICON);
		//JToolBarHelper::save();
		//JToolBarHelper::apply();
		//$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();
		// get data from the model
		$item = & $this->get('data');

		$mProcess = new TeamtimebpmModelProcess();
		$mSpace = new TeamtimebpmModelSpace();

		//if ($item) {
		//	$item->user_name = $mProcess->getModifiedUserName($item->modified_by);
		//}
		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}