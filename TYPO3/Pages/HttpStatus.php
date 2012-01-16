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
		$ch = curl_init ( $url );
		// deactivate debug output
		curl_setopt ( $ch, CURLOPT_NOBODY, TRUE );
		curl_exec ( $ch );
		$http = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		curl_close ( $ch );
		return $http;
	}
}