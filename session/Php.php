<?php
/**
 * PHP Session Storage
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Session;

/**
 * PHP Session Storage
 * 
 * Default PHP Session storage with added security (session fiaxation and hijacking
 * prevention messures). 
 * 
 * @see \KoolDevelop\Session\Session
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Php implements \KoolDevelop\Session\ISessionStorage
{

	/**
	 * Constructor
	 */
	public function __construct() {

        
        @session_start();        
        
        // Prevent session fixation
        if (!isset($_SESSION['PhpSessionStorageInitiated'])) {
            session_regenerate_id();
            $_SESSION['PhpSessionStorageInitiated'] = true;
        }
        
        // Prevent session hijacking
        if (!isset($_SESSION['PhpSessionStorageUA']) OR !isset($_SESSION['PhpSessionStorageIP'])) {
            $_SESSION['PhpSessionStorageUA'] = sha1(@$_SERVER['HTTP_USER_AGENT']);
            $_SESSION['PhpSessionStorageIP'] = sha1(@$_SERVER['REMOTE_ADDR']);
            return;
            
        // We mark a session save when user agent matches or remote IP matches
        } else if ($_SESSION['PhpSessionStorageUA'] == sha1(@$_SERVER['HTTP_USER_AGENT'])) {
            // Session considerd safe
            return;
        } else if ($_SESSION['PhpSessionStorageUA'] == sha1(@$_SERVER['REMOTE_ADDR'])) {
            // Session considerd safe
            return;
        }
        
        // Session is unsafe, destroy it and die
        session_destroy();
        $_SESSION = array();
        die("SESSION CORRUPTED");
        
	}

	/**
	 * Get value
	 *
	 * @param string $id      Identifier
	 * @param mixed  $default Result when value not found
	 *
	 * @return mixed Value
	 */
	public function get($id) {
		return $_SESSION[$id];
	}


	/**
	 * Set value
	 *
	 * @param string $id    Identifier
	 * @param mixed  $value Value
	 * @param int    $timeout Timeout in seconds, 0 browser session
	 *
	 * @return void
	 */
	public function set($id, $value, $timeout = 0) {
		if ($timeout != 0) {
			throw new \RuntimeException(__f('Timeout not allowed for PHP Session','kooldevelop'));
		}
		$_SESSION[$id] = $value;
	}

	/**
	 * Check if Value exists
	 *
	 * @param string $id Identifier
	 *
	 * @return boolean Exists
	 */
	public function exists($id) {
		return isset($_SESSION[$id]);
	}

	/**
	 * Delete Value
	 *
	 * @param string $id Identifier
	 *
	 * @return void
	 */
	public function del($id) {
		unset($_SESSION[$id]);
	}

}