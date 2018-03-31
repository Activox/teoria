<?php

defined('_EXEC_APP') or die( 'Sorry, the application stopped' );

use lib\Config;
use lib\routes\Web;
use lib\routes\Post;

class Route
{    
    /**
     *
     * @var string variable with the current controller
     */
    private static $_current_controller     =   "";
    /**
     *
     * @var string variable with the current model
     */
    public static $_current_model          =   "";
    /**
     *
     * @var string variable with the current view
     */
    public static $_current_view           =   "";
    
    /**
     *
     * @var string variable with the current namespace
     */
    private static $_current_namespace      =   "";
          
    /**
     * run request
     * @return void
     */    
    public static function _get( $url, array $params = array(), $content = "" ) {
        
        $content    =   ( empty( $content ) ) ? "html" : $content;

        $requestURL =   $url;
        
        $ocurrences  =   self::returnOcurrence($requestURL, $content );
        
        $founded    =  self::searchOcurrence( $ocurrences, $requestURL );

        if( count( $founded ) < 1 ){
            throw new Exception( 'Web rule not found' );
        }

        $key = key($founded);
        
        if( empty( $key ) ){
            throw new Exception( 'Web rule key not found' );
        }

        //valid parameters
        $keyValue  = explode("/", $key);        
        $splitRequest   = explode("/", $requestURL);
        
        $parameters     =   array();
        $classes        =   $founded[$key];
        
        if( count( $keyValue ) > 0 && count( $splitRequest ) > 0 )
        {
            for( $i = 0; $i < count( $keyValue ); $i++ ) 
            {
                if( strpos($keyValue[$i], "[") !== FALSE && strpos($keyValue[$i], "]") !== FALSE )
                {
                    array_push($parameters, $splitRequest[$i]);
                }
            }
        }
                       
        if( count( $params ) > 0 ){
            for( $i = 0; $i < count($params); $i++ ){
                array_push($parameters, $params[$i]);
            }
        }
        
        $content_    =   self::readClasses( $classes, $parameters );       
           
        $response = null;

        switch ($content) {
            case "json":
                $response   = json_encode($content_);
                break;
            case "html":
                
                $response   =   $content_;
                
                if( strpos($response, '.php') !== FALSE ) 
                {
                    if( strpos($response, "View") === FALSE  ) {
                        $stack  = explode(".", $response);
                        $response   =   $stack[0] . "View." . $stack[1];
                    }                    
                            
                    $path   =   _BASE_ . _DS_ . Config::$_MODULES_ . _DS_ . self::$_current_namespace . 'Views' . _DS_ . $response;

                    $path   = str_replace("\\", "/", $path);
                    
                    if( file_exists($path) ) {                        
                        require_once $path;
                    }
                    else {
                        throw new Exception( 'view not found' );
                    }
                    return;
                }
                                
                break;
            case "text":
                $response   =  (string) str_replace(".php", "", $content_);
                break;
            default:
                $response   =   "content undefined";
                break;
        }

        return $response;  
        
    }
    
    /**
     * create namespace for call all classes
     * @param string $module
     * @return \stdClass
     */    
    private static function createNameSpace( $module ){
        
//        $namespace  = Config::$_MODULES_ . "\\" . $module . "\\";
        $namespace  = $module . "\\";
        
        $std    =   new stdClass();
        
        $std->namespace             =   $namespace;
        $std->namespaceController   =   $namespace."Controllers\\";
        $std->namespaceModel        =   $namespace."Models\\";
        
        return $std;
        
    }
    
    /**
     * get References class
     * @param string $classes
     * @return \stdClass
     * @throws Exception
     */
    private static function getReferences( $classes ){
        
        $exec_method    =   "display";
        $display        =   TRUE;
        $setView        =   "";
            
        if( empty( $classes ) ){
            throw new Exception( 'The references Class not found' );
        }
        
        list( $module, $refMethod)  = explode("@", $classes);

        $diff_ref_method = explode(".", $refMethod);
        if( count( $diff_ref_method ) > 1 ){
            list( $reference, $method_view )    = $diff_ref_method;
            $array_met_vie = explode("*", $method_view);            
            if( count($array_met_vie) > 1 ) {
                list( $exec_method, $setView ) = $array_met_vie;
            } else {
                list( $exec_method ) = $array_met_vie;
            }            
            $display    =   FALSE;
        } else {
            list( $reference )    = $diff_ref_method;
        }

        if( $exec_method == "display") { $display = TRUE; }
        
        $ns  =   self::createNameSpace($module);
        
        $std    =   new stdClass();
        
        $std->controller    =   $ns -> namespaceController . $reference . "Controller";
        $std->model         =   $ns -> namespaceModel . $reference . "Model";
        $std->view          =   $reference . "View";
        $std->otherview     =   $setView;
        $std->method        =   $exec_method;
        $std->display       =   $display;
        
        self::$_current_controller  =   $std->controller;
        
        self::$_current_model       =   $std->model;
        
        self::$_current_view        =   $std->view;
        
        self::$_current_namespace   =   $ns->namespace;
        
        return $std;
    }
    
