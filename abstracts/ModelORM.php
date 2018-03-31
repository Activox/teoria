<?php
namespace abstracts;

defined('_EXEC_APP') or die('Ups! access not allowed');

use lib\Config;
use Exception;
use stdClass;

/**
 * Object Relation Mapping
 *
 * @author Miguel Peralta
 * @version 2.0
 * @property string $primary_key you must define primary key field name of table to insert, update, delete
 * @property mixed $value define value to update and delete
 * @property string $table you must define this property (table name) to get result and insert or update entity
 * @property string $alias form to call the table (deprecated since 2.0)
 * @property string $find_by find resource by field name
 * @property string $table_prefix put prefix in string table example (`, ', example., .)
 * @property string $table_prefix_pos to indicate the position prefix (start-and-end,start,end)
 */
abstract class ModelORM
{    
    /**
     *
     * @var Idatabase
     */
    private $_dbo   =   null,
            $_object = null,
            $_sql = "",
            $_resource_db = null,
            $_reserved_key = ["primary_key", "value", "table", "alias", "find_by", "table_prefix", "table_prefix_pos"],
            
            /**
             * query conditions
             */
            $_conditions = []
            ;    
    
    /**
     * 
     * @param stdClass $properties
     */    
    protected function __construct( stdClass $properties  =   null ) {
        
        $dbo    =   null;
        
        $provider = 'lib\database\\' . Config::$_DATABASE_;
               
        if( !class_exists( $provider ) ){
            throw new \Exception( 'provider database not found' );
        }
        
        $dbo = new $provider(FALSE);
  
        if( $properties != null ) {
            $dbo->setProperties($properties);
        }
       
        //open connection
        $dbo->connect();

        //set dbo
        $this->_dbo  =   $dbo;
        
        //attribute define
        $this->_object = new stdClass();        
        
    }
    
    /**
     * set db properties
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {        
        $this->_object->$name = $value ;
    }
    
    /**
     * get db properties
     * @param type $name
     * @return type
     */
    public function __get($name) {        
        if( isset( $this->_object->$name ) ) {
            return $this->_object->$name;
        }
    }
    
    /**
     * get link database
     */
    public function getLink(){
        return $this->_dbo->getLink();
    }
     
    /**
     * set query to execute
     * @param string $sql
     * @return \abstracts\ModelORM
     */    
    public function query( $sql ){
        $this->_sql = $sql;
        return $this;
    }
    
    /**
     * start to build query by properties defined in model. You can chain these methods (inner, left, right, where ) you must respect the references order
     * @return \abstracts\ModelORM
     * @throws Exception
     */
    protected function get() {
        
        $this->_sql = " SELECT ";
        $attr = "";
        
        if( !empty( $this->_object ) ) {
            
            $alias = ( !empty( $this->_object->alias ) ) ? $this->_object->alias . "." : "";
            
            foreach ( $this->_object as $key => $value ) 
            {
                if( !in_array( $key, $this->_reserved_key ) )
                {
                    $attr .= $value . ",";
                }
            }
            
            if( !empty( $attr ) ){
                $attr = trim($attr, ",");
            } else {
                $attr = "*";
            }
                        
            if( !isset( $this->_object->table ) ){
                throw new Exception( 'table undefined' );
            }
            
            $alias = str_replace(".", "", $alias);

            $table = $this->handlePrefix( $this->_object->table );
            
            $this->_sql .=  $attr . " FROM " . $table . " " . $alias;

            return $this;
            
        } else {
            throw new Exception( 'Properties could not find' );
        }
        
    }
    
    /**
     * get all records of table
     * @param string $FORMAT format resource returned. Could be 'object' or 'array' (optional)
     * @return mixed
     */
    protected function all( $FORMAT = 'object' ) {
        $res = null;
        switch ($FORMAT) {
            case "object":
                $res    = $this->get()->objectList();
                break;

            default:
                $res    = $this->get()->arrayList();
                break;
        }
        
        return $res;
    }
    
