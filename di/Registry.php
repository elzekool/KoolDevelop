<?php
/**
 * Registry
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Di
 **/

namespace KoolDevelop\Di;

/**
 * Registry
 *
 * Dependency Injection Registry
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Di
 **/
class Registry
{    
 
    /**
     * Contents of Registry
     * @var mixed[]
     */
    private $Contents = array();
    
    
    /**
     * Singleton Instance
     * @var \KoolDevelop\Di\Registry
     */
    protected static $Instance;

    /**
     * Get Registry instance
     *
     * @return \KoolDevelop\Di\Registry
     */
    public static function getInstance() {
        if (self::$Instance === null) {
            self::$Instance = new self();
          }
          return self::$Instance;
    }
    
    /**
     * Create new object of class
     * 
     * @param string $classname Classname
     * 
     * @return mixed[] Object
     */
    private function _createNewObject($classname) {
        $reflection = new \ReflectionClass($classname);
        $class_constructor = $reflection->getConstructor();
        
        if ($class_constructor === null) {
            $object = new $classname();
            $this->injectAll($object);
            return $object;
        }
                
        if ($class_constructor->getNumberOfRequiredParameters() > 0) {
            throw new \KoolDevelop\Exception\DependencyException(sprintf(__f('Class constructor for %s requires parameters', 'kooldevelop'), $classname));
        }
        
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $object = $reflection->newInstanceWithoutConstructor();
        } else {
            $object = unserialize(sprintf('O:%d:"%s":0:{}',strlen($classname), $classname));
        }
        
        $this->injectAll($object);
        $class_constructor->invoke($object);
        
        return $object;
    }
    
    /**
     * Inject registry item into object
     * 
     * @param mixed  $object     Object to insert into
     * @param string $property   Property to insert to
     * @param mixed  $injectable Object name to insert
     */
    private function _injectIntoProperty(&$object, $property, $injectable) {
        $refl_property = new \ReflectionProperty(get_class($object), $property);
        $refl_property->setAccessible(true);
        if ($refl_property->getValue($object) !== null) {
            return;
        }
        $refl_property->setValue($object, $injectable);        
    }
    
    /**
     * Get dependency from string
     * 
     * @param string $definition Defintion
     * 
     * @return mixed Object
     */
    public function _fromString($definition) {
        if (class_exists($definition)) {
            return $this->_createNewObject($definition);
        } else if (preg_match('/^([A-Za-z0-9_\\\\]+::[A-Za-z0-9_\\\\]+)\\(\\)$/', $definition) != 0) {
            return call_user_func(substr($definition, 0, -2));
        } else {
            throw new \KoolDevelop\Exception\DependencyException(sprintf(__f('Cannot resolve dependency %s', 'kooldevelop'), $definition));
        }
    }
    
    /**
     * Inject dependencies into object
     * 
     * @param mixed $object Object to insert into
     * 
     * @return void
     */
    public function injectAll(&$object) {
        $annotation_reader = \KoolDevelop\Annotation\Reader::createForClass(get_class($object));
        foreach($annotation_reader->getAnnotatedProperties() as $property) {
            foreach($annotation_reader->getAllForProperty($property, 'Inject') as $inject) {
                $this->_injectIntoProperty($object, $property, $this->get($inject->getName()));
            }
        }
    }
    
    /**
     * Get Object from Registry
     * 
     * @param string $name    Object Name
     * @param mixed  $default Default value
     * 
     * @return mixed[] Object
     */
    public function get($name, $default = null) {        
        if (array_key_exists($name, $this->Contents)) {
            $content = $this->Contents[$name];
            // If it's a closure, resolve it and save the bean
            if ($content instanceof \Closure) {
                $content = $content($this);
                $this->Contents[$name] = $content;
            }
            return $content; 
            
        } else if ($default !== null) {
            return $this->Contents[$name] = $this->get($default);
        } else if (is_string($name)) {
            return $this->Contents[$name] = $this->_fromString($name);
        } else {
            throw new \KoolDevelop\Exception\DependencyException(sprintf(__f('Cannot resolve dependency %s', 'kooldevelop'), $name));
        }
    }
    
    /**
     * Save object into Registry
     * 
     * @param string                 $name     Object Name
     * @param \Closure|string|mixed  $contents Object to store
     * 
     * @return void
     */
    public function set($name, $contents) {
        $this->Contents[$name] = $contents;
    }
    
    
    
    
}