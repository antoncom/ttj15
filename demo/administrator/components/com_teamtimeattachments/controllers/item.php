<?php

class ComponentnameControllerItem extends Core_EditController {

	function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'item';
		$this->viewList = 'items';
	}

	function ajaxActionExample() {
		// do some job
		//...

		jexit();
	}

}