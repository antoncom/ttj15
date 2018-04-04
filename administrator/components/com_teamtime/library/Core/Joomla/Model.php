<?php

class Core_Joomla_Model extends JTable {

	/**
	 * @var array validate errors
	 */
	protected $errors;

	/**
	 * @return boolean validate function
	 */
	public function check() {
		return true;
	}

	/**
	 * @param string $fieldName
	 * @return mixed validate errors
	 */
	public function getErrors($fieldName = null) {
		if ($fieldName == null) {
			return $this->errors;
		}
		else {
			return isset($this->errors[$fieldName]) ?
					$this->errors[$fieldName] : "";
		}
	}

}