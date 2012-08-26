<?php
/**
 * Email View
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Email
 **/

namespace KoolDevelop\View;

/**
 * Email View
 *
 * View used for sending e-mail messages.
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
abstract class EmailView extends \KoolDevelop\View\View implements \KoolDevelop\Configuration\IConfigurable
{
    /**
	 * Set View
	 *
	 * @param string $view View
	 *
	 * @return void
	 */
	public function setView($view) {

        if (preg_match('/^[a-z](([a-z0-9_])+(\/|\\\)?([a-z0-9_])*)*[a-z0-9]$/', $view) == false) {
			throw new \InvalidArgumentException(__f("View name contains invalid characters",'kooldevelop'));
		}

		// Check if file exists
		$view_file = \KoolDevelop\Configuration::getInstance('email')->get('core.view_path') . DS . str_replace(array('\\', '/'), DS, $view) . '.php';
		if (!file_exists($view_file)) {
			throw new \InvalidArgumentException(__f("Email view file not found",'kooldevelop'));
		}

		$this->View = $view;

    }

	/**
	 * Get Helper
	 *
	 * @param string $classname Helper classname
	 * @param string $namespace Namespace
	 *
	 * @return \Helper Helper
	 */
	public function helper($classname, $namespace = '\\View\\Helper\\') {
		return $this->Parent->helper($classname, $namespace);
	}


    /**
	 * View Renderen
	 *
	 * @return void
	 */
	public function render() {

		if ($this->View == null) {
			throw new \LogicException(__f("No view set to render",'kooldevelop'));
		}

		// Set View vars
		foreach ($this->ViewVars as $var_name => $var_value) {
			$$var_name = $var_value;
		}

		// Load/Render Element
        require \KoolDevelop\Configuration::getInstance('email')->get('core.view_path') . DS . str_replace(array('\\', '/'), DS, $this->View) . '.php';


		// Unset set Vars
		foreach ($this->ViewVars as $var_name => $var_value) {
			unset($$var_name);
		}

	}

    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array();
    }
    
    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {      
        return array(
            new \KoolDevelop\Configuration\IConfigurableOption('email', 'core.view_path', 'APP_PATH "" DS "view" DS "email"', ('Path where Email view files are stored'))
        );
    }


}
