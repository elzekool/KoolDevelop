<?php
/**
 * Annotation Reader
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Annotation
 **/

namespace KoolDevelop\Annotation;

/**
 * Annotation Reader
 *
 * Reads Annotations for class, including its methods and properties.
 * 
 * @author Elze Kool    
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Annotation
 **/
class Reader implements \KoolDevelop\Configuration\IConfigurable
{    
    /**
     * Annotated Class
     * @var string
     */
    private $ClassName;    
    
    /**
     * Namespace of annotated class
     * @var string
     */
    private $Namespace;

    /**
     * Class is parsed
     * @var boolean
     */
    private $Parsed = false;
       
    /**
     * Annotations for Methods
     * @var \KoolDevelop\Annotation\IAnnotation[][]
     */
    private $Methods = array();
    
    /**
     * Annotations for Properties
     * @var \KoolDevelop\Annotation\IAnnotation[][]
     */
    private $Properties = array();
    
    /**
     * Annotations for Class
     * @var \KoolDevelop\Annotation\IAnnotation[]
     */
    private $Class = array();

    /**
     * Reader instances
     * @var \KoolDevelop\Annotation\Reader[]
     */
    private static $Instances = array();
    
    /**
     * Create new Reader for Class
     * 
     * Creates a new Reader for the given classname. When lazy initialisation
     * is enabled the class is parsed on first usage. 
     * 
     * @param string  $class Class
     * @param boolean $lazy  Lazy initialisation
     * 
     * @return \KoolDevelop\Annotation\Reader Reader
     */
    public static function createForClass($class, $lazy = true) {
        if (!isset(self::$Instances[$class])) {
            self::$Instances[$class] = new self($class, $lazy);
        } else if (!$lazy) {
            self::$Instances[$class]->parse();
        }        
        return self::$Instances[$class];
    }
    
    /**
     * Constructor. 
     * 
     * @param string  $class Class Name
     * @param boolean $lazy  Lazy Initialisation
     */
    private function __construct($class, $lazy) {
        if (!class_exists($class, true)) {
            throw new \KoolDevelop\Exception\AnnotationException(__f('Class does not exist.'), 'kooldevelop');
        }
        $this->ClassName = $class;
        if (!$lazy) {
            $this->parse();
        }
    }
    
    /**
     * Get full Classname for Annotation or null of class
     * does not exists
     * 
     * @param string $class Class
     * 
     * @return void
     */
    private function _getFullClassName($class) {
        $prefixes = array(
            '\\', '\\Annotation\\', '\\KoolDevelop\\Annotation\\',
            $this->Namespace . '\\', $this->Namespace . '\\Annotation\\'
        );
        
        foreach($prefixes as $prefix) {
            $full_classname = $prefix . $class;
            if (!class_exists($full_classname)) {
                continue;
            }                 
            if (is_a($full_classname, '\KoolDevelop\Annotation\IAnnotation', true)) {
                return $full_classname;
            }
        }
        
        return null;
    }
    
    
    
