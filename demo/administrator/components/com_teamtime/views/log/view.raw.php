<?php

jimport( 'joomla.application.component.view');

class LogViewLog extends JView {

	function display($tpl = null) {
		global $mainframe;

		// get request vars
		$option     = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');
		$project_id = JRequest::getVar('project_id');

		// task select
		$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Task').' -'));
		$project = new Project($project_id);
		if ($project) {
			foreach ($project->getTaskTypeArray() as $typename => $tasks){
				if (count($tasks)) {
					$options[] = JHTML::_('select.option', '', $typename);
					foreach ($tasks as $task) {
						$options[] = JHTML::_('select.option', $task->id, '- '.$task->name);
					}
				}
			}
		}
		$lists['select_task'] = JHTML::_('select.genericlist', $options, 'task_id', 'class="inputbox"', 'value', 'text', '-');

		// set template vars
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		// set layout
		$this->setLayout('tasks');

		parent::display($tpl);
	}

}