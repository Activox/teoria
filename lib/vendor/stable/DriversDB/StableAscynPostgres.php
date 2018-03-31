<?php

namespace lib\vendor\stable\DriversDB;

use lib\vendor\stable\IstableAscyn;

/**
 * Stable Ascyn Para Postgres SQL
 *
 * @version 1.0
 * @author Enmanuel Bisono Payamps <enmanuel0894@gmail.com>
 */
class StableAscynPostgres implements IstableAscyn {
    
    /**
     *
     * @var type 
     */
    private static $instance = null;

    /**
     *
     * @var type 
     */
    private static $resource = null;
    
    /**
     *
     * @var type 
     */
    private $_result = null;
    
    /**
     * 
     * @param type $resource
     */
    private function __construct( $resource ){
        if( is_resource( $resource ) ){
            self::$resource = $resource;
        } else {
            self::$resource = pg_connect("
                host="     . $resource['DB_HOST'] . "
                port="     . $resource['DB_PORT'] . "
                dbname="   . $resource['DB_NAME'] . "
                user="     . $resource['DB_USER'] . "
                password=" . $resource['DB_PASS']
            );
        }
    }
    
    /**
     * 
     * @param type $resource
     * @return type
     */
    public static function connect( $resource ){        
        if( self::$instance == null ){
            self::$instance = new StableAscynPostgres( $resource );
        }
        return self::$instance;
    }
    
    /**
     * 
     * @param type $query
     * @return type
     */
    public function getData( $query ){
        try{
            $this -> _result = pg_query( $query );
            if($this -> _result)
            return  pg_fetch_all( $this -> _result );
        } catch( Exception $e ){
            echo $e -> getMessage();
            exit;
        }
    }
    
    /**
     * 
     * @param type $index
     * @return type
     */
    public function getFieldName( $index = -1 ){
        if( $index > -1 ){
            $fields = pg_field_name( $this -> _result, $index );
        } else {
            $fields = array();
            $nfl = pg_num_fields( $this -> _result );
            for( $x = 0; $x < $nfl; $x++ ){
                $fieldname = pg_field_name( $this -> _result, $x );
                $fields[] = $fieldname;
            }
        }
        return $fields;
    }
    
    /**
     * 
     * @param type $name
     * @return type
     */
    public function getFieldNum( $name ){
		return pg_field_num( $this -> _result, $name );
	}
    
    /**
     * 
     * @param type $fields
     * @return type
     */
    public function generateFilterLikes( $fields, $str ){
        $condition = "";
        if( gettype( $fields) == "array" ){
            foreach( $fields as $k => $v ){
                $condition .=  $v . "::VARCHAR ILIKE '%" . $str . "%' OR ";
            }
            $condition = rtrim( $condition, " OR " );
        } else {
            $condition = $fields . "::VARCHAR ILIKE '%" . $str . "%'";
        }
        return $condition;
    }

}