    /**
     * Parse Arguments for Annotation
     * 
     * @param string $arguments Arguments
     * 
     * @return mixed[] Arguments
     */
    private function _parseArguments($arguments) {
        
        $keywords = array('false' => false, 'true' => true, 'null' => null);
        
        $parsed = array();
        $p_parsed =& $parsed;
        $parser_tree = array(&$parsed);
        
        $current = '';
        $type = null;
        $key = null;
        $str_start = '';
        
        $length = strlen($arguments);
        
        for($x = 0; $x < $length; $x++) {
            $end = ($x >= $length-1);
            $c = $arguments[$x];
            
            // No type yet
            if ($type === null) {
                if (($c == '"') OR ($c == "'")) {
                    $type = 'string';
                    $str_start = $c;
                } else if (preg_match('/[0-9\.\-\+]/', $c)) {
                    $type = 'number';
                    $x--;
                } else if (preg_match('/[a-zA-Z]/', $c)) {
                    $type = 'keyword';
                    $x--;
                } else if ($c == '{') {
                    if ($key !== null) { 
                        $p_parsed[$key] = array();
                        $p_parsed =& $p_parsed[$key];
                        $key = null;
                    } else { 
                        $p_parsed[] = array();
                        $p_parsed =& $p_parsed[array_pop(array_keys($p_parsed))];
                    }                      
                    $parser_tree[] =& $p_parsed;
                } else if (($c == '}') AND (count($parser_tree) > 0)) {
                    $p_parsed =& $parser_tree[count($parser_tree)-1];
                    array_pop($parser_tree);
                } else if (($c == ' ') || ($c == ',')) {
                    // ignore
                } else {
                    throw new \KoolDevelop\Exception\AnnotationException(__f('Invalid syntax for Annotation parameter' . $c, 'kooldevelop'));
                }
                
            // String
            } else if ($type === 'string') {                
                if (($c == '\\') AND !$end) {
                    $current .= $arguments[++$x];
                } else if ($c == $str_start OR $end) {                    
                    if ($key !== null) { 
                        $p_parsed[$key] = $current; 
                        $key = null;
                    } else { 
                        $p_parsed[] = $current; 
                    }
                    
                    $current = '';
                    $type = 'end';
                } else {
                    $current .= $c;
                }               
                
            // Number
            } else if ($type == 'number') {
                if (preg_match('/[0-9\.\-\+]/', $c)) {
                    $current .= $c;
                }
                if ((preg_match('/[0-9\.\-\+]/', $c) == 0) OR $end) {
                    $number = (strpos($current, '.') !== false) ? floatval($current) : intval($current); 
                    if ($key !== null) { 
                        $p_parsed[$key] = $number;
                        $key = null;
                    } else { 
                        $p_parsed[] = $number; 
                    }                    
                    if (!$end) {
                        $x--;
                    }
                    $current = '';
                    $type = 'end';
                }       
                
            // Keyword or array key
            } else if ($type == 'keyword') {
                if (preg_match('/[a-zA-Z0-9_]/', $c)) {
                    $current .= $c;
                }
                if ((preg_match('/[a-zA-Z0-9_]/', $c) == 0) OR $end) {                    
                    if (array_key_exists($current, $keywords)) {
                        $current = $keywords[$current];                        
                        if ($key !== null) { 
                            $p_parsed[$key] = $current;
                            $key = null;
                        } else { 
                            $p_parsed[] = $current; 
                        }                                               
                        if (!$end) {
                            $x--;
                        }
                        $type = 'end';
                    
                    } else if (preg_match('/^\s*=\s*/', substr($arguments, $x), $matches)) {
                        if (count($parser_tree) == 1) {
                            throw new \KoolDevelop\Exception\AnnotationException(__f('Cannot add array keys to main level', 'kooldevelop'));
                        }
                        $x += strlen($matches[0])-1;                        
                        $key = $current;                        
                        $type = null;
                    }
                    
                    $current = '';
                    
                }    
                
                
            // End
            } else if ($type == 'end') {
                if ($c == ',') {
                    $type = null;
                } else if ($c == '}') {
                    $type = null;
                    $x--;
                } else if ($c != ' ') {
                    throw new \KoolDevelop\Exception\AnnotationException(__f('Invalid syntax for Annotation parameter', 'kooldevelop'));
                }
            }
        }
        
        return $parsed;
    }
    
    
    /**
     * Parse DocBlock for Annotations
     * 
     * @param string $content Contents
     * 
     * @return \KoolDevelop\Annotation\IAnnotation[] Found Annotations
     */
    private function _parseDocBlock($content) {        
        $annotations = array();        
        if (preg_match_all('/\s\*\s?(@([A-Z].[A-Za-z0-9_\\\\]+)(\((.+)\))?)\s/m', $content, $matches) > 0) {            
            foreach($matches[2] as $match_index => $class) {                
                if (null !== ($full_class = $this->_getFullClassName($class))) {
                    $properties = $matches[4][$match_index];
                    if (empty($properties)) {
                        $annotations[] = new $full_class();
                    } else {
                        $refl = new \ReflectionClass($full_class);                        
                        $annotations[] = $refl->newInstanceArgs($this->_parseArguments($properties));
                    }
                }
            }
        }        
        return $annotations;        
    }
    
