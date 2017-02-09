<?php

class Util_Define {
	public static function defineDefault($name, $default) {
		if(!defined($name)) {
			define($name, $default);
		}
	}
}

?>
