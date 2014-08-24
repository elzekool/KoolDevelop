<?php
/**
 * Array <-> Model Converter Interface
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/

namespace KoolDevelop\Model;

/**
 * Array <-> Model Converter Interface
 *
 * Interface for Array <-> Model Conversion. Classes implementing this interface
 * support the conversion of model data to a simple array. Use this interface
 * for RESTfull controller actions and saving/retrieving form data.
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Model
 **/
interface IArrayConverter
{
    /**
     * Convert Model data to array
     * 
     * @param \Model $model Model
     * 
     * @return mixed[] Array
     */
    public function convertModelToArray(\KoolDevelop\Model\Model &$model);
    
    
    /**
     * Convert Array to Model data
     * 
     * @param mixed[] $data  Input Arrat
     * @param \Model  $model Model
     * 
     * @return mixed[] Array
     */
    public function convertArrayToModel($data, \KoolDevelop\Model\Model &$model);
    
    
}