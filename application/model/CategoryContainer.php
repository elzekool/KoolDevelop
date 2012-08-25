<?php
/**
 * Category Container Model
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

namespace Model;

/**
 * Category Container Model
 * 
 * This is the Container Model for the Category Model. A Container Model is responsible
 * for retrieving/updating Model data
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/
final class CategoryContainer extends \KoolDevelop\Model\ContainerModel
{
    /**
     * Database table to use
     * @var string
     */
    protected $DatabaseTable = 'categories';

    /**
     * Database configuration to use
     * @var string
     */
    protected $DatabaseConfiguration = 'default';

    /**
     * Model to use
     * @var string
     */
    protected $Model = '\\Model\\Category';

    /**
     * Convert Model to Database Row
     *
     * @param \KoolDevelop\Model\Model  $model        Model
     * @param \KoolDevelop\Database\Row $database_row Database Row
     *
     * @return void
     */
    protected function _ModelToDatabase(\KoolDevelop\Model\Model &$model, \KoolDevelop\Database\Row &$database_row) {
        /* @var $model \Model\Category */
        $database_row->id = $model->getId();
        $database_row->text = $model->getText();
    }

    /**
     * Convert Database Row to Model
     *
     * @param \KoolDevelop\Database\Row $database_row Database Row
     * @param \KoolDevelop\Model\Model  $model        Model
     *
     * return void
     */
    protected function _DatabaseToModel(\KoolDevelop\Database\Row &$database_row, \KoolDevelop\Model\Model &$model) {
        /* @var $model \Model\Category */
        $model->setId($database_row->id);
        $model->setTitle($database_row->title);
    }

    /**
     * Get Primary Key value from Model
     *
     * @return mixed[] Value
     */
    protected function getPrimaryKey(\KoolDevelop\Model\Model &$model) {
        /* @var $model \Model\Category */
        return $model->getId();
    }

    /**
     * Get Primary Key value from Model
     *
     * @param $value mixed[] Value
     *
     * @return void
     */
    protected function setPrimaryKey(\KoolDevelop\Model\Model &$model, $value) {
        /* @var $model \Model\Category */
        $model->setId($value);        
    }

    /**
     * Proces Conditions into Query
     *
     * @param mixed[]                     $conditions Conditons
     * @param \KoolDevelop\Database\Query $query      Prepared Query
     *
     * @return void
     */
    protected function _ProcesConditions($conditions, \KoolDevelop\Database\Query &$query) {
        
        foreach($conditions as $condition => $value) {
            
            // Tip: if / elseif is faster then select/case
            
            // Check Id
            if ($condition == 'id') {
                $query->where('id = ?', $value);
            }
            
        }
        
    }
}