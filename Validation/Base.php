<?php

/**
 * Base class for validators
 *
 * @author Neil Crosby <neil@neilcrosby.com>
 * @license Creative Commons Attribution-Share Alike 3.0 Unported http://creativecommons.org/licenses/by-sa/3.0/
 *
 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
 * Refactoring and integration into TestingFramework. Usage as page objects
 */
abstract class AoeComponents_Validation_Base extends Menta_Component_Abstract {
    
    const FILE_NOT_FOUND = -2;
    const NO_VALIDATOR_RESPONSE = -1;
    const NO_ERROR = false;

    const FILE_IDENTIFIER = 'file://';
    const HTTP_IDENTIFIER = 'http://';
    
    protected $errorPointer = array('envBody', 'mmarkupvalidationresponse', 'mresult', 'merrors');

	protected $validationUrl;

	protected $errorMessageFilter = array();

	/**
	 * Is valid
	 *
	 * @abstract
	 * @param $input
	 * @param array $aOptions
	 * @return void
	 */
    abstract public function isValid($input, $aOptions = array());


    protected function getCurlResponse( $url, $aOptions = array() ) {

        $session = curl_init();
        curl_setopt($session, CURLOPT_URL, $url);
        
        $showHeader = (isset($aOptions['headers']) && $aOptions['headers'] ) ? true : false;
        
        curl_setopt($session, CURLOPT_HEADER, $showHeader);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        
        if (isset($aOptions['post'])) {
            curl_setopt($session, CURLOPT_POST, 1);
            curl_setopt($session, CURLOPT_POSTFIELDS, $aOptions['post']);
        }
        
        $w3c_web_services = array(
            'http://validator.w3.org/check',
            'http://jigsaw.w3.org/css-validator/validator'
        );

		/*
        if (in_array($url, $w3c_web_services) ) {
            error_log("\nUsing W3C web service ${url} so waiting for 2 seconds. Consider specifying a non-W3C installation of the web service.\n");
            sleep(2);
        }
		*/

        $result = curl_exec($session);
        curl_close($session);
        return $result;
    }

	/**
	 * Get SimpleXMLElement
	 *
	 * @param string $xmlString
	 * @return SimpleXMLElement
	 */
    protected function getSanitisedSimpleXml($xmlString) {
        // turns pesky colon namespaced element anems into simple ones, just
        // by getting rid of the colons
        $result = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xmlString);
		// $result = $xmlString;
        return simplexml_load_string($result);
    }

	/**
	 * Get errors
	 *
	 * @return array|bool|int
	 */
    public function getErrors() {
        if (!isset($this->lastResult) || !$this->lastResult) {
            return self::NO_VALIDATOR_RESPONSE;
        }
        
        if (strpos($this->lastResult, "<m:validity>true</m:validity>")) {
            return self::NO_ERROR;
        }
        
        $result = $this->getSanitisedSimpleXml($this->lastResult);
        
        foreach ($this->errorPointer as $item) {
            foreach ($result->children() as $child) {
                if ($child->getName() == $item) {
                    $result = $child;
                    break;
                }
            } 
        }
        
        $errors = array();
        foreach ($result->merrorlist->children() as $error) {
            $orig = $error->mcontext;
            if ($error->msource) {
                $orig = $error->msource;
                $orig = str_replace('<strong title="Position where error was detected.">', '', $orig);
                $orig = str_replace('</strong>', '', $orig);
                $orig = html_entity_decode($orig);
            }
            $errors[] = array(
                'line' => (string)$error->mline,
                'errortype' => (string)$error->merrortype,
                'error' => trim((string)$error->mmessage),
                'original_line' => (string)$orig,
            );
        }

		if (count($this->errorMessageFilter) == 0) {
        	return $errors;
		}

		// filter error messages
		$filteredErrors = array();
		foreach ($errors as $key => $error) {
			if (!$this->isFiltered($error)) {
				$filteredErrors[$key] = $error;
			}
		}

		return (count($filteredErrors) == 0) ? self::NO_ERROR : $filteredErrors;
    }

	/**
	 * Check if this error matches an errorMessageFilter regex
	 *
	 * @param array $error
	 * @return bool
	 */
	protected function isFiltered(array $error) {
		foreach ($this->errorMessageFilter as $regex) {
			if (preg_match($regex, $error['error'])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get validation errors
	 *
	 * @param $input
	 * @param array $options
	 * @return array|bool|int
	 */
	public function getValidationErrors($input, $options = array()) {
        $isValid = $this->isValid($input, $options);

        if (self::NO_VALIDATOR_RESPONSE === $isValid ) {
            $this->getTest()->markTestSkipped('No validator response');
        } elseif ($isValid) {
            return false;
        }

        return $this->getErrors();
    }

	/**
	 * Set error message filter (array of regex patterns)
	 * Error messages that match at least one of the given patterns will be ignores
	 *
	 * @param array $errorMessageFilter
	 * @return void
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 */
	public function setErrorMessageFilter(array $errorMessageFilter) {
		$this->errorMessageFilter = $errorMessageFilter;
	}

}