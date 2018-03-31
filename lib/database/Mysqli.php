<?php 

namespace lib\database;

defined('_EXEC_APP') or die('Ups! access not allowed');

use interfaces\Idatabase;
use lib\Config;
use Factory;
use Exception;
use stdClass;

class Mysqli extends \lib\database\Config implements Idatabase
{
    /**
     *
     * @var object mysql
     */
    private $_link = NULL;
    
    /**
     *
     * @var string sql sentences
     */
    private $_query = "";
    
    /**
     *
     * @var database resource
     */    
    private $_resource = NULL;

    /**
     * @var database link use into application
     */
    public static $db = NULL;
            
    /**
     * 
     * @param $_DEFAULT_CONNECTION_
     */
    
    function __construct( $_DEFAULT_CONNECTION_ = TRUE ) {
        
       if( $_DEFAULT_CONNECTION_ )
       {
           $this->connect();
       }
       
    }
    
    /**
     * get Link
     * @return link
     */
    
    public function getLink(){
        return $this->_link;
    }
    
    /**
     * Set properties or credentials
     * @param string $server
     * @param string $user
     * @param string $pass
     * @param string $db
     */
    
    public function setProperties( stdClass $properties ) {
        
        $std = new stdClass();
        
        $std -> server = $properties->server;
        
        $std -> user = $properties->user;
        
        $std -> pass = $properties->pass;
        
        $std -> db = $properties->db;
        
        $std -> port = $properties->port;
        
        parent::set($std);
    }
    
    /**
     * connect to database
     * @throws \Exception
     */
    
    public function connect() {
       
        if( Config::$_USE_DB === FALSE ){
            return NULL;
        }

        if( self::$db != NULL ) {
            $this->_link = self::$db;
            return NULL;
        }

        $this->_link = mysqli_connect($this->_SERVER_, $this->_USER_, $this->_PASS_, $this->_DB_);
        
        if ( !$this->_link )
        {
            $msg    =   "Error trying connect to database";
           //logger error
           Factory::loggerError($msg);
            
            throw new Exception( $msg );
        }

        self::$db = $this->_link;
    }
    
    /**
     * execute query
     * @param string $query
     * @return resource mysqli
     */
    
    public function query($query) {
        
        if( Config::$_USE_DB === FALSE ){
            return NULL;
        }
        
        $result = mysqli_query($this->_link, $query);
        
        if($result)
        {
            return $result;
        }

        $msg    =   " Sql Error -> $query ";
        Factory::loggerError($msg);

        if( !Config::$_DEVELOPING_ ) {
            $msg    =   " Internal Error please call the administrator ";
        }
        
        throw new Exception( $msg );
    }
    
    /**
     * 
     * @param string define sql sentence
     */
    
    public function setQuery($query) {
        $this->_query = $query;
        return $this;
    }
    
    /**
     * call this method after call setQuery
     */
    
    public function exec() {
        $this->_resource = $this->getObjectList($this->_query);
        return $this;
    }
    
    /**
     * get list count by records
     * @param array $resource resource database
     */
    
    public function getListCount($resource = NULL) {
        $res = ( $resource == NULL ) ? $this->_resource : $resource;
        return count($res);
    }
    
    /**
     * begin transaction
     */
    
    public function beginTransaction(){               
        $this->query("START TRANSACTION");
    }
    
    /**
     * commit transaction
     */
    
    public function commitTransaction(){
        $this->query("COMMIT");
    }
    
    /**
     * rollback transaction
     */
    
    public function rollbackTransaction( $_EXCEPTION_MSG = 'Rollback executing...', $_THROW_EXCEPTION = TRUE ){
        $this->query("ROLLBACK");
        if( $_THROW_EXCEPTION ){
            throw new Exception( $_EXCEPTION_MSG );
        }
    }

    /**
     * close connection
     * @return resource mysqli
     */
    
    public function closeConnection(){
        return mysqli_close($this->_link);
    }
    
    /**
     * execute query in database and return resource
     * @param string $query
     * @return array object resource database
     */
    
    public function getObjectList($query){

        $returning = array();
        
        $result = $this->query($query);

        if(is_object($result))
        {
            while( $row = mysqli_fetch_object( $result ) )
            {
                $returning[] = $row;
            }
        }
        
        return $returning;

    }
    
    /**
    * execute query in database and return one resource
    * @param string $query
    * @return stdClass resource database
    */ 
    
    public function getRowObjectList($query) {
        
        $resource   =   $this->getObjectList( $query );
        
        if( count( $resource ) > 0 ){
            return $resource[0];
        }
    }
    
    /**
     * execute query in database and return resource
     * @param string $query
     * @return array resource database
     */
    
    public function getArrayList($query){

        $returning = array();
        $result = $this->query($query);

        if(is_object($result))
        {
            while($row = mysqli_fetch_array($result))
            {
                $returning[] = $row;
            }
        }
        
        return $returning;

    }
    
