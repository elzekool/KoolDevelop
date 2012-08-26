<?php
/**
 * Interface for Router routings
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Route;

/**
 * Interface for Router routings
 *
 * Implement this interface to create your own route modifiers. 
 * 
 * @see \KoolDevelop\Router
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
interface IRoute
{
	/**
	 * Proces routing
	 *
	 * @param string $route Reference to current route
	 *
	 * @return boolean Stop further processing
	 */
	public function route(&$route);
}