    /**
     * read Classes
     * @param string $classes
     * @param array $params
     * @throws \Exception
     */    
    private static function readClasses( $classes, $params ){
          
        try
        {            
            $ref    = self::getReferences($classes);            
            
            if( $ref->display ) {
                array_unshift($params, $ref->view );
                
                $stack  =   $params;
                
                $v      =   array_shift($stack);
                                
                $params     =   array();

                array_push($params, $v);
                array_push($params, $stack);
            } 
            
            /**
            * get class controller
            */
            
            $reflection_class = new ReflectionClass( $ref->controller );           
            
            /**
             * get exec method
             */

            $reflection_method = $reflection_class->getMethod( $ref->method );
            $parameters = $reflection_method->getParameters();

            $class = $reflection_class->name;

            //set application or controller
            Factory::set($class);

            $method = $reflection_method;               

            $required_param = count( self::requiredParameters( $parameters ) );                  

            if( count($params) > 0 && ( count( $required_param ) > count( $params ) )  )
            {
                throw new Exception( "specify all params" );
            }
            
            //set aux view
            if( $ref->method == "display" ) {
                //when you send the parameters to one view
                if( isset( $params[1][0] ) ) {
//                    $params[0] = $params[1][0];                
                    $params[0] = ( !empty( $ref->otherview ) ) ? $ref->otherview : $ref->view;                
                } else {
                    $params[0] = $ref->otherview;
                }
                self::$_current_view = $params[0];
            }
            
            $content    =   ( count( $params ) > 0 ) ? $method->invokeArgs( new $class(), $params ) : $content = $method->invoke( new $class() );
            
            return $content;

        }
        catch( ReflectionException $ex)
        {
            throw new Exception ( $ex->getMessage() . ' on file ' . $ex->getFile() . ' line ' . $ex->getLine() );
        }
        
    }
    
    /**
     * search ocurrence on rules
     * @param string $requestURL http request
     * @return array
     */    
    public static function returnOcurrence( $requestURL, $content ){
        
        $diff   = explode("/", $requestURL);
                     
        switch ($content) {
            case "html":
                $webInterfaces  =   Web::$_rules;
                break;
            default:
                $webInterfaces  =   Post::$_rules;    
                break;
        }        
        
        $ocurrences =   array();
        
        //search ocurrence on rules
        for( $i = 0; $i < count($diff); $i++ )
        {
            if( !empty( $diff[$i] ) )
            {
                foreach ($webInterfaces as $key => $value) 
                {                
                    if( stristr( strtolower( $key ), strtolower( $diff[$i] ) ) !== FALSE )
                    {
                        $ocurrences[$key]   =   $value;
                    }
                }
            }
        }
        
        return $ocurrences;
    }
    
