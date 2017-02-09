<?php

class Shell_Args_Define {
	private $name = '';

	public function __construct($name) {
		$this->name = $name;
	}

	public function define($value) {
		if(is_null($value)) {
			Shell_Args::usage();
		} else {
			define($this->name, $value);
		}
	}
}

?>
