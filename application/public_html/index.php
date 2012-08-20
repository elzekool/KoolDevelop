<?php
/**
 * Application Entry point
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/


/**
 * Request type (web|webservice|console)
 * @var string
 */
define('REQUEST_TYPE', 'web');


/**
 * Application path
 * @var string
 */
define('APP_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * Framework path
 * @var string
 */
define('FRAMEWORK_PATH', realpath(dirname(__FILE__) . '/../../framework/'));

/**
 * Configuration path
 * @var string
 */
define('CONFIG_PATH', APP_PATH . '/config');

/**
 * Libraries path
 * @var string
 */
define('LIBS_PATH', realpath(dirname(__FILE__) . '/../../libraries/'));

/**
 * Load initialisaton
 */
require FRAMEWORK_PATH . '/init.php';


