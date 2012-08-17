<?php
/**
 * Configurable interface
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Configuration;

/**
 * Configurable interface
 * 
 * Implement this interface in your class to allow automatic generating of missing configuration
 * files using the Configure console task.
 * 
 * @see \KoolDevelop\Console\ConfigureTask
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
interface IConfigurable
{
    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions();
}