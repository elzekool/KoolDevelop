<?php
/**
 * Inject Annotation
 * 
 * @author Elze Kool    
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Di
 **/

namespace KoolDevelop\Annotation;

/**
 * Inject Annotation
 * 
 * Allows injecting external objects trough the dependency injector
 * Example: @Inject("DatabaseAdaptor")
 * 
 * @author Elze Kool    
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Di
 **/
class Inject implements \KoolDevelop\Annotation\IAnnotation
{
    /**
     * Object name
     * @var string
     **/
    private $Name;
    
    /**
     * Default Contents
     * @var string
     */
    private $Default;
    
    /**
     * Construct
     * 
     * @param string $name    Object name
     * @param string $default Default Contents
     */
    function __construct($name, $default = null) {     
        if (!is_string($name)) {
            throw new \KoolDevelop\Exception\AnnotationException(__f('Inject name should be a string', 'kooldevelop'));
        }
        if ($default !== null AND !is_string($default)) {
            throw new \KoolDevelop\Exception\AnnotationException(__f('Inject default should be a string or left empty', 'kooldevelop'));
        }
        
        $this->Name = $name;
        $this->Default = $default;
        
    }
    
    /**
     * Get Name
     * 
     * @return string Name
     */
    public function getName() {
        return $this->Name;
    }

    /**
     * Get Default
     * 
     * @return string Default Contents
     */
    public function getDefault() {
        return $this->Default;
    }



}