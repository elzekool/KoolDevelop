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
 * Application path
 * @var string
 */
define('_APP_PATH_', realpath(dirname(__FILE__) . '/../'));

/**
 * Framework path
 * @var string
 */
define('_FRAMEWORK_PATH_', realpath(dirname(__FILE__) . '/../../framework/'));

/**
 * Configuration path
 * @var string
 */
define('_CONFIG_PATH_', _APP_PATH_ . '/config');

/**
 * Libraries path
 * @var string
 */
define('_LIBS_PATH_', realpath(dirname(__FILE__) . '/../../libraries/'));

/**
 * Load initialisaton
 */
require _FRAMEWORK_PATH_ . '/init.php';


