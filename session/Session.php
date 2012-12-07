<?php
/**
 * Session
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Session;

/**
 * Session
 * 
 * Base session control. Allows one ore more Session Storage objects to be registrated.
 * Registrate new Session Storage objects with registerSessionStorage or trough session.ini
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Session implements \KoolDevelop\Configuration\IConfigurable
{
	/**
	 * Singleton Instance
	 * @var \KoolDevelop\Session\Session
	 */
	private static $Instance;

	/**
	 * Registered session storage handlers
	 * @var KoolDevelop\Session\ISessionStorage[]
	 */
	private $Storage = array();

	/**
	 * Get \KoolDevelop\Session\Session instance
	 *
	 * @return \KoolDevelop\Session\Session
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
        $config = \KoolDevelop\Configuration::getInstance('session');
        foreach($config->get('storage', array()) as $name => $classname) {
            $storage = new $classname();
            $this->registerSessionStorage($name, $storage);
            
            $logger = \KoolDevelop\Log\Logger::getInstance();
            $logger->low(sprintf('Registered new Session storage: %s => %s', $name, $classname), 'KoolDevelop.Session.');
        }
	}

	/**
	 * Unify Storage argument to an array
	 *
	 * @param  null|string|string[] $storage Storage Argument
	 *
	 * @return string[] Storage Handlers
	 */
	private function _unifyStorageArgument($storage) {
		if ($storage === null) {
			return array_keys($this->Storage);
		} else if (is_string($storage)) {
			return array($storage);
		}
		return $storage;
	}

	/**
	 * Register new storage Handler
	 *
	 * @param string                                      $id      Identifier
	 * @param \KoolDevelop\Session\Session\ISessionStorage $storage Storage Handler
	 *
	 * @return void
	 */
	public function registerSessionStorage($id, \KoolDevelop\Session\ISessionStorage &$storage) {
		if (isset($this->Storage[$id])) {
			throw new \RuntimeException(__f('Session Storage Id already defined','kooldevelop'));
		}
		$this->Storage[$id] = $storage;
	}

	/**
	 * Set Value
	 * 
	 * @param string               $id      Identifier
	 * @param mixed                $value   Value
	 * @param int                  $timeout Timeout in sec of 0 for browser session
	 * @param null|string|string[] $storage Storage Handlers
	 */
	public function set($id, $value, $timeout = 0, $storage = null) {
		$storage = $this->_unifyStorageArgument($storage);
		foreach($storage as $storage_handler) {
			if (!isset($this->Storage[$storage_handler])) {
				throw new \InvalidArgumentException(__f('Session storage Handler Not Found','kooldevelop'));
			}
			$this->Storage[$storage_handler]->set($id, $value, $timeout);
		}
	}

	/**
	 * Get Value
	 *
	 * @param string               $id      Identifier
	 * @param mixed                $default Default value
	 * @param null|string|string[] $storage Storage Handlers
	 *
	 * @return mixed Value
	 */
	public function get($id, $default = null, $storage = null) {
		$storage = $this->_unifyStorageArgument($storage);
		foreach($storage as $storage_handler) {
			if (!isset($this->Storage[$storage_handler])) {
				throw new \InvalidArgumentException(__f('Session storage Handler Not Found','kooldevelop'));
			}
			if ($this->Storage[$storage_handler]->exists($id)) {
				return $this->Storage[$storage_handler]->get($id);
			}
		}
		return $default;
	}

	/**
	 * Check if Value Exists
	 *
	 * @param string               $id      Identifier
	 * @param null|string|string[] $storage Storage Handlers
	 *
	 * @return boolean Exists
	 */
	public function exists($id, $storage = null) {
		$storage = $this->_unifyStorageArgument($storage);
		foreach($storage as $storage_handler) {
			if (!isset($this->Storage[$storage_handler])) {
				throw new \InvalidArgumentException(__f('Session storage Handler Not Found','kooldevelop'));
			}
			if ($this->Storage[$storage_handler]->exists($id)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Unset Value
	 *
	 * @param string               $id      Identifier
	 * @param null|string|string[] $storage Storage Handlers
	 *
	 * @return void
	 */
	public function del($id, $storage = null) {
		$storage = $this->_unifyStorageArgument($storage);
		foreach($storage as $storage_handler) {
			if (!isset($this->Storage[$storage_handler])) {
				throw new \InvalidArgumentException(__f('Session storage Handler Not Found','kooldevelop'));
			}
			if ($this->Storage[$storage_handler]->exists($id)) {
				$this->Storage[$storage_handler]->del($id);
			}
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
            new \KoolDevelop\Configuration\IConfigurableOption('session', 'storage.Default', '"\KoolDevelop\Session\Php"', ('Define your session storage here. Add new options to define a new one.'))
        );
    }
    
}