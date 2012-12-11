<?php
/**
 * Session Storage Interface
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @package Core
 **/

namespace KoolDevelop\Session;

/**
 * Session Storage Interface
 *
 * Implement this interface for usage in combination with \KoolDevelop\Session
 * class.
 * 
 * @see \KoolDevelop\Session\Session
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @package Core
 **/
interface ISessionStorage
{
    /**
     * Get value
     *
     * @param string $id      Identifier
     * @return mixed Value
     */
    public function get($id);

    /**
     * Set value
     *
     * @param string $id    Identifier
     * @param mixed  $value Value
     * @param int    $timeout Timeout in seconds, 0 browser session
     *
     * @return void
     */
    public function set($id, $value, $timeout);

    /**
     * Check if Value exists
     *
     * @param string $id Identifier
     *
     * @return boolean Exists
     */
    public function exists($id);

    /**
     * Delete Value
     *
     * @param string $id Identifier
     *
     * @return void
     */
    public function del($id);

}