<?php


class AoeComponents_Curl extends Menta_Component_Abstract {

	/**
	 * Get http header for get request
	 *
	 * @param string $url
	 * @param array $requestHeader
	 * @param int $status
	 * @return array|false
	 */
	public function getHeader($url, array $requestHeader=array(), &$status=NULL) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (count($requestHeader) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
		}
		$c = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return ($c !== false) ? $this->parseHeaderToArray($c) : false;
	}

	/**
	 * Parse raw response header to array
	 *
	 * @param string $headerString
	 * @return array
	 */
	protected function parseHeaderToArray($headerString) {
		$headerArray = array();
		foreach (explode("\n", $headerString) as $line) {
			if (empty($line)) {
				// html body starts here
				return $headerArray;
			}
			if (strpos($line, ':') === false) { continue; }
			list($key, $value) = explode(':', $line, 2);
			if ($key && $value) {
				$headerArray[trim($key)] = trim($value);
			}
		}
		return $headerArray;
	}

}