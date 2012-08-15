<?php
/**
 * Console Application
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Console
 **/


/**
 * Directory Seperator
 * @var string
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Application path
 * @var string
 */
define('_APP_PATH_', dirname(__FILE__) . DS . 'application');

/**
 * Framework path
 * @var string
 */
define('_FRAMEWORK_PATH_', dirname(__FILE__)  . DS . 'framework');

/**
 * Configuration path
 * @var string
 */
define('_CONFIG_PATH_', _APP_PATH_  . DS . '/config');

/**
 * Libraries path
 * @var string
 */
define('_LIBS_PATH_', dirname(__FILE__)  . DS . 'libraries');

// Load AutoLoader
require_once _FRAMEWORK_PATH_ . DS . 'AutoLoader.php';
$autoload = KoolDevelop\AutoLoader::getInstance();
    
// Load shorthand functions
require _FRAMEWORK_PATH_ . DS . 'shorthand.php';

// Start Console Application
\KoolDevelop\Console\Console::getInstance()->start();