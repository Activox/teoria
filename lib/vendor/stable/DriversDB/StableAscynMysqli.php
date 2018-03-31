<?php

namespace lib\vendor\stable\DriversDB;

use lib\vendor\stable\IstableAscyn;

/**
 * Stable Ascyn Para MYSQLI
 *
 * @version 1.0
 * @author Enmanuel Bisono Payamps <enmanuel0894@gmail.com>
 */

class StableAscynMysqli implements IstableAscyn {
    
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
    private function __construct( $resource ) {        
        if( !is_array( $resource ) ){
            self::$resource = $resource;
        } else {
            self::$resource = mysqli_connect(
                $resource['DB_HOST'], 
                $resource['DB_USER'], 
                $resource['DB_PASS'], 
                $resource['DB_NAME']
            );
        }
    }
    
    // Documentacion en la interface IstableAscyn
    public static function connect( $resource ) {
        if( self::$instance == null ){
            self::$instance = new StableAscynMysqli( $resource );
        }
        return self::$instance;
    }
    
    // Documentacion en la interface IstableAscyn
    public function getData( $query ) {
         try{
            $this -> _result = mysqli_query( self::$resource, $query );
            return mysqli_fetch_all( $this -> _result, MYSQLI_ASSOC );
        } catch( mysqli_sql_exception $e ){
            echo $e -> getMessage();
            exit;
        }
    }
    
    // Documentacion en la interface IstableAscyn
    public function getFieldName( $index = -1 ) {
        
        $names_fields = mysqli_fetch_fields( $this -> _result );
        
        if( $index > -1 ){
            foreach( $names_fields as $k => $v ){
                if( $k == $index ){
                    $fields = $v -> name;
                    break;
                }
            }
        } else {
            $fields = array();
            foreach( $names_fields as $v ){
                $fields[] = $v -> name;
            }
        }
        return $fields;
    }
    
    // Documentacion en la interface IstableAscyn
    public function getFieldNum( $name ) {
        $names_fields = mysqli_fetch_fields( $this -> _result );
        $index = -1;
        if(  count( $names_fields ) ){
            foreach( $names_fields as $k => $v ){
                if( $name == $v -> name ){
                    $index = $k;
                    break;
                }
            }
        }
        return $index;
    }

    public function generateFilterLikes( $fields, $str ) {
        $condition = "";
        if( gettype( $fields) == "array" ){
            foreach( $fields as $k => $v ){
                $condition .=  $v . " LIKE '%" . $str . "%' OR ";
            }
            $condition = rtrim( $condition, " OR " );
        } else {
            $condition = $fields . " LIKE '%" . $str . "%'";
        }
        return $condition;
    }

}