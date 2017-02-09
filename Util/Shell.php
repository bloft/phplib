<?php

class Util_Shell {
	public static function exec($command, $callback) {
		$fp=popen($command." 2>&1","r");
		while (($line = fgets($fp, 4096)) !== false) {
			call_user_func($callback, substr($line, 0, -1));
		}
		return pclose($fp);
	}
}

?>
