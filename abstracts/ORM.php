<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace abstracts;

defined('_EXEC_APP') or die('Ups! access not allowed');

use lib\Config;

/**
 * Description of ORM
 *
 * @author Miguel Peralta
 * 
 * @property string $primary_key you must define primary key field name of table to insert, update, delete
 * @property mixed $value define value to update and delete
 * @property string $table you must define this property (table name) to get result and insert or update entity
 * @property string $alias form to call the table
 * @property string $find_by find resource by field name
 */
abstract class ORM 
{    
    /**
     *
     * @var Idatabase
     */
    private $_dbo   =   null,
            $_object = null,
            $_sql = "",
            $_resource_db = null,
            $_reserved_key = ["primary_key", "value", "table", "alias", "find_by"]
            ;    
    
    /**
     * 
     * @param \stdClass $properties
     */    
    protected function __construct( \stdClass $properties  =   null ) {
        
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
        $this->_object = new \stdClass();        
        
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
     * set query
     * @param string $sql
     * @return \abstracts\ORM
     */    
    public function query( $sql ){
        $this->_sql = $sql;
        return $this;
    }
    
    /**
     * you can call these methods after get (inner, left, right, where ) you must respect the references order
     * @return \abstracts\ORM
     * @throws \Exception
     */
    protected function get( $FIELD_ALIAS = FALSE ) {
        
        $this->_sql = " SELECT ";
        $attr = "";
        
        if( !empty( $this->_object ) ) {
            
            $alias = ( !empty( $this->_object->alias ) ) ? $this->_object->alias . "." : "";
            
            foreach ( $this->_object as $key => $value ) 
            {
                if( !in_array( $key, $this->_reserved_key ) )
                {
                    $addAlias = ( $FIELD_ALIAS ) ? $alias . $value . "," : $value . ","; 
                    $attr .= $addAlias;
                }
            }
            
            if( !empty( $attr ) ){
                $attr = trim($attr, ",");
            } else {
                $attr = ( $FIELD_ALIAS ) ? $alias . "*" : "*";
            }
                        
            if( !isset( $this->_object->table ) ){
                throw new \Exception( 'undefined table' );
            }
            
            $alias = str_replace(".", "", $alias);
            
            $this->_sql .=  $attr . " FROM " . $this->_object->table . " " . $alias;
                                    
            return $this;
            
        } else {
            throw new \Exception( 'Properties not found' );
        }
        
    }
    
    /**
     * get all records of table
     * @param string $FORMAT return format resource (optional) could be 'object' or 'array'
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
     * @param string $format return format resource (optional) could be 'object' or 'array'
     * @param string $operator compare operator (optional) could be '=', 'whatever' to compare with any value...
     * @param array $where custom where array
     * @return \abstracts\ORM
     * @throws \Exception
     */
    protected function find( $value, $format = 'object', $operator = '=', array $where = array() ) {
        
        if ( !isset( $this->_object->find_by ) ) {
            throw new \Exception( 'undefined find by' );
        }

        $res = null;
        $conditions = ( count( $where ) ) ? $where : [ $this->_object->find_by => [ "operator" => $operator, "value" => $value, "nextcondition" => ""] ];
        
        switch ($format) {
            case "object":
                $res    = $this->get()->where( $conditions )->getObject();
                break;

            default:
                $res    = $this->get()->where( $conditions )->getArray();
                break;
        }
        
        return $res;
    }
    
    /**
     * conditions
     * @param array $properties properties condition ["field"=>["operator"=>?,"value"=>?, "nextcondition"=>?]]
     * @return \abstracts\ORM
     * @throws \Exception
     */
    protected function where( array $properties ){
        
        $this->_sql .= " WHERE ";
                
        foreach ($properties as $key => $value) 
        {
            if( !empty( $key ) )
            {                
                if( isset( $value["operator"] ) ) {
                    
                    switch ( trim( strtolower( $value["operator"] ) ) ) 
                    {
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
                            
                            if( !isset( $value['value'] ) ){
                                throw new \Exception( 'value cannot be empty' );
                            }
                            
                            if( is_string( $value['value'] ) ) {
                                $this->_sql .= " ( " . $key . " ".$value['operator']." '" . $value['value'] . "' OR '".$value['value']."' ".$value['operator']." '' ) " . $value['nextcondition'] . " ";
                            } else {
                                if( is_integer( $value['value'] ) || is_numeric( $value['value'] ) ) {
                                    $this->_sql .= " ( " . $key . " ".$value['operator']." " . $value['value'] . " OR ".$value['value']." ".$value['operator']." 0 ) " . $value['nextcondition'] . " ";
                                }
                                else{
                                    throw new \Exception( 'we cant find value type' );
                                }
                            }
                            
                            break;
                    }
                    
                }                
                
            }                                    
        }
        
        return $this;                    
                
    }
    
    /**
     * relation operator
     * @param string $table table name
     * @param array $properties field => compare field
     * @param string $OPERATOR operator ('=','!=', '>', '<', etc...)
     * @return \abstracts\ORM
     */
    protected function rigth( $table, array $properties, $OPERATOR = '=' ){
        
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
     * @param string $OPERATOR operator ('=','!=', '>', '<', etc...)
     * @return \abstracts\ORM
     */
    protected function left( $table, array $properties, $OPERATOR = '=' ){
        
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
     * @param string $OPERATOR operator ('=','!=', '>', '<', etc...)
     * @return \abstracts\ORM
     */
    protected function inner( $table, array $properties, $OPERATOR = '=' ){
        
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
            throw new \Exception( 'undefined primery key' );
        }
        
        $object_insert = new \stdClass();
        
        foreach ( $this->_object as $key => $value ) 
        {
            if( !in_array( $key, $this->_reserved_key ) )
            {
                $object_insert -> $key = $this->_dbo -> Escape( $value );
            }
        }
        
        $inserted = $this->_dbo->insertObject( $object_insert, $this->_object->table, $this->_object->primary_key );
        
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
                throw new \Exception( 'undefined primery key' );
            }

            if ( !isset( $this->_object->value ) ) {
                throw new \Exception( 'undefined value update' );
            }
        }
        else {
            $conditionSentences = $condition;
        }
        $object_insert = new \stdClass();
        
        foreach ( $this->_object as $key => $value ) 
        {
            if( !in_array( $key, $this->_reserved_key ) )
            {
                $object_insert -> $key = $this->_dbo -> Escape( $value );
            }
        }
        
        $updated = $this->_dbo->updateObject( $object_insert, $this->_object->table, $conditionSentences );
        
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
        
        $sql = "DELETE FROM " . $this->_object->table . " WHERE ";
        
        $addCondition = ( empty( $condition ) ) ? $this->_object->primary_key . " = " . $this->_object->value : $condition;
        
        $sql .= $addCondition;
        
        $resouce = $this->_dbo->query( $sql );
        
        return $this->_dbo->rowAffect( $resouce );
    }
    
    /**
     * clear elements defined
     */
    protected function clear(){
        $this->_object = new \stdClass();
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
     * @return \abstracts\ORM
     */
    protected function groupBy( $fields ) {
        $this->_sql .= " GROUP BY " . $fields;
        return $this;
    }
    
    /**
     * order by query
     * @param string $fields
     * @return \abstracts\ORM
     */
    protected function orderBy( $fields ) {
        $this->_sql .= " ORDER BY " . $fields;
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
     * @return \abstracts\ORM
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
     * @return \abstracts\ORM
     */
    protected function limit( $quantity ) {
        $this->_sql .= " LIMIT $quantity ";
        return $this;
    }
}
