<?php
/**
 * Regular expression route
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Route;

/**
 * Regular expression route
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class RegEx implements \KoolDevelop\Route\IRoute
{
	/**
	 * Regular expression to match
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
	 * @param <type> $in   Regular expression to match
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
		$matches = array();

		// Check match
		if (preg_match($this->In, $route, $matches) > 0) {
			$route = $this->Out;
			foreach ($matches as $match_id => $match_value) {
				$route = str_replace('$' . $match_id, $match_value, $route);
			}

			return $this->Stop;
		}

		return false;
	}

}
