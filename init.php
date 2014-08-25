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

if (!defined('APP_PATH')) {
    throw new Exception("Application path not set!");
}

if (!defined('VENDORS_PATH')) {
    throw new Exception("Vendors path not set!");
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

// Load Composer autoloader
require_once VENDORS_PATH . '/autoload.php';


// Initialize Pimple Container
$container = new \Pimple\Container();

// Load shorthand functions
require FRAMEWORK_PATH . DS . 'shorthand.php';
    
// Load Configuration
require FRAMEWORK_PATH . DS . 'Configuration.php';

// Load Bootstrapper
require FRAMEWORK_PATH . DS . 'Bootstrapper.php';
if (file_exists(APP_PATH . DS . 'Bootstrapper.php')) {
    require APP_PATH . DS . 'Bootstrapper.php';
    $container['bootstrapper'] = function($c) { return new \Bootstrapper($c); };
} else {
    $container['bootstrapper'] = function($c) { return new \KoolDevelop\Bootstrapper($c); };
}


// Add Router to container
$container['router'] = function($c) {
    return new \KoolDevelop\Router($c);
};

// Add Logger to container
$container['logger'] = function($c) {
    return \KoolDevelop\Log\Logger::getInstance();
};

// Create ErrorHandler
$container['error_handler'] = function($c) {
    return new KoolDevelop\ErrorHandler();
};

// Set Environment
$environment = $container['bootstrapper']->getEnvironment();
\KoolDevelop\Configuration::setCurrentEnvironment($environment);

try {

    // Start Logger
    $container['logger']->low(sprintf('Finished loading bootstrapper, environment %s, now at application entry point', $environment), 'KoolDevelop.Core');

    // Init
    $container['bootstrapper']->init();

    // Start routing
    $container['bootstrapper']->route();

} catch(\Exception $e) {

    // Clear all output buffering
    while(ob_get_level() != 0) {
        ob_end_clean();
    }

    // Send Error to Handler
    $container['error_handler']->handleException(__f($e,'kooldevelop'));
    die();
    
}

?>