    /**
     * find by value
     * @param mixed $value
     * @param string $format format resource returned. Could be 'object' or 'array' (optional)
     * @param string $operator compare operator. Could be ('=','in','notin','like','ilike','like','!=','<>','different' : it's the same as !=,'whatever' : to compare with any value)(optional)
     * @param array $where custom condition, respect the condition form in where method
     * @return \abstracts\ModelORM
     * @throws Exception
     */
    protected function find( $value, $format = 'object', $operator = '=', array $where = array() ) {
        
        if ( !isset( $this->_object->find_by ) ) {
            throw new Exception( 'find by undefined' );
        }

        $res = null;

        switch ($format) {
            case "object":
                $res    = $this->get()->condition($this->_object->find_by, $operator, $value )->apply()->getObject();
                break;

            default:
                $res    = $this->get()->condition($this->_object->find_by, $operator, $value )->apply()->getArray();
                break;
        }
        
        return $res;
    }
    
    /**
     * Query conditions
     * @param array $properties properties condition ["field name"=>["operator"=>?,"value"=>?, "nextcondition"=>?]]
     * field name : name of the field into table
     * operator : ('=','in','notin','like','ilike','like','!=','<>','different' : it's the same as !=,'whatever' : to compare with any value)
     * value : value to compare, is necesary specify the var type. Example: ( string, int, float )
     * @return \abstracts\ModelORM
     * @throws \Exception
     * @deprecated since version 2.0
     */
    protected function where( array $properties ){
        
        $this->_sql .= " WHERE ";

        for ($a = 0; $a < count($properties); $a++) {
            foreach ($properties[$a] as $key => $value) {
                if (!empty($key)) {
                    if (isset($value["operator"])) {
                        switch (trim(strtolower($value["operator"]))) {
                            case "in":

                                $this->_sql .= $key . " in ( " . $value['value'] . " ) " . $value['nextcondition'] . " ";

                                break;

                            case "notin":

                                $this->_sql .= $key . " not in ( " . $value['value'] . " ) " . $value['nextcondition'] . " ";

                                break;

                            case "ilike":

                                $this->_sql .= $key . " ilike " . $value['value'] . " " . $value['nextcondition'] . " ";

                                break;

                            case "like":

                                $this->_sql .= $key . " like " . $value['value'] . " " . $value['nextcondition'] . " ";

                                break;

                            case "whatever":

                                $this->_sql .= $key . " " . $value['value'] . " " . $value['nextcondition'] . " ";

                                break;

                            case "!=":

                                $this->_sql .= $key . " != " . $value['value'] . " " . $value['nextcondition'] . " ";

                                break;

                            case "<>":

                                $this->_sql .= $key . " != " . $value['value'] . " " . $value['nextcondition'] . " ";

                                break;

                            case "different":

                                $this->_sql .= $key . " != " . $value['value'] . " " . $value['nextcondition'] . " ";

                                break;

                            default:
                                if (!isset($value['value'])) {
                                    throw new Exception('value cannot be empty');
                                }

                                if (is_string($value['value'])) {
                                    $this->_sql .= " ( " . $key . " " . $value['operator'] . " '" . $value['value'] . "' OR '" . $value['value'] . "' " . $value['operator'] . " '' ) " . $value['nextcondition'] . " ";
                                } else {
                                    if (is_integer($value['value']) || is_numeric($value['value'])) {
                                        $this->_sql .= " ( " . $key . " " . $value['operator'] . " " . $value['value'] . " OR " . $value['value'] . " " . $value['operator'] . " 0 ) " . $value['nextcondition'] . " ";
                                    } else {
                                        throw new Exception('we cant find value type');
                                    }
                                }

                                break;
                        }

                    }

                }
            }
        }
        return $this;                    
                
    }
    
    /**
     * buffering condition
     * @param string $field name of the field into table
     * @param string $operator ('=','in','notin','like','ilike','like','!=','<>','different' : it's the same as !=,'whatever' : to compare with any value)
     * @param mixed $value
     * @param string $link could be ('and', 'or')(optional)
     * @return \abstracts\ModelORM
     */
    protected function condition( $field, $operator, $value, $link = '' ){
        array_push($this->_conditions, [ $field => [ "operator" => $operator, "value" => $value, "nextcondition" => $link] ]);
        return $this;
    }
    
    /**
     * apply conditions to query
     * @return \abstracts\ModelORM
     */
    protected function apply(){
        $res = $this->where($this->_conditions);
        $this->_conditions = [];
        return $res;
    }
    
