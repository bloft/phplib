<?php

abstract class Log_Writer {
	protected $levelText = array(
		LOG_TRACE => 'Trace', 
		LOG_DEBUG => 'Debug', 
		LOG_INFO => 'Info', 
		LOG_NOTICE => 'Notice', 
		LOG_WARNING => 'Warning', 
		LOG_ERR => 'Error',
		LOG_CRIT => 'Critical',
		LOG_ALERT => 'Alert',
		LOG_EMERG => 'Emerg');

	protected $format = '[%level%] %msg%';
	protected $timeFormat = 'H:i:s';
	protected $dateFormat = 'Y-m-d';

	protected $logLevel = LOG_DEBUG;

	public function setFormat($format) {
		$this->format = $format;
	}

	public function setLogLevel($level) {
		$this->logLevel = $level;
	}

	public function getLogLevel() {
		return $this->logLevel;
	}

	public function isLevel($level) {
		return $level <= $this->logLevel;
	}

	public function log($level, $msg) {
		if(!$this->isLevel($level)) {
			return;
		}
		$message = $this->format;
		$message = str_replace('%time%', date($this->timeFormat, time()), $message);
		$message = str_replace('%date%', date($this->dateFormat, time()), $message);
		$message = str_replace('%level%', $this->levelText[$level], $message);
		$message = str_replace('%pid%', getmypid(), $message);
		$message = str_replace('%msg%', $msg, $message);
		$this->write($level, $message);
	}

	protected abstract function write($level, $message);
}

?>
