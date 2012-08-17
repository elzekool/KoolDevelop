<?php
/**
 * Application Bootstrapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

/**
 * Base Bootstrapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/
class Bootstrapper extends \KoolDevelop\Bootstrapper
{

    /**
     * Function called on application initialisation
     *
     * @return void
     */
    public function init() {

        // Register session storage
        \KoolDevelop\Session::getInstance()->registerSessionStorage(
            'Php',
            new \KoolDevelop\SessionStorage\Php()
        );

        // Add default route
        $router = \KoolDevelop\Router::getInstance();
        $router->addRoute(new \KoolDevelop\Route\Literal('/', '/start/index'));
        $router->addRoute(new \KoolDevelop\Route\RegEx('/^\/tips(.*)$/', '/tips/index/$1'));

    }

    /**
     * Function called on console launch
     *
     * @return void
     */
    public function console() {

    }

    /**
     * Determine current environment. This environment is used
     * to determine configuration files
     *
     * @return string
     */
    public function getEnvironment() {

        $hostname = $_SERVER['HTTP_HOST'];
        if (preg_match('/\.kooldevelop$/', $hostname) != 0) {
            return 'development';
        } else if (preg_match('/localhost$/', $hostname) != 0) {
            return 'localhost';            
        } else {
            return 'production';
        }

    }

}
