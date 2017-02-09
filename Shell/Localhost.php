<?php

class Shell_Localhost implements Shell_Shell {

	public function exec($command, $callback = null) {
		if(is_null($callback)) {
			return $this->execCmd($command);
		} else {
			return $this->execWithCallback($command, $callback);
		}
	}

	/**
	 * @param string the shell command (the command will not be escaped)
	 * @return array output from command
	 */
	public function execCmd($command)
	{
		$return_var;
		$output = array();
		exec($command." 2>&1", $output, $return_var);

		if($return_var != 0){
			$message = "Failed to execute command '$command': \n".implode("\n", $output);
			throw new Exception($message);
		}
		return $output;
	}

	/**
	 * @param string the shell command (the command will not be escaped)
	 * @param string The function to be called. Class methods may also be invoked statically using this function by passing array($classname, $methodname) to this parameter. Additionally class methods of an object instance may be called by passing array($objectinstance, $methodname) to this parameter.
	 * @return int termination status of the process that was run. In case of an error then -1 is returned.
	 */
	public function execWithCallback($command, $callback) {
		$fp=popen($command." 2>&1","r");
		while (!feof($fp)) {
			$line = substr(fgets($fp, 4096), 0, -1);
			call_user_func($callback, $line);
		}
		return pclose($fp);
	}

}

?>