    /**
     * relation operator
     * @param string $table table name
     * @param array $properties field => compare field
     * @param string $OPERATOR operator ('=','!=', '>', '<', etc...)(optional)
     * @return \abstracts\ModelORM
     */
    protected function rigth( $table, array $properties, $OPERATOR = '=' ){

        $table = $this->handlePrefix($table);

        $this->_sql .= " RIGHT JOIN $table ON ";
        
        foreach ($properties as $key => $value) {            
            $this->_sql .= $key . " $OPERATOR " . $value ;
        }
        
        return $this;
        
    }
    
    /**
     * relation operator
     * @param string $table table name
     * @param array $properties field => compare field
     * @param string $OPERATOR operator ('=','!=', '>', '<', etc...)(optional)
     * @return \abstracts\ModelORM
     */
    protected function left( $table, array $properties, $OPERATOR = '=' ){

        $table = $this->handlePrefix($table);

        $this->_sql .= " LEFT JOIN $table ON ";
        
        foreach ($properties as $key => $value) {            
            $this->_sql .= $key . " $OPERATOR " . $value ;
        }
        
        return $this;
        
    }
    
    /**
     * relation operator
     * @param string $table table name
     * @param array $properties field => compare field
     * @param string $OPERATOR operator ('=','!=', '>', '<', etc...)(optional)
     * @return \abstracts\ModelORM
     */
    protected function inner( $table, array $properties, $OPERATOR = '=' ){

        $table = $this->handlePrefix($table);

        $this->_sql .= " INNER JOIN $table ON ";
        
        foreach ($properties as $key => $value) {            
            $this->_sql .= $key . " $OPERATOR " . $value ;
        }
        
        return $this;
    }
    
    /**
     * type list result consult
     * @return object
     */
    protected function objectList(){        
        return $this->_dbo->getObjectList( $this->_sql );        
    }
    
    /**
     * type list result consult
     * @return array
     */
    protected function arrayList(){        
        return $this->_dbo->getArrayList( $this->_sql );        
    }
    
    /**
     * type one result consult
     * @return object
     */
    protected function getObject(){        
        return $this->_dbo->getRowObjectList( $this->_sql );        
    }
    
    /**
     * type one result consult
     * @return array
     */
    protected function getArray() {
        return $this->_dbo->getRowArrayList( $this->_sql );        
    }
    
    /**
     * sql sentences
     * @return string
     */
    protected function getSqlSentences( ) {
        return $this->_sql;
    }
    
    /**
     * save define data
     * @param bool $clear for clear object after save any entity, default value is true. if you dont want clear the object for any reason define false
     * @return int
     * @throws \Exception
     */
    protected function save( $clear = true ) {
        
        if ( !isset( $this->_object->primary_key ) ) {
            throw new Exception( 'primery key undefined' );
        }
        
        $object_insert = new stdClass();
        
        foreach ( $this->_object as $key => $value ) 
        {
            if( !in_array( $key, $this->_reserved_key ) )
            {
                $object_insert -> $key = $this->_dbo -> Escape( $value );
            }
        }

        $table = $this->handlePrefix($this->_object->table);
        
        $inserted = $this->_dbo->insertObject( $object_insert, $table, $this->_object->primary_key );
        
        if( $inserted > 0 ) {
            if( $clear ) {
                $this->clear();
            }
        }
        
        return $inserted;
    }
    
    /**
     * update define data
     * @return int
     * @throws \Exception
     */
    protected function update( $clear = true, $condition = '' ) {
        
        $conditionSentences = $this->_object->primary_key . ' = ' . $this->value;
        
        if( empty( $condition ) ){
            if ( !isset( $this->_object->primary_key ) ) {
                throw new Exception( 'primery key undefined' );
            }

            if ( !isset( $this->_object->value ) ) {
                throw new Exception( 'value update undefined' );
            }
        }
        else {
            $conditionSentences = $condition;
        }
        $object_insert = new stdClass();
        
        foreach ( $this->_object as $key => $value ) 
        {
            if( !in_array( $key, $this->_reserved_key ) )
            {
                $object_insert -> $key = $this->_dbo -> Escape( $value );
            }
        }

        $table = $this->handlePrefix($this->_object->table);
        
        $updated = $this->_dbo->updateObject( $object_insert, $table, $conditionSentences );
        
        if( $updated > 0 ) {
            if( $clear ) {
                $this->clear();
            }
        }
        
        return $updated;
        
    }

