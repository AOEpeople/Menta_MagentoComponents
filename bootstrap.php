<?php

define('AOECOMPONENTS_ROOTDIR', dirname(__FILE__));

/**
 * Simple autoloading
 *
 * @param string $className
 * @return bool
 * @throws Exception
 * @author Fabrizio Branca
 * @since 2011-11-24
 */
spl_autoload_register(function ($className) {

	// don't do autoloading for external classes
	if (strpos($className, 'AoeComponents_') !== 0) {
		return false;
	}

	$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if (!file_exists($fileName)) {
		throw new Exception("File '$fileName' not found.");
	}
	require_once($fileName);
	if (!class_exists($className) && !interface_exists($className)) {
		throw new Exception("File '$fileName' does not contain class/interface '$className'");
	}
	return true;
		
});
