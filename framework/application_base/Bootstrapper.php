<?php
/**
 * Application Bootstrapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/

/**
 * Base Bootstrapper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/
class Bootstrapper extends \KoolDevelop\Bootstrapper
{

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

}
