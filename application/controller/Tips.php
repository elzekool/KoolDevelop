<?php
/**
 * Tips Controller
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

namespace Controller;

/**
 * Tips Controller
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/
final class Tips extends \Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->View->set('controller', 'tips');
    }
    
    /**
     * Search for Tips
     * 
     * @return void
     */
    public function index() {
                
        $pagination = $this->View->helper('Pagination');
        /* @var $pagination \View\Helper\Pagination */
        
                
        $pagination
            ->setContainerModel(new \Model\TipContainer())
            ->setSearchConditions(array('language' => 'en'))
            ->setAllowedSortingFields(array('title', 'id'))
            ->setPageSize(25)
            ->paginate();
        

        
        
        $this->View->setTitle(__('Search for tips'));
        $this->View->setLayout('default');
        $this->View->setView('tips/index');
        $this->View->render();
    }

}

?>