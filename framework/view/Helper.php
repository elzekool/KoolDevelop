<?php
/**
 * Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\View;

/**
 * Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
abstract class Helper
{

	/**
	 * Parent View
	 * @var \KoolDevelop\View\View
	 */
	private $View;

	/**
	 * Get View
	 *
	 * @return \KoolDevelop\View\View View
	 */
	public function getView() {
		return $this->View;
	}

	/**
	 * Constructor
	 *
	 * @param \KoolDevelop\View\View $View View
	 */
	function __construct(\KoolDevelop\View\View &$View) {
		$this->View = $View;
	}


}
