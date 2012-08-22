<?php
/**
 * Pagination Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/

namespace View\Helper;

/**
 * Pagination Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/
class Pagination extends \Helper
{
    /**
     * ContainerModel
     * @var \KoolDevelop\Model\ContainerModel
     */
    private $ContainerModel;    
    
    /**
     * Page Size
     * @var int
     */
    private $PageSize = 25;
    
    /**
     * Search Conditions
     * @var mixed[]
     */
    private $SearchConditions = array();
    
    /**
     * Allowed Sorting Field. First field given is default and failback
     * @var string[]
     */
    private $AllowedSortingFields = array('id');
    
    /**
     * Parameters
     * @var string[]
     */
    private $Parameters = array();
    
    /**
     * Set ContainerModel
     * 
     * @param \KoolDevelop\Model\ContainerModel $ContainerModel
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setContainerModel(\KoolDevelop\Model\ContainerModel &$ContainerModel) {
        $this->ContainerModel =& $ContainerModel;
        return $this;
    }

    /**
     * Set Page Size
     * 
     * @param int $PageSize Page Size
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setPageSize($PageSize) {
        $this->PageSize = $PageSize;
        return $this;
    }

    /**
     * Set Search Conditions
     * 
     * @param mixed[] $SearchConditions Search Conditions
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setSearchConditions($SearchConditions) {
        $this->SearchConditions = $SearchConditions;
        return $this;
    }
    
    /**
     * Set Allowed Sorting Fields 
     * 
     * The first field given is default and failback.
     * 
     * @param string[] $AllowedSortingFields Allowed Sorting Fields
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setAllowedSortingFields($AllowedSortingFields) {
        $this->AllowedSortingFields = $AllowedSortingFields;
        return $this;
    }

    /**
     * Get Parameter
     * 
     * Get Parameter using the following rules
     * - Load with given default
     * - Fetch from preset
     * - Check for named parameter, update if set
     * - Check for $_POST parameter, update if set
     * 
     * @param string  $name    Name
     * @param mixed[] $default Default value
     * 
     * @return mixed[] Parameter value
     */
    public function getParameter($name, $default = null) {
        
        $value = $default;
        $named = \KoolDevelop\Router::getInstance()->getNamedParameters();
        
        if (array_key_exists($name, $this->Parameters)) {
            $value = $this->Parameters[$name];
        }
        
        if (array_key_exists($name, $named)) {
            $value = $named[$name];
        }
        
        if (array_key_exists($name, $_POST)) {
            $value = $_POST[$name];
        }
        
        return $value;
                
    }
        
    /**
     * Get Link to sort on field
     * 
     * 
     * @param string $field       Field
     * @param string $default_dir Default direction (ASC/DESC)
     * 
     * @return string URL
     */
    public function getSortLink($field, $default_dir = 'ASC') {
        
    }
    
    /**
     * Paginate
     * 
     * Load items from Container and set the following View vars
     * $paginate_count = Total number of items
     * $paginate_page  = Current page
     * $paginate_pages = Total number of pages
     * $paginate_items = Items from ContainerModel
     * 
     * @return \View\Helper\Pagination Self
     */
    public function Paginate() {
        
        $page = $this->getParameter('page', 0);
        $sort = $this->getParameter('sort', $this->AllowedSortingFields[0]);
        $direction = strtoupper($this->getParameter('direction', 'ASC'));
        
        $count = $this->ContainerModel->count($this->SearchConditions);
        $pages = ceil($count / $this->PageSize);        
        
        if ($page >= $pages) {
            $page = $pages - 1;
        }
        
        if (!in_array($sort, $this->AllowedSortingFields)) {
            $sort = $this->AllowedSortingFields[0];
        }
        
        if (!in_array($direction, array('ASC', 'DESC'))) {
            $direction = 'ASC';
        }
        
        $items = $this->ContainerModel->index(
            $this->SearchConditions,
            array(array($sort,$direction)), 
            $this->PageSize, 
            $page * $this->PageSize
        );
        
        $this->getView()->set('paginate_count', $count);
        $this->getView()->set('paginate_page', $page);
        $this->getView()->set('paginate_pages', $pages);
        $this->getView()->set('paginate_items', $items);
        
        
    }
    
    
}