<?php

class Shell_Args_Logger {
	public static function setup() {
		Shell_Args::bool('debug', 'DEBUG', 'Log debug', array('Shell_Args_Logger', 'debug'));
		Shell_Args::string('log', 'LOG', 'Log to file', false, null, null, array('Shell_Args_Logger', 'toFile'));
	}

	public static function debug($value) {
		if($value) {
			Log_Logger::get()->logToConsole(LOG_DEBUG);
		} else {
			Log_Logger::get()->logToConsole(LOG_INFO);
		}
	}

	public static function toFile($value) {
		if($value !== false) {
			Log_Logger::get()->logToFile($value, LOG_DEBUG);
		}
	}
}

?>
