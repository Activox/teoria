<?php

namespace lib\database;

defined('_EXEC_APP') or die('Ups! access not allowed');

/**
 * All configuration app, how Database credential
 *
 * @author Miguel Peralta
 */

class Config
{
    /**
     * server database
     * @var string 
     */
    protected $_SERVER_    = "localhost";
    
    /**
     * user database
     * @var string
     */
    protected $_USER_      = "root";
    
    /**
     * pass database
     * @var string
     */
    protected $_PASS_      = "";
    
    /**
     * database connection
     * @var string
     */
    protected $_DB_        = "db_simulacion";

	/**
     *
     * @var int port database
     */
    protected $_PORT_       =   5432;
    
    /**
     * set configuration to database
     * @param \stdClass $std
     */
    protected function set( \stdClass $std ){
        
        $this->_SERVER_     = $std -> server;
        
        $this->_USER_       = $std -> user;
        
        $this->_PASS_       = $std -> pass;
        
        $this->_DB_         = $std -> db;
		
        $this->_PORT_       = $std -> port;
        
    }
    
}
