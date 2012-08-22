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
 * @ignore
 */
define('REQUEST_TYPE', 'console');

/**
 * Directory Seperator
 * @var string
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Application path
 * @var string
 */
define('APP_PATH', dirname(__FILE__) . DS . 'application');

/**
 * Framework path
 * @var string
 */
define('FRAMEWORK_PATH', dirname(__FILE__)  . DS . 'framework');

/**
 * Configuration path
 * @var string
 */
define('CONFIG_PATH', APP_PATH  . DS . '/config');

/**
 * Libraries path
 * @var string
 */
define('LIBS_PATH', dirname(__FILE__)  . DS . 'libraries');

// Load AutoLoader
require_once FRAMEWORK_PATH . DS . 'AutoLoader.php';
$autoload = KoolDevelop\AutoLoader::getInstance();

// Load shorthand functions
require FRAMEWORK_PATH . DS . 'shorthand.php';


// Start Console Application
\KoolDevelop\Console\Console::getInstance()->start();