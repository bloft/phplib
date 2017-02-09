<?php

class Shell_ObjectWithShell {
	protected $shell;

	public function __construct($shell = null) {
		if(is_null($shell)) {
			$shell = new Shell_Localhost();
		}
		$this->shell = $shell;
	}

	public function setShell($shell) {
		$this->shell = $shell;
	}
}

?>
