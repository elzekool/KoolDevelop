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
     * Construct
     * 
     * @param string $name Object name
     */
    function __construct($name) {     
        if (!is_string($name)) {
            throw new \KoolDevelop\Exception\AnnotationException(__f('Inject setting should be a string', 'kooldevelop'));
        }
        $this->Name = $name;
    }
    
    /**
     * Get Name
     * 
     * @return string
     */
    public function getName() {
        return $this->Name;
    }


}

?>
