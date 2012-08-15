<?php
/**
 * Database Adaptor
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Database
 **/

namespace KoolDevelop\Database;

/**
 * Database Adaptor
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Database
 **/
final class Adaptor 
{

	/**
	 * Instances
	 * @var \KoolDevelop\Database\Adaptor[]
	 */
	private static $Instances = array();

	/**
	 * Get Instance of KoolDevelopDb_Adaptor
	 *
	 * @param string $config Configuration
	 *
	 * @return \KoolDevelop\Database\Adaptor Database Adaptor
	 */
	public static function getInstance($config = 'default') {
		if (!isset(self::$Instances[$config])) {
			return self::$Instances[$config] = new self($config);
		}
		return self::$Instances[$config];
	}
	
	/**
	 * PDO Connection
	 * @var PDO
	 */
	private $PdoConnection;

	/**
	 * Transaction Depth
	 * @var int
	 */
	private $TransactionDepth = 0;

    /**
     * Profiling Enabled
     * @var boolean 
     */
    private $Profiling = false;
    
	/**
	 * Constructor
	 * 
	 * @param string $config Configuration
	 */
	private function __construct($config) {

		$configuration = \KoolDevelop\Configuration::getInstance('database');

        $this->Profiling = ($configuration->get($config . '.profiling', 0) == 1);
        
		try {

			$this->PdoConnection = new \PDO(
				$configuration->get($config . '.dsn', ''),
				$configuration->get($config . '.username', ''),
				$configuration->get($config . '.password', '')
			);

            $this->PdoConnection->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\\' . __NAMESPACE__  . '\\' . 'Result'));
            $this->PdoConnection->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND , "SET NAMES utf8");
            
		} catch(\PDOException $e) {
			$exception = new \KoolDevelop\Exception\DatabaseException(__f("Failed to connect to database",'kooldevelop'));
			$exception->setPDOException(__f($e,'kooldevelop'));
			throw $exception;
		}

	}

	/**
	 * Create new Query
	 * 
	 * @return \KoolDevelop\Database\Query Query
	 */
	public function newQuery() {
		return new \KoolDevelop\Database\Query($this->PdoConnection, $this->Profiling);
	}
	
    /**
     * Return last inserted 
     * 
     * @param string $name Name of field
     * 
     * @return void
     */
    public function getLastInsertedId($name = null) {
        return $this->PdoConnection->lastInsertId($name);        
    }
}