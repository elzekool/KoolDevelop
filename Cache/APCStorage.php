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
class APCStorage implements \KoolDevelop\Cache\ICacheStorage
{
    /**
     * Prefix used for APC
     * @var string
     */
    private $Prefix;
    
    /**
     * Default Timeout
     * @var int
     */
    private $Timeout = 0;
    
    /**
     * Instantiate new storage
     *
     * @param $options Options from configuration file
     */
    public function __construct($options) {
        if (!isset($options['prefix'])) {
            $logger = \KoolDevelop\Log\Logger::getInstance();            
            $logger->severe('You should set an prefix for APC caching, generating prefix based on file path', 'KoolDevelop.Cache.APC');            
            $this->Prefix = sha1(___FILE__);           
        } else {
            $this->Prefix = $options['prefix'];
        }
        if (isset($options['timeout'])) {
            $this->Timeout = intval($options['timeout']);
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
        if (false === apc_store($this->Prefix . $key, serialize($object), $expire === null ? $this->Timeout : $expire)) {
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
        if (false !== ($serialized = apc_fetch($this->Prefix . $key))) {
            return unserialize($serialized);
        }
        return $default;
    }

    /**
     * Check if storage object exists
     *
     * @param string $key Key
     * 
     * @return boolean Object exists and is valid
     */
    public function objectExists($key) {
        if(function_exists('apc_exists')) {
            return apc_exists($this->Prefix . $key);
        } else {
            return (false === apc_fetch($this->Prefix . $key));
        }
    }
    
    /**
     * Delete Cache Object
     * 
     * @param string $key Key
     *  
     * @return void
     */
    public function deleteObject($key) {
        apc_delete($this->Prefix . $key);
    }

}