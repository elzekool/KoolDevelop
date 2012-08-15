<?php
/**
 * Invalid Class Exception
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Database
 **/

namespace KoolDevelop\Exception;

/**
 * Invalid Class Exception
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Database
 **/
class DatabaseException extends \KoolDevelop\Exception\Exception
{
    /**
     * PDO Exception
     * @var \PDOException
     */
	private $PDOException;
    
    /**
     * Get associated PDO Exception
     * 
     * @return \PDOException PDO Exception
     */
    public function getPDOException() {
        return $this->PDOException;
    }

    /**
     * Set associated PDO Exception
     * 
     * @param \PDOException PDO Exception
     * 
     * @return void
     */
    public function setPDOException(\PDOException $PDOException) {
        $this->PDOException = $PDOException;
    }
    
}