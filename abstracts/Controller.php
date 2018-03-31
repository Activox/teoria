<?php

namespace abstracts;

defined('_EXEC_APP') or die('Ups! access not allowed');

use lib\Config;

abstract class Controller
{
    /**
     *
     * @var object current controller
     */
    private $_objectController  =   null;
    
    /**
     * 
     * @param object $objectController
     */
    protected function __construct( $objectController ) {        
        $this->_objectController    =   $objectController;
    }
    
    /**
     * show the display to user
     * @param string $view current view
     */
    abstract function display( $view = '', array $params = array() );  
    
    /**
     * get Model by controller
     * @param string $model access with another model, you must especify the module
     */
    protected function getModel( $model = '', $properties = null ){
        
        $referenceModel =   \Route::$_current_model;

        if( !empty( $model ) ) {
            
            $stack  = explode("/", $model);
            if( count($stack) < 2 ){
                throw new \Exception( 'You must specify the module and model' );
            }
            
//            $referenceModel =   Config::$_MODULES_ . '\\' . $stack[0] . '\\Models\\' . $stack[1] . "Model";
            $referenceModel =  $stack[0] . '\\Models\\' . $stack[1] . "Model";
        }                        
        
        return new $referenceModel( $properties );
        
    }
    
}