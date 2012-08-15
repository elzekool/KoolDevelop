<?php
/**
 * Placeholder
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\View;

/**
 * Placeholder
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
final class Placeholder
{

    /**
     * Is capturing
     * @var boolean
     */
    private $Capturing = false;

    /**
     * Replace current content, false to append
     * @var boolean
     */
    private $Replace = false;

    /**
     * Captured content
     * @var string
     */
    private $Contents = '';

    /**
     * Start capturing
     *
     * @param boolean $replace Replace current content, false to append
     *
     * @return void
     */
    public function start($replace = false) {

        if ($this->Capturing) {
            throw new \RuntimeException(__f('Already started capturing content','kooldevelop'));
        }

        $this->Replace = $replace;
        $this->Capturing = true;

        ob_start();

    }

    /**
     * End capturing content
     *
     * @return void
     */
    public function end() {

        if (!$this->Capturing) {
            throw new \RuntimeException(__f('Start capturing content first','kooldevelop'));
        }

        $this->Capturing = false;
        if ($this->Replace) {
            $this->Contents = ob_get_clean();
        } else {
            $this->Contents .= ob_get_clean();
        }

    }

    /**
     * Return contents
     *
     * @return string
     */
    public function __toString() {
        return $this->Contents;
    }

}
