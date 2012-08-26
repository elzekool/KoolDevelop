<?php
/**
 * KoolDevelop Base Exception
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/

namespace KoolDevelop\Exception;

/**
 * KoolDevelop Base Exception
 *
 * Extention of \Exception class with getDetails() / setDetails(). The default
 * error handler can display this data, usefull for adding extra debugging data.
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
class Exception extends \Exception
{
    /**
     * Details
     * @var string
     */
    private $Detail;

    /**
     * Get Details
     *
     * @return string
     */
    public function getDetail() {
        return $this->Detail;
    }

    /**
     * Set Details
     *
     * @param string $Detail
     *
     * return void;
     */
    public function setDetail($Detail) {
        $this->Detail = $Detail;
    }



}