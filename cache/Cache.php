<?php
/**
 * Cache
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @package Cache
 **/

namespace KoolDevelop\Cache;

/**
 * Cache
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @package Cache
 **/
class Cache
{

    /**
     * Cache Instance
     * @var \KoolDevelop\Cache\Cache[]
     */
    protected static $Instances = array();

    /**
     * Get Cache instance
     *
     * @param string $config Cache configuration identifier
     *
     * @return \KoolDevelop\Cache\Cache
     */
    public static function getInstance($config = 'default') {
        if (!isset(self::$Instances[$config])) {
            self::$Instances[$config] = new self($config);
        }
        return self::$Instances[$config];
    }

    /**
     * Storage voor cache
     * @var \KoolDevelop\Cache\ICacheStorage
     */
    private $Storage;

    /**
     * Load Storage
     *
     * @param string $storage_class Storage class to load
     * @param string $config        Configuration
     *
     * @return void
     */
    private function loadStorage($storage_class, $config) {

        if (!class_exists($storage_class)) {
            throw new \InvalidArgumentException(__f("Cache Storage class not found",'kooldevelop'));
        }

        $this->Storage = new $storage_class($config);
        if (!($this->Storage instanceOf \KoolDevelop\Cache\ICacheStorage)) {
            throw new \RuntimeException(__f("Cache storage not an instance of \KoolDevelop\Cache\ICacheStorage",'kooldevelop'));
        }
    }

    /**
     * Constructor
     *
     * @param string $config Configuration identifier
     */
    protected function __construct($config) {
        
        $configuration = \KoolDevelop\Configuration::getInstance('cache');
        if (null === ($cache_config = $configuration->get($config, null))) {
            return;
        }
        
        $this->loadStorage($cache_config['class'], $cache_config);        

    }

    /**
     * Save object to cache
     *
     * @param string $key    Key
     * @param mixed  $object Object
     * @param int    $expire Expiration time in seconds
     *
     * @return void
     */
    public function saveObject($key, $object, $expire = null) {
        if (null !== $this->Storage) {
            $this->Storage->saveObject($key, $object, $expire);
        }
    }

    /**
     * Load object from cache
     *
     * @param string $key     Key
     * @param mixed  $default Default value
     *
     * @return mixed
     */
    public function loadObject($key, $default = null) {
        if (null !== $this->Storage) {
            return $this->Storage->loadObject($key, $default);
        }
        return $default;
    }

    /**
     * Check if storage object exists
     *
     * @return boolean Object exists and is valid
     */
    public function objectExists($key) {
        if (null !== $this->Storage) {
            return $this->Storage->objectExists($key);
        }
        return false;
    }

}