    /**
     * execute query in database and return one resource
     * @param string $query
     * @return array resource database
     */
    
    public function getRowArrayList($query) {
        
        $resource   =   $this->getArrayList( $query );
        
        if( count( $resource ) > 0 ){
            return $resource[0];
        }
        
    }
    
    /**
     * 
     * @param \stdClass $values
     * @param string $table
     * @param string $filters
     * @return int
     */
    
    public function updateObject( stdClass $values, $table, $filters ){
        
        $update = "UPDATE %s SET %s WHERE ".$filters;
        
        $keys = "";
        foreach($values as $key => $value) {
            $keys .= $key . "= '".$value."',";
        }
        $update = sprintf( $update, $table, trim($keys,",") );
        
        $this->query($update);
        $affect = $this->rowAffect();
        
        if( $affect < 0 ){

            $msg    =   " Sql Error -> $update ";
            //logger
            Factory::loggerError($msg);
            if( !Config::$_DEVELOPING_ ) {
                $msg    =   " Internal Error please call the administrator ";
            }

            throw new Exception( $msg );
            
        }
        return $affect;
        
    }
    
    /**
     * 
     * @param array $values
     * @param string $table
     * @param string $filters
     * @return int
     */    
    public function update(array $values, $table, $filters){
        
        $update = "UPDATE %s SET %s WHERE ".$filters;
        
        $keys = "";
        foreach($values as $key => $value) {
            $keys .= $key . "= '".$value."',";
        }
        $update = sprintf( $update, $table, trim($keys,",") );
                
        $this->query($update);
        $affect = $this->rowAffect();
        
        if( $affect < 0 ){

            $msg    =   " Sql Error -> $update ";
            //logger
            Factory::loggerError($msg);
            if( !Config::$_DEVELOPING_ ) {
                $msg    =   " Internal Error please call the administrator ";
            }

            throw new Exception( $msg );
            
        }
        return $affect;
        
    }
    
    /**
     * 
     * @param array $values
     * @param string $table
     * @param string $id
     * @return int
     */
    
    public function insert( array $values,$table, $id = '') {
        
        $insert = "INSERT INTO %s(%s) VALUES(%s)";

        $keys = "";
        $valueStr = "";

        foreach($values as $key => $value) {
                $keys .= $key . ',';
                $valueStr .= "'" . $value . "',";
        }

        $insert = sprintf($insert, $table, trim($keys,","), trim($valueStr,","));
			          
        $this->query($insert);
        $affect = mysqli_insert_id($this->_link);
        
        if( $affect < 1 ){

            $msg    =   " Sql Error -> $insert ";
            //logger
            Factory::loggerError($msg);
            if( !Config::$_DEVELOPING_ ) {
                $msg    =   " Internal Error please call the administrator ";
            }

            throw new Exception( $msg );
            
        }
        
        return $affect;

    }
    
    /**
     * 
     * @param array $values
     * @param string $table
     * @param string $id
     * @return int
     */
    
    public function insertObject( \stdClass $values,$table, $id = '') {
        
        $insert = "INSERT INTO %s(%s) VALUES(%s)";

        $keys = "";
        $valueStr = "";

        foreach($values as $key => $value) {
                $keys .= $key . ',';
                $valueStr .= "'" . $value . "',";
        }

        $insert = sprintf($insert, $table, trim($keys,","), trim($valueStr,","));
			  
        $this->query($insert);
        $affect = mysqli_insert_id($this->_link);
        
        if( $affect < 1 ){

            $msg    =   " Sql Error -> $insert ";
            //logger
            Factory::loggerError($msg);
            if( !Config::$_DEVELOPING_ ) {
                $msg    =   " Internal Error please call the administrator ";
            }

            throw new \Exception( $msg );
            
        }
        
        return $affect;

    }
    
    /**
     * 
     * @param array $values
     * @param string $table
     * @return string
     */
    
    public function getInsert($values,$table) {
        
        $insert = "INSERT INTO %s(%s) VALUES(%s)";
		
        $keys = "";
        $valueStr = "";

        foreach($values as $key => $value) {
                $keys .= $key . ',';
                $valueStr .= "'" . $value . "',";
        }

        $insert = sprintf($insert, $table, trim($keys,","), trim($valueStr,","));
        
        return $insert;

    }
    
    /**
     * row affect
     * @return int
     */
    
    public function rowAffect( $resource = NULL ){
        return mysqli_affected_rows($this->_link);
    }
    
    /**
     * Returns the escaped string
     * @param string $escapestring Required. The string to be escaped. Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
     */
    
    public function Escape( $escapestring ) {
        return mysqli_real_escape_string( $this->_link, $escapestring );
    }        

}

?>