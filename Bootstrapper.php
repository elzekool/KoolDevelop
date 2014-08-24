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
class Bootstrapper
{
    /**
     * Service Container
     * @var \Pimple\Container
     */
    protected $Container;
    
    /**
     * Constructor
     * 
     * @param \Pimple\Container $container Container
     */
    public function __construct($container) {
       $this->Container = $container; 
    }
        
    /**
     * Function called on application initialisation
     * 
     * @return void
     */
    public function init() {
        
    }
    
    /**
     * Function called on console launch
     * 
     * @return void
     */
    public function console() {
        
    }
    
    /**
     * Function called on webservice request
     * 
     * @return void
     */
    public function webservice() {
        
    }
    
    /**
     * Determine current environment. This environment is used
     * to determine configuration files
     * 
     * @return string
     */
    public function getEnvironment() {
        return REQUEST_TYPE;
    }
    
    /**
     * Route 
     * 
     * @param string $route Route to use, null to use route from URL
     * 
     * @return void
     */
    public function route($route = null) {
        
        if ($route === null) {
           $route = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ''; 
        }
        
        $this->Container['router']->route($route);
        
    }
    
    
}
