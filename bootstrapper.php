<?php
/**
 * PHP Unit Bootstrapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Unit Tests
 **/


/**
 * Request type (web|webservice|console|test)
 * @var string
 */
define('REQUEST_TYPE', 'test');

/**
 * Application path
 * @var string
 */
define('APP_PATH', realpath(dirname(__FILE__) . '/unit_test_app/'));

/**
 * Framework path
 * @var string
 */
define('FRAMEWORK_PATH', realpath(dirname(__FILE__) . '/../framework/'));

/**
 * Configuration path
 * @var string
 */
define('CONFIG_PATH', APP_PATH . '/config');

/**
 * Directory Seperator
 * @var string
 */
define('DS', DIRECTORY_SEPARATOR);


// Load shorthand functions
require FRAMEWORK_PATH . DS . 'shorthand.php';
    
// Load Configuration
require FRAMEWORK_PATH . DS . 'Configuration.php';

// Load Bootstrapper
require FRAMEWORK_PATH . DS . 'Bootstrapper.php';
if (file_exists(APP_PATH . DS . 'Bootstrapper.php')) {
    require APP_PATH . DS . 'Bootstrapper.php';
} else {
    require FRAMEWORK_PATH . DS . 'application_base' . DS . 'Bootstrapper.php';
}

// Set Environment
$bootstrapper = new \Bootstrapper();
$environment = $bootstrapper->getEnvironment();
\KoolDevelop\Configuration::setCurrentEnvironment($environment);

// Load AutoLoader
require_once FRAMEWORK_PATH . DS . 'AutoLoader.php';
$autoload = KoolDevelop\AutoLoader::getInstance();



