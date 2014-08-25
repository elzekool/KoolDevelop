<?php

/**
 * Controller
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 * */

namespace KoolDevelop\Controller;

/**
 * Controller
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 * */
abstract class Controller {

    /**
     * Service Container
     * @var \Pimple\Container
     */
    protected $Container;

    /**
     * Constructor
     * 
     * @param \Pimple\Container $container Container
     */
    public function __construct($container) {
        $this->Container = $container;
    }
    

    /**
     * Action to perform
     * @var string
     */
    private $Action = 'index';

    /**
     * Parameters for action
     * @var array
     */
    private $Parameters = array();

    /**
     * Not allowed actions
     * @var array
     */
    private static $InvalidCommands = array(
        'init',
        'getAction', 'setAction', 'getParameters', 'setParameters',
        'runAction', 'getNamedParameters'
    );

    /**
     * Get action to perform
     *
     * @return string Action to perform
     */
    protected function getAction() {
        return $this->Action;
    }

    /**
     * Set action to perform
     *
     * @param string $action Action
     *
     * @return void
     */
    public function setAction($action) {

        if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $action) == false) {
            throw new \InvalidArgumentException(__f("Controller action contains invalid characters", 'kooldevelop'));
        }

        if (in_array($action, self::$InvalidCommands)) {
            throw new \InvalidArgumentException(__f("Controller action not allowed", 'kooldevelop'));
        }

        if (!method_exists($this, $action)) {
            throw new \KoolDevelop\Exception\NotFoundException(__f("Controller action not found", 'kooldevelop'));
        }

        // Check if action public
        $class_reflection = new \ReflectionClass($this);
        $method_reflection = $class_reflection->getMethod($action);
        if (!$method_reflection->isPublic()) {
            throw new \InvalidArgumentException(__f("Controller action not a public funtion", 'kooldevelop'));
        }

        // Sla nieuwe actie op
        $this->Action = $action;
    }

    /**
     * Fetch all parameters
     *
     * @return array
     */
    protected function getParameters() {
        return $this->Parameters;
    }

    /**
     * Set Parameters
     *
     * @param string[] $parameters Parameters
     *
     * @return void
     */
    public function setParameters($parameters) {
        $this->Parameters = $parameters;
    }

    /**
     * Call set Action
     *
     * @return void
     */
    public function runAction() {

        // Check if required parameters are set
        $class_reflection = new \ReflectionClass($this);
        $method_reflection = $class_reflection->getMethod($this->Action);

        $required_parameters = $method_reflection->getNumberOfRequiredParameters();
        $given_parameters = count($this->Parameters);

        if ($required_parameters > $given_parameters) {
            throw new \InvalidArgumentException(__f("Controller action requires " . $required_parameters . " parameters, " . $given_parameters . " given", 'kooldevelop'));
        }

        switch (count($this->Parameters)) {
            case 0:
                $this->{$this->Action}();
                break;
            case 1:
                $this->{$this->Action}($this->Parameters[0]);
                break;
            case 2:
                $this->{$this->Action}($this->Parameters[0], $this->Parameters[1]);
                break;
            case 3:
                $this->{$this->Action}($this->Parameters[0], $this->Parameters[1], $this->Parameters[2]);
                break;
            case 4:
                $this->{$this->Action}($this->Parameters[0], $this->Parameters[1], $this->Parameters[2], $this->Parameters[3]);
                break;
            case 5:
                $this->{$this->Action}($this->Parameters[0], $this->Parameters[1], $this->Parameters[2], $this->Parameters[3], $this->Parameters[4]);
                break;
            default:
                call_user_func_array(array(&$this, $this->Action), $this->Parameters);
                break;
        }

    }

}
