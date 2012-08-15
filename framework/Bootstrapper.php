<?php
/**
 * Base Bootstrapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * Base Bootstrapper
 * 
 * This is the base bootstapper. The bootstrapper is used for application initalisation.
 * Callback functions are called on important events in the start of a request.
 * 
 * Implement your own bootstapper with the classname \Bootstapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
abstract class Bootstrapper 
{

    /**
     * Function called on application initialisation
     * 
     * @return void
     */
    abstract public function init();
    
    /**
     * Function called on console launch
     * 
     * @return void
     */
    abstract public function console();
    
    
    /**
     * Determine current environment. This environment is used
     * to determine configuration files
     * 
     * @return string
     */
    abstract public function getEnvironment();
    
    /**
     * Route 
     * 
     * @param string $route Route to use, null to use route from URL
     * 
     * @return void
     */
    public function route($route = null) {
        
        if ($route === null) {
           $route = isset($_GET['url']) ? $_GET['url'] : ''; 
        }
        
        $router = \KoolDevelop\Router::getInstance();
        $router->route($route);
        
    }
    
    
}
