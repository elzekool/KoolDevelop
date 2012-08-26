<?php
/**
 * File Cache Storage
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/

namespace KoolDevelop\Cache;

/**
 * File Cache Storage
 *
 * Stores cache files on disk in small text files. Path is configurable
 * with the [path] configuration option.
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Cache
 **/
class FileStorage implements \KoolDevelop\Cache\ICacheStorage
{

    /**
     * Basepath for files
     * @var string
     */
    private $Path;

    /**
     * Load Cache File
     *
     * @param string $file Filename
     *
     * @return string Data from file, null when not found/expired
     */
    private function _laadCacheFile($file) {

        if (!file_exists($file)) {
            return null;
        }
        $handle = fopen($file, 'r');

        // Look if expired
        $expire = stream_get_line($handle, 25, "\n");
        if ($expire < time()) {
            fclose($handle);
            unlink($file);
            return null;
        }

        $data = stream_get_contents($handle);
        fclose($handle);
        return $data;
    }

    /**
     * Instantiate new storage
     *
     * @param $options Options from configuration file
     */
    public function __construct($options) {
        if (!isset($options['path'])) {
            throw new \RuntimeException(__f('Path not configured for \KoolDevelop\Cache\FileStorage','kooldevelop'));
        }
        $this->Path = $options['path'] . DS;
        if (!file_exists($this->Path)) {
            mkdir($this->Path, 0777, true);
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
        if (preg_match('/^([a-z0-9_])+$/', $key) == 0) {
            throw new \InvalidArgumentException(__f("Invalid key for cache",'kooldevelop'));
        }
        $data = serialize($object);
        $expire = time() + (($expire === null) ? 60 : $expire);
        file_put_contents($this->Path . $key . '.filecache', $expire . "\n" . $data);
        @chmod($file, 0777);
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
        if (null === ($data = $this->_laadCacheFile($this->Path . $key . '.filecache'))) {
            return $default;
        }
        return unserialize($data);
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
        return (null === $this->_laadCacheFile($this->Path . $key . '.filecache'));
    }
}