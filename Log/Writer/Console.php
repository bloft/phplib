<?php

class Log_Writer_Console extends Log_Writer {
	public function __construct() {
		$this->format = "[ %level% ] %msg%\n";
	}

	public function write($level, $message) {
		if($level > LOG_WARNING) {
			fwrite(STDERR, $message);
		} else {
			fwrite(STDOUT, $message);
		}
	}
}

?>
