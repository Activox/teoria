<?php

namespace lib\vendor\stable;

use lib\Lang;

/**
 * Stable Ascyn
 * Esta version del Stable es Ascincrona permitira
 * paginar los datos con ajax
 *
 * Esta nueva version permite utilizar varios servidores de bases de datos entre ellos estan:
 *      - Postgres
 *      - Mysql (Mysqli)
 * 
 * @version 2.0
 * @author Enmanuel Bisono Payamps <enmanuel0894@gmail.com>
 */
 class StableAscyn {
     
    /**
     * $db Instancia de el motor de base de datos que se este utilizando
     * 
     * @var object
     */
    private $db = null;
    
    /**
     * $query   Query generado por el desarrollador para presentar los datos en el Stable
     * 
     * @var string 
     */
    private $query = null;
    
    /**
     * Construct
     * 
     * @param string  $engine   Motor de base de datos en le que trabajara el
     *                          stable. Motores soportados [Postgres, Mysqli]
     * @param mixed  $resource  Recursos de la conexion de la conexion de la
     *                          base de datos o array con los datos de dicha
     *                          base de datos para tratar de establecer dicha
     *                          conexion.
     * @param string $query     Query que devolvera los datos para presentarlos
     *                          en el stable del lado del cliente.
     */
    public function __construct( $engine, $resource, $query ){
        try{            
            $motor = ucwords( strtolower( $engine ) );
            $ns = "lib/vendor\stable\DriversDB\StableAscyn" . $motor;
            $ns = str_replace("/", "\\", $ns);            
            $this -> db = call_user_func(array($ns,"connect"),$resource);
        } catch( Exception $e ){
            echo $e -> getMessage();
            exit;
        }

        $this -> query = $query;
    }
    
    /**
     * getResourceDB    Obtiene la instancia del motor de base de datos que se este utilizando,
     *                  eso es por motivo de extender funcionabilidades al Stable
     * @return object
     */
    protected function getResourceDB(){
        return $this -> db;
    }
    
    /**
     * getOriginalQuery     Obtiene el query original del desarrollador para extender 
     *                      funcionabilidades al stable
     * 
     * @return string
     */
    protected function getOriginalQuery(){
        return $this -> query;
    }
    
    /**
     * getDataPrimaryKey              Obtiene la informacion contenida en un campo primario
     * 
     * @param string   $primaryKey    Nombre del campo con la clave primaria
     * @param resource $result        Recurso devuelto de la base de datos
     * 
     * @return array                  Datos alamacenados en el primary Key
     */
    private function getDataPrimaryKey( $primaryKey = null, $result ){
        $datos = array();
        if( $result ){
            if( !empty( $primaryKey ) ){
                foreach( $result as $k => $v ){
                    $datos[] = $v[ $primaryKey ];
                }
            }
        }
		return $datos;
    }

    /**
     * getDataRequest   Captura y filtra el numero la pagina que se va a 
     *                  mostrar y la cantidad de records, que son pasados
     *                  por la URL (POST)
     *
     * @return array    Paginacion recurrente y la cantidad de records a 
     *                  mostrar
     */
    protected function getPagesRows(){
        $page = filter_input( INPUT_POST, 'page', FILTER_VALIDATE_INT );
        $rows = filter_input( INPUT_POST, 'rows', FILTER_VALIDATE_INT );
        return array(
            "page" => $page?($page - 1) * $rows:0,
            "rows" => $rows?$rows:0
        );
    }
    
    /**
     *  queryPrepare            Se encarga de preparar el query con los filtro de paginacion, 
     *                          filtros por criterios especificado por el usuario y filtros de 
     *                          ordenamiento de datos
     * 
     * @param string $query     Query realizado por el desarrollador
     * 
     * @return string           Query preparado
     */
    protected function queryPrepare( $query ){
       
        $sql = "SELECT * FROM (" . $query . ") as q ";
        
        // Esta ejecucion de este query se envia para poder capturar los nombre de los
        // campos seleccionados en el query que consultara los datos a la base de datos
        $this -> db -> getData( "SELECT * FROM (" . $query . ") as q LIMIT 1" );
        
        // Verificando si la clave para filtrar los datos esta en el array POST
        if( array_key_exists( 'field', $_POST ) ){
            
            // Filtrando los caracteres especiales de la cadena de caracteres a buscar
            $str = addslashes( $_POST['str'] );
            
            // SI SE SELECCIONA LA OPCION DE TODOS LOS CAMPOS
            if( $_POST['field'] == "all" ){
                // Capturando los campos seleccionados en el query
                $fields = $this -> db -> getFieldName();
                
                // Generando la condicion para filtrar los datos por todos los campos 
                // seleccionados en el query
                $sql .= "WHERE " . $this -> db -> generateFilterLikes( $fields, $str ) . " ";
                
            // SI SE SELECCIONA UN CAMPO EN ESPECIFICO
            } else {
                
                // Filtrando el indice del campo seleccionado a filtrar los records
                $fieldsearch = filter_input( INPUT_POST, 'field', FILTER_VALIDATE_INT );
                
                // Capturando el nombre del campo para filtrar los datos
				$field = $this -> db -> getFieldName( $fieldsearch );
                
                // Validando que el criterio enviado desde el cliente sea diferente de vacio
				if( !empty( $str ) ){
					$sql .= "WHERE " . $this -> db -> generateFilterLikes( $field, $str );
				}
            }
        }   
        
        // VERIFICANDO SI EL USUARIO QUIERE ORDENAR LOS DATOS MOSTRADOS
        if( array_key_exists( 'fieldorder', $_POST ) && !empty( $_POST['fieldorder'] ) ){
            
            // Capturando el indice del campo seleccionado a ordenar los datos
            $fieldorder = filter_input( INPUT_POST, 'fieldorder', FILTER_VALIDATE_INT );
            
            // Capturando el tipo de ordenamiento que se le aplicara a los datos [ASC, DESC]
            $typeorder  = addslashes( $_POST['typeorder'] );
            
            // Capturando el nombre del campo a seleccionado a ordenar los campos
            $field = $this -> db -> getFieldName( $fieldorder );
            
            $sql .= "ORDER BY " . $field . " " . $typeorder . " ";
        }
        
        return $sql;
    }
    
    /**
     * addLimitQuery        Añade el limite de records, capturado desde la url para controlar la
     *                      paginacion de los mismos
     * 
     * @param string $sql   Query a limitar
     * 
     * @return string       Query limitado
     */
    protected function addLimitQuery( $sql ){
        $pagesRows = $this -> getPagesRows();
        $sql .= " LIMIT " . $pagesRows['rows'] . " OFFSET " . $pagesRows['page'];
        return $sql;
    }
    
    /**
     * getTotalRecords          Se encarda de obtener el total de records encontrados por el query
     *                          para poder realizar el calculo de la paginacion del lado del 
     *                          cliente
     * 
     * @param string $query     Query origial realizado por el desarrollador
     * 
     * @return integer          Total de records encontrados
     */
    protected function getTotalRecords( $query ){
        $sql = "SELECT COUNT(1) as total FROM ( " . $query . " ) as q";
        $data = $this -> db -> getData( $sql );
        return $data[0]['total'];
    }
    
    /**
     * replaceField          Se encarga de reemplazar el underscorel por un espacio y poner la 
     *                       primera letra en mayuscula de cada campo seleccionado en el query 
     *                       original
     * 
     * @param array $data   Arreglo con lo nombres de los campos seleccionados
     * 
     * @return array        Arreglo con los nombres de los campos y el cambio realizado
     */
    protected function replaceField( $data ){
        if( gettype( $data ) == "array" ){
            foreach( $data as $k => $v ){
                $data[$k] = ucwords( str_replace( "_", " ", $v ) );
            }
        } else {
            $data = ucwords( str_replace( "_", " ", $data ) );
        }
        return $data;
    }
    
    /**
     * get                      Devuelve todos los datos solicitados en base al query enviado 
     *                          en el constructor
     * 
     * @param array $options    Arreglo de opciones para para funciones opcionales como:
     *                          retornar los codigos del campo primario para hablitar la 
     *                          opcion de [editar, eliminar, ...] del lado del cliente.
     * 
     * @return string           JSON con los datos devuelto de la base de datos
     * 
     */
    public function get( $options = array() ){
        
        // Preparando query para agregar los filtros enviados por el cliente
        $sqlPrepare = $this -> queryPrepare( $this -> query );
        
        // Limitando el query para la paginacion de los datos
        $sql = $this -> addLimitQuery( $sqlPrepare );

        // Consultando los datos
        $data = $this -> db -> getData( $sql );
        
        // Obteniendo los datos de la clave primaria
        $dataPrimaryKey = $this -> getDataPrimaryKey(
            // Clave primaria
            array_key_exists('pk', $options )? $options['pk']: null,
            // Datos devuelto del query
            $data
        );            
        
        //translate stable result
        $data = $this->translateResult($data);
            
        $json = array(
            "fields"   => $this -> db -> getFieldName(),
            "rows"     => $data,
            "totalrow" => $this -> getTotalRecords( $sqlPrepare ),
            "pks"      => $dataPrimaryKey
        );

        return json_encode( $json );
    }
    
    private function translateResult( $data ) {

        for ( $i = 0; $i < count( $data ); $i++ ) 
        {
            if( !empty($data[$i]) ) {
                foreach ($data[$i] as $key => $value) {
                    $data[$i][$key] = html_entity_decode(Lang::get($value));
                }
            }
        }
        
        return $data;
        
    }

}
