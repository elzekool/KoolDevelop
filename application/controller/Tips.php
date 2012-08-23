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
     * View specific tip
     * 
     * @param int $id Tip Id
     * 
     * @return void
     */
    public function view($id) {
        
        // Get Container
        $tip_container = new \Model\TipContainer();
        
        // Load Tip
        if (null === ($tip = $tip_container->first(array('id' => $id)))) {
            throw new \KoolDevelop\Exception\NotFoundException(__f('Tip not found!'));
        }
        
        $this->View->setTitle(htmlspecialchars($tip->getTitle()));
        $this->View->set('tip', $tip);
        $this->View->setLayout('default');
        $this->View->setView('tips/view');
        $this->View->render();
        
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
            ->setPageSize(3)
            ->setDefaultBaseUrl(r()->getBase() . '/tips')
            ->setAllowedSortingFields(array('title', 'id'))
            ->setContainerModel(new \Model\TipContainer())
            ->paginate();

        $this->View->setTitle(__('Search for tips'));
        $this->View->setLayout('default');
        $this->View->setView('tips/index');
        $this->View->render();
    }

}

?>