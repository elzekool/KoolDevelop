<?php
/**
 * In memory Cache Storage
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/

namespace KoolDevelop\Cache;

/**
 * In memory Cache Storage
 * 
 * Stores cache objects in memory. Use this for single request caching.
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/
class MemoryStorage implements \KoolDevelop\Cache\ICacheStorage
{

    /**
     * Cached data
     * @var string
     */
    private $Cache = array();

    /**
     * Instantiate new storage
     *
     * @param $options Options from configuration file
     */
    public function __construct($options) {       
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
    public function saveObject($key, $object, $expire) {
        if (preg_match('/^([a-z0-9_])+$/', $key) == 0) {
            throw new \InvalidArgumentException(__f("Invalid key for cache",'kooldevelop'));
        }               
        if ($expire !== null) {
            throw new \InvalidArgumentException(__f("Expire not supported by MemoryCache",'kooldevelop'));
        }
        $this->Cache[$key] = $object;
        
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
        if (preg_match('/^([a-z0-9_])+$/', $key) == 0) {
            throw new \InvalidArgumentException(__f("Invalid key for cache",'kooldevelop'));
        }
        
        if (array_key_exists($key, $this->Cache)) {
            return $this->Cache[$key];
        } else {
            return $default;
        }
    }

    /**
     * Check if storage object exists
     *
     * @return boolean Object exists and is valid
     */
    public function objectExists($key) {
        if (preg_match('/^([a-z0-9_])+$/', $key) == 0) {
            throw new \InvalidArgumentException(__f("Invalid key for cache",'kooldevelop'));
        }
        return array_key_exists($key, $this->Cache);
    }
}