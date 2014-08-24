<?php
/**
 * Memcache Cache Storage
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/

namespace KoolDevelop\Cache;

/**
 * Memcache Cache Storage
 * 
 * Stores cache objects in Memcache Cache. 
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/
class MemcacheStorage implements \KoolDevelop\Cache\ICacheStorage
{
    /**
     * Prefix
     * @var string
     */
    private $Prefix;
    
    /**
     * Default Timeout
     * @var int
     */
    private $Timeout = 0;   
    
    /**
     * Memcache connection
     * @var \Memcache
     */
    private $Memcache;
    
    /**
     * Instantiate new storage
     *
     * @param $options Options from configuration file
     */
    public function __construct($options) {
        
        if (!isset($options['prefix'])) {
            $logger = \KoolDevelop\Log\Logger::getInstance();            
            $logger->severe('You should set an prefix for Memcache caching, generating prefix based on file path', 'KoolDevelop.Cache.Memcache');            
            $this->Prefix = sha1(___FILE__);           
        } else {
            $this->Prefix = $options['prefix'];
        }
        
        $this->Memcache = new \Memcache;
        if (!isset($options['servers'])) {
            throw new \KoolDevelop\Exception\Exception(__f('No servers found for Memcache Cache Storage', 'kooldevelop'));
        }
        
        // Add servers to the pool
        foreach($options['servers'] as $server) {
            if (preg_match('/^(.+):([0-9]+)$/', $server, $matches)) {
                $this->Memcache->addserver($matches['1'], $matches[2], false);
            } else {
                $this->Memcache->addserver($server, 11211, false);
            }
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
        $data = serialize($object);
        if (false === @$this->Memcache->replace($this->Prefix . $key, $data, 0, $expire === null ? $this->Timeout : $expire)) {
            if (false === @$this->Memcache->set($this->Prefix . $key, $data, 0, $expire === null ? $this->Timeout : $expire)) {
                // Do not throw an exception, but add it to the log
                $logger = \KoolDevelop\Log\Logger::getInstance();
                $logger->severe(sprintf('Error storing %s into Memcache cache', $key), 'KoolDevelop.Cache.Memcache');
            }
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
        if (false !== ($serialized = @$this->Memcache->get($this->Prefix . $key))) {
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
        if (false !== ($serialized = @$this->Memcache->get($this->Prefix . $key))) {
            return true;
        } else {
            return false;
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
        @$this->Memcache->delete($this->Prefix . $key);
    }

}