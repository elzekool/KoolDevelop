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
 * Placeholders persits content for displaying in views and layouts. Capture
 * content with start() and end() functions or add arbritairy content with add().
 * Read Placeholder as string (e.g. with echo) to retrieve collected content.
 *
 * A placeholder can be created/retrieved from a view with the placeholder() function
 *
 * A good use for placeholders are layout elements outside the view container (like sidebars)
 *
 * @see \KoolDevelop\View\View::placeholder()
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Placeholder
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
	 * Add arbritairy content
	 * 
	 * @param string  $content Content to add
	 * @param boolean $replace Replace current content, false to append
	 * 
	 * @retrun void
	 */
	public function add($content, $replace = false) {
		if ($this->Capturing) {
			throw new \RuntimeException(__f('Cannot add content to placeholder while capturing.'));
		}
		if ($replace) {
			$this->Contents = $content;
		} else {
			$this->Contents .= $content;
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
