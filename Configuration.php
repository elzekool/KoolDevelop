<?php
/**
 * Configuration
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * Configuration
 * 
 * Reads and parsers configuration files. These configuration files are put in
 * the CONFIG_PATH of your application. The .ini files are parsed with PHP so you
 * can use PHP for special needs. 
 * 
 * It is possible to use specific configuration files based on the current environment
 * place the specific configuration files in a subfolder and call 
 * \\KoolDevelop\\Configuration::setCurrentEnvironment()
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Configuration
{
    /**
     * Current Environment
     * @var string
     */
    private static $CurrentEnvironment = 'production';
    
    /**
     * Singleton Instance
     * @var \KoolDevelop\Configuration[]
     */
    private static $Instances = array();
    
    /**
     * Loaded configuration
     * @var mixed[]
     */
    private $Configuration;
        
    /**
     * Set Current Environment
     * 
     * @param string $environment Environment
     * 
     * @return 
     */
    public static function setCurrentEnvironment($environment) {
        if (count(self::$Instances) > 0) {
            throw new \RuntimeException(
                'Configuration files are loaded before environment is set. Problable cause: ' .
                'Usage of configuration class in Bootstrapper::getEnvironment()'
            );
        }
        self::$CurrentEnvironment = $environment;        
    }   
   
    /**
     * Get \KoolDevelop\Configuration instance
     *
     * @param string $configuration Configuration
     * 
     * @return \KoolDevelop\Configuration
     */
    public static function getInstance($configuration) {
        if (!isset(self::$Instances[$configuration])) {
            self::$Instances[$configuration] = new self($configuration);
        }
          return self::$Instances[$configuration];
    }

    /**
     * Process INI file
     * 
     * @param string $filename Filename
     * 
     * @return mixed[] Parsed ini file
     */
    protected function parseIniFile($filename) {
        
        // Read configuration
        ob_start();
        require $filename;
        $ini = ob_get_clean();

        // Parse and store
        return parse_ini_string($ini, true);

    }

    /**
     * Constructor
     * 
     * @param string $configuration Configuration
     */
    protected function __construct($configuration) {        
        
        if (preg_match('/^[a-z_]+$/', $configuration) == 0) {
            throw new \InvalidArgumentException(__f("Invalid Configuration File",'kooldevelop'));
        }
        
        // First try to parse global configuration file
        if (file_exists(CONFIG_PATH . DS . $configuration . '.ini')) {
            $this->Configuration = $this->parseIniFile(CONFIG_PATH . DS . $configuration . '.ini');
        }
        
        // Then try to parse environment specific configuration file
        if (file_exists(CONFIG_PATH . DS . self::$CurrentEnvironment . DS . $configuration . '.ini')) {
            
            $environment_config = $this->parseIniFile(CONFIG_PATH . DS . self::$CurrentEnvironment . DS . $configuration . '.ini');
            
            if ($this->Configuration === null) {
                $this->Configuration = $environment_config;                
            } else {                            
                foreach(array_keys($environment_config) as $key) {
                    if (!isset($this->Configuration[$key])) {
                        $this->Configuration[$key] = $environment_config[$key];
                    } else {
                        $this->Configuration[$key] = array_merge($this->Configuration[$key], $environment_config[$key]);
                    }
                }                
            }
        }
        
        if ($this->Configuration === null) {
            $this->Configuration = array();         
        }
        
        

    }

    /**
     * Read Setting, return default if not found
     *
     * @param string $setting Setting
     * @param mixed  $default Default value
     *
     * @return mixed Setting value 
     */
    public function get($setting, $default = null) {

        if (preg_match('/^([a-zA-Z0-9_]+?)\.([a-zA-Z0-9_]+?)$/', $setting, $matches) != 0) {
            return isset($this->Configuration[$matches[1]][$matches[2]]) ? $this->Configuration[$matches[1]][$matches[2]] : $default;
        } else if (preg_match('/^([a-zA-Z0-9_]+)$/', $setting)) {
            return isset($this->Configuration[$setting]) ? $this->Configuration[$setting] : $default;
        } else {
            throw new \InvalidArgumentException(__f("Invalid setting name",'kooldevelop'));
        }

    }

    

}