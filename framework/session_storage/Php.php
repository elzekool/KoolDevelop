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

namespace KoolDevelop\SessionStorage;

/**
 * PHP Session Storage
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
final class Php implements \KoolDevelop\SessionStorage\ISessionStorage
{
	/**
	 * Constructor
	 */
	public function __construct() {
		@session_start();
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