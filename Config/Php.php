<?php

class Config_Php extends Config_Dataset {
	private $data;

	public function __construct($file) {
		if(!is_readable($file)) {
			throw new Exception("Unable to read configuration file");
		}
		include($file);
		parent::__construct(get_defined_vars());
	}
}


?>
