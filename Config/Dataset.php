<?php

class Config_Dataset {
	private $data;
	private $toLower;

	public function __construct($data, $toLower = false) {
		$this->data = $data;
		$this->toLower = $toLower;
	}

	public function getDataset($key, $default = array()) {
		return new Config_Dataset($this->getArray($key, $default), $this->toLower);
	}

	public function getArray($key, $default = array()) {
		$value = $this->get($key, $default);
		if(is_string($value)) {
			if(trim($value) == "") {
				$value = array();
			} else {
				$value = explode(",", $value);
				foreach($value as $key => $val) {
					$value[$key] = trim($val);
				}
			}
		}
		return $value;
	}

	public function getString($key, $default = null) {
		$value = $this->get($key, $default);
		if(is_array($value)) {
			$value = implode(", ", $value);
		}
		return $value;
	}

	public function getBoolean($key, $default = false) {
		$trueValues = array('yes', 'y', 'true', '1');
		$value = $this->get($key, $default);
		if(is_string($value)) {
			return in_array(strtolower($value), $trueValues);
		} else {
			return (boolean) $value;
		}
	}

	public function set($key, $value) {
		$this->data[($this->toLower ? strtolower($key) : $key)] = $value;
	}

	public function __set($key, $value) {
		$this->set($key, $value);
	}

	public function get($key, $default = null) {
		if($this->contains($key)) {
			return $this->data[($this->toLower ? strtolower($key) : $key)];
		} else {
			return $default;
		}
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function contains($key) {
		return array_key_exists(($this->toLower ? strtolower($key) : $key), $this->data);
	}

	public function __toString() {
		$res = "";
		foreach($this->data as $key => $val) {
			$res .= sprintf("%s = '%s'\n", $key, $val);
		}
		return $res;
	}
}

?>
