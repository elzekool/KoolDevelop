<?php
/**
 * Database Query
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Database
 **/

namespace KoolDevelop\Database;

/**
 * Database Query
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage Database
 **/
class Query 
{

    /**
     * Profiling Log
     * @var mixed[][]
     */
    public static $ProfileLog = array();

    /**
     * PDO Connection
     * @var \PDO
     * */
    private $Pdo = null;

    /**
     * Profiling Enabled
     * @var boolean
     */
    private $Profiling = false;

    /**
     * Type
     * @var string
     * */
    private $Type = null;

    /**
     * Fields
     * @var string[]
     * */
    private $Fields = array();

    /**
     * Where
     * @var string[]
     * */
    private $Where = array();

    /**
     * Field <-> Value
     * @var string[]
     * */
    private $Values = array();

    /**
     * Parameters to bind
     * @var mixed[]
     */
    private $Parameters = array();

    /**
     * Limit
     * @var int[]
     */
    private $Limit = array();

    /**
     * From/To Table
     * @var string
     * */
    private $Table;

    /**
     * Joins
     * @var string[][]
     */
    private $Joins = array();

    /**
     * Custom SQL
     * @var string
     */
    private $CustomSQL = '';

    /**
     * Order By
     * @var string[]
     */
    private $OrderBy = array();

    /**
     * Group By
     * @var string[]
     */
    private $GroupBy = array();

    /**
     * Prepared statement
     * @var PDOStatement
     */
    private $Prepared = null;

    /**
     * Select For Update
     * @var boolean
     */
    private $ForUpdate = false;

    /**
     * Constructor
     *
     * @internal Do not create directly, only \KoolDevelop\Database\Adaptor should
     * create a new Query
     *
     * @see \KoolDevelop\Database\Adaptor
     *
     * @param PDO     $pdo       PDO Connection
     * @param boolean $profiling Profiling enabled
     */
    public function __construct(\PDO $pdo, $profiling = false) {
        $this->Pdo = $pdo;
        $this->Profiling = $profiling;
    }

    /**
     * Cast Query to string
     *
     * @return string
     * */
    public function __toString() {
        return $this->_getSQL();
    }

    /**
     * Get SQL for current Query
     */
    private function _getSQL() {

        switch ($this->Type) {

            // Custom SQL
            case 'custom':
                return
                        $this->CustomSQL;

            // Select
            case 'select':
                return
                        'SELECT ' .
                        join(', ', $this->Fields) .
                        ' FROM ' . $this->Table .
                        $this->_sqlJoin() .
                        $this->_sqlWhere() .
                        $this->_sqlGroupBy() .
                        $this->_sqlOrderBy() .
                        $this->_sqlLimit() .
                        ($this->ForUpdate ? ' FOR UPDATE' : '');

            // Delete
            case 'delete':
                return
                        'DELETE ' .
                        join(', ', $this->Fields) .
                        ' FROM ' .
                        $this->Table .
                        $this->_sqlJoin() .
                        $this->_sqlWhere() .
                        $this->_sqlLimit();

            // Insert/Replace share syntax
            case 'insert':
            case 'replace':

                return
                        strtoupper($this->Type) . ' INTO ' .
                        $this->Table .
                        ' SET ' .
                        join(',', $this->Values) .
                        $this->_sqlJoin();

            // Update
            case 'update':
                return
                        'UPDATE ' .
                        $this->Table .
                        ' SET ' .
                        join(', ', $this->Values) .
                        $this->_sqlWhere() .
                        $this->_sqlOrderBy() .
                        $this->_sqlLimit();

            default:
                return "ERROR: Query type not set/unimplemented";
        }
    }

    /**
     * Set Type, check if another type is already set
     *
     * @return void
     * */
    private function _setType($type) {
        if ($this->Type === null) {
            $this->Type = $type;
        } else if ($this->Type != $type) {
            throw new \KoolDevelop\Exception\DatabaseException(__f('Query already started with another type', 'kooldevelop'));
        }
    }

    /**
     * Return WHERE part of SQL query
     *
     * @return string Where part
     * */
    private function _sqlWhere() {
        return (count($this->Where) == 0) ? '' : (' WHERE ' . join(' AND ', $this->Where));
    }

    /**
     * Return LIMIT part of SQL query
     *
     * @return string Limit part
     * */
    private function _sqlLimit() {
        return (count($this->Limit) == 0) ? '' : (' LIMIT ' . join(',', $this->Limit));
    }

