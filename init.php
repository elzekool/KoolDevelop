<?php
/**
 * Framework Entry point
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

ini_set('display_errors', '1');

if (!defined('APP_PATH')) {
	throw new Exception("Application path not set!");
}

if (!defined('FRAMEWORK_PATH')) {
	throw new Exception("Framework path not set!");
}

if (!defined('CONFIG_PATH')) {
	throw new Exception("Configuration path not set!");
}

if (!defined('DS')) {
	/**
	 * Directory Seperator
	 * @var string
	 */
	define('DS', DIRECTORY_SEPARATOR);
}


// Load shorthand functions
require FRAMEWORK_PATH . DS . 'shorthand.php';

// Load AutoLoader
require_once FRAMEWORK_PATH . DS . 'AutoLoader.php';
$autoload = KoolDevelop\AutoLoader::getInstance();

try {

    // Load Bootstrapper
    $bootstrapper = new \Bootstrapper();
	
	// Get current environment, and save this in the configuration class
    \KoolDevelop\Configuration::setCurrentEnvironment($bootstrapper->getEnvironment());

	// Init
    $bootstrapper->init();

    // Start routing
    $bootstrapper->route();

} catch(\Exception $e) {

    // Clear all output buffering
	while(ob_get_level() != 0) {
		ob_end_clean();
	}

	// Send Error to Handler
	\KoolDevelop\ErrorHandler::getInstance()->handleException(__f($e,'kooldevelop'));
	die();
	
}

?>