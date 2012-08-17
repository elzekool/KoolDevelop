<?php
/**
 * Configure Console Task
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\Console;


/**
 * Configure Console Task
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/
class ConfigureTask implements \KoolDevelop\Console\ITask
{
    
    private $DefaultClasses = array(
        '\\KoolDevelop\\Router',
        '\\KoolDevelop\\ErrorHandler',
        '\\KoolDevelop\\View\\View',
        '\\KoolDevelop\\View\\Element',
        '\\KoolDevelop\\International\\I18n'
    );
    
    
    /**
     * Process collected classnames
     * 
     * @param string[] $classnames Classnames
     * 
     * @return void
     */
    private function processClasses($classnames) {

        $options = array();
        
        foreach($classnames as $class) {
            $refl = new \ReflectionClass($class); 
            if ($refl->implementsInterface('\\KoolDevelop\\Configuration\\IConfigurable')) {
                foreach($class::getConfigurationOptions() as $option) {
                    echo $option->getFile() . ' ' . $option->getOption() . "\n";
                    $this->processOption($option, $options);
                }
            }
        }
        
        print_r($options);
        
    }
    
    /**
     * Process Configuration option and add it to the total list of options
     * 
     * @param \KoolDevelop\Configuration\IConfigurableOption $option  Option
     * @param string[][][]                                   $options Total list op options
     * 
     * @return void
     */
    private function processOption(\KoolDevelop\Configuration\IConfigurableOption $option, &$options) {
        
        $file = $option->getFile();
        list($section, $property) = explode('.', $option->getOption());
                
        // Check if file exists
        if (!isset($options[$file])) {
            $options[$file] = array(
                $section => array(
                    $property => array()
                )
            );
            
        // Check if section exists
        } else if (!isset($options[$file][$section])) {
            $options[$file][$section] = array(
                $property => array()
            );
            
        // Check if option exists            
        } else if (!isset($options[$file][$section][$property])) {
            $options[$file][$section][$property] = array();
        }
        
        $options[$file][$section][$property][] = $property;
        
    }
    
    /**
     * Default function for task
     * 
     * @return void
     */
    public function index() {
        
        $this->processClasses($this->DefaultClasses);
        
    }
    
}