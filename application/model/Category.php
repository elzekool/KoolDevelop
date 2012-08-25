<?php
/**
 * Category Model
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

namespace Model;

/**
 * Category Model
 * 
 * Categories are used for finding Tips faster
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/
final class Category extends \Model
{
    /**
     * Id
     * @var int
     */
    private $Id;
    
    /**
     * Title
     * @var string
     */
    private $Title;
    
    /**
     * Get Id
     *
     * @return int Id
     **/
    public function getId() {
        return $this->Id;
    }

    /**
     * Set Id
     *
     * @param int Id
     *
     * @return void 
     **/
    public function setId($Id) {
        $this->Id = $Id;
    }
    
    
    /**
     * Get Title
     *
     * @return string Title
     **/
    public function getTitle() {
        return $this->Title;
    }

    /**
     * Set Title
     *
     * @param string Title
     *
     * @return void 
     **/
    public function setTitle($Title) {
        $this->Title = $Title;
    }

    
}