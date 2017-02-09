<?php

class Shell_Args_Logger {
	public static function setup() {
		Shell_Args::bool('debug', 'DEBUG', 'Log debug', array('Shell_Args_Logger', 'debug'));
	}

	public function debug() {
		Log_Logger::get()->logToConsole(LOG_DEBUG);
	}
}

?>