    /**
     * Search for annotations in specific list
     * 
     * @param \KoolDevelop\Annotation\IAnnotation[] $list         List to search
     * @param string                                $class_filter Type of annotation to search
     * 
     * @return \KoolDevelop\Annotation\IAnnotation[] Found annotations
     */
    private function _getAnnotations(&$list, $class_filter) {        
        if ($class_filter === null) {
            return $list;
        } if (null !== ($full_class = ($this->_getFullClassName($class_filter)))) {
            $found = array();
            foreach($list as $item) {
                if ($item instanceOf $full_class) {
                    $found[] = $item;
                }
            }
            return $found;
        } else {
            return array();
        }
    }
    
    /**
     * Get Annotations for class
     * 
     * @param string $class_filter Set to only return annotations of this class
     * 
     * @return \KoolDevelop\Annotation\IAnnotation[] Found annotations
     */
    public function getAllForClass($class_filter = null) {
        $this->parse();
        return $this->_getAnnotations($this->Class, $class_filter);
    }
    
    /**
     * Get Annotations for method
     * 
     * @param string $method       Method name
     * @param string $class_filter Set to only return annotations of this class
     * 
     * @return \KoolDevelop\Annotation\IAnnotation[] Found annotations
     */
    public function getAllForMethod($method, $class_filter = null) {
        $this->parse();
        if (!isset($this->Methods[$method])) {
            return array();
        } else {
            return $this->_getAnnotations($this->Methods[$method], $class_filter);
        }
    }
    
     /**
     * Get Annotations for property
     * 
     * @param string $property     Property name
     * @param string $class_filter Set to only return annotations of this class
     * 
     * @return \KoolDevelop\Annotation\IAnnotation[] Found annotations
     */
    public function getAllForProperty($property, $class_filter = null) {
        $this->parse();
        if (!isset($this->Properties[$property])) {
            return array();
        } else {
            return $this->_getAnnotations($this->Properties[$property], $class_filter);
        }
    }
    
    /**
     * Parse annotations
     * 
     * @return void
     */
    public function parse() {
        
        if ($this->Parsed) {
            return;
        }
        
        // Load Cache Handler
        $cache = \KoolDevelop\Cache\Cache::getInstance(
            \KoolDevelop\Configuration::getInstance('core')->get('annotations.cache', 'annotations')
        );
        
        $class_reflect = new \ReflectionClass($this->ClassName);
        $this->Namespace = '\\' . $class_reflect->getNamespaceName();

        // Check if cached version exists and is up-to-date
        $cache_key = strtolower($class_reflect->getShortName()) . '_' . sha1($class_reflect->getFileName());
        $file_mtime = filemtime($class_reflect->getFileName());        
        if (null !== ($cached = $cache->loadObject($cache_key))) {
            if ($cached['mtime'] >= $file_mtime) {
                $this->Class = $cached['Class'];
                $this->Properties = $cached['Properties'];
                $this->Methods = $cached['Methods'];
                $this->Parsed = true;
                return;
            }
        }
        
        
        if (false !== ($class_docblock = $class_reflect->getDocComment())) {
            $this->Class = $this->_parseDocBlock($class_docblock);
        }
        
        // Go trough properties of class
        foreach($class_reflect->getProperties() as $property_reflect) {
            /* @var $property_reflect \ReflectionProperty */
            if (false !== ($property_docblock = $property_reflect->getDocComment())) {
                $this->Properties[$property_reflect->name] = $this->_parseDocBlock($property_docblock);
            }
        }
        
        // Go trough methods of class
        foreach($class_reflect->getMethods() as $method_reflect) {
            /* @var $method_reflect \ReflectionMethod */
            if (false !== ($method_docblock = $method_reflect->getDocComment())) {
                $this->Methods[$method_reflect->name] = $this->_parseDocBlock($method_docblock);
            }
        }
        
        // Save in cache
        $cache->saveObject($cache_key, array(
            'mtime' => $file_mtime,
            'Class' => $this->Class,
            'Properties' => $this->Properties,
            'Methods' => $this->Methods
        ), 60 * 60 * 24);
        
        // Store that we've parsed the class
        $this->Parsed = true;
        
    }   

    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array(
            '\\KoolDevelop\\Cache\\Cache',
        );
    }
    
    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {      
        return array(
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'annotations.enabled', '0', ('When enabled core annotations are enabled. Make sure the cache option is configured correctly')),
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'annotations.cache', '\'annotations\'', ('Cache configuration that is used'))
        );
    }

    
    
    
}