    /**
     * Return ORDER BY part of SQL query
     *
     * @return string Order By part
     * */
    private function _sqlOrderBy() {
        return (count($this->OrderBy) == 0) ? '' : (' ORDER BY ' . join(', ', $this->OrderBy));
    }

    /**
     * Return GROUP BY part of SQL query
     *
     * @return string Order By part
     * */
    private function _sqlGroupBy() {
        return (count($this->GroupBy) == 0) ? '' : (' GROUP BY ' . join(', ', $this->GroupBy));
    }

    /**
     * Return Joins part of SQL query
     *
     * @return string Join part
     * */
    private function _sqlJoin() {
        if (count($this->Joins) == 0) {
            return '';
        }
        $sql = '';
        foreach ($this->Joins as $join) {
            $sql .= ' ' . $join[0] . ' JOIN ' . $join[1] . ' ON ' . $join[2];
        }
        return $sql;
    }

    /**
     * Custom SQL
     *
     * Accepts more parameters for position based parameters
     *
     * @param string $sql Custom SQL
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function custom($sql) {
        $this->Prepared = null;
        $this->CustomSQL = $sql;
        $this->_setType('custom');

        if (func_num_args() > 1) {
            for ($x = 1; $x < func_num_args(); $x++) {
                $this->Parameters[] = func_get_arg($x);
            }
        }

        return $this;
    }

    /**
     * Select
     *
     * @param string|string[] $fields     Fields to select
     * @param boolean         $for_update Select FOR UPDATE
     * 
     * @return \KoolDevelop\Database\Query
     * */
    public function select($fields, $for_update = false) {
        $this->Prepared = null;

        $this->_setType('select');
        $this->Fields = array_merge($this->Fields, (array) $fields);
        $this->ForUpdate = $for_update;

        return $this;
    }

    /**
     * Insert
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function insert() {
        $this->Prepared = null;
        $this->_setType('insert');
        return $this;
    }

    /**
     * Replace
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function replace() {
        $this->Prepared = null;

        $this->_setType('replace');
        return $this;
    }

    /**
     * Update
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function update() {
        $this->Prepared = null;

        $this->_setType('update');
        return $this;
    }

    /**
     * Delete
     *
     * @param string $fields Tables to delete (in case of JOIN)
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function delete($fields = '') {
        $this->Prepared = null;

        $this->_setType('delete');
        $this->Fields = array_merge($this->Fields, (array) $fields);

        return $this;
    }

    /**
     * Set table to select/delete from
     *
     * @param string $table Table
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function from($table) {
        $this->Prepared = null;

        if (($this->Type != 'select') AND ($this->Type != 'delete')) {
            throw new \KoolDevelop\Exception\DatabaseException(__f("From only allowed for select/delete queries", 'kooldevelop'));
        }
        $this->Table = $table;
        return $this;
    }

    /**
     * Set table to update/insert/replace into
     *
     * @param string $table Table
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function into($table) {
        $this->Prepared = null;

        if (($this->Type != 'update') AND ($this->Type != 'insert') AND ($this->Type != 'replace')) {
            throw new \KoolDevelop\Exception\DatabaseException(__f("Into only allowed for update/insert/replace queries", 'kooldevelop'));
        }
        $this->Table = $table;
        return $this;
    }

    /**
     * Set/Add where conditions
     *
     * Accepts more parameters for position based parameters
     *
     * @param string|string[] $conditions Conditions
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function where($conditions) {
        $this->Prepared = null;

        $this->Where = array_merge($this->Where, (array) $conditions);

        if (func_num_args() > 1) {
            for ($x = 1; $x < func_num_args(); $x++) {
                $this->Parameters[] = func_get_arg($x);
            }
        }

        return $this;
    }

    /**
     * Set Limit
     *
     * @param <type> $count  Count
     * @param <type> $offset Offset
     *
     * @return \KoolDevelop\Database\Query
     */
    public function limit($count, $offset = 0) {
        $this->Prepared = null;

        $this->Limit = array(
            $offset,
            $count
        );
        return $this;
    }

    /**
     * Add Left Join
     *
     * @param string $table Table
     * @param string $on    Join on
     *
     * @return \KoolDevelop\Database\Query
     */
    public function leftJoin($table, $on) {
        return $this->join('LEFT', $table, $on);
    }

