<?php

class Log_Writer_File extends Log_Writer {
	protected $file;

	public function __construct($file) {
		$this->file = $file;
		$this->format = "%date% %time% [ %level% ]: %msg%\n";
	}

	public function write($level, $message) {
		file_put_contents($this->file, $message, FILE_APPEND);
	}
}

?>
