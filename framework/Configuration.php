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
 * the _CONFIG_PATH_ of your application. The .ini files are parsed with PHP so you
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
final class Configuration
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
	 * Constructor
     * 
     * @param string $configuration Configuration
	 */
	protected function __construct($configuration) {		
                
		if (preg_match('/^[a-z_]+$/', $configuration) == 0) {
			throw new \InvalidArgumentException(__f("Invalid Configuration File",'kooldevelop'));
		}
        
        // First check in envirionment location
        if (file_exists(_CONFIG_PATH_ . DS . self::$CurrentEnvironment . DS . $configuration . '.ini')) {
            $filename = _CONFIG_PATH_ . DS . self::$CurrentEnvironment . DS . $configuration . '.ini';            
        // Then check in default location
        } else if (file_exists(_CONFIG_PATH_ . DS . $configuration . '.ini')) {
            $filename = _CONFIG_PATH_ . DS . $configuration . '.ini';            
        } else {
            throw new \InvalidArgumentException(__f("Invalid Configuration File",'kooldevelop'));            
        }

		// Read configuration
		ob_start();
		require $filename;
		$ini = ob_get_clean();

		// Parse and store
		$this->Configuration = parse_ini_string($ini, true);

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