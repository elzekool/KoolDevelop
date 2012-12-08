<?php
/**
 * ViewConfig Annotation
 * 
 * @author Elze Kool    
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Controller
 **/

namespace Controller\Annotation;

/**
 * ViewConfig Annotation
 * 
 * Allows setting the View parameters in a declerative manner
 * Example annotation: @ViewConfig({AutoRender = true, View = '/test/index', Layout = "default"})
 * 
 * @author Elze Kool    
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Controller
 **/
class ViewConfig implements \KoolDevelop\Annotation\IAnnotation
{
    /**
     * Auto Render View
     * @var boolean
     */
    private $AutoRender = null;
    
    /**
     * View File
     * @var string
     */
    private $View = null;
    
    /**
     * Layout File
     * @var string
     */
    private $Layout = null;

    /**
     * Title
     * @var string
     */
    private $Title = null;

    
    /**
     * Construct
     * 
     * @param mixed[] $settings Settings
     */
    function __construct($settings) {     
        if (isset($settings['AutoRender'])) {
            $this->AutoRender = $settings['AutoRender'];
        }        
        if (isset($settings['View'])) {
            $this->View = $settings['View'];
        }
        if (isset($settings['Layout'])) {
            $this->Layout = $settings['Layout'];
        }
        if (isset($settings['Title'])) {
            $this->Title = $settings['Title'];
        }
    }

    /**
     * Get Auto Render
     * 
     * @return boolean Auto Render
     */
    public function getAutoRender() {
        return $this->AutoRender;
    }

    /**
     * Get View File
     * 
     * @return string View File
     */
    public function getView() {
        return $this->View;
    }

    /**
     * Get Layout File
     * 
     * @return string Layout File
     */
    public function getLayout() {
        return $this->Layout;
    }
    
    /**
     * Get Title
     * 
     * @return string Title
     */
    public function getTitle() {
        return $this->Title;
    }


}

?>
