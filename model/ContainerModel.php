<?php

/**
 * Container Model
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/

namespace KoolDevelop\Model;

use KoolDevelop\Database\Adaptor as DatabaseAdaptor;

/**
 * Container Model
 *
 * With the container model you can retrieve data from a database table 
 * and convert this data into Models. The ContainerModel is a form of ORM.
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/
abstract class ContainerModel extends \Model implements \KoolDevelop\Configuration\IConfigurable
{

    /**
     * Database table to use
     * @var string
     */
    protected $DatabaseTable = '';

    /**
     * Database configuration to use
     * @var string
     */
    protected $DatabaseConfiguration = 'default';

    /**
     * Model to use
     * @var string
     */
    protected $Model = '';
    
    /**
     * Field used as Primary Key
     * @var string 
     */
    protected $PrimaryKeyField = 'id';

    /**
     * Convert Model to Database Row
     *
     * @param \KoolDevelop\Model\Model  $model        Model
     * @param \KoolDevelop\Database\Row $database_row Database Row
     *
     * @return void
     */
    abstract protected function _ModelToDatabase(\KoolDevelop\Model\Model &$model, \KoolDevelop\Database\Row &$database_row);

    /**
     * Convert Database Row to Model
     *
     * @param \KoolDevelop\Database\Row $database_row Database Row
     * @param \KoolDevelop\Model\Model  $model        Model
     *
     * return void
     */
    abstract protected function _DatabaseToModel(\KoolDevelop\Database\Row &$database_row, \KoolDevelop\Model\Model &$model);

    /**
     * Get Primary Key value from Model
     *
     * @return mixed[] Value
     */
    abstract protected function getPrimaryKey(\KoolDevelop\Model\Model &$model);

    /**
     * Get Primary Key value from Model
     *
     * @param $value mixed[] Value
     *
     * @return void
     */
    abstract protected function setPrimaryKey(\KoolDevelop\Model\Model &$model, $value);


    /**
     * Proces Conditions into Query
     *
     * @param mixed[]                     $conditions Conditons
     * @param \KoolDevelop\Database\Query $query      Prepared Query
     *
     * @return void
     */
    protected function _ProcesConditions($conditions, \KoolDevelop\Database\Query &$query) {

    }


    /**
     * Create new Model instance
     *
     * @return \KoolDevelop\Model\Model Model
     */
    public function newObject() {
        $model = new $this->Model();
        return $model;
    }
    
    /**
     * Save Object
     *
     * @param \KoolDevelop\Model\Model $model Model
     *
     * @return void
     */
    public function save(\KoolDevelop\Model\Model &$model) {

       $database_row = new \KoolDevelop\Database\Row();
       $this->_ModelToDatabase($model, $database_row);

       $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();

       // Update/Insert depending on Primary Key value
       if (null !== ($primary_value = $this->getPrimaryKey($model))) {
            $query->update()->into($this->DatabaseTable)->where($this->PrimaryKeyField . ' = ?', $primary_value);
       } else {
            $query->insert()->into($this->DatabaseTable);
       }

       foreach(get_object_vars($database_row) as $field => $value) {
           $query->set($field . ' = ?', $value);
       }

       // Execute insert
       $query->execute();

       // Save primary key in model
       $this->setPrimaryKey($model, DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->getLastInsertedId());

    }

    /**
     * Delete row(s) from Database based on conditions
     *
     * @return void
     */
    public function delete($conditions = array()) {

        // Create new SELECT query
        $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();
        $query->delete()->from($this->DatabaseTable);

        // Process conditions
        $this->_ProcesConditions($conditions, $query);
        $result = $query->execute();

    }

    /**
     * Update data in database for all objects that comply to the conditions
     *
     * @param mixed[] $data       Data
     * @param mixed[] $conditions Conditions
     *
     * @return void
     */
    public function update($data, $conditions = array()) {

        // Create new SELECT query
        $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();
        $query->update()->into($this->DatabaseTable);

        foreach($data as $field => $value) {
            $query->set($field . ' = ?', $value);
        }

        // Process conditions
        $this->_ProcesConditions($conditions, $query);
        $result = $query->execute();

    }

    /**
     * Get Count based on conditons
     *
     * @param mixed[] $conditions Conditions
     *
     * @return int Count
     */
    public function count($conditions = array()) {

        // Create new SELECT query
        $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();
        $query->select('COUNT(*)')->from($this->DatabaseTable);

        // Process conditions
        $this->_ProcesConditions($conditions, $query);
        $result = $query->execute();

        return $result->fetchColumn(0);

    }

    /**
     * Return first Model that complies to the conditions or null if none match
     *
     * @param mixed[]    $conditions Conditions
     * @param string[][] $sort       Sorting [0] => Field, [1] => Direction
     *
     * @return \KoolDevelop\Model\Model Model
     */
    public function first($conditions = array(), $sort = array()) {

        // Create new SELECT query
        $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();
        $query->select($this->DatabaseTable . '.*')->from($this->DatabaseTable);

        // Limit on 1 
        $query->limit(1, 0);

        if (count($sort) > 0) {
            if (!is_array($sort[0])) {
                $sort = array($sort);
            }
            foreach($sort as $_sort) {
                $query->orderby($_sort[0], $_sort[1]);
            }
        }

        // Process conditions
        $this->_ProcesConditions($conditions, $query);
        $result = $query->execute();

        $model = null;
        if(false !== ($database_row = $result->fetch())) {
            $model = $this->newObject();
            $this->_DatabaseToModel($database_row, $model);
        }

        return $model;

    }
    
    /**
     * Return Models that complies to the conditions
     *
     * @param mixed[]    $conditions Conditions
     * @param string[][] $sort       Sorting [0] => Field, [1] => Direction
     * @param int        $limit      Limit number of returned models
     * @param int        $start      Offset for returned models
     *
     * @return \KoolDevelop\Model\Model[] Models
     */
    public function index($conditions = array(), $sort = array(), $limit = null, $start = 0) {

        // Create new SELECT query
        $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();
        $query->select($this->DatabaseTable . '.*')->from($this->DatabaseTable);

        if ($limit !== null) {
            $query->limit($limit, $start);
        }

        if (count($sort) > 0) {
            if (!is_array($sort[0])) {
                $sort = array($sort);
            }
            foreach($sort as $_sort) {
                $query->orderby($_sort[0], $_sort[1]);
            }
        }

        // Process conditions
        $this->_ProcesConditions($conditions, $query);
        $result = $query->execute();

        $models = array();
        while($database_row = $result->fetch()) {
            $model = $this->newObject();
            $this->_DatabaseToModel($database_row, $model);
            $models[] = $model;
        }

        return $models;

    }

    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array(
            '\\KoolDevelop\\Database\\Adaptor',
            '\\KoolDevelop\\Database\\Query',
            '\\KoolDevelop\\Database\\Result',
            '\\KoolDevelop\\Database\\Row'
        );
    }
    
    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {
        return array();
    }


}