<?php
/**
 * Router
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop;

/**
 * Router
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
final class Router extends \KoolDevelop\Observable
{
	/**
	 * RegEx for named parameters
	 * 
	 */
	const NAMED_PARAMETER_REGEG = '/^(.*):(.*)$/U';

	/**
	 * Singleton Instance
	 * @var \KoolDevelop\Router
	 */
	private static $Instance;

	/**
	 * Registered routes
	 * @var \KoolDevelop\Route\IRoute[]
	 */
	private $Routes = array();

	/**
	 * URL after routing
	 * @var string 
	 */
	private $Url = null;

	/**
	 * Parameters after routing
	 * @var string[]
	 */
	private $Parameters = null;

	/**
	 * Get \KoolDevelop\Router instance
	 *
	 * @return \KoolDevelop\Router
	 */
	public static function getInstance() {
		if (self::$Instance === null) {
        	self::$Instance = new self();
      	}
      	return self::$Instance;
    }


	/**
	 * Get Base URL
	 *
	 * @param boolean $secure Get secure URL
	 *
	 * @return string Base URL
	 */
	public function getBase($secure = false) {
		$config = \KoolDevelop\Configuration::getInstance('core');

		if ($secure) {
			if (null === ($url = $config->get('url.secure_base'))) {
				$url = 'https://' . $_SERVER['HTTP_HOST'];
			}
		} else {
			if (null === ($url = $config->get('url.base'))) {
				$url = 'http://' . $_SERVER['HTTP_HOST'];
			}
		}

		return $url;
	}

	/**
	 * Get URL after routing
	 *
	 * @return string URL
	 */
	public function getUrl($base = false) {
		if ($this->Url === null) {
			throw new \RuntimeException(__f('Routing not performed yet!','kooldevelop'));
		}
		return ($base ? $this->getBase() : '') . $this->Url;
	}

	/**
	 * Get Parameters after routing
	 *
	 * @return string[] Parameters
	 */
	public function getParameters() {
		if ($this->Parameters === null) {
			throw new \RuntimeException(__f('Routing not performed yet','kooldevelop'));
		}
		return $this->Parameters;
	}

	/**
	 * Get list of namedparameters
	 *
	 * @return string[] Named parameters
	 */
	public function getNamedParameters() {

		$parameters = array();
		$matches = array();

		foreach($this->getParameters() as $parameter) {
			if (preg_match(self::NAMED_PARAMETER_REGEG, $parameter, $matches) > 0) {
				$parameters[$matches[1]] = $matches[2];
			}
		}

		return $parameters;
	}

	public function getNamedUrl($parameters = array(), $base = null, $reset = false) {
		$params = $reset ? array() : $this->getNamedParameters();
		foreach($parameters as $key => $value) {
			if (empty($value)) {
				unset($params[$key]);
			} else {
				$params[$key] = $value;
			}
		}
		
		if ($base === null) {
			$base = '';
			foreach(explode('/', $this->getUrl()) as $url_part) {
				if (\preg_match(self::NAMED_PARAMETER_REGEG, $url_part)) {
					break;
				}
				$base .= $url_part . '/';
			}
		}

		$url = $base;
		foreach($params as $key => $value) {
			$url .= urlencode($key) . ':' . urlencode($value) . '/';
		}

		return $url;
		
	}

	/**
	 * Constructor
	 */
	private function __construct() {
        $this->addObservable('beforeLoadController');
        $this->addObservable('afterLoadController');
	}

	/**
	 * Load Controller
	 * 
	 * @param string $controller_name Controller name from url
	 * 
	 * @return \Controller
	 */
	private function loadController($controller_name) {

		if ((preg_match('/^([a-z0-9_])*$/', $controller_name) == false)) {
			throw new \InvalidArgumentException(__f("Controller name contains invalid characters",'kooldevelop'));
		}

		$controller_name = '\\Controller\\' . \KoolDevelop\StringUtilities::camelcase($controller_name);

		if (!class_exists($controller_name)) {
			throw new \KoolDevelop\Exception\NotFoundException(__f("Controller not found",'kooldevelop'));
		}

		$controller = new $controller_name();
		if (!($controller instanceof \Controller)) {
			throw new \KoolDevelop\Exception\InvalidClassException(__f("Controller class not instance of \Controller",'kooldevelop'));
		}

		return $controller;
		
	}

	/**
	 * Add new Route element
	 * 
	 * @param \KoolDevelop\Route\IRoute $route Route
	 * 
	 * @return void
	 */
	public function addRoute(\KoolDevelop\Route\IRoute $route) {
		$this->Routes[] = $route;
	}

	/**
	 * Route URL
	 *
	 * @param string $url
	 */
	public function route($url) {

		// Unify url
		$url = str_replace('\\', '/', $url);

		// Make sure url starts with '/'
		if ((strlen($url) == 0) OR ($url[0] != '/')) {
			$url = '/' . $url;
		}

		// Make sure url does not end with '/' 
		if ((strlen($url) > 1) AND ($url[strlen($url)-1] == '/')) {
			$url = substr($url, 0, -1);
		}


		// Proces route
		foreach($this->Routes as &$route) {
			if ($route->route($url)) {
				break;
			}
		}

		

		$url_explode = explode('/', $url);
		if (count($url_explode) < 3) {
			throw new \KoolDevelop\Exception\NotFoundException(__f("Invalid Route",'kooldevelop'));
		}

		$route = array(
			'controller' => $url_explode[1],
			'action' => $url_explode[2],
			'parameters' => array()
		);
		
		for ($i = 0; $i < 10; $i++) {
			if (isset($url_explode[$i+3]) AND (strlen($url_explode[$i+3]) != 0)) {
				$route['parameters'][] = $url_explode[$i+3];
			}
		}

		$this->Url = $url;
		$this->Parameters = $route['parameters'];

        $this->fireObservable('beforeLoadController', false, $route);
		$controller = $this->loadController($route['controller']);
        $this->fireObservable('afterLoadController', false, $route, $controller);
        
		$controller->setAction($route['action']);
		$controller->setParameters($route['parameters']);
        
		$controller->runAction();
		
	}

}
