<?php
/**
 * Simple Model <-> Array converter
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/

namespace KoolDevelop\Model;

/**
 * Simple Model <-> Array converter
 *
 * Converter that converts underscored array values to CamelCased
 * getter and setter functions
 * 
 * Example: display_name => getDisplayName / setDisplayName
 * 
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/
class SimpleArrayConverter implements \KoolDevelop\Model\IArrayConverter
{
    /**
     * Underscored field names
     * @var string[] 
     */
    private $Fields;
    
    /**
     * Constructor
     * 
     * @param string[] $Fields Underscored field names
     */
    function __construct($Fields) {
        $this->Fields = $Fields;
    }

   /**
     * Convert Model data to array
     * 
     * @param \Model $model Model
     * 
     * @return mixed[] Array
     */
    public function convertModelToArray(\Model &$model) {
        $data = array();
        foreach($this->Fields as $field_underscored) {
            $getter = 'get' . \KoolDevelop\StringUtilities::camelcase($field_underscored);
            $data[$field_underscored] = $model->$getter();
        }
        return $data;
    }
    
    
    /**
     * Convert Array to Model data
     * 
     * @param mixed[] $data  Input Arrat
     * @param \Model  $model Model
     * 
     * @return void
     */
    public function convertArrayToModel($data, \Model &$model) {
        foreach($this->Fields as $field_underscored) {
            $setter = 'set' . \KoolDevelop\StringUtilities::camelcase($field_underscored);
            $model->$setter($data[$field_underscored]);
        }
    }
 
    
}