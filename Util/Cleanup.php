<?php

Util_Define::defineDefine("TMP_DIR", "/var/tmp");

class Util_Cleanup {
	private static $path = null;

	public static function add($path) {
		$log = Log_Logger::get();
		if(is_null(Util_Cleanup::$path)) {
			register_shutdown_function(array("Util_Cleanup", "cleanup"));
			Util_Cleanup::$path = TMP_DIR . "/" . __CLASS__;
			if(!is_dir(Util_Cleanup::$path)) {
				mkdir(Util_Cleanup::$path, 0777, true);
				chmod(Util_Cleanup::$path, 0777); // Is seames that mkdir dont set the correct premision, so we need to do this ourself
			}
		}

		$log->debug("Adding cleanup path '$path'");
		file_put_contents(Util_Cleanup::getFile(getmypid()), "$path\n", FILE_APPEND | LOCK_EX);
		chmod(Util_Cleanup::getFile(getmypid()), 0600); // Only the owner of this process can change this file, this prevents error when trying to cleanup other processes files
	}

	public static function cleanup() {
		Util_Cleanup::cleanupPid(Util_Cleanup::getFile(getmypid()));
		$files = scandir(Util_Cleanup::$path);
		foreach($files as $file) {
			$fullFile = Util_File::mergePaths(Util_Cleanup::$path, $file);
			if(is_file($fullFile) && is_readable($fullFile)) { // This if the file still exists and that we ownes the file (can delete the files)
				$pid = basename($fullFile, ".list");
				if(! file_exists(sprintf("/proc/%s", $pid))) { // Check if the process is still running
					Util_Cleanup::cleanupPid($fullFile);
				}
			}
		}
	}

	private static function getFile($pid) {
		return sprintf("%s/%s.list", Util_Cleanup::$path, $pid);
	}

	private static function cleanupPid($file) {
		$log = Log_Logger::get();
		$pid = basename($file, ".list");
		if(file_exists($file)) {
			if($fp = fopen($file, "r")) {
				if(flock($fp, LOCK_EX | LOCK_NB)) {
					$log->debug("Running cleanup for " . ($pid == getmypid() ? "current process" : "pid " . $pid));
					while (($path = fgets($fp, 4096)) !== false) {
						$path = trim($path);
						if(file_exists($path)) {
							$log->debug("Deleting $path");
							Util_File::rm($path);
						}
					}
					flock($fp, LOCK_UN);
					Util_File::rm($file);
				}
				fclose($fp);
			}
		}
	}
}

?>
