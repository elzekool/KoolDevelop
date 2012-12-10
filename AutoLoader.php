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
 * Require \KoolDevelop\Configuration\IConfigurable
 */
require_once FRAMEWORK_PATH . DS . 'configuration' . DS . 'IConfigurable.php';

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
class AutoLoader implements \KoolDevelop\Configuration\IConfigurable
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
     * Classpath caching
     * @var string[]
     */
    private $ClassPaths = array();
    
    /**
     * Cache for Classpaths
     * @var \KoolDevelop\Cache\Cache
     */
    private $Cache;
    
    /**
     * Get \KoolDevelop\AutoLoader instance
     *
     * @return \KoolDevelop\AutoLoader
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
        $this->addMapping('\\KoolDevelop\\', FRAMEWORK_PATH);        
        $this->addMapping('\\', APP_PATH);
        $this->addMapping('\\', \FRAMEWORK_PATH . DS . 'application_base');
        
        // Register autoloader
        spl_autoload_register(array($this, 'autoload'));
    }
	
    /**
     * Function called when enviroment is available
     * 
     * @return void
     */
    public function environmentAvailable() {
        // Laad Cache
        $this->Cache = \KoolDevelop\Cache\Cache::getInstance('autoloader');
        $this->ClassPaths = array_merge($this->Cache->loadObject('classpaths', array()), $this->ClassPaths);               
    }
    
    /**
     * Destructor
     * @ignore
     */
    public function __destruct() {
        // Save classpaths to cache
		if (isset($this->Cache)) {
            $this->Cache->saveObject('classpaths', $this->ClassPaths);
        }
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
        
        $_classname = ltrim($classname, '\\');
        $filename  = '';
        $namespace = '';
        if ($last_ns_pos = strripos($_classname, '\\')) {
            $namespace = substr($_classname, 0, $last_ns_pos);
            $_classname = substr($_classname, $last_ns_pos + 1);
            $filename  = str_replace('\\', DS, $namespace) . DS;
        }        
        $filename .= str_replace('_', DS, $_classname) . '.php';

        if (file_exists(APP_PATH . DS . 'libs' . DS . $filename)) {            
            $this->ClassPaths[$classname] = $filename;
            require APP_PATH . DS . 'libs' . DS . $filename;
            return true;
            
        } elseif (file_exists(FRAMEWORK_PATH . DS . 'libs' . DS . $filename)) {
            $this->ClassPaths[$classname] = $filename;
            require FRAMEWORK_PATH . DS . 'libs' . DS . $filename;
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
        
        $_classname = str_replace('\\\\', '\\', '\\' . \str_replace('_', '\\', $classname));

        // Loop trough Prefix Mappings
        foreach ($this->PrefixMappings as $prefix => $mappings) {
            if (\strpos($_classname, $prefix) === 0) {
                foreach ($mappings as $mapping) {                    
                    
                    // Underscore path elements and don't touch class                    
                    $_classpath = explode('\\', \substr($_classname, strlen($prefix)));
                    $_classfile = array_pop($_classpath);                    
                    $_classpath = $this->underscore_path('\\' . join('\\', $_classpath)) . '\\' . $_classfile;
                    
                    $filename = $mapping . DS . \str_replace(array('\\'), DS, $_classpath) . '.php';				
                    
                    if (\file_exists($filename)) {
                        $this->ClassPaths[$classname] = $filename;
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

    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array();
    }
    

    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {      
        return array(
            new \KoolDevelop\Configuration\IConfigurableOption('cache', 'autoloader.class', '"\KoolDevelop\Cache\FileStorage"', ('You can add a cache for classpaths, this prevents scanning of source tree for each class. Don\'t use on development machines.'), false),
            new \KoolDevelop\Configuration\IConfigurableOption('cache', 'autoloader.path', 'APP_PATH "" DS "cache"', '', false),            
        );
    }
    
}
