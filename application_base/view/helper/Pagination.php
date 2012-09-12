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
	 * Default Sorting Field
	 * @var string
	 **/
	private $DefaultSortField = 'id';
	
	/**
	 * Default Sorting Direction
	 * @var string
	 **/
	private $DefaultSortDirection = 'ASC';
	    
    /**
     * Parameters
     * @var string[]
     */
    private $BaseParameters = array();
    
    /**
     * Default Base URL
     * @var string
     */
    private $DefaultBaseUrl = null;
    
    /**
     * Current page number
     * @var int
     */
    private $CurrentPageNumber = 0;
    
    /**
     * Number of pages
     * @var int
     */
    private $NumberOfPages = 0;    
    
    /**
     * Session storage name
     * @var string
     */
    private $SessionStorageName = null;
    
    /**
     * Pararameters to store in session
     * @var string[]
     */
    private $SessionStorageParameters = array('sort', 'direction');    
    
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
     * Set Base Parameters
     * 
     * @param string[] $parameters Base parameters
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setBaseParameters($parameters) {
        $this->BaseParameters = $parameters;
        return $this;
    }
    
    /**
     * Set Default Base URL
     * 
     * @param string $base Base URL
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setDefaultBaseUrl($base) {
        $this->DefaultBaseUrl = $base;
        return $this;
    }
    
	/**
	 * Set Default Sorting
	 *
	 * @param string $field     Default sorting Field
	 * @param string $direction Set default sorting direction (ASC|DESC)
	 *
	 * @return \View\Helper\Pagination Self
     */
	public function setDefaultSorting($field, $direction = 'ASC') {
		$this->DefaultSortField = $field;
		$this->DefaultSortDirection = $direction;
		return $this;
	}
    
    /**
     * Set Session storage options.
     * 
     * Sets the session storage options. The name is used to uniquely identify
     * the pagination options.. Also the stored parameters are configurable. 
     * Don't forget to include 'sort' and 'direction' parameters (if needed).
     * 
     * @param string $name       Unique name for pagination settings, use null to disable storage
     * @param string $parameters Parameters to store
     * 
     * @return \View\Helper\Pagination Self
     */
    public function setSessionStorage($name = null, $parameters = array('sort', 'direction')) {
        $this->SessionStorageName = $name;
        $this->SessionStorageParameters = $parameters;
        return $this;
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
    public function paginate() {
        
        $page = $this->getParameter('page', 0);
        $sort = $this->getParameter('sort', $this->DefaultSortField);
        $direction = strtoupper($this->getParameter('direction', $this->DefaultSortDirection));
        
        $count = $this->ContainerModel->count($this->SearchConditions);
        $pages = ceil($count / $this->PageSize);        
        
        if ($page >= $pages) {
            $page = $pages - 1;
        }
        
        if ($page < 0) {
            $page = 0;
        }
        
        if (!in_array($sort, $this->AllowedSortingFields)) {
            $sort = $this->DefaultSortField;
        }
        
        if (!in_array($direction, array('ASC', 'DESC'))) {
            $direction = $this->DefaultSortDirection;
        }
        
        $items = $this->ContainerModel->index(
            $this->SearchConditions,
            array(array($sort,$direction)), 
            $this->PageSize, 
            $page * $this->PageSize
        );
        
        if ($this->SessionStorageName !== null) {
            foreach($this->SessionStorageParameters as $param) {
                $session = \KoolDevelop\Session\Session::getInstance();
                $session->set($this->SessionStorageName . '.' . $param, $this->getParameter($param));
            }
        }
        
        
        $this->getView()->set('paginate_count', $count);
        $this->getView()->set('paginate_page', $this->CurrentPageNumber = $page);
        $this->getView()->set('paginate_pages', $this->NumberOfPages = $pages);
        $this->getView()->set('paginate_items', $items);        
        
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
        
        if (array_key_exists($name, $this->BaseParameters)) {
            $value = $this->BaseParameters[$name];
        }
        
        if ($this->SessionStorageName !== null) {
            $session = \KoolDevelop\Session\Session::getInstance();
            if ($session->exists($this->SessionStorageName . '.' . $name)) {
                $value = $session->get($this->SessionStorageName . '.' . $name);
            }
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
     * Get Link with Named parameters added
     * 
     * @param string[] $parameters Parameters, use null to unset
     * @param string   $base       Base URL
     * @param boolean  $reset      Reset current base parameters
     * 
     * @return string URL
     */
    public function getLink($parameters = array(), $base = null, $reset = true) {
        
        $base_parameters = $reset ? array() : $this->BaseParameters;
        $base_parameters['sort'] = $this->getParameter('sort', null);
        $base_parameters['direction'] = $this->getParameter('direction', null);
        $base_parameters['page'] = $this->getParameter('page', 0);
        
        $parameters = array_merge($base_parameters, $parameters);
        $base = ($base === null) ? $this->DefaultBaseUrl : null;
        
        return r()->getNamedUrl($parameters, $base, $reset);
        
    }
    
    /**
     * Get Link to sort on field
     * 
     * @param string   $field       Field
     * @param string   $default_dir Default direction (ASC/DESC)
     * @param string   $base        Base URL to use, null to use current
     * @param string[] $parameters  Parameters to pass along, null to use default
     * 
     * @return string URL
     */
    public function getSortLink($field, $default_dir = 'ASC', $base = null, $parameters = null) {
        
        if ($parameters === null) {
            $reset = false;
            $parameters = array();
        } else {
            $reset = true;
        }
        
        if ($field == $this->getParameter('sort', null)) {
            $parameters['direction'] = strtoupper($this->getParameter('direction')) == 'ASC' ? 'DESC' : 'ASC';
        } else {
            $parameters['direction'] = $default_dir;
        }
        
        $parameters['sort'] = $field;

        return $this->getLink($parameters, $base, $reset);
        
    }
    
    /**
     * Get list of page numbers in range of current
     * 
     * @param type $count Number of pages to return
     * 
     * @return int[] Page numbers
     */
    public function getPageNumbers($count = 10) {
        $pages = array();
        $page = $this->CurrentPageNumber;
        for($x = $page - ($count / 2); $x < ($page + ($count / 2)); $x++) {           
            if (($x >= 0) AND ($x < $this->NumberOfPages)) {
                $pages[] = $x;
            }            
        }
        return $pages;
    }
    
    
    
}