    /**
     * delete records
     * @param string $condition
     * @return int
     */
    protected function delete( $condition = '' ) {

        $table = $this->handlePrefix($this->_object->table);

        $sql = "DELETE FROM " . $table . " WHERE ";
        
        $addCondition = ( empty( $condition ) ) ? $this->_object->primary_key . " = " . $this->_object->value : $condition;
        
        $sql .= $addCondition;
        
        $resouce = $this->_dbo->query( $sql );
        
        return $this->_dbo->rowAffect( $resouce );
    }
    
    /**
     * you can remove one record with primary key value
     * @param int $primary_key_value
     * @return int
     */
    protected function destroy( $primary_key_value ) {
        return $this->delete( $this->_object->primary_key . ' = ' . $primary_key_value );
    }
    
    /**
     * clear elements defined
     */
    protected function clear(){

        $table = $this->handlePrefix($this->_object->table);

        $primary_key = $this->_object->primary_key;
        
        $this->_object = new stdClass();
        $this->_conditions = [];
        
        $this->_object->table = $table;
        $this->_object->primary_key = $primary_key;
    }
    
    /**
     * begin transaction in database
     */
    protected function begin(){
        $this->_dbo->beginTransaction();
    }
    
    /**
     * commit transation in database
     */
    protected function commit() {
        $this->_dbo->commitTransaction();
    }
    
    /**
     * rollback transaction in database
     * @param string $_EXCEPTION_MSG message on throw exception
     * @param bool $_THROW_EXCEPTION throw exception
     */
    protected function rollback( $_EXCEPTION_MSG = 'Rollback executing...', $_THROW_EXCEPTION = TRUE ) {
        $this->_dbo->rollbackTransaction( $_EXCEPTION_MSG, $_THROW_EXCEPTION );
    }
    
    /**
     * group by group
     * @param string $fields
     * @return \abstracts\ModelORM
     */
    protected function groupBy( $fields ) {
        $this->_sql .= " GROUP BY " . $fields;
        return $this;
    }
    
    /**
     * order by query
     * @param string $fields
     * @param string $sort type could be 'asc' or 'desc'
     * @return \abstracts\ModelORM
     */
    protected function orderBy( $fields, $sort = '' ) {
        $this->_sql .= " ORDER BY " . $fields . " " . $sort;
        return $this;
    }
    
    /**
     * Returns the escaped string
     * @param string $escapestring Required. The string to be escaped. Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
     */
    
    protected function escape( $escapestring ) {
        return $this->_dbo->Escape( $escapestring );
    }
    
    /**
     * execute set query, always you must call query method before this
     * @return \abstracts\ModelORM
     */
    protected function execute() {
        $this->_resource_db = $this->_dbo->query( $this->_sql );
        return $this;
    }
    
    /**
     * return affected rows
     * @return int
     */
    protected function getRowAffected(){
        return $this->_dbo->rowAffect( $this->_resource_db );
    }
    
    /**
     * return string link (union)
     * @param string $option (optional : default ALL)
     * @return string
     */
    protected function union( $option = 'ALL' ) {
        return ' UNION ' . $option;
    }
    
    /**
     * limit in query
     * @param int $quantity
     * @return \abstracts\ModelORM
     */
    protected function limit( $quantity ) {
        $this->_sql .= "LIMIT $quantity";
        return $this;
    }

    /**
     * put the prefix into table
     * @param $table
     * @return string
     */
    private function handlePrefix( $table ) {

        $position = "";
        $out = $table;

        if( isset($this->_object->table_prefix) && !empty($this->_object->table_prefix) )
        {
            $prefix = $this->_object->table_prefix;
            $position = (isset($this->_object->table_prefix_pos) && !empty($this->_object->table_prefix_pos)) ? $this->_object->table_prefix_pos : "start-and-end";

            switch ($position) {
                case "start-and-end":
                    $out = $prefix . $table . $prefix;
                break;

                case "start":
                    $out = $prefix . $table;
                    break;

                case "end":
                    $out = $table . $prefix;
                    break;

                default:
                    $out = $table;
                    break;
            }
        }

        return $out;

    }
}
