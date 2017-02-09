<?php

Util_Define::defineDefault("LOG_TRACE", 8);

class Log_Logger {
	private static $instance = null;
	private $loggers = array();

	public static function get() {
		if(is_null(Log_Logger::$instance)) {
			Log_Logger::$instance = new Log_Logger();
		}
		return Log_Logger::$instance;
	}

	public function addLogger($logger) {
		$this->loggers[] = $logger;
	}

	public function logToConsole($level = LOG_INFO) {
		$console = new Log_Writer_Console();
		$console->setLogLevel($level);
		$this->addLogger($console);
	}

	public function logToFile($file, $level = LOG_INFO) {
		$file = new Log_Writer_File($file);
		$file->setLogLevel($level);
		$this->addLogger($file);
	}

	public function emerg() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_EMERG, $message);
	}

	public function alert() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_ALERT, $message);
	}

	public function crit() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_CRIT, $message);
	}

	public function error() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_ERR, $message);
	}

	public function warn() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_WARNING, $message);
	}

	public function notice() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_NOTICE, $message);
	}

	public function info() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_INFO, $message);
	}

	public function debug() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_DEBUG, $message);
	}

	public function trace() {
		$message = call_user_func_array('sprintf', func_get_args());
		$this->log(LOG_TRACE, $message);
	}

	public function isDebug() {
		return $this->isLevel(LOG_DEBUG);
	}
	
	public function isTrace() {
		return $this->isLevel(LOG_TRACE);
	}
	
	public function isLevel($level) {
		foreach($this->loggers as $logger) {
			if($logger->isLevel($level)) {
				return true;
			}
		}
		return false;
	}

	protected function log($level, $message) {
		foreach($this->loggers as $logger) {
			$logger->log($level, $message);
		}
	}
}

?>
