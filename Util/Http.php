<?php

public class Util_Http {

	public static function GET($url, $data, $userHeader = array()) {
		return Util_Http::send($url, $data, "GET", $userHeader);
	}

	public static function POST($url, $data, $userHeader = array()) {
		return Util_Http::send($url, $data, "POST", $userHeader);
	}

	public static function PUT($url, $data, $userHeader = array()) {
		return Util_Http::send($url, $data, "PUT", $userHeader);
	}

	public static function DELETE($url, $data, $userHeader = array()) {
		return Util_Http::send($url, $data, "DELETE", $userHeader);
	}

	public static function custom($method, $url, $data, $userheader = array()) {
		return Util_Http::send($url, $data, $method, $userHeader);
	}

	private static function buildData($data) {
		if(is_array($data)) {
			$tmp = array();
			foreach($data as $k => $v) {
				$tmp[] = sprintf("%s=%s", urlencode($k), urlencode($v)); 
			}
			return implode("&", $tmp);
		}
		return $data;
	}

	private static function buildBody($method, $path, $header, $data) {
		$body = sprintf("%s %s Util_Http/1.1\r\n", $method, $path);
		foreach($header as $k => $v) {
			if(trim($k) != "") {
				$out .= sprintf("%s: %s\r\n", $k, $v);
			}
		}
		$out .= "\r\n";
		$out .= $data . "\r\n";
	}

	private static function send($url, $data, $method = "GET", $userHeader = array()) {
		$info = parse_url($url);

		$header = array();
		$header['accept'] = "*/*";

		if(strtoupper($method) == "POST") {
			$header['content-type'] = "application/x-www-form-urlencoded";
		}

		$port = array_key_exists("port", $info) ? $info['port'] : 80;
		$path = $info['path'];
		if(array_key_exists("query", $info)) {
			$path .= "?" . $info['query'];
		}
		if(array_key_exists("fragment", $info)) {
			$path .= "#" . $info['fragment'];
		}
		if(array_key_exists("user", $info) && array_key_exists("pass", $info)) {
			$header['authorization'] = 'Basic ' . base64_encode($info['user'] . ':' . $info['pass']);
		}
		foreach($userHeader as $k => $v) {
			$header[strtolower($k)] = $v;
		}

		$data = Util_Http::buildData($data);
		$header["content-length"] = strlen($data);
		$header["connection"] = "Close";
		$header["host"] = $info['host'];
		$body = Util_Http::buildBody($method, $path, $header, $data);

		//	print_r($body);
		$fp = fsockopen($info['host'], $port, $errno, $errstr, 30);
		if (!$fp) die("Error $errno: $errstr\n");
		fwrite($fp, $body);
		list($tmp, $Util_HttpCode, $Util_HttpText) = explode(" ", (fgets($fp, 4096)), 3); 
		$result = array("Util_Http_code" => trim($Util_HttpCode), "Util_Http_code_text" => trim($Util_HttpText), "header" => array());
		while (($buffer = fgets($fp, 4096)) !== false) {
			if(trim($buffer) != "") {
				list($k, $v) = explode(":", $buffer, 2); 
				$result['header'][$k] = trim($v);
			} else {
				break;
			}
		}

		$result["content"] = stream_get_contents($fp);
		fclose($fp);
		return $result;
	}
}
