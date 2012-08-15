<?php
/**
 * Literal expression route
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/


namespace KoolDevelop\Route;


/**
 * Literal expression route
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Literal implements \KoolDevelop\Route\IRoute
{
	/**
	 * Route to match
	 * @var string
	 */
	private $In;

	/**
	 * Output when matched
	 * @var string
	 */
	private $Out;

	/**
	 * Stop routing if matched
	 * @var bool
	 */
	private $Stop;

	/**
	 * Constructor
	 *
	 * @param <type> $in   Route to match
	 * @param <type> $out  Output on match
	 * @param <type> $stop Stop on match
	 */
	public function __construct($in, $out, $stop = false) {
		$this->In = $in;
		$this->Out = $out;
		$this->Stop = $stop;
	}

	/**
	 * Proces routing
	 *
	 * @param string $route Reference to current route
	 *
	 * @return bool Stop further processing
	 */
	public function route(&$route) {
		if ($route == $this->In) {
			$route = $this->Out;
			return $this->Stop;
		}
		return false;
	}

}
