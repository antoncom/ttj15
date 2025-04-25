<?php

class TeamlogViewReport extends TeamtimeViewReport {

	public function __construct($config = array()) {
		parent::__construct($config);

		$this->client_view = true;

		$token = JRequest::getVar("token");
		$client = JRequest::getVar("client");
		$clientIds = JRequest::getVar("client_ids");

		if ($clientIds) {
			JRequest::setVar("client", explode(",", $clientIds));
		}
		else if ($client != "" && !is_array($client)) {
			$client = TeamTime::helper()->getFormals()
					->getClientId(base64_decode($client), base64_decode($token));
			JRequest::setVar("client", $client);
		}
	}

}