    /**
     * search into occurences
     * @param array $ocurrences ocurrences of rules
     * @param string $requestURL http request
     * @return array
     */    
    public static function searchOcurrence( $ocurrences, $requestURL ){

        $founded    =   array();
        
        $auxFound   =   array();
        
        $diff   = explode("/", $requestURL);
        
        $countRequest   = count($diff);

        /***********************verify if ocurrency exists*************************************************************/
        $thereArePosibleOccurrency = false;

        foreach ($ocurrences as $key => $value)
        {
            $diff_ocurrency = explode("/", $key);
            $breakOcurrency = false;
            $thisisnotoccurrency = true;
            $counterAux = 0;

            if ( count($diff_ocurrency) != count($diff) ) {
                continue;
            }

            for($i=0;$i<count($diff_ocurrency);$i++)
            {
                $validParams = false;

                if ( strpos($diff_ocurrency[$i], "[") !== FALSE && strpos($diff_ocurrency[$i], "]") !== FALSE )
                {
                    $validParams = true;
                }

                for($p=$counterAux;$p<count($diff);$p++)
                {

                    if ( $diff_ocurrency[$i] != $diff[$p] && $validParams == false ) {
                        $breakOcurrency = true;
                        break;
                    }

                    if ( $validParams && !isset($diff[$p]) ) {
                        $breakOcurrency = true;
                        break;
                    }

                    $counterAux = $p+1;
                    break;
                }

                if ( $breakOcurrency ) {
                    $thisisnotoccurrency = false;
                    break;
                }

            }

            if ( $thisisnotoccurrency ) {
                $thereArePosibleOccurrency = true;
                break;
            }

        }

        if ( !$thereArePosibleOccurrency ) {
            return array();
        }

        /**************************************************************************************************************/
        
        foreach ($ocurrences as $key => $value) 
        {
            $diff_  = explode('/', $key);            
            $countOcurrence = count($diff_);
                       
            if( $countRequest == $countOcurrence )
            {
                //if not found match
                if( count( $auxFound ) < 1 )
                    $auxFound[$key]     =   $value;
                
                $found  =   TRUE;
                for( $c=0;$c<count($diff);$c++ ) 
                {                                        
                    if( ( strpos($diff_[$c], "[") === FALSE && strpos($diff_[$c], "]") === FALSE ) && $diff[$c] !== $diff_[$c] ) 
                    {
                        $found = FALSE;
                        break;
                    }                   
                }
                
                if( $found )
                {
                    $founded[$key]  =   $value;
                    return $founded;
                }                
            }
        }

        return ( count( $founded ) > 0 ) ? $founded : $auxFound;
        
    }
    
    /**
     * Get js
     * @param array $js
     * @param string $_MODULE module where the files will be searched (optional)
     * @param array $_DIRECTORIES_ tree directory after main module (optional)
     * @param bool $_SEARCH_ROOT flag if you want search all file into root directory or in tree directory (optional)
     */    
    public static function getJs( array $js, $_MODULE = "", $_DIRECTORIES_ = array(), $_SEARCH_ROOT = TRUE ){
        
        $define_dir = Config::$_ROOT_JS . _DS_;
        
        if( count( $_DIRECTORIES_ ) > 0 ){
            
            if( $_SEARCH_ROOT == FALSE )
                $define_dir = "";
            
            for($i=0;$i<count($_DIRECTORIES_);$i++){
                $define_dir .= $_DIRECTORIES_[$i] . _DS_;
            }
        }
        
        $dir    =   ( $_SEARCH_ROOT === FALSE ) ? _DIR_MODULE_ . _DS_ : "";
        
        $is_module  =   (!empty($_MODULE)) ? $_MODULE . _DS_ : "";
        
        $path   =   _HOST_ . _DIRECTORY_ . _DS_ . $dir . $is_module . $define_dir;
        
        $path_   =   _BASE_ . _DS_ . $dir . $is_module . $define_dir;
        
        if( count( $js ) )
        {
            for( $i = 0; $i < count( $js ); $i++ )
            {
                if( file_exists( $path_ . $js[$i] . '.js' ) )
                {    
                    echo '<script type="text/javascript" src="'. $path . $js[$i] . '.js"></script>';
                }
            }
        }
                        
    }
    
    /**
     * Get css
     * @param array $css
     * @param string $_MODULE module where the files will be searched (optional)
     * @param array $_DIRECTORIES_ tree directory after main module (optional)
     * @param bool $_SEARCH_ROOT flag if you want search all file into root directory or in tree directory (optional)
     */    
    public static function getCss( array $css, $_MODULE = "", $_DIRECTORIES_ = array(), $_SEARCH_ROOT = TRUE ){
        
        $define_dir = Config::$_ROOT_CSS . _DS_;
        
        if( count( $_DIRECTORIES_ ) > 0 ){
            
            if( $_SEARCH_ROOT == FALSE )
                $define_dir = "";
            
            for($i=0;$i<count($_DIRECTORIES_);$i++){
                $define_dir .= $_DIRECTORIES_[$i] . _DS_;
            }
        }
        
        $dir    =   ( $_SEARCH_ROOT === FALSE ) ? _DIR_MODULE_ . _DS_ : "";
        
        $is_module  =   (!empty($_MODULE)) ? $_MODULE . _DS_ : "";
        
        $path   =   _HOST_ . _DIRECTORY_ . _DS_ . $dir . $is_module . $define_dir;
        
        $path_   =   _BASE_ . _DS_ . $dir . $is_module . $define_dir;
        
        if( count( $css ) )
        {
            for( $i = 0; $i < count( $css ); $i++ )
            {
                if( file_exists( $path_ . $css[$i] . '.css' ) )
                {    
                    echo '<link href="'.$path . $css[$i].'.css" media="screen" rel="stylesheet" type="text/css" >';
                }
            }
        }
        
    }
    
