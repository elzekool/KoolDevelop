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
    private $AutoRender = false;
    
    /**
     * View File
     * @var string
     */
    private $View = '';
    
    /**
     * Layout File
     * @var string
     */
    private $Layout = 'Layout';
    
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
    }

}

?>
