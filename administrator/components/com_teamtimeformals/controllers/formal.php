<?php

class TeamtimeformalsControllerFormal extends Core_Joomla_EditController {

	public $viewPrint;

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('print', 'display');

		$this->viewEdit = 'formal';
		$this->viewList = 'formals';
		$this->viewPrint = 'formalprint';
		//$this->acl = new TeamTime_Acl();
	}

	public function display() {
		if ($this->getTask() == 'print') {
			JRequest::setVar('view', $this->viewPrint);
		}

		parent::display();
	}

	private function generateDocumentData($post, $returnErrors = false) {
		$mFormal = new TeamtimeformalsModelFormal();
		$date = & JFactory::getDate();
		$post["created"] = $date->toMySQL();

		$format = "%d.%m.%Y";
		$fromPeriod = JHTML::_('date', $post["from_period"], $format);
		$untilPeriod = JHTML::_('date', $post["until_period"], $format);
		$doctype = $mFormal->getDoctype($post["doctype_id"]);
		$post["name"] = JText::_($doctype->name) . " " . sprintf(
						JText::_("FORMAL DOCUMENT TEMPLATENAME"), $fromPeriod, $untilPeriod);

		$res = $mFormal->generateContent($post, $doctype->using_in);
		$post["content"] = $res[0];
		$post["price"] = $res[1];

		if ($returnErrors) {
			$post["todo_errors"] = $res[2];
		}

		return $post;
	}

	public function save() {
		if (!$this->isAllowed()) {
			return;
		}

		$mainframe =& JFactory::getApplication();		

		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');
		$view = JRequest::getVar('view');

		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '?', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '?', 'string');
		$assignementId = $mainframe->getUserStateFromRequest(
				$option . '.filter_assignement', 'project_id', 0, 'int');
		$doctypeId = $mainframe->getUserStateFromRequest(
				$option . '.filter_doctype_id', 'doctype_id', '');

		// check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		$post['content'] = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);

		$model = $this->getModel($this->viewEdit);
		if (!$post["is_edit"]) {
			$post = $this->generateDocumentData($post, true);
		}

		$msg = "";
		if (isset($post["todo_errors"]) && sizeof($post["todo_errors"]) > 0) {
			$msg = JText::_("Errors in todos") . "<p>" .
					JText::_("Type or task not defined in todo") . "<p>" .
					implode("<p>", array_values($post["todo_errors"]));
		}
		else if ($this->checkPost($post)) {
			if ($model->store($post)) {
				$msg = JText::_('Formal Saved');
			}
			else {
				$msg = JText::_('Error Saving Formal');
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

	//
	// ajax actions
	//

	public function load_assignment() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$mFormal = new TeamtimeformalsModelFormal();
		
		$assignementId = $mainframe->getUserStateFromRequest($option . '.filter_assignement',
				'project_id', 0, 'int');
		$post = JRequest::get('post');

		$doctype = $mFormal->getDoctype($post["doctype_id"]);
		$usingIn = $doctype->using_in;

		$options = array(
			JHTML::_('select.option', '- ' . JText::_('Using in') . ' -', '', 'text', 'value')
		);
		print JHTML::_('teamtimeformals.assignment_filter', $options, 'project_id', 'class="inputbox"',
						'value', 'text', $assignementId, $usingIn);
		jexit();
	}

	public function load_using_in() {
		$post = JRequest::get('post');

		if ($post["using_in"] == 0) {
			$options = array(
				JHTML::_('select.option', '', '- ' . JText::_('Nowhere') . ' -'),
				JHTML::_('select.option', '0', '- ' . JText::_('All Projects') . ' -')
			);
			print '<label for="projects">' . JText::_("Used in project data") . '</label>:<br>' .
					JHTML::_('teamtime.projectlist', $options, 'projects[]', 'size="10" multiple class="inputbox"',
							'value', 'text', $project_ids) .
					'<input type="hidden" value="" name="users[]">';
		}
		else {
			$options = array(
				JHTML::_('select.option', '', '- ' . JText::_('No one') . ' -'),
				JHTML::_('select.option', '0', '- ' . JText::_('All Users') . ' -')
			);
			print '<label for="users">' . JText::_("Used in user data") . '</label>:<br>' .
					JHTML::_('teamtime.userlist', $options, 'users[]', 'size="10" multiple class="inputbox"',
							'value', 'text', $user_ids) .
					'<input type="hidden" value="" name="projects[]">';
		}

		jexit();
	}

	public function can_notify_client() {
		$projectId = JRequest::getVar('project_id');

		if ($projectId) {
			$mVariable = new VariableModelVariable();
			$disabled = !$mVariable->canNotifyClientByEmail($projectId);
		}
		else {
			$disabled = true;
		}

		print $disabled ? "0" : "1";

		jexit();
	}

	public function load_templates() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$name = "formals";

		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project', 'filter_project', '', 'cmd');

		$filterUsing = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_using', 'filter_using', '', 'cmd');

		if (JRequest::getVar('byname')) {
			$doctypeModel = new DoctypeModelDoctype();
			$filterUsing = $doctypeModel->getUsingType($filterUsing);
		}

		$model = new TemplateModelTemplate();
		$rows = $model->getTemplatesByUsing($filterUsing);

		$options = array();
		$options[] = "<option value=''>- " . JText::_('Select Template Type') . " -</option>";
		foreach ($rows as $row) {
			$options[] = "<option value='{$row->id}' $selected>{$row->name}</option>";
		}

		print implode("\n", $options);

		jexit();
	}

}