    /**
     * get any library
     * @param array $libraries file name
     * @param string $_MAIN_DIRECTORY_ main directory from path root
     * @param string $_EXTENSION_ extension file (without dot) (optional)
     * @param string $_MODULE module where the files will be searched (optional)
     * @param array $_DIRECTORIES_ tree directory after main module (optional)
     * @param bool $_SEARCH_ROOT flag if you want search all file into root directory or in tree directory (optional)
     */
    public static function getLibrary( array $libraries, $_MAIN_DIRECTORY_, $_EXTENSION_ = '', $_MODULE = "", $_DIRECTORIES_ = array(), $_SEARCH_ROOT = TRUE ) {
        
        $define_dir = $_MAIN_DIRECTORY_ . _DS_;
        
        if( count( $_DIRECTORIES_ ) > 0 ){
            
            if( $_SEARCH_ROOT == FALSE )
                $define_dir = "";
            
            for($i=0;$i<count($_DIRECTORIES_);$i++){
                $define_dir .= $_DIRECTORIES_[$i] . _DS_;
            }
        }
        
        $dir    =   ( $_SEARCH_ROOT === FALSE ) ? _DIR_MODULE_ . _DS_ : "";
        
        $is_module  =   (!empty($_MODULE)) ? $_MODULE . _DS_ : "";
        
        $path   =   _HOST_ . _DIRECTORY_ . _DS_ . $dir . $is_module . $define_dir;
        
        $path_   =   _BASE_ . _DS_ . $dir . $is_module . $define_dir;
        
        if( count( $libraries ) )
        {
            for( $i = 0; $i < count( $libraries ); $i++ )
            {
                switch ($_EXTENSION_) 
                {
                    case 'js':
                        
                            if( file_exists( $path_ . $libraries[$i] . '.js' ) )
                            {    
                                echo '<script type="text/javascript" src="'. $path . $libraries[$i] . '.js"></script>';
                            }

                        break;
                    
                    case 'css':

                            if( file_exists( $path_ . $libraries[$i] . '.css' ) )
                            {    
                                echo '<link href="'.$path . $libraries[$i].'.css" media="screen" rel="stylesheet" type="text/css" >';
                            }
                        
                        break;
                        
                    case 'php':
                        
                            if( file_exists( $path_ . $libraries[$i] . '.php' ) )
                            {    
                                require_once $path . $libraries[$i] . '.php';
                            }
                        
                        break;

                    default:
                        
                            $file_content = file_get_contents($path . $libraries[$i] . '.' . $_EXTENSION_);
                        
                            echo $file_content;
                        
                        break;
                }                                
            }
        }
                        
    }
    
    /**
     * verify required params by method
     * @param array $parameters
     * @return array
     */    
    private static function requiredParameters( Array $parameters ) {
        $returning = array();
        if( !empty($parameters) )
        {
                for( $i = 0; $i < count( $parameters ); $i++ )
                {
                        if( !$parameters[$i]->isOptional() )
                        {
                                array_push(
                                        $returning,
                                        array(
                                                "name" => $parameters[$i]->name,
                                                "position" => $parameters[$i]->getPosition()
                                        )
                                );
                        }
                }
        }

        return $returning;
    }
    
    /**
     * header layout
     * @param string $header
     */
    public static function header( $header = '' ){
        
        $header = ( !empty( $header ) ) ? $header : "header";
        
        $root_path  =   _BASE_ . _DS_ . _LAYOUT_ . _DS_ . $header . ".php";
        if( ( file_exists($root_path) ) ) {
            require_once $root_path;
        } else {
            throw new Exception( 'Layout header not found' );
        }        
    }
    
    /**
     * footer layout
     * @param string $footer
     */
    public static function footer( $footer = '' ){
        
        $footer = ( !empty( $footer ) ) ? $footer : "footer";
        
        $root_path  =   _BASE_ . _DS_ . _LAYOUT_ . _DS_ . $footer . ".php";
        if( ( file_exists($root_path) ) ) {
            require_once $root_path;
        } else {
            throw new Exception( 'Layout footer not found' );
        }  
    }
    
}