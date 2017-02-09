<?php

class Shell_Args {
	private static $args = array();
	private static $description = '';

	private static function addArgs($type, $name, $longName, $description, $default, $callback, $pattern = null, $pattern_desc = null) {
		if(is_null($callback)) {
			$callback = array(new Shell_Args_Define($longName) ,'define');
		}
		$arg = array();
		$arg['type'] = $type;
		$arg['longName'] = $longName;
		$arg['description'] = $description;
		$arg['default'] = $default;
		$arg['callback'] = $callback;
		$arg['pattern'] = $pattern;
		$arg['pattern_desc'] = $pattern_desc;
		Shell_Args::$args[$name] = $arg;
	}
	
	public static function int($name, $longName, $description, $default = null, $callback = null) {
		Shell_Args::addArgs('text', $name, $longName, $description, $default, $callback, '/^\d+$/', "is not an Integer");
	}

	public static function string($name, $longName, $description, $default = null, $pattern = null, $pattern_desc = null, $callback = null) {
		Shell_Args::addArgs('text', $name, $longName, $description, $default, $callback, $pattern, $pattern_desc);
	}

	public static function bool($name, $longName, $description, $callback = null) {
		Shell_Args::addArgs('bool', $name, $longName, $description, false, $callback);
	}

	public static function done($description = null) {
		Shell_Args::$description = is_null($description) ? '' : "\n---------------------------------------------------------------------\n " . str_replace("\n", "\n ", $description);
		Shell_Args::bool('help', 'HELP', 'Print this help page', array('Shell_Args', 'help'));
		Shell_Args::handleArgs();
	}

	private static function handleArgs() {
		$shortArgs = array();
		$longArgs = array();
		foreach(Shell_Args::$args as $name => $arg) {
			$value = $arg['type'] == 'text' ? $name.':' : $name;
			if(strlen($name) == 1) {
				$shortArgs[] = $value;
			} else {
				$longArgs[] = $value;
			}
		}
		$opt = getopt(implode($shortArgs), $longArgs);
		foreach(Shell_Args::$args as $name => $arg) {
			$value = $arg['default'];
			if(array_key_exists($name, $opt)) {
				$value = $arg['type'] == 'bool' ? true : $opt[$name];
			}
			if(!is_null($arg['pattern'])) {
				if(!is_null($value) && is_null($arg['pattern']) || preg_match($arg['pattern'], $value) == 1) {
					call_user_func($arg['callback'], $value);
				} else {
					$error = strlen($name) == 1 ? "-${name}" : "--${name}";
					$error .= is_null($arg['pattern_desc']) ? " don't matches pattern " . $arg['pattern'] : ' ' . $arg['pattern_desc'];
					Shell_Args::usage($error);
				}
			} else {
				if(!is_null($value)) {
					call_user_func($arg['callback'], $value);
				} else {
					$error = "Missing argument: ";
					$error .= strlen($name) == 1 ? "-${name}" : "--${name}";
					Shell_Args::usage($error);
				}
			}
		}
	}

	public static function usage($error = null) {
		echo "Usage: php " . $GLOBALS['argv'][0] . " [OPTIONS]";
		foreach(Shell_Args::$args as $name => $arg) {
			if(is_null($arg['default'])) {
				printf(" %s {%s}", (strlen($name) == 1 ? "-${name}" : "--${name}"), $arg['longName']);
			}
		}
		if(!is_null($error)) echo "\n---------------------------------------------------------------------\n $error";
		echo Shell_Args::$description . "\n";
		$printHeader = true;
		foreach(Shell_Args::$args as $name => $arg) {
			if(is_null($arg['default'])) {
				if($printHeader) {
					echo "\nMandatory arguments\n";
					$printHeader = false;
				}
				Shell_Args::printUsageLine($name, $arg);
			}
		}
		$printHeader = true;
		foreach(Shell_Args::$args as $name => $arg) {
			if(!is_null($arg['default'])) {
				if($printHeader) {
					echo "\nOptional arguments\n";
					$printHeader = false;
				}
				Shell_Args::printUsageLine($name, $arg);
			}
		}
		exit();
	}

	private static function help($print) {
		if(!$print) return;
		Shell_Args::usage();
	}
	
	private static function printUsageLine($name, $arg) {
		$oName = strlen($name) == 1 ? "-${name}" : "--${name}";
		$default = (!is_null($arg['default']) && $arg['default'] !== false) ? sprintf(" ( default: %s )", $arg['default']) : '';
		printf("%10s : %s%s\n", $oName, $arg['description'], $default);
	}
}

?>
