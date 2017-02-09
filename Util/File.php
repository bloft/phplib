<?php

Util_Define::defineDefine("TMP_DIR", "/var/tmp");

class Util_File {
	public static function mergePaths() {
		$paths = func_get_args();
		$res = array_shift($paths);
		foreach($paths as $path) {
			$res .= substr($res, -1) != '/' ? '/' : '';
			$res .= substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
		}
		return $res;
	}

	public static function createTmpDir($name) {
		$tmpDir = tempnam(TMP_DIR, $name."_");
		Util_Cleanup::add($tmpDir);
		unlink($tmpDir);
		mkdir($tmpDir, 0777, true);
		return $tmpDir;
	}

	public static function createTmpFile($name) {
		$tmpFile = tempnam(TMP_DIR, $name."_");
		Util_Cleanup::add($tmpFile);
		return $tmpFile;
	}

	public static function lock($file) {
		$log = Log_Logger::get();
		if(($fp = @fopen($file, "x")) !== false)	{
			$log->debug("Lock file $file acquired");
			fwrite($fp, serialize(array(time(), posix_getpid())));
			fclose($fp);
			Util_Cleanup::add($file);
		} else {
			$message="A lock file: $file already exist - aborting..";
			$log->warning($message);
			throw new Exception($message);
		}
	}

	public static function rm($file) {
		if(file_exists($file)) {
			if(!is_link($file) && is_dir($file)) {
				$files = scandir($file);
				foreach($files as $f) {
					if($f != '.' && $f != '..') {
						Util_File::rm(Util_File::mergePaths($file, $f));
					}
				}
				rmdir($file);
			} else {
				unlink($file);
			}
		}
	}
}

?>
