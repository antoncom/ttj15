<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class TemplateController extends JController {
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
	}

	function display() {
		switch ($this->getTask()) {
			case 'add':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'template');
				JRequest::setVar('edit', false);
				JRequest::setVar('report', false);
				break;
			case 'edit':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'template');
				JRequest::setVar('edit', true);
				JRequest::setVar('report', false);
				break;
			case 'report':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'template');
				JRequest::setVar('edit', false);
				JRequest::setVar('report', true);
				break;
		}

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'templates');
		}

		$cid = JRequest::getVar('cid', array(0), 'get', 'array');
		//JRequest::setVar('projects', $this->getProjectIds($cid[0]));

		parent::display();
	}

	/* function setState(){
	  global $option;

	  // Check for request forgeries
	  JRequest::checkToken() or die('Invalid Token');

	  $cid  = JRequest::getVar('state_change_id', array(), 'post', 'array');

	  $id = (isset($cid[0]))?(int) $cid[0]:null;
	  $state  = JRequest::getVar('state' . $id, 0);

	  $model = $this->getModel();

	  if ($model->storeState($id, $state)) {
	  $msg = JText::_('State Changed');
	  } else {
	  $msg = JText::_('Error Changing State');
	  }

	  $link = 'index.php?option='.$option.'&controller='.$this->getName();
	  $this->setRedirect($link, $msg);
	  } */

	/* function saveProjectIds($template_id, $project_ids){
	  $db =& JFactory::getDBO();

	  $db->Execute("delete from #__teamtimeformals_template_project
	  where template_id = ".(int)$template_id);

	  if(sizeof($project_ids) > 0 && $project_ids[0] !== "") {
	  foreach($project_ids as $id){
	  $db->Execute("insert into #__teamtimeformals_template_project
	  values(".(int)$id.", ".(int)$template_id.")");
	  }
	  }
	  }

	  function getProjectIds($template_id){
	  $db =& JFactory::getDBO();

	  $db->setQuery("select * from #__teamtimeformals_template_project
	  where template_id = ".(int)$template_id);
	  $res = array();
	  foreach($db->loadObjectList() as $row){
	  $res[]=$row->project_id;
	  }
	  return $res;
	  } */

	function save() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		$post['description'] = $_REQUEST["description"];
		//$project_ids = JRequest::getVar('projects', array(0), 'post', 'array');

		$model = $this->getModel();

		$msg = "";
		if ($this->checkPost($post)) {
			if ($model->store($post)) {
				//$this->saveProjectIds($model->_data->id, $project_ids);

				$msg = JText::_('Template Saved');
			}
			else {
				$msg = JText::_('Error Saving Template');
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

		$msg = JText::_('Template Deleted');
		$link = 'index.php?option=' . $option . '&controller=' . $this->getName();

		$this->setRedirect($link, $msg);
	}

	function loadTemplates() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$name = "templates";

		$filter_project = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project', 'filter_project', '', 'cmd');
		
		$filter_using = $mainframe->getUserStateFromRequest(
				$option . '.filter_using', 'filter_using', '', 'cmd');

		if (JRequest::getVar('byname')) {
			$doctypeModel = new DoctypeModelDoctype();
			$filter_using = $doctypeModel->getUsingType($filter_using);
		}

		$model = new TemplateModelTemplate();
		$rows = $model->getTemplatesByUsing($filter_using);

		$options = array();
		$options[] = "<option value=''>- " . JText::_('Select Template Type') . " -</option>";
		foreach ($rows as $row) {
			$options[] = "<option value='{$row->type}' $selected>{$row->name}</option>";
		}

		print implode("\n", $options);

		jexit();
	}

}