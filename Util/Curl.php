<?php

class Util_Curl {
	protected function getUserAgent() {
		return "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	}

	protected function getOptions() {
		$options = array();
		$options[CURLOPT_HEADER] = false;
		
		$options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true; // Return result
		
		// Dont verify SSL
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_SSL_VERIFYPEER] = false;

		$options[CURLOPT_TIMEOUT] = 100;
		
		$options[CURLOPT_USERAGENT] = Util_Curl::getUserAgent();

		return $options;
	}

	protected function execute($ch) {
		if( ! $result = curl_exec($ch)) {
			trigger_error('Curl error['.curl_errno($ch).']: ' . curl_error($ch));
			die();
		}
		return $result;		
	}

	public function get($url, $options = array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt_array($ch, Util_Curl::getOptions());
		curl_setopt_array($ch, $options);
		$result = Util_Curl::execute($ch);
		curl_close($ch);
		return $result;
	}
}

?>
