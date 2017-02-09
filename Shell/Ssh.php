<?php

class Shell_Ssh implements Shell_Shell {
	private $log;
	private $host;
	private $port = 22;
	private $user;
	private $identity_file;
	private $proxy = null;
	private $portForward = array();

	private $OPTIONS = '-q -o "UserKnownHostsFile=/dev/null" -o "StrictHostKeyChecking no"';

	public function __construct($host, $user = "root") {
		$this->log = Incident_IncidentManager::instance();
		$this->host = $host;
		$this->user = $user;
		if(defined("SSH_IDENTITY")) {
			$this->identity_file = SSH_IDENTITY;
		}
	}

	public function setProxy($ssh) {
		$this->log->debug("Adding Ssh proxy");
		$oldPort = $this->port;
		$this->port = $this->getLocalPort();
		$ssh->createPortForward($oldPort, $this->port, $this->host);
		$this->proxy = $ssh;
	}

	public function __destruct() {
		$this->killPortForwards();
	}

	public function getHost() {
		return $this->host;
	}

	public function setIdentityFile($file) {
		$this->identity_file = $file;
	}

	private function getIdentityArg() {
		$identity = "";
		if(!is_null($this->identity_file) && file_exists($this->identity_file)) {
			$identity = sprintf('-i "%s"', $this->identity_file);
		}
		return $identity;
	}

	private function buildSshHost() {
		if(is_null($this->proxy)) {
			$host = $this->host;
		} else {
			$host = 'localhost';
		}
		return sprintf('%s@%s', $this->user, $host);
	}

	private function buildSshArgs() {
		return sprintf('%s %s -p %s -t %s', $this->OPTIONS, $this->getIdentityArg(), $this->port, $this->buildSshHost());
	}

	private function buildCommand($cmd) {
		$escape = array('"' => '\"', '$' => '\$');
		return sprintf('ssh %s "%s"', $this->buildSshArgs(), strtr($cmd, $escape));
	}

	public function exec($command, $callback = null) {
		if(is_null($callback)) {
			return $this->execCmd($command);
		} else {
			return $this->execWithCallback($command, $callback);
		}
	}

	public function execCmd($cmd) {
		$cmd = $this->buildCommand($cmd);
		$this->log->debug("Running ssh command: " . $cmd);
		$result = Util_ShellUtil::execCmd($cmd);
		$lastLine = array_pop($result);
		if(!(strlen($lastLine) >= 13 && substr_compare($lastLine, "Connection to", 0, 13, true) == 0)) {
			array_push($result, $lastLine);
		}
		return $result;
	}

	public function execWithCallback($cmd, $callback) {
		$cmd = $this->buildCommand($cmd);
		$this->log->debug("Running ssh command: " . $cmd);
		return Util_ShellUtil::execWithCallback($cmd, $callback);
	}

	public function scp_upload($source, $dest) {
		$cmd = sprintf('scp %s %s -P %s %s %s:%s', $this->OPTIONS, $this->getIdentityArg(), $this->port, $source, $this->buildSshHost(), $dest);
		$this->log->debug("Running scp command: " . $cmd);
		return Util_ShellUtil::execWithCallback($cmd, array($this->log, "info"));
	}
	
	public function scp_download($source, $dest) {
		$cmd = sprintf('scp %s %s -P %s %s:%s %s', $this->OPTIONS, $this->getIdentityArg(), $this->port, $this->buildSshHost(), $source, $dest);
		$this->log->debug("Running scp command: " . $cmd);
		return Util_ShellUtil::execWithCallback($cmd, array($this->log, "debug"));
	}

	public function createPortForward($remote, $local, $host = "localhost") {
		$this->log->debug("Creating port forward");
		$cmd = sprintf('ssh %s -L %s:%s:%s', $this->buildSshArgs(), $local, $host, $remote);
		$data = array();
		$this->log->debug("Running command: $cmd");
		$data['proc'] = proc_open($cmd, array(array("pipe","r"),array("pipe","w"),array("pipe","w")),$data['pipes'], null, $_ENV);
		if(is_resource($data['proc'])) {
			fwrite($data['pipes'][0], 'echo "Bah"'); // Write a command to the host so that there is an output on stdout
			$this->log->debug("Result for ssh host: " . fgets($data['pipes'][1]));
			$this->portForward[] = $data;
		} else {
			$this->log->error("Unable to create port forward!!!");
			die();
		}
	}

	private function killPortForwards() {
		foreach($this->portForward as $forward) {
			$this->log->debug("Killing port forward process");
			foreach($forward['pipes'] as $pipe) {
				fclose($pipe);
			}
			proc_close($forward['proc']);
		}
	}

	protected function getLocalPort() {
		$cmd = "netstat -atn|grep LISTEN | grep 127.0.0.1 | awk '{print $4}' | awk -F ':' '{print $2}'";
		$result = Util_ShellUtil::execCmd($cmd);
		for( $i = 30000; $i < 50000; $i++) {
			if(!in_array($i, $result)) {
				return $i;
			}
		}
	}

	public function __tostring() {
		$res = $this->getHost();
		if(!is_null($this->proxy)) {
			$res .= " through " . $this->proxy;
		}
		return $res;
	}
}

?>