    /**
     * Add Right Join
     *
     * @param string $table Table
     * @param string $on    Join on
     *
     * @return \KoolDevelop\Database\Query
     */
    public function rightJoin($table, $on) {
        return $this->join('RIGHT', $table, $on);
    }

    /**
     * Add Inner Join
     *
     * @param string $table Table
     * @param string $on    Join on
     *
     * @return \KoolDevelop\Database\Query
     */
    public function innerJoin($table, $on) {
        return $this->join('INNER', $table, $on);
    }

    /**
     * Add Outer Join
     *
     * @param string $table Table
     * @param string $on    Join on
     *
     * @return \KoolDevelop\Database\Query
     */
    public function outerJoin($table, $on) {
        return $this->join('OUTER', $table, $on);
    }

    /**
     * Add Join
     *
     * @param string $type  Type
     * @param string $table Table
     * @param string $on    Join on
     *
     * @return \KoolDevelop\Database\Query
     */
    public function join($type, $table, $on) {
        $this->Prepared = null;

        if (!in_array($type, array('LEFT', 'RIGHT', 'INNER', 'OUTER'))) {
            throw new \KoolDevelop\Exception\DatabaseException(__f("Invalid JOIN type", 'kooldevelop'));
        }

        if (!in_array($this->Type, array('select', 'delete', 'update'))) {
            throw new \KoolDevelop\Exception\DatabaseException(__f("join only allowed for select/update/delete queries", 'kooldevelop'));
        }

        $this->Joins[] = array(
            $type,
            $table,
            $on
        );
        return $this;
    }

    /**
     * Add Field <-> Value
     *
     * Accepts more parameters for position based parameters
     *
     * @param string|string[] $values Field <-> Value combination(s)
     *
     * @return \KoolDevelop\Database\Query
     * */
    public function set($values) {
        $this->Prepared = null;

        if (!in_array($this->Type, array('insert', 'update'))) {
            throw new \KoolDevelop\Exception\DatabaseException(__f("Set only allowed for insert/update queries", 'kooldevelop'));
        }
        $this->Values = array_merge($this->Values, (array) $values);

        if (func_num_args() > 1) {
            for ($x = 1; $x < func_num_args(); $x++) {
                $this->Parameters[] = func_get_arg($x);
            }
        }

        return $this;
    }

    /**
     * Order By
     *
     * @param string $field     Field
     * @param string $direction Direction (ASC|DESC)
     *
     * @return \KoolDevelop\Database\Query
     */
    public function orderby($field, $direction = 'ASC') {
        $direction = strtoupper($direction);
        if (!in_array($direction, array('ASC', 'DESC'))) {
            throw new \KoolDevelop\Exception\DatabaseException(__f('Invalid direction for Order By', 'kooldevelop'));
        }
        $this->OrderBy[] = $field . ' ' . $direction;
        return $this;
    }

    /**
     * Group By
     *
     * @param string $field     Field
     *
     * @return \KoolDevelop\Database\Query
     */
    public function groupby($field) {
        $this->GroupBy[] = $field;
        return $this;
    }

    /**
     * Execute Query and return result
     *
     * @param mixed[] $params Override params
     *
     * @return \KoolDevelop\Database\Result
     */
    public function execute($params = null) {

        // Check if there is a prepared statement
        if ($this->Prepared === null) {
            $this->Prepared = $this->Pdo->prepare($this->_getSQL());
            $this->Prepared->setFetchMode(\PDO::FETCH_CLASS, '\\' . __NAMESPACE__ . '\\' . 'Row');
        } else {
            // Make sure cursor is closed
            $this->Prepared->closeCursor();
        }

        // Check which parameters to use
        if ($params === null) {
            $params = $this->Parameters;
        }

        if ($this->Profiling) {
            $start = microtime(true);
        }

        if (!$this->Prepared->execute($params)) {
            $error = $this->Prepared->errorInfo();
            $exception = new \KoolDevelop\Exception\DatabaseException('[' . $error[0] . '] ' . $error[2]);

            ob_start();
            $this->Prepared->debugDumpParams();
            $exception->setDetail(ob_get_clean());

            throw $exception;
        }

        if ($this->Profiling) {
            $time_ms = (microtime(true) - $start) * 1000;
            self::$ProfileLog[] = array(
                'SQL' => $this->_getSQL(),
                'Params' => $params,
                'Time' => sprintf('%f mS', $time_ms),
                'RowCount' => $this->Prepared->rowCount()
            );
        }

        return $this->Prepared;
    }

}
