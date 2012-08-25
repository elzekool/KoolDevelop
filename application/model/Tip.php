<?php
/**
 * Tip Model
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

namespace Model;

/**
 * Tip Model
 * 
 * Tips are small snippets of code and/or text explaining something.
 * Tips are filtered on language
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/
final class Tip extends \Model
{
    /**
     * Id
     * @var int
     */
    private $Id;
    
    /**
     * Language
     * @var string
     */
    private $Language;
    
    /**
     * Category Id
     * @var int
     */
    private $CategoryId;
    
    /**
     * Title
     * @var string
     */
    private $Title;
    
    /**
     * Text
     * @var string
     */
    private $Text;
    
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
     * Get Language
     *
     * @return string Language
     **/
    public function getLanguage() {
        return $this->Language;
    }

    /**
     * Set Language
     *
     * @param string Language
     *
     * @return void 
     **/
    public function setLanguage($Language) {
        $this->Language = $Language;
    }

    /**
     * Get Category Id
     * 
     * @return int Category Id
     */
    public function getCategoryId() {
        return $this->CategoryId;
    }

    /**
     * Set Category Id
     * 
     * @param int $CategoryId Category Id
     * 
     * @return void
     */
    public function setCategoryId($CategoryId) {
        $this->CategoryId = $CategoryId;
    }

    /**
     * Get \Model\Category for Tip
     * 
     * @return \Model\Category Category
     */
    public function getCategory() {
        if ($this->getCategoryId() === null) {
            return null;
        }       
        $category_container = new \Model\CategoryContainer();
        return $category_container->first(array('id' => $this->getCategoryId()));
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


    /**
     * Get Text
     *
     * @return string Text
     **/
    public function getText() {
        return $this->Text;
    }

    /**
     * Set Text
     *
     * @param string Text
     *
     * @return void 
     **/
    public function setText($Text) {
        $this->Text = $Text;
    }



    
}