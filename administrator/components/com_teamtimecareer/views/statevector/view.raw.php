<?php

class TeamtimecareerViewStatevector extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		// get request vars		
		$controller = JRequest::getWord('controller');

		$userId = JRequest::getVar('user_id');
		$targetId = JRequest::getVar('target_id');

		$model = new TeamtimecareerModelTargetvector();
		$targetBalance = $model->getTargetBalance($userId);
		$skillItems = $model->getSkills($targetId);

		$modelState = new TeamtimecareerModelStatevector();
		foreach ($skillItems as $i => $row) {
			$skillItems[$i]->state = $modelState->getStateVectorValue($row->id, $userId, true);
			if ($skillItems[$i]->state === null) {
				$skillItems[$i]->state = 0;
			}
		}

		$stateModel = new TeamtimecareerModelStatevector();
		$markedSkills = $stateModel->getMarkedSkills($targetId, $userId);

		$this->assignRef('skillItems', $skillItems);
		$this->assignRef('targetBalance', $targetBalance);
		$this->assignRef('markedSkills', $markedSkills);

		parent::display("skills");
	}

}