<?php

class TeamtimeControllerTask extends Core_Joomla_EditController {

	private $typeUsings = array();
	private $taskUsings = array();

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'task';
		$this->viewList = 'tasks';
		$this->acl = new TeamTime_Acl();
	}

	public function setState() {
		if (!$this->isAllowed()) {
			return;
		}

		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('state_change_id', array(), 'post', 'array');

		$id = (isset($cid[0])) ? (int) $cid[0] : null;
		$state = JRequest::getVar('state' . $id, 0);

		$model = $this->getModel($this->viewEdit);

		if ($model->storeState($id, $state)) {
			$msg = JText::_('State Changed');
		}
		else {
			$msg = JText::_('Error Changing State');
		}

		$link = 'index.php?option=' . $option . '&controller=' . $controller;
		$this->setRedirect($link, $msg);
	}

	private function checkTypeUsings() {
		$id = JRequest::getVar('id');
		$newTypeId = JRequest::getVar('type_id');
		$model = new TeamtimeModelTask();
		$task = $model->getById($id);
		$typeId = $task->type_id;
		$taskId = $task->id;

		if ($typeId != $newTypeId) {
			$this->typeUsings = TeamTime::helper()->getBase()->getTypeUsings($typeId, $taskId);
		}
	}

	public function setRedirect($link, $msg) {
		$typeId = JRequest::getVar('type_id');

		if (sizeof($this->typeUsings) > 0) {
			$msg .= "<p>ID:$typeId - " . JText::_('Type Usings') . ":<p>" . implode("<p>", $this->typeUsings);
		}

		if (sizeof($this->taskUsings) > 0) {
			$msg .= "<p>" . JText::_('Task Usings') . ":<p>" . implode("<p>", $this->taskUsings);
		}

		parent::setRedirect($link, $msg);
	}

	public function save() {
		$this->checkTypeUsings();
		parent::save();
	}

	protected function checkPost($post) {
		if (!isset($post['name']) || $post['name'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
			return false;
		}
		if (!isset($post['type_id']) || $post['type_id'] == "" || $post['type_id'] == 0) {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid Type'));
			return false;
		}
		return true;
	}

	private function checkTaskUsings() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		$this->taskUsings = TeamTime::helper()->getBase()->getTaskUsings($cid);
	}

	public function remove() {
		$this->checkTaskUsings();
		parent::remove();
	}

	//
	// ajax actions
	//

	public function loadtasks() {
		$params = JRequest::get('get');

		$project = new Project($params["project_id"]);
		$this->task_type_array = $project->getTaskTypeArray();
		?>
		<?php
		$typeNums = count($this->task_type_array);
		$todo_id = JRequest::getVar('todo_id');

		if (isset($todo_id) && $todo_id != 0) {
			$nTodo = new Todo($todo_id);
			$log_task_id = $nTodo->task_id;
			$log_proj_id = $nTodo->project_id;
			$disabled = "";
		}
		else {
			$disabled = ($log_proj_id > 0 && $log_task_id > 0) ? " disabled style='background-color: #FFDDDD;'"
						: "";
		}
		?>

		<select id="curtaskid" class="task" name="task_id" size="15" <?php echo $disabled; ?>>
			<option disabled class="option1" value ="">-- <?php echo JText::_('Task'); ?> --</option>
			<?php foreach ($this->task_type_array as $typename => $tasks) : ?>
				<option disabled class="option2" value =""><?php echo $typename; ?></option>
				<?php foreach ($tasks as $task) : ?>
					<?php $taskNums = count($tasks); ?>
					<?php
					$selected = (($log_task_id == $task->id) || ($taskNums == 1 && $typeNums == 1)) ? " selected" : "";
					?>
					<option value ="<?php echo $task->id; ?>"<?php echo $selected ?>>- <?php echo $task->name; ?></option>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</select><?
		jexit();
	}

	public function loadpriceinfo() {
		$todo = new Todo();
		$price = $todo->getHourlyRateByParams(JRequest::get('get'));

		print round($price, 2);
		jexit();
	}

}