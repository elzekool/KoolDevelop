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
    protected $View;

    /**
     * Get View
     *
     * @return \KoolDevelop\View\View View
     */
    public function getView() {
        return $this->View;
    }

    /**
     * Set View
     *
     * @param \KoolDevelop\View\View $view View
     * 
     * @return void
     */
    public function setView($view) {
        $this->View = $view;
    }

    

}
