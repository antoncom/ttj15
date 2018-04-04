<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/*
  Class: ProjectController
  The controller class for Project
 */

class ProjectController extends JController {
	/*
	  Function: Constructor

	  Parameters:

	  $default -
	 */

	function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('apply', 'save');
		$this->registerTask('preview', 'display');
		$this->registerTask('edit', 'display');
		$this->registerTask('add', 'display');
		$this->registerTask('cancel', 'cancel');
	}

	function display() {
		global $mainframe;

		$is_set_backurl = JRequest::getVar('backurl', '', 'get');
		if ($is_set_backurl)
			$mainframe->setUserState('com_teamtimeformals_backurl', 1);
		else
			$mainframe->setUserState('com_teamtimeformals_backurl', 0);

		switch ($this->getTask()) {
			case 'add':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'project');
				JRequest::setVar('edit', false);
				JRequest::setVar('report', false);
				break;
			case 'edit':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'project');
				JRequest::setVar('edit', true);
				JRequest::setVar('report', false);
				break;
			case 'report':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'project');
				JRequest::setVar('edit', false);
				JRequest::setVar('report', true);
				break;
		}

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'projects');
		}

		$cid = JRequest::getVar('cid', array(0), 'get', 'array');
		JRequest::setVar('users', $this->getUsersIds($cid[0]));

		parent::display();
	}

	function get_redirect_url($default_url) {
		global $mainframe;

		$is_set_backurl = $mainframe->getUserState('com_teamtimeformals_backurl');

		//var_dump($is_set_backurl);
		//exit();

		if ($is_set_backurl)
			$url = TeamTime::_("formalsdata_get_variables_url", "");
		else
			$url = $default_url;

		$mainframe->setUserState('com_teamtimeformals_backurl', 0);

		return $url;
	}

	function setState() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('state_change_id', array(), 'post', 'array');

		$id = (isset($cid[0])) ? (int) $cid[0] : null;
		$state = JRequest::getVar('state' . $id, 0);

		$model = $this->getModel();

		if ($model->storeState($id, $state)) {
			$msg = JText::_('State Changed');
		}
		else {
			$msg = JText::_('Error Changing State');
		}

		$link = 'index.php?option=' . $option . '&controller=' . $this->getName();
		$this->setRedirect($link, $msg);
	}

	function saveUsersIds($project_id, $user_ids) {
		$db = & JFactory::getDBO();

		$db->Execute("delete from #__teamlog_project_user
			where project_id = " . (int) $project_id);

		if (sizeof($user_ids) > 0 && $user_ids[0] != 0) {
			foreach ($user_ids as $id) {
				$db->Execute("insert into #__teamlog_project_user
					values(" . (int) $project_id . ", " . (int) $id . ")");
			}
		}
	}

	function getUsersIds($project_id) {
		$db = & JFactory::getDBO();

		$db->setQuery("select * from #__teamlog_project_user
			where project_id = " . (int) $project_id);

		$res = array();
		foreach ($db->loadObjectList() as $row) {
			$res[] = $row->user_id;
		}
		return $res;
	}

	function save() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		$post['description'] = $_REQUEST["description"];
		$user_ids = JRequest::getVar('users', array(0), 'post', 'array');

		$model = $this->getModel();

		$msg = "";
		if ($this->checkPost($post)) {
			if ($model->store($post)) {
				$this->saveUsersIds($model->_data->id, $user_ids);
				TeamTime::_("save_project_params", $model, $post);

				$msg = JText::_('Project Saved');
			}
			else {
				$msg = JText::_('Error Saving Project');
			}
		}

		switch ($this->_task) {
			case 'apply':
				$item = & $model->getData();
				$link = 'index.php?option=' . $option . '&controller=' . $this->getName() .
					'&view=type&task=edit&cid[]=' . $item->id;
				break;
			case 'save':
			default:
				$link = 'index.php?option=' . $option . '&controller=' . $this->getName();
				break;
		}

		$this->setRedirect($this->get_redirect_url($link), $msg);
	}

	function checkPost($post) {
		if (!isset($post['name']) || $post['name'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
			return false;
		}
		return true;
	}

	function cancel() {
		global $option;

		$link = $this->get_redirect_url("");
		if ($link == "")
			$link = 'index.php?option=' . $option . '&controller=' . $this->getName();

		$this->setRedirect($this->get_redirect_url($link));
	}

	function remove() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select an item to delete'));
		}

		$model = $this->getModel();

		if (!$model->delete($cid)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$msg = JText::_('Project Deleted');
		$link = 'index.php?option=' . $option . '&controller=' . $this->getName();

		$this->setRedirect($link, $msg);
	}

}