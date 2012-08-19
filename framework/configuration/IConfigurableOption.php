<?php
/**
 * Configurable interface Option
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Configuration;

/**
 * Configurable interface Option
 * 
 * Option as returned by the \KoolDevelop\Configuration\IConfigurable::getConfigurationOptions() function. 
 * 
 * @see \KoolDevelop\Configuration\IConfigurable
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class IConfigurableOption
{
    /**
     * File for configuration (without .ini suffix)
     * @var string
     */
    private $File;
    
    /**
     * Option name in the format section.property
     * @var string
     */
    private $Option;
    
    /**
     * Default value (Exact string as required in .ini file)
     * @var string 
     */
    private $Default;

    /**
     * Documentation (Is added just before property as comment)
     * @var string
     */
    private $Documentation;
    
    /**
     * Option is required
     * @var boolean
     */
    private $Required;
    
    /**
     * File
     * 
     * File for configuration (without .ini suffix)
     * 
     * @return string File
     */
    public function getFile() {
        return $this->File;
    }
    
    /**
     * Get Option
     * 
     * Option name in the format section.property
     * 
     * @return string Option
     */
    public function getOption() {
        return $this->Option;
    }

    /**
     * Default
     * 
     * Default value (Exact string as required in .ini file)
     * 
     * @return string Default
     */
    public function getDefault() {
        return $this->Default;
    }

    /**
     * Documentation
     * 
     * Documentation (Is added just before property as comment)
     * 
     * @return string Documentation
     */
    public function getDocumentation() {
        return $this->Documentation;
    }

    /**
     * Required
     * 
     * Option is required
     * 
     * @return boolean Required
     */
    public function getRequired() {
        return $this->Required;
    }
    
    
    /**
     * Constructor
     * 
     * @param string  $file          File for configuration (without .ini suffix)
     * @param string  $option        Option name in the format section.property
     * @param string  $default       Default value (Exact string as required in .ini file)
     * @param string  $documentation Documentation (Is added just before property as comment)
     * @param boolean $required      Required
     */
    function __construct($file, $option, $default, $documentation = '', $required = true) {
        $this->File = $file;
        $this->Option = $option;
        $this->Default = $default;
        $this->Documentation = $documentation;
        $this->Required = $required;
    }



    
}