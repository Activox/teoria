<?php

namespace lib\vendor\stable;

/**
 * Stable Ascyn Interface
 * 
 * @version 1.0
 * @author Enmanuel Bisono Payamps <enmanuel0894@gmail.com>
 */

 interface IstableAscyn {
    /**
     * connect  Realiza la conexion en caso de que no exista un recurso de base de datos
     *
     * Este metodo debe ser estatico para poder utilizar el patron de diseño Singleton
     *
     * @param mixed     $resource
     *
     * @return object   Instancia del objecto
     */
    public static function connect( $resource );

    /**
     * getData                  Optiene los records enviado en el query
     *
     * @param string $query     Query a ejecutar en la base de datos
     * 
     * @return resource         Records devuelto del query
     */
    public function getData( $query );
    
    /**
     * getFieldName             Obtiene el nombre de todos los campos seleccionado en el query o
     *                          uno en especifico
     *
     * @param integer $index    Indice del campo a buscar, si no se pasa ningun indice el
     *                          metodo debera retornar todos los campos almacenado en el
     *                          resource del query ejecutado
     * @return array            Listado de campos encontrados
     */
    public function getFieldName( $index = -1 );
    
    /**
     * getFieldNum  Busca el indice de un campo dado
     * 
     * @param string $name  Nombre del campo a buscar el indice
     * 
     * @return integer  Indice que pertenece el campo buscado
     */
    public function getFieldNum( $name );
    
    /**
     * generateFilterLikes       Genera la condicion por la cual se filtraran los datos en el
     *                           Stable
     * 
     * @param array  $fields     Listado con los nombre de los campos para generar la condicion
     * @param string $str        Valor a filtrar en la busqueda
     * 
     * @return string            Condicion generada 
     */
    public function generateFilterLikes( $fields, $str );
 }
