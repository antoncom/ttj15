<?php

class TeamTime_Cronjob {

	private $logName;

	public function __construct($name, $webAllowed = false) {
		define("CRONJOB_NAME", $name);

		$this->logName = JPATH_ROOT .
				"/tmp/log_" . self::getName() . "_" . date("Y-m-d_H-i");

		error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
		ini_set("error_log", $this->logName);
		ini_set("log_errors", "on");

		set_time_limit(0);

		if (!$webAllowed) {
			if (!$this->isCli()) {
				throw new Exception("Start cronjob from command line");
			}
		}
	}

	public static function getName() {
		return CRONJOB_NAME;
	}

	private static function getFile() {
		return JPATH_ROOT . "/tmp/" . self::getName();
	}

	public static function shutdown() {
		$fname = self::getFile();
		if (file_exists($fname)) {
			unlink($fname);
		}
	}

	protected function isWork() {
		return file_exists(self::getFile());
	}

	private function isCli() {
		return (0 == strncasecmp(PHP_SAPI, 'cli', 3));
	}

	public function run() {
		
	}

	public function start() {
		if (!$this->isWork()) {
			register_shutdown_function(self::getName() . "::shutdown");
			file_put_contents(self::getFile(), "1");

			$this->clearOldLogs();
			$this->run();
		}
	}

	private function clearOldLogs() {
		$dateInterval = 7 * 24 * 60 * 60;

		foreach (glob(JPATH_ROOT .
				"/tmp/log_" . self::getName() . "_") as $oldName) {
			// clear by date
			if (filemtime($oldName) < time() - $dateInterval) {
				if (file_exists($oldName)) {
					unlink($oldName);
				}
			}

			// clear by size
			else if (filesize($oldName) > 1000000000) {
				if (file_exists($oldName)) {
					unlink($oldName);
				}
			}
		}
	}

	private function log($msg) {
		error_log($msg);
	}

}