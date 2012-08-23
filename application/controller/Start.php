<?php
/**
 * Start Controller
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

namespace Controller;

/**
 * Start Controller
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/
final class Start extends \Controller
{

    public function __construct() {
        parent::__construct();
        $this->View->set('controller', 'start');
    }

    /**
     * Homepage of application
     *
     * @return void
     */
    public function index() {
        $this->View->setTitle(__('KoolDevelop - PHP Framework - Kool Software en Development'));
        $this->View->setLayout('default');
        $this->View->setView('start/index');
        $this->View->render();
    }

}

?>