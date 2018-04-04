<?php

class Core_Joomla_Controller extends JController {
	
	public $acl = null;

	protected function toJson($result) {
		header('Content-type:text/javascript;charset=UTF-8');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: " . date("r"));

		print json_encode($result);

		jexit();
	}

}