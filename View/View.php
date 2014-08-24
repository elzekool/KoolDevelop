<?php
/**
 * View
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\View;

/**
 * View
 *
 * Base part of the Model-View-Container pattern. The view is responsible
 * for displaying data. Rending of a view consists of rendering a view file.
 * The rendered view file is then passed to a layout file, this is then render to
 * the browser. 
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
abstract class View extends \KoolDevelop\Observable implements \KoolDevelop\Configuration\IConfigurable
{

    /**
     * Layout
     * @var string
     */
    protected $Layout = 'default';

    /**
     * Page title
     * @var string
     */
    protected $PageTitle = '';

    /**
     * View
     * @var string
     */
    protected $View = null;

    /**
     * Vars for View
     * @var mixed[]
     */
    protected $ViewVars = array();

    /**
     * Placeholders
     * @var \Kooldevelop\View\Placeholder[]
     */
    protected $Placeholders = array();

    /**
     * Helpers
     * @var \Helper[]
     */
    protected $Helpers = array();

    /**
     * Var names that are invalid
     * @var array
     */
    private static $InvalidVariableNames = array(
        'view_content', 'page_title', 'Helpers', 'ViewVars', 'Layout', 'Placeholders', 'Helpers'
    );

    /**
     * Create new View instance
     */
    public function __construct() {
        $this->addObservable('beforeView');
        $this->addObservable('beforeLayout');
    }

    /**
     * Register new Helper
     * 
     * @param string   $name   Name
     * @param callable $helper Helper
     */
    public function registerHelper($name, callable $helper) {
        $this->Helpers[$name] = $helper;
    }


    /**
     * Get Page Title
     *
     * @return string Page Title
     */
    public function getTitle() {
        return $this->PageTitle;
    }

    /**
     * Set Page Title
     *
     * @param string $title Page Title
     *
     * @return void
     */
    public function setTitle($title) {
        $this->PageTitle = $title;
    }

    /**
     * Set Layout
     *
     * @param string $layout Layout
     *
     * @return void
     */
    public function setLayout($layout) {

        if (preg_match('/^[a-z](([a-z0-9_])+(\/|\\\)?([a-z0-9_])*)*[a-z0-9]$/', $layout) == false) {
            throw new \InvalidArgumentException(__f("Layout name contains invalid characters",'kooldevelop'));
        }

        $layout_file = \KoolDevelop\Configuration::getInstance('core')->get('path.layout') . DS . str_replace(array('\\', '/'), DS, $layout) . '.php';
        if (!file_exists($layout_file)) {
            throw new \KoolDevelop\Exception\FileNotFoundException(__f("Layout file not found",'kooldevelop'));
        }

        $this->Layout = $layout;
    }

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

        // Kijk of view wel bestaat
        $view_file = \KoolDevelop\Configuration::getInstance('core')->get('path.view') . DS . str_replace(array('\\', '/'), DS, $view) . '.php';
        if (!file_exists($view_file)) {
            throw new \KoolDevelop\Exception\FileNotFoundException(__f("View file not found",'kooldevelop'));
        }

        $this->View = $view;

    }


    /**
     * Set Var for View
     *
     * @param string $name  Name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function set($name, $value) {

        if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $name) == false) {
            throw new \InvalidArgumentException(__f("Variabele name contains invalid characters",'kooldevelop'));
        }

        if (in_array($name, self::$InvalidVariableNames)) {
            throw new \InvalidArgumentException(__f("Variabele name not allowed",'kooldevelop'));
        }

        // Save value
        $this->ViewVars[$name] = $value;
    }

    /**
     * Get Var set for View
     *
     * @param string $name    Name
     * @param mixed  $default Default value
     *
     * @return void
     */
    public function get($name, $default = null) {

        if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $name) == false) {
            throw new \InvalidArgumentException(__f("Variabele name contains invalid characters",'kooldevelop'));
        }

        if (in_array($name, self::$InvalidVariableNames)) {
            throw new \InvalidArgumentException(__f("Variabele name not allowed",'kooldevelop'));
        }
        
        if (!(array_key_exists($name, $this->ViewVars))) {
            return $default;
        }
        
        return $this->ViewVars[$name];

    }
    
    /**
     * Get placeholder
     *
     * @param string $name Placeholder name
     *
     * @return \KoolDevelop\View\Placeholder Placeholder
     */
    public function placeholder($name) {

        if (preg_match('/^[a-zA-Z0-9]+$/', $name) == false) {
            throw new \InvalidArgumentException(__f("Invalid placeholder name",'kooldevelop'));
        }

        if (isset($this->Placeholders[$name])) {
            return $this->Placeholders[$name];
        } else {
            return $this->Placeholders[$name] = new \KoolDevelop\View\Placeholder();
        }

    }

    /**
     * Get Helper
     *
     * @param string $name Helper name
     *
     * @return \Helper Helper
     */
    public function helper($name) {
        if (!isset($this->Helpers[$name])) {
            throw new \Exception(__f("Unkown helper " . $name,'kooldevelop'));
        }
        $helper = $this->Helpers[$name]();
        $helper->setView($this);
        return $helper;
    }

    /**
     * Render Element
     *
     * @param string  $element    Element
     * @param mixed[] $parameters Parameters
     *
     * @return void
     */
    protected function element($element, $parameters = array()) {

        $n_element = new \Element($this);
        $n_element->setView($element);
        foreach($parameters as $name => $value) {
            $n_element->set($name, $value);
        }
        $n_element->render();

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

        $page_title = $this->PageTitle;
        
        $this->fireObservable('beforeView');

        // Load/Render View
        ob_start();
        require \KoolDevelop\Configuration::getInstance('core')->get('path.view') . DS . str_replace(array('\\', '/'), DS, $this->View) . '.php';
        $view_content = ob_get_clean();

        $this->fireObservable('beforeLayout');

        // Load/Render Layout
        require \KoolDevelop\Configuration::getInstance('core')->get('path.layout') . DS . str_replace(array('\\', '/'), DS, $this->Layout) . '.php';

        // Unset View Content And Title
        unset($view_content);
        unset($page_title);

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
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'path.view', 'APP_PATH "" DS "view"', ('Path where View files are stored')),
            new \KoolDevelop\Configuration\IConfigurableOption('core', 'path.layout', 'APP_PATH "" DS "view" DS "layout"', ('Path where Layout files are stored'))
        );
    }

}
