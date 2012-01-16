<?php
/**
 * 
 * operations for httpstatus check
 * @author joerg.winkler <joerg.winkler@aoemedia.de>
 *
 */
class AoeComponents_TYPO3_Pages_HttpStatus extends Menta_Component_AbstractTest {
	
	/**
	 * returns only the http status code
	 * @return int $http
	 */
	public function getHttpStatus($url) {
		$useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
		$options = array (
			CURLOPT_USERAGENT => $useragent, 	// fake a user agent
			CURLOPT_NOBODY => TRUE				// deactivate debug output 
		);
		$ch = curl_init ( $url );
		curl_setopt_array ( $ch, $options );
		if (curl_exec ( $ch ) === false) {
			trigger_error ( curl_error ( $ch ) );
		} else {
			$http = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		}
		curl_close ( $ch );
		return $http;
	}
}