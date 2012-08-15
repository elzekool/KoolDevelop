<?php
/**
 * Observable
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * Observable
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Observable
{
	/**
	 * Observers
	 * @var mixed[][]
	 */
	private $Observers = array();

	/**
	 * Add observable
	 * 
	 * @param string $name Name
	 *
	 * @return void
	 */
	protected function addObservable($name) {

		if (preg_match('/^[a-zA-Z]+$/', $name) == 0) {
			throw new \InvalidArgumentException(__f("Invalid Observable name",'kooldevelop'));
		}

		if (!isset($this->Observers[$name])) {
			$this->Observers[$name] = array();
		}
		
	}

	/**
	 * Execute Observers for Observable 
	 * 
	 * @param string $observable Observable
	 * @param bool   $stopontrue When enabled stops fireing when observer returns true
	 * 
	 * @return void
	 */
	protected function fireObservable($observable, $stopontrue = false) {

		if (!isset($this->Observers[$observable])) {
			throw new \InvalidArgumentException(__f("Invalid observable",'kooldevelop'));
		}

		foreach($this->Observers[$observable] as $index => &$observer) {
			$arguments = array();
			if (func_num_args() > 2) {
				for($x = 2; $x < func_num_args(); $x++) {
					$arguments[] = func_get_arg($x);
				}
			}
			$result = call_user_func_array($observer['callback'], $arguments);
			if ($stopontrue AND $result) {
				return;
			}
		}

	}

	/**
	 * Add observer for a specific observable. Returns unique observer id
	 * that can be used for deleting observer
	 *
	 * @param string $observable Observable
	 * @param <type> $callback   Callback
	 * @param <type> $prepend    True to prepend, false to append
	 *
	 * @return string Unique observer id
	 */
	public function addObserver($observable, $callback, $prepend = false) {
		
		if (!isset($this->Observers[$observable])) {
			throw new \InvalidArgumentException(__f("Invalid observable",'kooldevelop'));
		}

		$observer = array(
			'uuid'     => uniqid("", true),
			'callback' => $callback
		);

		if ($prepend) {
			array_unshift($this->Observers[$observable], $observer);
		} else {
			array_push($this->Observers[$observable], $observer);
		}

		return $observer['uuid'];
	}

	/**
	 * Clear specific or all observers for a observable
	 *
	 * @param string $observable Observable
	 * @param string $id Unique observer id returned by addObserver, null to delete all
	 *
	 * @return void
	 */
	public function clearObservers($observable, $id = null) {

		if (!isset($this->Observers[$observable])) {
			throw new \InvalidArgumentException(__f("Invalid observable",'kooldevelop'));
		}

		if ($id === null) {
			$this->Observers[$observable] = array();
		} else {
			foreach($this->Observers[$observable] as $index => &$observer) {
				if ($observer['uuid'] == $id) {
					unset($this->Observers[$observable][$index]);
					return;
				}
			}
		}
	}

	
}