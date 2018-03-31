<?php

namespace Defaults\Models;

defined('_EXEC_APP') or die('Ups! access not allowed');

use abstracts\ModelORM;
use stdClass;
use Exception;

class DefaultModel extends ModelORM
{
    /**
     * DefaultModel constructor.
     * @param stdClass|null $properties object { server : ??, user : ??, pass : ??, db : ??, port : ??}
     */
    public function __construct(stdClass $properties = null)
    {
        parent::__construct($properties);
        $this->table = "actividades";
        $this->primary_key = "id_record";
        $this->value = 0;
        $this->find_by = "act";
    }

    /**
     *
     */
    public function getEjecucion()
    {
        $sql = "SELECT COALESCE(max(ejecucion),0) max_ejecion FROM actividades";
        return $this->query($sql)->objectList();
    }

    public function getOperations()
    {
        $sql = "SELECT id_record,name  FROM operacion";
        return $this->query($sql)->objectList();
    }

    public function getOperationsProblems($id_operation)
    {
        $sql = "select id_record from problema where id_operacion =  $id_operation";
        return $this->query($sql)->objectList();
    }

    public function setOrden($orden)
    {
        $sql = " INSERT INTO ordenes (name,pares) VALUES ('$orden->name', '$orden->pares');  ";
        $this->query($sql)->objectList();
        $sql2 = "SELECT max(id_record) max_id FROM ordenes;";
        return $this->query($sql2)->objectList();
    }

    public function setActividad($params)
    {
        $sql = "
        INSERT INTO actividades(id_modulo,id_orden,id_problema,id_operacion,tiempo,ejecucion)
        VALUES('$params->id_modulo','$params->id_orden','$params->id_problem','$params->id_operation','$params->tiempo','$params->ejecucion')
        ";
        return $this->query($sql)->objectList();
    }

    public function getActividad($params)
    {
        $sql = "
        SELECT
          ord.id_record,
          CONCAT('Orden ',ord.name) orden,
          ord.pares,
          act.id_operacion,
          act.tiempo,
            (
                SELECT sum(act2.tiempo)
                FROM actividades act2
                WHERE act2.id_orden = ord.id_record
            ) total_time
        FROM ordenes ord
          INNER JOIN actividades act ON act.id_orden = ord.id_record 
        WHERE ord.active = 1 and act.ejecucion = $params->max_ejecion and act.id_modulo = $params->id_modulo
        GROUP BY 1,2,4
        ORDER BY 1 
        ";
        return $this->query($sql)->objectList();
    }

    public function getTotalByOperation($params)
    {
        $sql = "
        SELECT
          SUM(CASE WHEN act.id_operacion = 1 THEN act.tiempo ELSE 0 END) planificacion,
          SUM(CASE WHEN act.id_operacion = 2 THEN act.tiempo ELSE 0 END) cutting,
          SUM(CASE WHEN act.id_operacion = 3 THEN act.tiempo ELSE 0 END) handsewing,
          SUM(CASE WHEN act.id_operacion = 4 THEN act.tiempo ELSE 0 END) horno,
          SUM(CASE WHEN act.id_operacion = 5 THEN act.tiempo ELSE 0 END) bottoming,
          SUM(CASE WHEN act.id_operacion = 6 THEN act.tiempo ELSE 0 END) packing
        FROM ordenes ord
          INNER JOIN actividades act ON act.id_orden = ord.id_record
        WHERE ord.active = 1 and act.ejecucion = $params->max_ejecion and act.id_modulo = $params->id_modulo;
        ";
        return $this->query($sql)->objectList();
    }

    public function getTotalProblemsByEjection($params)
    {
        $sql = "
        SELECT
           act.id_problema,
          pro.descripcion problema,
          count(*)        cant_problema,
          (
            SELECT sum(a.cant_problema) sum_problema
            FROM (
                   SELECT count(*) cant_problema
                   FROM actividades act
                     INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
                     INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
                   WHERE act.id_problema != 0 and act.ejecucion = $params
                   GROUP BY act.id_problema
                 ) a
          )               sum_problem
        FROM actividades act
          INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
          INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
        WHERE act.id_problema != 0  and act.ejecucion = $params
        GROUP BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getTotalProblems()
    {
        $sql = "
        SELECT
          act.id_problema,
          pro.descripcion        problema,
          sol.descripcion        solucion,
          count(*) cant_problema
        FROM actividades act
          INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
          INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
        WHERE act.id_problema != 0 
        GROUP BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getTotalProduction()
    {
        $sql = "
        SELECT
          modu.id_record,
          modu.name      modulo,
          sum(ord.pares) pares
        FROM actividades act
          INNER JOIN modulo modu ON modu.id_record = act.id_modulo
          INNER JOIN ordenes ord ON ord.id_record = act.id_orden
        WHERE act.active = 1
        GROUP BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getProblemsModule($params){
        $sql="
        SELECT
          act.id_modulo,
          act.id_problema,
          pro.descripcion problema,
          count(*)        cant_problema
        FROM actividades act
          INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
          INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
        WHERE act.id_problema != 0 and act.ejecucion = $params
        GROUP BY 1,2
        ";
         return $this->query($sql)->objectList();
    }

}
