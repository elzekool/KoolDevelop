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
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Core
 **/
abstract class Exception extends \Exception
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