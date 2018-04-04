<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/*
   Class: LogController
   The controller class for Log
*/
class LogController extends JController {

 	/*
	   Function: Constructor

	   Parameters:

	      $default -

	   Returns:

	      See Also:

	*/
	function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('apply', 'save');
		$this->registerTask('preview', 'display');
		$this->registerTask('edit', 'display');
		$this->registerTask('add', 'display');
	}

	function display() {

		switch($this->getTask()) {
			case 'add':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'log');
				JRequest::setVar('edit', false);
				break;
			case 'edit':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'log');
				JRequest::setVar('edit', true);
				break;
		}

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'logs');
		}

		parent::display();
	}

	function save() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		
		$cid  = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = $this->getModel();

		$msg = "";
		if ($this->checkPost($post)){
			if ($model->store($post)) {
				$msg = JText::_('Log Saved');
			} else {
				$msg = JText::_('Error Saving Log');
			}
		}

		switch ($this->_task) {
			case 'apply':
				$item =& $model->getData();
				$link = 'index.php?option='.$option.'&controller='.$this->getName().
						'&view=type&task=edit&cid[]='.$item->id;
				break;
			case 'save':
			default:
				$link = 'index.php?option='.$option.'&controller='.$this->getName();
				break;
		}

		$this->setRedirect($link, $msg);
	}

	function checkPost($post){
		if(!isset($post['description']) || $post['description'] == ""){
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid description'));
			return false;
		}

		if(!isset($post['project_id']) || $post['project_id'] == ""){
			JError::raiseWarning(0, JText::_('Error Saving: Please select a project'));
			return false;
		}

		if(!isset($post['task_id']) || $post['task_id'] == "" || $post['task_id'] < 0){
			JError::raiseWarning(0, JText::_('Error Saving: Please select a task'));
			return false;
		}
		return true;
	}

	function remove() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select an item to delete'));
		}

		$model = $this->getModel();

		$log = new Log($cid[0]);		

		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$msg = JText::_('Log Deleted');
		$link = 'index.php?option='.$option.'&controller='.$this->getName();

		//update todo summ
		$todo = new Todo($log->todo_id);
		$todo->hours_fact = $todo->getLogsSumm();
		$todo->save();	

		$this->setRedirect($link, $msg);
	}

}