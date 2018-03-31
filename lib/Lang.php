<?php

namespace lib;

use lib\Config;

class Lang { 
    
    /**
     * get All Language
     * @param string $lang
     * @return json
     */
    public static function getJsonLanguage($lang){
        if ( !Config::$_DEVELOPING_ ) {
            $lang .= "_min";
        }
        $json = file_get_contents( _BASE_ . _DS_ . "lib" ._DS_. "lang" . _DS_ .$lang.".json");
        return $json;
    }    
    /**
     * get json for language
     * @param string $lang
     * @return json
     */
    private static function chargeJson($lang){
        $json = self::getJsonLanguage($lang);
        return json_decode($json,true);
    }    
    /**
     * get string with the language specified
     * @param string $key
     * @param stirng $lang
     * @return string
     */
    public static function get($key,$lang = 'en'){
        if( isset( $_SESSION["language"] ) ){
            $lang = $_SESSION["language"];
        }

        $alllang = Lang::chargeJson($lang);
        $valor = (isset($alllang[$key]))?$alllang[$key]:$key;    
        return html_entity_decode( $valor );
    }
    /**
     * check the json language file
     * @param $lang file name
     * @return bool
     */
    public static function checkJSON( $lang ){
        if( is_readable(_BASE_ . _DS_ . "lib" ._DS_. "lang" . _DS_ .$lang.".json") ) {
            return true;
        }
        return false;
    }
}
