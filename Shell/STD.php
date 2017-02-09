<?php

/**
  * Using the STDOUT, STDERR and STDIN to handle user input and output
  */
class Shell_STD {

	/**
	  * Read a Yes/No reply from the user
	  */
	public static function yesNo($text, $default = false) {
		Shell_STD::write($text, false);
		Shell_STD::write($default ? " [Y/n]" : " [y/N]", false);
		Shell_STD::write(": ", false);
		switch(strtoupper(Shell_STD::read())) {
			case "YES":
			case "Y":
				return true;
				break;
			case "NO":
			case "N":
				return false;
				break;
			case "":
				return $default;
				break;
			default:
				Shell_STD::error("Unknown responce !!!");
				return Shell_STD::yesNo($text, $default);
				break;
		}
	}

	public static function input($text, $default = null) {
		Shell_STD::write($text, false);
		if(!is_null($default)) {
			Shell_STD::write(" [${default}]", false);
		}
		Shell_STD::write(": ", false);
		$input = Shell_STD::read();
		return is_null($default) ? $input : ($input == "" ? $default : $input);
	}

	public static function read() {
		return chop(fgets(STDIN));
	}

	public static function write($text, $includeNewLine = true) {
		fwrite(STDOUT, $text . ($includeNewLine ? "\n" : ""));
	}

	public static function error($text, $includeNewLine = true) {
		fwrite(STDERR, $text . ($includeNewLine ? "\n" : ""));
	}
}

?>
