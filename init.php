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

try {
    
    // Inject Router into Bootstrapper
    \KoolDevelop\Di\Registry::getInstance()->injectAll($bootstrapper);

    // Start Logger
    $logger = \KoolDevelop\Log\Logger::getInstance();
    $logger->low(sprintf('Finished loading bootstrapper, environment %s, now at application entry point', $environment), 'KoolDevelop.Core');

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