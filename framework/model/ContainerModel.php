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
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/
abstract class ContainerModel
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
     * Get Model By
     *
     * @param string $field Field
     * @param string $value Value
     *
     * @return \KoolDevelop\Model\Model Model
     */
    public function getBy($field, $value) {

        // Create new SELECT query
        $query = DatabaseAdaptor::getInstance($this->DatabaseConfiguration)->newQuery();
        $query->select('*')->from($this->DatabaseTable)->where($field . ' = ?', $value);
        $result = $query->execute();

        // Try to fetch row
        if (false === ($database_row = $result->fetch())) {
            return null;
        }

        $model = $this->newObject();
        $this->_DatabaseToModel($database_row, $model);

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
       if ($this->getPrimaryKey($model) !== null) {
            $query->update()->into($this->DatabaseTable);
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
     * Return Models that comply to the conditions
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
        $query->select('*')->from($this->DatabaseTable);

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


}