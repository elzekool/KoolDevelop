<?php
/**
 * AutoLoader
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * AutoLoader
 * 
 * This Autoloader is one of the main components of the KoolDevelop framework.
 * It find's sourcecode files based on their classnames. It uses a custom loader for application/framework files
 * and a PSR-0 compatible loader for libraries. 
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
final class AutoLoader 
{

    /**
     * AutoLoader instance
     * @var \KoolDevelop\AutoLoader
     */
    private static $Instance;
    
    /**
     * PrefixMappings for Framework/Application
     * @var string[]
     */
    private $PrefixMappings = array();

    /**
     * Allowed Vendors for PSR-0 autoloading
     * @var type 
     */
    private $Vendors = array();
    
    /**
     * Get KoolDevelop\AutoLoader instance
     *
     * @return KoolDevelop\AutoLoader
     */
    public static function getInstance() {
        if (self::$Instance === null) {
            self::$Instance = new self();
        }
        return self::$Instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
  
        // Add default mappings
        $this->addMapping('\\KoolDevelop\\', _FRAMEWORK_PATH_);        
        $this->addMapping('\\', _APP_PATH_);
        $this->addMapping('\\', \_FRAMEWORK_PATH_ . DS . 'application_base');
        
        // Register autoloader
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Underscore path element
     *
     * @param string $path CamelCased path name
     *
     * @return string under_scored path name
     */
    private function underscore_path($path) {
        return preg_replace_callback('/.[A-Z]/', function($matches) {
            if ($matches[0][0] == '\\') {
                return strtolower($matches[0]);
            } else {
                return $matches[0][0] . '_' . strtolower($matches[0][1]);
            }    
        }, $path);
    }
    
    /**
     * PSR-0 style autoloader 
     * 
     * @see: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
     * 
     * @param string $classname Classname
     * 
     * @return boolean Class loaded
     */
    private function autoloadPSR0($classname) {
        
        $classname = ltrim($classname, '\\');
        $filename  = '';
        $namespace = '';
        if ($last_ns_pos = strripos($classname, '\\')) {
            $namespace = substr($classname, 0, $last_ns_pos);
            $classname = substr($classname, $last_ns_pos + 1);
            $filename  = _LIBS_PATH_ . DS . str_replace('\\', DS, $namespace) . DS;
        }        
        $filename .= str_replace('_', DS, $classname) . '.php';

        if (file_exists($filename)) {
            require $filename;
            return true;
        }
        
        return false;
    }
    
    /**
     * Callback for spl_autoload_register
     *
     * @param string $classname Class name to load
     *
     * @return bool Class loaded
     */
    protected function autoload($classname) {
        
        // First use PSR-0 autoloader
        foreach($this->Vendors as $vendor) {
            if (\strpos($classname, $vendor) === 0) {
                if ($this->autoloadPSR0($classname)) {
                    return true;
                }
            }
        }
        
        $classname = '\\' . \str_replace('_', '\\', $classname);

        // Loop trough Prefix Mappings
        foreach ($this->PrefixMappings as $prefix => $mappings) {
            if (\strpos($classname, $prefix) === 0) {
                foreach ($mappings as $mapping) {                    
                    
                    // Underscore path elements and don't touch class                    
                    $_classpath = explode('\\', \substr($classname, strlen($prefix)));
                    $_classfile = array_pop($_classpath);                    
                    $_classpath = $this->underscore_path('\\' . join('\\', $_classpath)) . '\\' . $_classfile;
                    
                    $filename = $mapping . DS . \str_replace(array('\\'), DS, $_classpath) . '.php';				
                    if (\file_exists($filename)) {
                        include $filename;
                        return true;
                    }
                    
                }
            }
        }

        return false;
    }
    
    /**
     * Add PSR-0 Vendor
     *
     * @param string $vendor Vendor namespace
     *
     * @return void
     */
    public function addVendor($vendor) {
        if ($vendor[0] == '\\') {
            $vendor = substr($vendor, 1);
        }
        $this->Vendors[] = $vendor;
    }

    /**
     * Add Directory Mapping for Prefix
     *
     * @param <type> $prefix    Prefix
     * @param <type> $directory Directory
     * @param bool   $prepend   True to prepend, false to append
     *
     * @return void
     */
    public function addMapping($prefix, $directory, $prepend = false) {

        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(__f("Directory for AutoLoader Mapping does not exist.", 'kooldevelop'));
        }

        if (preg_match('/^[\\a-zA-Z0-9]+$/', $prefix) == 0) {
            throw new \InvalidArgumentException(__f("Invalid Prefix format for AutoLoader", 'kooldevelop'));
        }

        if (!isset($this->PrefixMappings[$prefix])) {
            $this->PrefixMappings[$prefix] = array();
        }

        if (\in_array($directory, $this->PrefixMappings[$prefix])) {
            return;
        }

        if ($prepend) {
            array_unshift($this->PrefixMappings[$prefix], $directory);
        } else {
            array_push($this->PrefixMappings[$prefix], $directory);
        }
    }

}
