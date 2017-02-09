<?php

class Util_Loader {
	public static function register() {
		return spl_autoload_register(array(__CLASS__, 'includeClass'));
	}

	public static function unregister() {
		return spl_autoload_unregister(array(__CLASS__, 'includeClass'));
	}
	
	public static function includeClass($classname) {
		$file = sprintf("%s.php", str_replace("_", "/", $classname));
		$fh = @fopen($file, 'r', true);
		if(is_resource($fh)) {
			fclose($fh);
			if(!include($file)) {
				Log_Logger::get()->error("Unable to include file: $file");
			}
			if(!class_exists($classname, false) && !interface_exists($classname, false)) {
				Log_Logger::get()->error("Class ($classname) not found in file: $file");
			}
		} else {
			Log_Logger::get()->error("File not found: $file");
		}
	}
}
