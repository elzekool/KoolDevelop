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
class Adaptor implements \KoolDevelop\Configuration\IConfigurable
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
     * Begin new transaction
     * 
     * @return boolean Success
     */
    public function beginTransaction() {
        return $this->PdoConnection->beginTransaction();
    }
    
    /**
     * Rollback transaction
     * 
     * @return boolean Success
     */
    public function rollbackTransaction() {
        return $this->PdoConnection->rollBack();
    }
    
    /**
     * Commit transaction
     * 
     * @return boolean Success
     */
    public function commitTransaction() {
        return $this->PdoConnection->commit();
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
    
    /**
     * Get list of (configurable) classes that this class
     * depends on. 
     * 
     * @return string[] Depends on
     */
    public static function getDependendClasses() {
        return array(
            '\\KoolDevelop\\Database\\Query',
            '\\KoolDevelop\\Database\\Result',
            '\\KoolDevelop\\Database\\Row'
        );
    }
    
    /**
     * Get Configuration options for this class
     * 
     * @return \KoolDevelop\Configuration\IConfigurableOption[] Options for class
     */
    public static function getConfigurationOptions() {
        return array(
            new \KoolDevelop\Configuration\IConfigurableOption('database', 'default.dsn', '"mysql:host=127.0.0.1;dbname="', ('Data Source Name')),
            new \KoolDevelop\Configuration\IConfigurableOption('database', 'default.username', '""', ('Username')),
            new \KoolDevelop\Configuration\IConfigurableOption('database', 'default.password', '""', ('Password')),
            new \KoolDevelop\Configuration\IConfigurableOption('database', 'default.profiling', '0', ('0 = Disable profiling, 1 = Enable profiling, see Query::ProfileLog')),
        );
    }
    
}