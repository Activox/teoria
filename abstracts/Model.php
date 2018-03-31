<?php

namespace abstracts;

defined('_EXEC_APP') or die('Ups! access not allowed');

use lib\Config;
use lib\database\Mysqli;
use lib\database\Postgresql;

abstract class Model
{
    /**
     *
     * @var Idatabase
     */
    private $_dbo   =   null;    
    
    /**
     * 
     * @param \stdClass $properties
     */    
    protected function __construct( \stdClass $properties  =   null ) {
        
        $dbo    =   null;
        switch ( Config::$_DATABASE_ ) {
            case "mysqli":
                $dbo    =   new Mysqli(FALSE);
                break;
            case "postgresql":
                $dbo    =   new Postgresql(FALSE);
                break;
            default:
                
                break;
        } 
        
       if( $properties != null ) {
           $dbo->setProperties($properties);
       }
       
       //open connection
       $dbo->connect();
       
       //set dbo
       $this->_dbo  =   $dbo;
        
    }
    
    /**
     * get Dbo from Database
     * @return Idatabase
     */
    protected function getDbo(){
        return $this->_dbo;
    }
    
    /**
     * execute query in database and return resource
     * @param string $query
     * @return array object resource database
     */
    
    protected function getObjectList( $query ){
        return $this->_dbo->getObjectList($query);
    }
    
    /**
     * execute query in database and return resource
     * @param string $query
     * @return array resource database
     */
    
    protected function getArrayList( $query ) {
        return $this->_dbo->getArrayList($query);
    }
    
    /**
     * begin transaction
     */
    
    protected function beginTransaction() {
        $this->_dbo->beginTransaction();
    }
    
    /**
     * commit transaction
     */
    
    protected function commitTransaction(){
        $this->_dbo->commitTransaction();
    }
    
    /**
     * rollback transaction
     * @param string $_EXCEPTION_MSG message of exception
     * @param boolean $_THROW_EXCEPTION if you want throw a exception
     */
    protected function rollbackTransaction($_EXCEPTION_MSG = 'Rollback executing...', $_THROW_EXCEPTION = TRUE) {
        $this->_dbo->rollbackTransaction($_EXCEPTION_MSG, $_THROW_EXCEPTION);
    }
    
    /**
     * insert object
     * @param array $values
     * @param string $table
     * @param string $id
     * @return int
     */
    
    protected function insertObject( \stdClass $values, $table, $id = '' ) {
        return $this->_dbo->insertObject($values, $table, $id);
    }
    
    /**
     * 
     * @param \stdClass $values
     * @param string $table
     * @param string $filters
     * @return int
     */
    
    protected function updateObject( \stdClass $values, $table, $filters ){
        return $this->_dbo->updateObject($values, $table, $filters);
    }
    
    /**
     * Returns the escaped string
     * @param string $escapestring Required. The string to be escaped. Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
     */
    
    protected function Escape( $escapestring ) {
        return $this->_dbo->Escape( $escapestring );
    }  

    /**
     * row affect
     * @return int
     */
    
    protected function rowAffect( $resource = NULL ) {
        return $this->rowAffect( $resource );
    }
    
}