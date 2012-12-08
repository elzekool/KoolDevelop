<?php
/**
 * APC Cache Storage
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/

namespace KoolDevelop\Cache;

/**
 * APC Cache Storage
 * 
 * Stores cache objects in APC Cache. 
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
     * Prefix used for APC
     * @var string
     */
    private $_Prefix;
    
    /**
     * Instantiate new storage
     *
     * @param $options Options from configuration file
     */
    public function __construct($options) {
        if (!isset($options['prefix'])) {
            $logger = \KoolDevelop\Log\Logger::getInstance();            
            $logger->severe('You should set an prefix for APC caching, generating prefix based on file path', 'KoolDevelop.Cache.APC');            
            $this->_Prefix = sha1(___FILE__);           
        } else {
            $this->_Prefix = $options['prefix'];
        }
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
        if (false === apc_store($this->_Prefix . $key, serialize($object), $expire === null ? 0 : $expire)) {
            // Do not throw an exception, but add it to the log
            $logger = \KoolDevelop\Log\Logger::getInstance();
            $logger->severe(sprintf('Error storing %s into APC cache', $key), 'KoolDevelop.Cache.APC');
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
        if (false !== ($serialized = apc_fetch($this->_Prefix . $key))) {
            return unserialize($serialized);
        }
        return $default;
    }

    /**
     * Check if storage object exists
     *
     * @return boolean Object exists and is valid
     */
    public function objectExists($key) {
        if(function_exists('apc_exists')) {
            return apc_exists($this->_Prefix . $key);
        } else {
            return (false === apc_fetch($this->_Prefix . $key));
        }
    }

}