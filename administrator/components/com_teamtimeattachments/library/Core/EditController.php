<?php

jimport('joomla.application.component.controller');

class Core_EditController extends JController {

	var $viewEdit = 'item';
	var $viewList = 'items';

	function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('apply', 'save');
		$this->registerTask('preview', 'display');
		$this->registerTask('edit', 'display');
		$this->registerTask('add', 'display');

		//...
	}

	function display() {
		switch ($this->getTask()) {
			case 'add':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', $this->viewEdit);
				JRequest::setVar('edit', false);
				break;
			case 'edit':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', $this->viewEdit);
				JRequest::setVar('edit', true);
				break;
		}

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', $this->viewList);
		}

		parent::display();
	}

	function save() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');
		$view = JRequest::getVar('view');

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		// for raw html of field
		$post['description'] = JRequest::getVar('description', '', 'post', 'string',
						JREQUEST_ALLOWHTML);

		$model = $this->getModel($this->viewEdit);

		$msg = "";
		if ($this->checkPost($post)) {
			if ($model->store($post)) {
				$msg = JText::_('Item saved');
			}
			else {
				$msg = JText::_('Error saving item');
			}
		}

		switch ($this->_task) {
			case 'apply':
				$item = & $model->getData();
				$link = 'index.php?option=' . $option . '&controller=' . $controller .
						'&view=' . $view . '&task=edit&cid[]=' . $item->id;
				break;
			
			case 'save':
			default:
				$link = 'index.php?option=' . $option . '&controller=' . $controller;
				break;
		}
		
		$this->setRedirect($link, $msg);
	}

	function checkPost($post) {
		if (!isset($post['name']) || $post['name'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
			return false;
		}

		return true;
	}

	function remove() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select an item to delete'));
		}

		$model = $this->getModel($this->viewEdit);

		if (!$model->delete($cid)) {
			print "<script> alert('" . $model->getError(true) .
					"'); window.history.go(-1); </script>\n";
		}

		$msg = JText::_('Item deleted');
		$link = 'index.php?option=' . $option . '&controller=' . $controller;

		$this->setRedirect($link, $msg);
	}

}